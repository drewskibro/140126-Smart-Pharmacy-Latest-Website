<?php
/**
 * The eligibility checker markup, rendered by the [smart_pharmacy_eligibility] shortcode.
 *
 * Direct port of the bolt.new prototype with three changes:
 *   - <html>/<head>/<body> stripped (it's embedded in a WP page).
 *   - Inline <style> stripped (lives in assets/css/eligibility.css now).
 *   - Inline <script> stripped (lives in assets/js/eligibility.js now).
 *
 * @package SmartPharmacyEligibility
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="spe-app">
	<div class="spe-main">
		<div class="spe-container">

			<!-- Screen 1: Agreement -->
			<div id="screen-1" class="screen active">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">0%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 5%"></div></div>
					</div>
				</div>
				<h1><?php esc_html_e( 'Do you agree to the following?', 'smart-pharmacy-eligibility' ); ?></h1>
				<div class="checkbox-group">
					<?php
					$agreements = array(
						__( 'I am completing this consultation for myself and to the best of my knowledge', 'smart-pharmacy-eligibility' ),
						__( 'I will disclose any medical conditions, serious illnesses or operations I have had', 'smart-pharmacy-eligibility' ),
						__( 'I will disclose any prescription medications I am currently taking and agree to use only one weight loss treatment at a time', 'smart-pharmacy-eligibility' ),
						__( 'I agree to the Terms & Conditions, Terms of Sale, and confirm that I have read the Privacy Policy', 'smart-pharmacy-eligibility' ),
						__( 'I understand that withholding or providing false information can severely harm my health and may result in life-threatening consequences', 'smart-pharmacy-eligibility' ),
					);
					foreach ( $agreements as $i => $text ) :
						?>
						<label class="checkbox-item">
							<input type="checkbox" class="agreement-checkbox" data-index="<?php echo (int) $i; ?>" />
							<span><?php echo esc_html( $text ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>
				<button class="button button-primary" id="agree-continue" disabled><?php esc_html_e( 'Agree and start consultation →', 'smart-pharmacy-eligibility' ); ?></button>
			</div>

			<!-- Screen 1b: Early Capture -->
			<div id="screen-1b" class="screen">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">20%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 20%"></div></div>
					</div>
				</div>
				<h2><?php esc_html_e( "Let's save your assessment", 'smart-pharmacy-eligibility' ); ?></h2>
				<p><?php esc_html_e( 'Enter your details so our clinicians can send your eligibility result and support you with the next steps if treatment is appropriate.', 'smart-pharmacy-eligibility' ); ?></p>
				<div class="form-group">
					<div class="form-grid-2">
						<div>
							<label class="form-label"><?php esc_html_e( 'First name', 'smart-pharmacy-eligibility' ); ?></label>
							<input type="text" class="form-input" id="early-first-name" placeholder="e.g. Sarah" />
						</div>
						<div>
							<label class="form-label"><?php esc_html_e( 'Last name', 'smart-pharmacy-eligibility' ); ?></label>
							<input type="text" class="form-input" id="early-last-name" placeholder="e.g. Jones" />
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="form-label"><?php esc_html_e( 'Email address', 'smart-pharmacy-eligibility' ); ?></label>
					<input type="email" class="form-input" id="early-email" placeholder="e.g. sarah@example.com" />
				</div>
				<div class="form-group">
					<label class="form-label"><?php esc_html_e( 'Phone number', 'smart-pharmacy-eligibility' ); ?></label>
					<input type="tel" class="form-input" id="early-phone" placeholder="e.g. 07XXX XXXXXX" />
				</div>
				<div id="early-form-error" class="error-message" style="display: none;"></div>
				<div class="button-group">
					<button class="button button-secondary" data-action="back"><?php esc_html_e( 'Back', 'smart-pharmacy-eligibility' ); ?></button>
					<button class="button button-primary" id="early-continue"><?php esc_html_e( 'Continue', 'smart-pharmacy-eligibility' ); ?></button>
				</div>
			</div>

			<!-- Screen 4: Age -->
			<div id="screen-4" class="screen">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">25%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 25%"></div></div>
					</div>
				</div>
				<h2><?php esc_html_e( 'How old are you?', 'smart-pharmacy-eligibility' ); ?></h2>
				<div class="radio-group">
					<label class="radio-item"><input type="radio" name="age" value="under-18" /><span><?php esc_html_e( 'Under 18', 'smart-pharmacy-eligibility' ); ?></span></label>
					<label class="radio-item"><input type="radio" name="age" value="18-74" /><span><?php esc_html_e( '18 to 74', 'smart-pharmacy-eligibility' ); ?></span></label>
					<label class="radio-item"><input type="radio" name="age" value="75-over" /><span><?php esc_html_e( '75 or over', 'smart-pharmacy-eligibility' ); ?></span></label>
				</div>
				<button class="button button-secondary" data-action="back"><?php esc_html_e( 'Back', 'smart-pharmacy-eligibility' ); ?></button>
			</div>

			<!-- Screen 5: Ethnicity -->
			<div id="screen-5" class="screen">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">30%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 30%"></div></div>
					</div>
				</div>
				<h2><?php esc_html_e( 'Which ethnicity are you?', 'smart-pharmacy-eligibility' ); ?></h2>
				<p><?php esc_html_e( 'Healthy BMI ranges differ by ethnicity. Our clinicians evaluate your BMI and full medical history together.', 'smart-pharmacy-eligibility' ); ?></p>
				<div class="radio-group">
					<?php
					$ethnicities = array(
						'asian or asian british'        => __( 'Asian or Asian British', 'smart-pharmacy-eligibility' ),
						'black (caribbean, african)'    => __( 'Black (Caribbean, African)', 'smart-pharmacy-eligibility' ),
						'mixed ethnicities'             => __( 'Mixed ethnicities', 'smart-pharmacy-eligibility' ),
						'other ethnic group'            => __( 'Other ethnic group', 'smart-pharmacy-eligibility' ),
						'white'                         => __( 'White', 'smart-pharmacy-eligibility' ),
					);
					foreach ( $ethnicities as $val => $label ) : ?>
						<label class="radio-item"><input type="radio" name="ethnicity" value="<?php echo esc_attr( $val ); ?>" /><span><?php echo esc_html( $label ); ?></span></label>
					<?php endforeach; ?>
				</div>
				<button class="button button-secondary" data-action="back"><?php esc_html_e( 'Back', 'smart-pharmacy-eligibility' ); ?></button>
			</div>

			<!-- Screen 6: Sex assigned at birth -->
			<div id="screen-6" class="screen">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">35%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 35%"></div></div>
					</div>
				</div>
				<h2><?php esc_html_e( 'What sex were you assigned at birth?', 'smart-pharmacy-eligibility' ); ?></h2>
				<div class="gender-buttons">
					<button class="button" data-set-sex="male"><?php esc_html_e( 'Male', 'smart-pharmacy-eligibility' ); ?></button>
					<button class="button" data-set-sex="female"><?php esc_html_e( 'Female', 'smart-pharmacy-eligibility' ); ?></button>
				</div>
				<button class="button button-secondary" data-action="back"><?php esc_html_e( 'Back', 'smart-pharmacy-eligibility' ); ?></button>
			</div>

			<!-- Screen 6b: Female screening -->
			<div id="screen-6b" class="screen">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">38%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 38%"></div></div>
					</div>
				</div>
				<h2><?php esc_html_e( 'A few important questions to keep you safe', 'smart-pharmacy-eligibility' ); ?></h2>
				<?php
				$f_questions = array(
					'pregnant'      => __( 'Are you currently pregnant?', 'smart-pharmacy-eligibility' ),
					'breastfeeding' => __( 'Are you currently breastfeeding?', 'smart-pharmacy-eligibility' ),
					'conceive'      => __( 'Are you trying to conceive?', 'smart-pharmacy-eligibility' ),
				);
				foreach ( $f_questions as $name => $label ) : ?>
					<div class="screening-question">
						<h3><?php echo esc_html( $label ); ?></h3>
						<div class="radio-button-group">
							<label class="radio-button-label"><input type="radio" name="<?php echo esc_attr( $name ); ?>" value="yes" /><?php esc_html_e( 'Yes', 'smart-pharmacy-eligibility' ); ?></label>
							<label class="radio-button-label"><input type="radio" name="<?php echo esc_attr( $name ); ?>" value="no" /><?php esc_html_e( 'No', 'smart-pharmacy-eligibility' ); ?></label>
						</div>
					</div>
				<?php endforeach; ?>
				<div class="button-group">
					<button class="button button-secondary" data-action="back"><?php esc_html_e( 'Back', 'smart-pharmacy-eligibility' ); ?></button>
					<button class="button button-primary" id="female-screening-continue" disabled><?php esc_html_e( 'Continue', 'smart-pharmacy-eligibility' ); ?></button>
				</div>
			</div>

			<!-- Screen 7: Weight -->
			<div id="screen-7" class="screen">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">40%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 40%"></div></div>
					</div>
				</div>
				<h2><?php esc_html_e( 'What is your weight?', 'smart-pharmacy-eligibility' ); ?></h2>
				<div class="unit-selector">
					<label class="unit-option"><input type="radio" name="weight-unit" value="kg" checked /><span>kg</span></label>
					<label class="unit-option"><input type="radio" name="weight-unit" value="st" /><span>st/lbs</span></label>
				</div>
				<input type="number" id="weight-kg-input" class="form-input" placeholder="Weight in kg" step="0.1" min="40" max="250" />
				<div id="weight-st-inputs" style="display: none;" class="grid-input-group">
					<div>
						<label class="form-label"><?php esc_html_e( 'Stone', 'smart-pharmacy-eligibility' ); ?></label>
						<input type="number" id="weight-stone" class="form-input" placeholder="St" min="6" max="40" />
					</div>
					<div>
						<label class="form-label"><?php esc_html_e( 'Pounds', 'smart-pharmacy-eligibility' ); ?></label>
						<input type="number" id="weight-pounds" class="form-input" placeholder="Lbs" min="0" max="13" />
					</div>
				</div>
				<div id="weight-error" class="error-message" style="display: none;"></div>
				<div class="button-group">
					<button class="button button-secondary" data-action="back"><?php esc_html_e( 'Back', 'smart-pharmacy-eligibility' ); ?></button>
					<button class="button button-primary" id="weight-next"><?php esc_html_e( 'Next →', 'smart-pharmacy-eligibility' ); ?></button>
				</div>
			</div>

			<!-- Screen 8: Height -->
			<div id="screen-8" class="screen">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">45%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 45%"></div></div>
					</div>
				</div>
				<h2><?php esc_html_e( 'What is your height?', 'smart-pharmacy-eligibility' ); ?></h2>
				<div class="unit-selector">
					<label class="unit-option"><input type="radio" name="height-unit" value="cm" checked /><span>cm</span></label>
					<label class="unit-option"><input type="radio" name="height-unit" value="ft" /><span>ft/in</span></label>
				</div>
				<input type="number" id="height-cm-input" class="form-input" placeholder="Height in cm" step="0.1" min="120" max="230" />
				<div id="height-ft-inputs" style="display: none;" class="grid-input-group">
					<div>
						<label class="form-label"><?php esc_html_e( 'Feet', 'smart-pharmacy-eligibility' ); ?></label>
						<input type="number" id="height-feet" class="form-input" placeholder="Ft" min="4" max="7" />
					</div>
					<div>
						<label class="form-label"><?php esc_html_e( 'Inches', 'smart-pharmacy-eligibility' ); ?></label>
						<input type="number" id="height-inches" class="form-input" placeholder="In" min="0" max="11" />
					</div>
				</div>
				<div id="height-error" class="error-message" style="display: none;"></div>
				<div class="button-group">
					<button class="button button-secondary" data-action="back"><?php esc_html_e( 'Back', 'smart-pharmacy-eligibility' ); ?></button>
					<button class="button button-primary" id="height-next"><?php esc_html_e( 'Next →', 'smart-pharmacy-eligibility' ); ?></button>
				</div>
			</div>

			<!-- Screen 8b: BMI Result -->
			<div id="screen-8b" class="screen">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">48%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 48%"></div></div>
					</div>
				</div>
				<h2><?php esc_html_e( 'Your BMI Result', 'smart-pharmacy-eligibility' ); ?></h2>
				<p><?php esc_html_e( 'Based on the weight and height you provided, here is your calculated BMI.', 'smart-pharmacy-eligibility' ); ?></p>
				<div class="info-box">
					<p class="info-label"><?php esc_html_e( 'Your Body Mass Index', 'smart-pharmacy-eligibility' ); ?></p>
					<p class="info-value" style="font-size: 48px; margin: 0;" id="bmi-display">-</p>
					<p class="info-label" style="margin-top: 8px; margin-bottom: 0;" id="bmi-category">-</p>
				</div>
				<div class="button-group">
					<button class="button button-secondary" data-action="back"><?php esc_html_e( 'Back', 'smart-pharmacy-eligibility' ); ?></button>
					<button class="button button-primary" id="bmi-continue"><?php esc_html_e( 'Continue →', 'smart-pharmacy-eligibility' ); ?></button>
				</div>
			</div>

			<!-- Screen 10a: Bariatric Timing -->
			<div id="screen-10a" class="screen">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">57%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 57%"></div></div>
					</div>
				</div>
				<h2><?php esc_html_e( 'Was your bariatric operation in the last 6 months?', 'smart-pharmacy-eligibility' ); ?></h2>
				<div class="two-col-buttons">
					<button class="button button-secondary" data-set-bariatric="yes"><?php esc_html_e( 'Yes', 'smart-pharmacy-eligibility' ); ?></button>
					<button class="button button-secondary" data-set-bariatric="no"><?php esc_html_e( 'No', 'smart-pharmacy-eligibility' ); ?></button>
				</div>
				<button class="button button-secondary" data-action="back"><?php esc_html_e( 'Back', 'smart-pharmacy-eligibility' ); ?></button>
			</div>

			<!-- Screen 18: Date of Birth -->
			<div id="screen-18" class="screen">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">85%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 85%"></div></div>
					</div>
				</div>
				<h2><?php esc_html_e( 'Almost there — just a couple more details', 'smart-pharmacy-eligibility' ); ?></h2>
				<p><?php esc_html_e( 'We need your date of birth to verify your eligibility.', 'smart-pharmacy-eligibility' ); ?></p>
				<div class="info-box">
					<p class="info-label"><?php esc_html_e( 'Completing as', 'smart-pharmacy-eligibility' ); ?></p>
					<p class="info-value" id="completing-as">-</p>
				</div>
				<div class="form-group">
					<label class="form-label"><?php esc_html_e( 'Date of Birth *', 'smart-pharmacy-eligibility' ); ?></label>
					<input type="date" id="dob" class="form-input" />
				</div>
				<div class="button-group">
					<button class="button button-secondary" data-action="back"><?php esc_html_e( 'Back', 'smart-pharmacy-eligibility' ); ?></button>
					<button class="button button-primary" id="dob-next"><?php esc_html_e( 'Next →', 'smart-pharmacy-eligibility' ); ?></button>
				</div>
			</div>

			<!-- Screen 19: Address -->
			<div id="screen-19" class="screen">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">90%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 90%"></div></div>
					</div>
				</div>
				<h2><?php esc_html_e( 'Where should we deliver your treatment?', 'smart-pharmacy-eligibility' ); ?></h2>
				<div class="form-group">
					<label class="form-label"><?php esc_html_e( 'Address Line 1 *', 'smart-pharmacy-eligibility' ); ?></label>
					<input type="text" id="address-line1" class="form-input" placeholder="Street address" />
				</div>
				<div class="form-group">
					<label class="form-label"><?php esc_html_e( 'Address Line 2', 'smart-pharmacy-eligibility' ); ?></label>
					<input type="text" id="address-line2" class="form-input" placeholder="Apartment, suite, etc. (optional)" />
				</div>
				<div class="form-grid-2">
					<div class="form-group">
						<label class="form-label"><?php esc_html_e( 'City/Town *', 'smart-pharmacy-eligibility' ); ?></label>
						<input type="text" id="city" class="form-input" placeholder="London" />
					</div>
					<div class="form-group">
						<label class="form-label"><?php esc_html_e( 'Postcode *', 'smart-pharmacy-eligibility' ); ?></label>
						<input type="text" id="postcode" class="form-input" placeholder="SW1A 1AA" />
					</div>
				</div>
				<div class="form-group">
					<label class="form-label"><?php esc_html_e( 'Country *', 'smart-pharmacy-eligibility' ); ?></label>
					<select id="country" class="form-select">
						<option value="United Kingdom">United Kingdom</option>
						<option value="England">England</option>
						<option value="Scotland">Scotland</option>
						<option value="Wales">Wales</option>
						<option value="Northern Ireland">Northern Ireland</option>
					</select>
				</div>
				<div id="address-error" class="error-message" style="display: none;"></div>
				<div class="button-group">
					<button class="button button-secondary" data-action="back"><?php esc_html_e( 'Back', 'smart-pharmacy-eligibility' ); ?></button>
					<button class="button button-primary" id="address-next"><?php esc_html_e( 'Next →', 'smart-pharmacy-eligibility' ); ?></button>
				</div>
			</div>

			<!-- Screen 21: Treatment Selection -->
			<div id="screen-21" class="screen">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">100%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 100%"></div></div>
					</div>
				</div>
				<div style="text-align: center; margin-bottom: 40px;">
					<div class="success-icon">✓</div>
					<h1 style="font-size: 32px; margin-bottom: 12px;"><?php esc_html_e( "You're eligible for treatment!", 'smart-pharmacy-eligibility' ); ?></h1>
					<p><?php esc_html_e( 'Based on your assessment, you qualify for GLP-1 weight loss treatment.', 'smart-pharmacy-eligibility' ); ?></p>
				</div>
				<h2 style="text-align: center;"><?php esc_html_e( 'Choose Your Treatment', 'smart-pharmacy-eligibility' ); ?></h2>
				<button class="treatment-card" id="wegovy-card" data-select-treatment="wegovy">
					<div class="treatment-header"><div class="treatment-title">Wegovy</div><span class="treatment-tag"><?php esc_html_e( 'Popular', 'smart-pharmacy-eligibility' ); ?></span></div>
					<div class="treatment-price">£109<span style="font-size: 14px; color: #6b7280;">/month</span></div>
					<p class="treatment-price-note"><?php esc_html_e( 'Starting dose (0.25mg)', 'smart-pharmacy-eligibility' ); ?></p>
					<p class="treatment-description"><?php esc_html_e( 'Clinically proven semaglutide injection for significant weight loss', 'smart-pharmacy-eligibility' ); ?></p>
				</button>
				<button class="treatment-card" id="mounjaro-card" data-select-treatment="mounjaro">
					<div class="treatment-header"><div class="treatment-title">Mounjaro</div><span class="treatment-tag" style="background-color: #dbeafe; color: #1e40af;"><?php esc_html_e( 'Advanced', 'smart-pharmacy-eligibility' ); ?></span></div>
					<div class="treatment-price">£159<span style="font-size: 14px; color: #6b7280;">/month</span></div>
					<p class="treatment-price-note"><?php esc_html_e( 'Starting dose (2.5mg)', 'smart-pharmacy-eligibility' ); ?></p>
					<p class="treatment-description"><?php esc_html_e( 'Dual-action tirzepatide formula for maximum weight loss results', 'smart-pharmacy-eligibility' ); ?></p>
				</button>
				<button class="button button-primary" id="submit-button"><?php esc_html_e( 'Submit Assessment', 'smart-pharmacy-eligibility' ); ?></button>
			</div>

			<!-- Screen Confirmed -->
			<div id="screen-confirmed" class="screen confirmed-screen">
				<div class="success-icon">✓</div>
				<h2><?php esc_html_e( 'Assessment Submitted', 'smart-pharmacy-eligibility' ); ?></h2>
				<p><?php esc_html_e( "Thank you. We're redirecting you to checkout to complete your order...", 'smart-pharmacy-eligibility' ); ?></p>
			</div>

			<!-- Screen Ineligible -->
			<div id="screen-ineligible" class="screen ineligible-screen">
				<div class="ineligible-icon">✕</div>
				<h2><?php esc_html_e( 'No suitable treatment', 'smart-pharmacy-eligibility' ); ?></h2>
				<p id="ineligible-reason">-</p>
				<div class="info-box" style="text-align: left;">
					<p><?php esc_html_e( 'We recommend speaking with your GP who can discuss alternative options and support you with your weight management goals.', 'smart-pharmacy-eligibility' ); ?></p>
				</div>
				<button class="button button-primary" id="review-button"><?php esc_html_e( 'Review your answers', 'smart-pharmacy-eligibility' ); ?></button>
			</div>

		</div>
	</div>
</div>
