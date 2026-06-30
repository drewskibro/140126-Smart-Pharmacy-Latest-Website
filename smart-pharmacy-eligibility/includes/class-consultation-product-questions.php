<?php
/**
 * Per-product "additional consultation questions".
 *
 * Murtaza adds product-specific questions on each P-medicine product as
 * he launches it, on top of the editable base set. This registers an
 * ACF repeater on the WC product edit screen and appends its rows to
 * the consultation form via the `spe_consultation_questions` filter the
 * base question model already exposes — so the form renders
 * base + per-product extras + disclaimer, and the server validates the
 * extras too.
 *
 * ACF is the theme's content engine (ACF Pro is required there), so we
 * register the field group through ACF when it's present and degrade
 * gracefully when it isn't: no ACF -> no extra questions, base form
 * still works. Every ACF call is guarded.
 *
 * @package SmartPharmacyEligibility
 */

defined( 'ABSPATH' ) || exit;

/**
 * SPE_Consultation_Product_Questions.
 */
class SPE_Consultation_Product_Questions {

	const FIELD = 'spe_product_questions';

	/** Question types an admin may pick (must match the form renderer). */
	const TYPES = array(
		'text'     => 'Short text',
		'textarea' => 'Long text',
		'radio'    => 'Single choice (radio)',
		'select'   => 'Single choice (dropdown)',
		'checkbox' => 'Multiple choice (checkboxes)',
	);

	/**
	 * Wire hooks.
	 */
	public static function register() {
		// Only fires when ACF is active.
		add_action( 'acf/init', array( __CLASS__, 'register_field_group' ) );

		// Append product questions to the consultation form.
		add_filter( 'spe_consultation_questions', array( __CLASS__, 'append' ), 10, 2 );
	}

	/**
	 * Register the ACF repeater on the WC product post type.
	 */
	public static function register_field_group() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		$type_choices = self::TYPES;

		acf_add_local_field_group(
			array(
				'key'      => 'group_spe_product_questions',
				'title'    => __( 'Consultation questions (this product)', 'smart-pharmacy-eligibility' ),
				'fields'   => array(
					array(
						'key'          => 'field_spe_product_questions',
						'label'        => __( 'Additional consultation questions', 'smart-pharmacy-eligibility' ),
						'name'         => self::FIELD,
						'type'         => 'repeater',
						'instructions' => __( 'Extra questions asked only for this product, shown after the standard questions. Leave empty to use just the standard set.', 'smart-pharmacy-eligibility' ),
						'button_label' => __( 'Add question', 'smart-pharmacy-eligibility' ),
						'layout'       => 'block',
						'sub_fields'   => array(
							array(
								'key'      => 'field_spe_pq_label',
								'label'    => __( 'Question', 'smart-pharmacy-eligibility' ),
								'name'     => 'label',
								'type'     => 'text',
								'required' => 1,
								'wrapper'  => array( 'width' => '60' ),
							),
							array(
								'key'     => 'field_spe_pq_type',
								'label'   => __( 'Answer type', 'smart-pharmacy-eligibility' ),
								'name'    => 'type',
								'type'    => 'select',
								'choices' => $type_choices,
								'default_value' => 'textarea',
								'wrapper' => array( 'width' => '25' ),
							),
							array(
								'key'     => 'field_spe_pq_required',
								'label'   => __( 'Required', 'smart-pharmacy-eligibility' ),
								'name'    => 'required',
								'type'    => 'true_false',
								'ui'      => 1,
								'wrapper' => array( 'width' => '15' ),
							),
							array(
								'key'   => 'field_spe_pq_help',
								'label' => __( 'Help text', 'smart-pharmacy-eligibility' ),
								'name'  => 'help',
								'type'  => 'text',
							),
							array(
								'key'               => 'field_spe_pq_options',
								'label'             => __( 'Options (one per line)', 'smart-pharmacy-eligibility' ),
								'name'              => 'options',
								'type'              => 'textarea',
								'rows'              => 3,
								'conditional_logic' => array(
									array( array( 'field' => 'field_spe_pq_type', 'operator' => '==', 'value' => 'radio' ) ),
									array( array( 'field' => 'field_spe_pq_type', 'operator' => '==', 'value' => 'select' ) ),
									array( array( 'field' => 'field_spe_pq_type', 'operator' => '==', 'value' => 'checkbox' ) ),
								),
							),
						),
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'product',
						),
					),
				),
				'menu_order' => 20,
			)
		);
	}

	/**
	 * Filter callback: append this product's extra questions after the
	 * base set. No-op unless a product_id is in scope and ACF returns
	 * rows for it.
	 *
	 * @param array[] $questions Base question records.
	 * @param array   $args      get_questions() args (may carry product_id).
	 * @return array[]
	 */
	public static function append( $questions, $args ) {
		$product_id = isset( $args['product_id'] ) ? (int) $args['product_id'] : 0;
		if ( $product_id <= 0 ) {
			return $questions;
		}
		return array_merge( $questions, self::get_for_product( $product_id ) );
	}

	/**
	 * Normalise this product's repeater rows into the same record shape
	 * the form renderer + validator expect from base questions.
	 *
	 * Keys are derived from the question text so a stored answer stays
	 * matched to its question even if the rows are reordered.
	 *
	 * @param int $product_id
	 * @return array[]
	 */
	public static function get_for_product( $product_id ) {
		if ( ! function_exists( 'get_field' ) ) {
			return array();
		}

		$rows = get_field( self::FIELD, $product_id );
		if ( empty( $rows ) || ! is_array( $rows ) ) {
			return array();
		}

		$out   = array();
		$order = 1000; // After the base set (orders 0..~80), before the disclaimer.
		$seen  = array();
		foreach ( $rows as $row ) {
			$label = isset( $row['label'] ) ? trim( (string) $row['label'] ) : '';
			if ( '' === $label ) {
				continue;
			}

			$type = isset( $row['type'] ) ? (string) $row['type'] : 'text';
			if ( ! isset( self::TYPES[ $type ] ) ) {
				$type = 'text';
			}

			$key = 'pq_' . substr( md5( $label ), 0, 8 );
			// Guard against two questions hashing to the same key.
			while ( isset( $seen[ $key ] ) ) {
				$key .= 'x';
			}
			$seen[ $key ] = true;

			$out[] = array(
				'key'      => $key,
				'label'    => $label,
				'help'     => isset( $row['help'] ) ? (string) $row['help'] : '',
				'type'     => $type,
				'required' => ! empty( $row['required'] ),
				'options'  => self::parse_options( isset( $row['options'] ) ? $row['options'] : '' ),
				'enabled'  => true,
				'order'    => $order,
			);
			$order += 10;
		}

		return $out;
	}

	/**
	 * Split a newline-delimited options string into a clean array.
	 *
	 * @param string $raw
	 * @return string[]
	 */
	protected static function parse_options( $raw ) {
		$lines = preg_split( '/\r\n|\r|\n/', (string) $raw );
		$out   = array();
		foreach ( $lines as $line ) {
			$line = trim( $line );
			if ( '' !== $line ) {
				$out[] = $line;
			}
		}
		return $out;
	}
}
