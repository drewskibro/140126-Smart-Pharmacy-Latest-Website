<?php
/**
 * The editable base question set for the P-med consultation form.
 *
 * The 9 base questions live here as CODE DEFAULTS. Admin edits (label,
 * help text, required, enabled, choice options, order) are stored in the
 * spe_consultation_questions option and merged OVER the defaults by key
 * on every read. That gives us the best of both:
 *
 *   - Murtaza can reword every question in admin (the brief's
 *     "editable, not hardcoded" requirement) and his wording survives
 *     plugin updates.
 *   - The canonical question set (keys + input types) is owned by code,
 *     so a future plugin update can add a 10th base question and it
 *     shows up automatically without wiping his overrides.
 *
 * Per-product *additional* questions are a separate concern (their own
 * ClickUp card) and plug in via the `spe_consultation_questions` filter
 * applied at the bottom of get_questions().
 *
 * @package SmartPharmacyEligibility
 */

defined( 'ABSPATH' ) || exit;

/**
 * SPE_Consultation_Questions.
 */
class SPE_Consultation_Questions {

	const OPTION = 'consultation_questions';

	/**
	 * Editable fields an admin may override per question. `key` and
	 * `type` are deliberately NOT here — they're structural, owned by
	 * code, and changing them would break stored submissions.
	 *
	 * @var string[]
	 */
	const EDITABLE = array( 'label', 'help', 'required', 'enabled', 'options', 'order' );

	/**
	 * The 9 base questions, in display order.
	 *
	 * Supported `type`: date | text | textarea | radio | select | checkbox.
	 * `options` applies to radio/select/checkbox only.
	 *
	 * @return array[] Keyed by question key.
	 */
	public static function defaults() {
		$q = array(
			'dob' => array(
				'label'    => __( 'What is your date of birth?', 'smart-pharmacy-eligibility' ),
				'help'     => __( 'We use this to confirm you are old enough for this treatment.', 'smart-pharmacy-eligibility' ),
				'type'     => 'date',
				'required' => true,
			),
			'who_for' => array(
				'label'    => __( 'Who is this consultation for?', 'smart-pharmacy-eligibility' ),
				'help'     => '',
				'type'     => 'radio',
				'required' => true,
				'options'  => array( 'Myself', 'Someone else' ),
			),
			'what_for' => array(
				'label'    => __( 'What are you seeking treatment for?', 'smart-pharmacy-eligibility' ),
				'help'     => __( 'Briefly describe your symptoms or the condition you want to treat.', 'smart-pharmacy-eligibility' ),
				'type'     => 'textarea',
				'required' => true,
			),
			'what_tried' => array(
				'label'    => __( 'What have you already tried?', 'smart-pharmacy-eligibility' ),
				'help'     => __( 'Any treatments, remedies or lifestyle changes — and whether they helped.', 'smart-pharmacy-eligibility' ),
				'type'     => 'textarea',
				'required' => false,
			),
			'other_meds' => array(
				'label'    => __( 'Are you taking any other medications?', 'smart-pharmacy-eligibility' ),
				'help'     => __( 'Include prescription medicines, over-the-counter products and supplements. Write "None" if not applicable.', 'smart-pharmacy-eligibility' ),
				'type'     => 'textarea',
				'required' => true,
			),
			'other_conditions' => array(
				'label'    => __( 'Do you have any other medical conditions?', 'smart-pharmacy-eligibility' ),
				'help'     => __( 'Tell us about any ongoing or past conditions. Write "None" if not applicable.', 'smart-pharmacy-eligibility' ),
				'type'     => 'textarea',
				'required' => true,
			),
			'pregnancy' => array(
				'label'    => __( 'Are you pregnant, breastfeeding, or trying to conceive?', 'smart-pharmacy-eligibility' ),
				'help'     => '',
				'type'     => 'radio',
				'required' => true,
				'options'  => array( 'No', 'Yes', 'Prefer not to say / not applicable' ),
			),
			'previous_use' => array(
				'label'    => __( 'Have you used this treatment before?', 'smart-pharmacy-eligibility' ),
				'help'     => __( 'If yes, please add any details in the final box below.', 'smart-pharmacy-eligibility' ),
				'type'     => 'radio',
				'required' => true,
				'options'  => array( 'No', 'Yes' ),
			),
			'anything_else' => array(
				'label'    => __( 'Is there anything else you would like the pharmacist to know?', 'smart-pharmacy-eligibility' ),
				'help'     => '',
				'type'     => 'textarea',
				'required' => false,
			),
		);

		// Stamp each with its key + a default order so callers always
		// get a complete, normalised record.
		$order = 0;
		foreach ( $q as $key => &$def ) {
			$def['key']      = $key;
			$def['enabled']  = true;
			$def['order']    = $order;
			$def['required'] = ! empty( $def['required'] );
			if ( ! isset( $def['help'] ) ) {
				$def['help'] = '';
			}
			if ( ! isset( $def['options'] ) ) {
				$def['options'] = array();
			}
			$order += 10;
		}
		unset( $def );

		return $q;
	}

