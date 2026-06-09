<?php
/**
 * Server-side mirror of the JS eligibility rules.
 *
 * The JS in eligibility.js applies the same rules client-side, but
 * because client-side checks are trivially bypassed (devtools, curl)
 * we re-run them here on every save_final call. This is the source of
 * truth -- the JS is for UX, not security.
 *
 * Returns either:
 *   array( 'eligible' => true )
 *   array( 'eligible' => false, 'reason' => 'Patient under 18.' )
 *
 * @package SmartPharmacyEligibility
 */

defined( 'ABSPATH' ) || exit;

/**
 * SPE_Eligibility_Rules.
 */
class SPE_Eligibility_Rules {

	/**
	 * Evaluate the full assessment payload.
	 *
	 * @param array $p Payload.
	 * @return array { eligible: bool, reason: string|null }
	 */
	public static function evaluate( array $p ) {

		$age_band = isset( $p['ageBand'] ) ? sanitize_text_field( $p['ageBand'] ) : '';
		if ( 'under-18' === $age_band ) {
			return self::fail( __( "Our weight loss plan isn't suitable for people under 18 years old.", 'smart-pharmacy-eligibility' ) );
		}
		if ( '75-over' === $age_band ) {
			return self::fail( __( "Our weight loss plan isn't suitable for people over 75 years old.", 'smart-pharmacy-eligibility' ) );
		}

		// Re-derive age from DOB if provided as a backstop.
		if ( ! empty( $p['dob'] ) ) {
			$dob_ts = strtotime( $p['dob'] );
			if ( $dob_ts ) {
				$age_years = (int) gmdate( 'Y' ) - (int) gmdate( 'Y', $dob_ts );
				if ( $age_years < 18 ) {
					return self::fail( __( 'You must be at least 18 years old to use this service.', 'smart-pharmacy-eligibility' ) );
				}
				if ( $age_years > 74 ) {
					return self::fail( __( "Our weight loss plan isn't suitable for people over 75 years old.", 'smart-pharmacy-eligibility' ) );
				}
			}
		}

		// BMI gate (with South Asian adjustment).
		$bmi       = isset( $p['bmi'] ) ? (float) $p['bmi'] : 0.0;
		$ethnicity = isset( $p['ethnicity'] ) ? strtolower( (string) $p['ethnicity'] ) : '';
		$is_asian  = ( false !== strpos( $ethnicity, 'asian' ) );
		$min_bmi   = $is_asian ? 23.0 : 27.0;
		if ( $bmi > 0 && $bmi < $min_bmi ) {
			$reason = sprintf(
				/* translators: 1: BMI value, 2: minimum BMI required. */
				__( 'Based on your BMI of %1$.1f, weight loss medication is not clinically appropriate at this time. A BMI of %2$.0f or above is required.', 'smart-pharmacy-eligibility' ),
				$bmi,
				$min_bmi
			);
			return self::fail( $reason );
		}

		// Female screening gates: pregnant / breastfeeding / trying to conceive.
		$sex = isset( $p['sex'] ) ? strtolower( (string) $p['sex'] ) : '';
		if ( 'female' === $sex ) {
			$pregnant      = isset( $p['pregnant'] ) ? strtolower( (string) $p['pregnant'] ) : '';
			$breastfeeding = isset( $p['breastfeeding'] ) ? strtolower( (string) $p['breastfeeding'] ) : '';
			$conceive      = isset( $p['conceive'] ) ? strtolower( (string) $p['conceive'] ) : '';
			if ( 'yes' === $pregnant || 'yes' === $breastfeeding || 'yes' === $conceive ) {
				return self::fail( __( 'For safety reasons, weight loss medications cannot be prescribed during pregnancy, when planning to become pregnant, or while breastfeeding.', 'smart-pharmacy-eligibility' ) );
			}
		}

		// Bariatric within 6 months is an automatic fail.
		$bariatric_recent = isset( $p['bariatricRecent'] ) ? strtolower( (string) $p['bariatricRecent'] ) : '';
		if ( 'yes' === $bariatric_recent ) {
			return self::fail( __( 'Weight loss medication is not suitable within 6 months of bariatric surgery.', 'smart-pharmacy-eligibility' ) );
		}

		return array(
			'eligible' => true,
			'reason'   => null,
		);
	}

	/**
	 * @param string $reason
	 * @return array
	 */
	protected static function fail( $reason ) {
		return array(
			'eligible' => false,
			'reason'   => $reason,
		);
	}
}
