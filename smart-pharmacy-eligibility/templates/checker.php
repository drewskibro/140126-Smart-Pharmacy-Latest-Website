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
	<header class="spe-header">
		<div class="spe-header__inner">
			<?php
			// Pulls the WP custom logo if the theme supports one
			// (Customizer -> Site Identity -> Logo). Falls back to
			// the site name so the header never renders empty.
			if ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) {
				$sp_logo_id  = get_theme_mod( 'custom_logo' );
				$sp_logo_src = wp_get_attachment_image_url( $sp_logo_id, 'medium' );
				if ( $sp_logo_src ) {
					echo '<a class="spe-header__logo" href="' . esc_url( home_url( '/' ) ) . '" aria-label="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
					echo '<img src="' . esc_url( $sp_logo_src ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" />';
					echo '</a>';
				}
			} else {
				echo '<a class="spe-header__title" href="' . esc_url( home_url( '/' ) ) . '">' . esc_html( get_bloginfo( 'name' ) ) . '</a>';
			}
			?>
		</div>
	</header>
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

			<!-- Screen 9: Diabetes -->
			<div id="screen-9" class="screen">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">50%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 50%"></div></div>
					</div>
				</div>
				<h2><?php esc_html_e( 'Have you been diagnosed with diabetes?', 'smart-pharmacy-eligibility' ); ?></h2>
				<p><?php esc_html_e( 'Diabetes treatments can affect how the weight loss medication works.', 'smart-pharmacy-eligibility' ); ?></p>
				<div class="radio-group">
					<?php
					$diabetes_opts = array(
						'medication' => __( 'I have diabetes and take medication for it', 'smart-pharmacy-eligibility' ),
						'diet'       => __( "I have diabetes and it's diet-controlled", 'smart-pharmacy-eligibility' ),
						'family'     => __( 'No, but there is family history of diabetes', 'smart-pharmacy-eligibility' ),
						'pre'        => __( 'I have pre-diabetes', 'smart-pharmacy-eligibility' ),
						'none'       => __( "I don't have diabetes", 'smart-pharmacy-eligibility' ),
					);
					foreach ( $diabetes_opts as $v => $label ) : ?>
						<label class="radio-item"><input type="radio" name="diabetes" value="<?php echo esc_attr( $v ); ?>" /><span><?php echo esc_html( $label ); ?></span></label>
					<?php endforeach; ?>
				</div>
				<button class="button button-secondary" data-action="back"><?php esc_html_e( 'Back', 'smart-pharmacy-eligibility' ); ?></button>
			</div>

			<!-- Screen 10: Contraindicated conditions -->
			<div id="screen-10" class="screen">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">55%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 55%"></div></div>
					</div>
				</div>
				<h2><?php esc_html_e( 'Do any of the following apply to you?', 'smart-pharmacy-eligibility' ); ?></h2>
				<p><?php esc_html_e( 'These conditions can lead to serious complications when losing weight or taking weight loss medication.', 'smart-pharmacy-eligibility' ); ?></p>
				<div class="checkbox-group">
					<?php
					$conditions = array(
						'malabsorption'    => __( 'I have chronic malabsorption syndrome', 'smart-pharmacy-eligibility' ),
						'cholestasis'      => __( 'I have cholestasis', 'smart-pharmacy-eligibility' ),
						'cancer'           => __( "I'm currently being treated for cancer", 'smart-pharmacy-eligibility' ),
						'retinopathy'      => __( 'I have diabetic retinopathy', 'smart-pharmacy-eligibility' ),
						'heart_failure'    => __( 'I have severe heart failure', 'smart-pharmacy-eligibility' ),
						'thyroid_cancer'   => __( "I have a family history of thyroid cancer and/or I've had thyroid cancer", 'smart-pharmacy-eligibility' ),
						'kidney_disease'   => __( 'I have end-stage kidney disease', 'smart-pharmacy-eligibility' ),
						'men2'             => __( 'I have Multiple endocrine neoplasia type 2 (MEN2)', 'smart-pharmacy-eligibility' ),
						'pancreatitis'     => __( 'I have a history of pancreatitis', 'smart-pharmacy-eligibility' ),
						'eating_disorder'  => __( 'I have or have had an eating disorder', 'smart-pharmacy-eligibility' ),
						'thyroid_surgery'  => __( 'I have had surgery or an operation to my thyroid', 'smart-pharmacy-eligibility' ),
						'bariatric'        => __( 'I have had a bariatric operation', 'smart-pharmacy-eligibility' ),
						'none'             => __( 'None of these apply', 'smart-pharmacy-eligibility' ),
					);
					foreach ( $conditions as $v => $label ) : ?>
						<label class="checkbox-item">
							<input type="checkbox" name="conditions" value="<?php echo esc_attr( $v ); ?>" data-condition="<?php echo esc_attr( $v ); ?>" />
							<span><?php echo esc_html( $label ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>
				<div id="conditions-error" class="error-message" style="display: none;"></div>
				<div class="button-group">
					<button class="button button-secondary" data-action="back"><?php esc_html_e( 'Back', 'smart-pharmacy-eligibility' ); ?></button>
					<button class="button button-primary" id="conditions-next"><?php esc_html_e( 'Next →', 'smart-pharmacy-eligibility' ); ?></button>
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

			<!-- Screen 10b: Bariatric details -->
			<div id="screen-10b" class="screen">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">60%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 60%"></div></div>
					</div>
				</div>
				<h2><?php esc_html_e( 'Please tell us more about your surgery', 'smart-pharmacy-eligibility' ); ?></h2>
				<p><?php esc_html_e( 'Type of surgery, when, post-op complications, BMI before surgery, ongoing monitoring.', 'smart-pharmacy-eligibility' ); ?></p>
				<textarea class="form-textarea" id="bariatric-details" placeholder="<?php esc_attr_e( 'Please provide details...', 'smart-pharmacy-eligibility' ); ?>"></textarea>
				<div class="button-group">
					<button class="button button-secondary" data-action="back"><?php esc_html_e( 'Back', 'smart-pharmacy-eligibility' ); ?></button>
					<button class="button button-primary" id="bariatric-details-next"><?php esc_html_e( 'Next →', 'smart-pharmacy-eligibility' ); ?></button>
				</div>
			</div>

			<!-- Screen 11: Weight-related conditions -->
			<div id="screen-11" class="screen">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">62%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 62%"></div></div>
					</div>
				</div>
				<h2><?php esc_html_e( 'Do any of the following apply to you?', 'smart-pharmacy-eligibility' ); ?></h2>
				<p><?php esc_html_e( 'These conditions are often weight-related and may improve as a result of losing weight.', 'smart-pharmacy-eligibility' ); ?></p>
				<div class="checkbox-group">
					<?php
					$weight_conditions = array(
						'mental_health'      => __( 'I have a mental health condition such as depression or anxiety', 'smart-pharmacy-eligibility' ),
						'social_anxiety'     => __( 'My weight makes me anxious in social situations', 'smart-pharmacy-eligibility' ),
						'joint_pain'         => __( 'I have joint pains and/or aches', 'smart-pharmacy-eligibility' ),
						'osteoarthritis'     => __( 'I have osteoarthritis', 'smart-pharmacy-eligibility' ),
						'gord'               => __( 'I have GORD and/or indigestion', 'smart-pharmacy-eligibility' ),
						'cardiovascular'     => __( 'I have a heart/cardiovascular problem', 'smart-pharmacy-eligibility' ),
						'high_bp'            => __( "I've been diagnosed with high blood pressure", 'smart-pharmacy-eligibility' ),
						'high_cholesterol'   => __( "I've been diagnosed with high cholesterol", 'smart-pharmacy-eligibility' ),
						'fatty_liver'        => __( 'I have fatty liver disease', 'smart-pharmacy-eligibility' ),
						'sleep_apnoea'       => __( 'I have sleep apnoea', 'smart-pharmacy-eligibility' ),
						'asthma_copd'        => __( 'I have asthma or COPD', 'smart-pharmacy-eligibility' ),
						'ed'                 => __( 'I have erectile dysfunction', 'smart-pharmacy-eligibility' ),
						'low_testosterone'   => __( 'I have low testosterone', 'smart-pharmacy-eligibility' ),
						'menopausal'         => __( 'I have menopausal symptoms', 'smart-pharmacy-eligibility' ),
						'pcos'               => __( 'I have polycystic ovary syndrome (PCOS)', 'smart-pharmacy-eligibility' ),
						'none'               => __( 'None of these apply', 'smart-pharmacy-eligibility' ),
					);
					foreach ( $weight_conditions as $v => $label ) : ?>
						<label class="checkbox-item">
							<input type="checkbox" name="weight-conditions" value="<?php echo esc_attr( $v ); ?>" />
							<span><?php echo esc_html( $label ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>
				<div id="weight-conditions-error" class="error-message" style="display: none;"></div>
				<div class="button-group">
					<button class="button button-secondary" data-action="back"><?php esc_html_e( 'Back', 'smart-pharmacy-eligibility' ); ?></button>
					<button class="button button-primary" id="weight-conditions-next"><?php esc_html_e( 'Next →', 'smart-pharmacy-eligibility' ); ?></button>
				</div>
			</div>

			<!-- Screen 11a: Mental health details -->
			<div id="screen-11a" class="screen">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">65%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 65%"></div></div>
					</div>
				</div>
				<h2><?php esc_html_e( 'Tell us about your mental health and how you manage it', 'smart-pharmacy-eligibility' ); ?></h2>
				<textarea class="form-textarea" id="mental-health-details" placeholder="<?php esc_attr_e( 'Please share condition + treatment...', 'smart-pharmacy-eligibility' ); ?>"></textarea>
				<div class="button-group">
					<button class="button button-secondary" data-action="back"><?php esc_html_e( 'Back', 'smart-pharmacy-eligibility' ); ?></button>
					<button class="button button-primary" id="mental-health-next"><?php esc_html_e( 'Next →', 'smart-pharmacy-eligibility' ); ?></button>
				</div>
			</div>

			<!-- Screen 12: Other conditions Y/N -->
			<div id="screen-12" class="screen">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">68%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 68%"></div></div>
					</div>
				</div>
				<h2><?php esc_html_e( 'Do you have any other medical conditions?', 'smart-pharmacy-eligibility' ); ?></h2>
				<p><?php esc_html_e( 'Our clinicians need your full medical history to keep your treatment safe.', 'smart-pharmacy-eligibility' ); ?></p>
				<div class="two-col-buttons">
					<button class="button button-secondary" data-set-other-conditions="yes"><?php esc_html_e( 'Yes', 'smart-pharmacy-eligibility' ); ?></button>
					<button class="button button-secondary" data-set-other-conditions="no"><?php esc_html_e( 'No', 'smart-pharmacy-eligibility' ); ?></button>
				</div>
				<button class="button button-secondary" data-action="back"><?php esc_html_e( 'Back', 'smart-pharmacy-eligibility' ); ?></button>
			</div>

			<!-- Screen 12a: Other conditions details -->
			<div id="screen-12a" class="screen">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">70%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 70%"></div></div>
					</div>
				</div>
				<h2><?php esc_html_e( 'Please list any other medical conditions you have', 'smart-pharmacy-eligibility' ); ?></h2>
				<textarea class="form-textarea" id="other-conditions" placeholder="<?php esc_attr_e( 'My health conditions are...', 'smart-pharmacy-eligibility' ); ?>"></textarea>
				<div class="button-group">
					<button class="button button-secondary" data-action="back"><?php esc_html_e( 'Back', 'smart-pharmacy-eligibility' ); ?></button>
					<button class="button button-primary" id="other-conditions-next"><?php esc_html_e( 'Next →', 'smart-pharmacy-eligibility' ); ?></button>
				</div>
			</div>

			<!-- Screen 13: Previous weight-loss medications -->
			<div id="screen-13" class="screen">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">72%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 72%"></div></div>
					</div>
				</div>
				<h2><?php esc_html_e( 'Have you ever taken any of these medications to help you lose weight?', 'smart-pharmacy-eligibility' ); ?></h2>
				<div class="checkbox-group">
					<?php
					$prev_meds = array(
						'wegovy'    => 'Wegovy',
						'ozempic'   => 'Ozempic',
						'saxenda'   => 'Saxenda',
						'rybelsus'  => 'Rybelsus',
						'mounjaro'  => 'Mounjaro',
						'alli'      => 'Alli',
						'mysimba'   => 'Mysimba',
						'other'     => __( 'Other', 'smart-pharmacy-eligibility' ),
						'never'     => __( 'I have never taken medication to lose weight', 'smart-pharmacy-eligibility' ),
					);
					foreach ( $prev_meds as $v => $label ) : ?>
						<label class="checkbox-item">
							<input type="checkbox" name="prev-meds" value="<?php echo esc_attr( $v ); ?>" />
							<span><?php echo esc_html( $label ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>
				<div id="prev-meds-error" class="error-message" style="display: none;"></div>
				<div class="button-group">
					<button class="button button-secondary" data-action="back"><?php esc_html_e( 'Back', 'smart-pharmacy-eligibility' ); ?></button>
					<button class="button button-primary" id="prev-meds-next"><?php esc_html_e( 'Next →', 'smart-pharmacy-eligibility' ); ?></button>
				</div>
			</div>

			<!-- Screen 13-weight: Weight before each previous med (iterates) -->
			<div id="screen-13-weight" class="screen">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">74%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 74%"></div></div>
					</div>
				</div>
				<h2 id="prev-weight-question"><?php esc_html_e( 'What was your weight before starting this medication?', 'smart-pharmacy-eligibility' ); ?></h2>
				<input type="number" id="prev-weight" class="form-input" placeholder="<?php esc_attr_e( 'Weight in kg', 'smart-pharmacy-eligibility' ); ?>" step="0.1" />
				<div class="button-group">
					<button class="button button-secondary" id="prev-weight-skip"><?php esc_html_e( 'Skip', 'smart-pharmacy-eligibility' ); ?></button>
					<button class="button button-primary" id="prev-weight-next"><?php esc_html_e( 'Next →', 'smart-pharmacy-eligibility' ); ?></button>
				</div>
			</div>

			<!-- Screen 14: Current prescription medications -->
			<div id="screen-14" class="screen">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">76%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 76%"></div></div>
					</div>
				</div>
				<h2><?php esc_html_e( 'Are you currently taking any regular prescription medications?', 'smart-pharmacy-eligibility' ); ?></h2>
				<p><?php esc_html_e( 'Critical for drug-interaction screening.', 'smart-pharmacy-eligibility' ); ?></p>
				<div class="radio-group">
					<?php
					$current_meds = array(
						'none'        => __( "No, I don't take any prescription medications", 'smart-pharmacy-eligibility' ),
						'bp'          => __( 'Blood pressure medication', 'smart-pharmacy-eligibility' ),
						'cholesterol' => __( 'Cholesterol medication', 'smart-pharmacy-eligibility' ),
						'diabetes'    => __( 'Diabetes medication', 'smart-pharmacy-eligibility' ),
						'mental'      => __( 'Mental health medication', 'smart-pharmacy-eligibility' ),
						'other'       => __( 'Other / I take more than one', 'smart-pharmacy-eligibility' ),
					);
					foreach ( $current_meds as $v => $label ) : ?>
						<label class="radio-item"><input type="radio" name="current-meds" value="<?php echo esc_attr( $v ); ?>" /><span><?php echo esc_html( $label ); ?></span></label>
					<?php endforeach; ?>
				</div>
				<button class="button button-secondary" data-action="back"><?php esc_html_e( 'Back', 'smart-pharmacy-eligibility' ); ?></button>
			</div>

			<!-- Screen 14a: Full medication list -->
			<div id="screen-14a" class="screen">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">77%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 77%"></div></div>
					</div>
				</div>
				<h2><?php esc_html_e( 'Please include a full list of all medication you currently take', 'smart-pharmacy-eligibility' ); ?></h2>
				<textarea class="form-textarea" id="medication-list" placeholder="<?php esc_attr_e( 'List all your current medications...', 'smart-pharmacy-eligibility' ); ?>"></textarea>
				<div class="button-group">
					<button class="button button-secondary" data-action="back"><?php esc_html_e( 'Back', 'smart-pharmacy-eligibility' ); ?></button>
					<button class="button button-primary" id="medication-list-next"><?php esc_html_e( 'Next →', 'smart-pharmacy-eligibility' ); ?></button>
				</div>
			</div>

			<!-- Screen 15: Allergies Y/N -->
			<div id="screen-15" class="screen">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">78%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 78%"></div></div>
					</div>
				</div>
				<h2><?php esc_html_e( 'Do you have any allergies?', 'smart-pharmacy-eligibility' ); ?></h2>
				<div class="two-col-buttons">
					<button class="button button-secondary" data-set-allergies="yes"><?php esc_html_e( 'Yes', 'smart-pharmacy-eligibility' ); ?></button>
					<button class="button button-secondary" data-set-allergies="no"><?php esc_html_e( 'No', 'smart-pharmacy-eligibility' ); ?></button>
				</div>
				<button class="button button-secondary" data-action="back"><?php esc_html_e( 'Back', 'smart-pharmacy-eligibility' ); ?></button>
			</div>

			<!-- Screen 15a: Allergies details -->
			<div id="screen-15a" class="screen">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">79%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 79%"></div></div>
					</div>
				</div>
				<h2><?php esc_html_e( 'Please list your allergies', 'smart-pharmacy-eligibility' ); ?></h2>
				<textarea class="form-textarea" id="allergies" placeholder="<?php esc_attr_e( 'My allergies are...', 'smart-pharmacy-eligibility' ); ?>"></textarea>
				<div class="button-group">
					<button class="button button-secondary" data-action="back"><?php esc_html_e( 'Back', 'smart-pharmacy-eligibility' ); ?></button>
					<button class="button button-primary" id="allergies-next"><?php esc_html_e( 'Next →', 'smart-pharmacy-eligibility' ); ?></button>
				</div>
			</div>

			<!-- Screen 16: Goal weight Y/N -->
			<div id="screen-16" class="screen">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">81%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 81%"></div></div>
					</div>
				</div>
				<h2><?php esc_html_e( 'Do you have a goal weight you would like to achieve?', 'smart-pharmacy-eligibility' ); ?></h2>
				<div class="two-col-buttons">
					<button class="button button-secondary" data-set-goal="yes"><?php esc_html_e( 'Yes', 'smart-pharmacy-eligibility' ); ?></button>
					<button class="button button-secondary" data-set-goal="no"><?php esc_html_e( 'No', 'smart-pharmacy-eligibility' ); ?></button>
				</div>
				<button class="button button-secondary" data-action="back"><?php esc_html_e( 'Back', 'smart-pharmacy-eligibility' ); ?></button>
			</div>

			<!-- Screen 17: Goal weight input -->
			<div id="screen-17" class="screen">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">82%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 82%"></div></div>
					</div>
				</div>
				<h2><?php esc_html_e( 'What is your goal weight?', 'smart-pharmacy-eligibility' ); ?></h2>
				<input type="number" id="goal-weight" class="form-input" placeholder="<?php esc_attr_e( 'Goal weight in kg', 'smart-pharmacy-eligibility' ); ?>" step="0.1" />
				<div class="button-group">
					<button class="button button-secondary" data-action="back"><?php esc_html_e( 'Back', 'smart-pharmacy-eligibility' ); ?></button>
					<button class="button button-primary" id="goal-weight-next"><?php esc_html_e( 'Next →', 'smart-pharmacy-eligibility' ); ?></button>
				</div>
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

			<!-- Screen 20: GP details + consents -->
			<div id="screen-20" class="screen">
				<div class="progress-section">
					<div class="progress-bar-container">
						<div class="progress-percentage">95%</div>
						<div class="progress-bar"><div class="progress-fill" style="width: 95%"></div></div>
					</div>
				</div>
				<h2><?php esc_html_e( 'Who is your GP?', 'smart-pharmacy-eligibility' ); ?></h2>
				<p><?php esc_html_e( 'Optional but recommended — lets our prescriber coordinate with your GP for safer care.', 'smart-pharmacy-eligibility' ); ?></p>
				<div class="form-group">
					<label class="form-label"><?php esc_html_e( 'GP Surgery Name', 'smart-pharmacy-eligibility' ); ?></label>
					<input type="text" id="gp-name" class="form-input" placeholder="<?php esc_attr_e( 'Surgery name', 'smart-pharmacy-eligibility' ); ?>" />
				</div>
				<div class="form-group">
					<label class="form-label"><?php esc_html_e( 'GP Surgery Postcode', 'smart-pharmacy-eligibility' ); ?></label>
					<input type="text" id="gp-postcode" class="form-input" placeholder="SW1A 1AA" />
				</div>
				<label class="checkbox-item" style="background: #fff; border: 2px solid #e5e7eb; border-radius: 12px; padding: 16px; margin-bottom: 12px;">
					<input type="checkbox" id="gp-consent-share" />
					<span><?php esc_html_e( 'I consent for Smart Pharmacy to share information regarding any treatment prescribed with my GP', 'smart-pharmacy-eligibility' ); ?></span>
				</label>
				<label class="checkbox-item" style="background: #fff; border: 2px solid #e5e7eb; border-radius: 12px; padding: 16px; margin-bottom: 24px;">
					<input type="checkbox" id="gp-consent-scr" />
					<span><?php esc_html_e( 'I consent to a one-off request from Smart Pharmacy to access my summary care record to verify the information I have provided', 'smart-pharmacy-eligibility' ); ?></span>
				</label>
				<div class="button-group">
					<button class="button button-secondary" data-action="back"><?php esc_html_e( 'Back', 'smart-pharmacy-eligibility' ); ?></button>
					<button class="button button-primary" id="gp-next"><?php esc_html_e( 'Next →', 'smart-pharmacy-eligibility' ); ?></button>
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
				<?php
				// Renders one card per configured treatment, pulling
				// title + price + image live from WooCommerce. Falls
				// back to a sensible default if the admin hasn't yet
				// mapped a product, so the screen never renders empty.
				foreach ( SPE_Treatment_Cards::starter_cards() as $card ) :
					?>
					<button class="treatment-card" data-select-treatment="<?php echo esc_attr( $card['key'] ); ?>" data-select-dose="<?php echo esc_attr( $card['dose'] ); ?>">
						<div class="treatment-header">
							<div class="treatment-title"><?php echo esc_html( $card['title'] ); ?></div>
							<span class="treatment-tag" style="<?php echo esc_attr( $card['tag_style'] ); ?>"><?php echo esc_html( $card['tag'] ); ?></span>
						</div>
						<div class="treatment-price">
							<?php echo wp_kses_post( $card['price_html'] ); ?>
							<span style="font-size: 14px; color: #6b7280;">/month</span>
						</div>
						<p class="treatment-price-note">
							<?php
							/* translators: %s is the starting dose label, e.g. "0.25mg" */
							printf( esc_html__( 'Starting dose (%s)', 'smart-pharmacy-eligibility' ), esc_html( $card['dose'] ) );
							?>
						</p>
						<p class="treatment-description"><?php echo esc_html( $card['description'] ); ?></p>
					</button>
				<?php endforeach; ?>
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