	/**
	 * The merged, ordered, ENABLED-only question set for rendering the
	 * front-end form. Defaults overlaid with the admin's stored edits.
	 *
	 * @param array $args { Optional. 'include_disabled' => bool. }
	 * @return array[] Ordered list (numeric, not keyed).
	 */
	public static function get_questions( $args = array() ) {
		$include_disabled = ! empty( $args['include_disabled'] );

		$defaults = self::defaults();
		$stored   = (array) spe_option( self::OPTION, array() );

		$merged = array();
		foreach ( $defaults as $key => $def ) {
			$override = isset( $stored[ $key ] ) && is_array( $stored[ $key ] ) ? $stored[ $key ] : array();
			foreach ( self::EDITABLE as $field ) {
				if ( array_key_exists( $field, $override ) ) {
					$def[ $field ] = $override[ $field ];
				}
			}
			$def['required'] = ! empty( $def['required'] );
			$def['enabled']  = ! empty( $def['enabled'] );
			$def['order']    = isset( $def['order'] ) ? (int) $def['order'] : 0;
			$merged[ $key ]  = $def;
		}

		uasort(
			$merged,
			static function ( $a, $b ) {
				return $a['order'] <=> $b['order'];
			}
		);

		if ( ! $include_disabled ) {
			$merged = array_filter(
				$merged,
				static function ( $q ) {
					return ! empty( $q['enabled'] );
				}
			);
		}

		/**
		 * Filter the consultation question set.
		 *
		 * Per-product additional questions (separate ClickUp card) hook
		 * here to append product-specific questions after the base set.
		 *
		 * @param array[] $questions Ordered question records.
		 * @param array   $args      Args passed to get_questions().
		 */
		return apply_filters( 'spe_consultation_questions', array_values( $merged ), $args );
	}

	/**
	 * Editable form copy (intro + disclaimer). The disclaimer has its
	 * own card for final wording; this is a sensible default so the
	 * form is never published without one.
	 *
	 * @param string $which 'intro' | 'disclaimer'.
	 * @return string
	 */
	public static function get_copy( $which ) {
		$defaults = array(
			'intro'      => __( 'Please answer the questions below honestly and as fully as you can. A pharmacist will review your answers before any medicine is supplied.', 'smart-pharmacy-eligibility' ),
			'disclaimer' => __( 'Your answers will be reviewed by a pharmacist who may contact you with further questions before dispatch.', 'smart-pharmacy-eligibility' ),
		);
		$default = isset( $defaults[ $which ] ) ? $defaults[ $which ] : '';
		$stored  = spe_option( 'consultation_' . $which, null );
		return ( null === $stored || '' === $stored ) ? $default : (string) $stored;
	}
}
