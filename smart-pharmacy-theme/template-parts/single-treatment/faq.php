<?php
/**
 * Single Treatment — FAQ accordion + contact sidebar.
 *
 * 12-col layout: 7-col native <details>/<summary> accordion on the
 * left, 5-col sticky sidebar on the right with a "Need more help?"
 * contact card and a GPhC trust badge. Uses the same sp-faq /
 * sp-faq-item / sp-faq-chevron CSS hooks as the homepage FAQ
 * (assets/css/styles.css:432-447), so the plus-to-X rotation and
 * teal-border open state are free.
 *
 * Editable via the Treatment post edit screen → D1 — Treatment FAQ.
 *
 * @package SmartPharmacy
 */

if ( ! (bool) sp_field( 'tx_faq_enabled', 1 ) ) {
	return;
}

$tx_badge_pre    = sp_field( 'tx_faq_badge_pre', 'Common' );
$tx_badge_hi     = sp_field( 'tx_faq_badge_highlight', 'Questions' );
$tx_h_pre        = sp_field( 'tx_faq_heading_pre', 'Frequently Asked' );
$tx_h_hi         = sp_field( 'tx_faq_heading_highlight', 'Questions' );
$tx_sub          = sp_field( 'tx_faq_subheading', 'Everything you need to know about our weight loss service' );
$tx_view_all_l   = sp_field( 'tx_faq_view_all_label', 'View All Questions' );
$tx_view_all_u   = sp_field( 'tx_faq_view_all_url', '/faqs/' );
$tx_side_h       = sp_field( 'tx_faq_sidebar_heading', 'Need more help?' );
$tx_side_b       = sp_field( 'tx_faq_sidebar_body', 'Our pharmacy team is here to answer your questions and provide expert guidance on your weight loss journey.' );

// GPhC number comes from I1 Compliance (options); sp_field() falls
// through the three-tier resolver to the options value.
$tx_gphc_number  = sp_field( 'comp_gphc_number', '9012842' );

$tx_faqs = sp_field( 'tx_faq_items', array() );
if ( empty( $tx_faqs ) || ! is_array( $tx_faqs ) ) {
	// Graceful default: 7 treatment-specific Q&As verbatim from the
	// weight-loss prototype.
	$tx_faqs = array(
		array( 'question' => 'How much does weight loss treatment cost?', 'answer' => 'Wegovy starts from £199/month and Mounjaro from £229/month. This includes your medication, prescription, ongoing support, and free delivery. There are no hidden fees or consultation charges.' ),
		array( 'question' => 'Is weight loss medication suitable for me?', 'answer' => "Weight loss medication is typically suitable for adults with a BMI of 30 or above, or 27+ with weight-related health conditions. Our doctors will assess your eligibility during the online consultation to ensure it's safe and appropriate for you." ),
		array( 'question' => 'How quickly will I see results?', 'answer' => 'Most patients start seeing results within 4-8 weeks. Clinical studies show that patients can lose 15-20% of their body weight over 12 months when combined with diet and exercise. Results vary by individual.' ),
		array( 'question' => 'What are the side effects?', 'answer' => 'Common side effects include nausea, diarrhea, constipation, and stomach discomfort, especially when starting treatment. These usually improve over time. Our doctors will discuss all potential side effects during your consultation and monitor you throughout treatment.' ),
		array( 'question' => 'Do I need a prescription?', 'answer' => "Yes, weight loss medications like Wegovy and Mounjaro are prescription-only. Our UK-registered doctors can issue a prescription after reviewing your online consultation. You don't need to visit your GP -- we handle everything online." ),
		array( 'question' => 'How long do I need to take the medication?', 'answer' => 'Treatment duration varies by individual. Most patients use the medication for 12-18 months to achieve their weight loss goals. Our doctors will work with you to create a personalized treatment plan and can adjust or stop treatment as needed.' ),
		array( 'question' => 'Is delivery discreet?', 'answer' => 'Yes, absolutely. All medication is delivered in plain, unmarked packaging with no indication of the contents. Your privacy is our priority, and we ensure complete discretion throughout the entire process.' ),
	);
}

$tx_contacts = sp_field( 'tx_faq_contacts', array() );
if ( empty( $tx_contacts ) || ! is_array( $tx_contacts ) ) {
	// Same defaults as A10 homepage FAQ, so the editor experience is
	// consistent. Editors override per-treatment if they want e.g. a
	// specialist inbox for HRT.
	$tx_contacts = array(
		array( 'icon' => 'document',     'title' => 'Email Us', 'body' => 'Get a response within 24 hours', 'url' => '/contact/' ),
		array( 'icon' => 'check_circle', 'title' => 'Call Us',  'body' => 'Mon-Fri, 9am-5pm',              'url' => 'tel://+441234567890' ),
	);
}
?>

<section class="relative bg-[linear-gradient(to_right_bottom,rgb(245,243,240),rgb(250,248,245),rgb(240,237,232))] box-border break-words overflow-hidden py-20 md:py-32">
	<div class="absolute bg-[linear-gradient(to_right,rgba(0,0,0,0),rgba(59,155,159,0.2),rgba(0,0,0,0))] box-border h-px break-words w-full left-0 top-0" aria-hidden="true"></div>
	<div class="absolute bg-[linear-gradient(to_right,rgba(0,0,0,0),rgba(59,155,159,0.2),rgba(0,0,0,0))] box-border h-px break-words w-full left-0 bottom-0" aria-hidden="true"></div>
	<div class="absolute bg-teal-500/10 box-border blur-3xl h-96 break-words w-96 rounded-full -left-20 -top-20" aria-hidden="true"></div>
	<div class="absolute bg-teal-700/10 box-border blur-3xl h-[500px] break-words w-[500px] rounded-full -right-20 bottom-40" aria-hidden="true"></div>

	<div class="relative box-border max-w-[1400px] break-words z-10 mx-auto px-8 md:px-24">
		<div class="box-border gap-x-12 grid grid-cols-[repeat(1,minmax(0px,1fr))] break-words gap-y-12 md:grid-cols-[repeat(12,minmax(0px,1fr))]">

			<!-- LEFT — accordion (7 of 12 cols) -->
			<div class="box-border break-words md:col-end-[span_7] md:col-start-[span_7]">
				<div class="box-border break-words mb-12">
					<div class="items-center backdrop-blur-sm bg-white/80 shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border gap-x-3 inline-flex break-words gap-y-3 border border-gray-100 mb-8 px-6 py-3 rounded-full border-solid">
						<div class="items-center bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] box-border flex h-10 justify-center break-words w-10 rounded-full">
							<?php echo sp_icon( 'check_circle', 'w-5 h-5 text-white' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
						<span class="text-neutral-900 text-base font-bold box-border block leading-6 break-words">
							<?php echo esc_html( $tx_badge_pre ); ?>
							<?php if ( $tx_badge_hi ) : ?><span class="text-teal-500"> <?php echo esc_html( $tx_badge_hi ); ?></span><?php endif; ?>
						</span>
						<div class="bg-teal-500 box-border h-2 break-words w-2 rounded-full"></div>
					</div>

					<h2 class="text-neutral-900 text-5xl font-black box-border tracking-[-1.2px] leading-[1.1] break-words mb-6 md:text-7xl md:tracking-[-1.8px]">
						<?php echo esc_html( $tx_h_pre ); ?>
						<?php if ( $tx_h_hi ) : ?>
							<span class="relative inline-block">
								<span class="text-transparent bg-clip-text bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))]"> <?php echo esc_html( $tx_h_hi ); ?></span>
							</span>
						<?php endif; ?>
					</h2>

					<?php if ( $tx_sub ) : ?>
						<p class="text-neutral-600 text-xl box-border leading-[32.5px] break-words md:text-2xl md:leading-8"><?php echo esc_html( $tx_sub ); ?></p>
					<?php endif; ?>

					<div class="items-center box-border gap-x-3 flex break-words gap-y-3 mt-6" aria-hidden="true">
						<div class="bg-[linear-gradient(to_right,rgba(0,0,0,0),rgba(59,155,159,0.3))] box-border h-px break-words w-16"></div>
						<div class="bg-teal-500/30 box-border h-2 break-words w-2 rounded-full"></div>
						<div class="bg-[linear-gradient(to_left,rgba(0,0,0,0),rgba(59,155,159,0.3))] box-border h-px break-words w-16"></div>
					</div>
				</div>

				<!-- Accordion -->
				<div class="box-border break-words mb-10 space-y-4 sp-faq">
					<?php foreach ( $tx_faqs as $sp_qna ) :
						$sp_q = isset( $sp_qna['question'] ) ? (string) $sp_qna['question'] : '';
						$sp_a = isset( $sp_qna['answer'] ) ? (string) $sp_qna['answer'] : '';
						if ( '' === $sp_q ) { continue; }
						?>
						<details class="sp-faq-item relative bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border break-words border border-gray-100 overflow-hidden rounded-2xl border-solid">
							<summary class="items-center box-border flex justify-between list-none break-words p-6 md:p-8 cursor-pointer">
								<span class="text-neutral-900 text-lg font-bold box-border block leading-7 break-words pr-4 md:text-xl"><?php echo esc_html( $sp_q ); ?></span>
								<div class="relative box-border shrink-0 break-words">
									<div class="relative items-center bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border flex h-10 justify-center break-words w-10 rounded-full md:h-12 md:w-12">
										<!-- Plus icon rotates 45deg to X when <details> is open (.sp-faq-item[open] .sp-faq-chevron) -->
										<svg class="sp-faq-chevron w-6 h-6 text-white transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
											<path d="M12 5v14M5 12h14" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
									</div>
								</div>
							</summary>
							<div class="box-border break-words pb-6 px-6 md:pb-8 md:px-8">
								<div class="box-border break-words border-gray-100 pt-4 border-t border-solid">
									<p class="text-neutral-600 text-base box-border leading-[26px] break-words md:text-lg md:leading-7"><?php echo esc_html( $sp_a ); ?></p>
								</div>
							</div>
						</details>
					<?php endforeach; ?>
				</div>

				<?php if ( $tx_view_all_l ) : ?>
					<div class="box-border break-words mt-10">
						<a href="<?php echo esc_url( $tx_view_all_u ); ?>" class="text-white text-lg font-bold items-center bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border gap-x-3 inline-flex leading-7 break-words gap-y-3 px-10 py-5 rounded-full hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.25)_0px_25px_50px_-12px] transition-all duration-300">
							<?php echo esc_html( $tx_view_all_l ); ?>
							<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
						</a>
					</div>
				<?php endif; ?>
			</div>

			<!-- RIGHT — sticky sidebar (5 of 12 cols) -->
			<div class="box-border break-words md:col-end-[span_5] md:col-start-[span_5]">
				<div class="sticky box-border break-words top-24">

					<!-- Help card -->
					<div class="relative bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.25)_0px_25px_50px_-12px] box-border break-words border border-gray-100 overflow-hidden p-8 rounded-3xl border-solid md:p-10">
						<div class="absolute bg-[linear-gradient(to_right_bottom,rgba(59,155,159,0.05),rgba(0,0,0,0))] box-border blur-2xl h-32 break-words w-32 rounded-full right-0 top-0" aria-hidden="true"></div>

						<div class="relative box-border break-words z-10">
							<div class="relative box-border inline-block break-words mb-8">
								<div class="absolute bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] box-border blur-xl opacity-30 break-words rounded-3xl inset-0" aria-hidden="true"></div>
								<div class="relative items-center bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_20px_25px_-5px,rgba(0,0,0,0.1)_0px_8px_10px_-6px] box-border flex h-20 justify-center break-words w-20 rounded-3xl">
									<?php echo sp_icon( 'check_circle', 'w-10 h-10 text-white' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
							</div>
							<?php if ( $tx_side_h ) : ?>
								<h3 class="text-neutral-900 text-3xl font-black box-border leading-[37.5px] break-words mb-4 md:text-4xl md:leading-10"><?php echo esc_html( $tx_side_h ); ?></h3>
							<?php endif; ?>
							<?php if ( $tx_side_b ) : ?>
								<p class="text-neutral-600 text-lg box-border leading-[29.25px] break-words mb-8"><?php echo esc_html( $tx_side_b ); ?></p>
							<?php endif; ?>

							<?php if ( ! empty( $tx_contacts ) ) : ?>
								<div class="box-border break-words space-y-3">
									<?php foreach ( $tx_contacts as $sp_c ) :
										$sp_icon_key = isset( $sp_c['icon'] ) ? (string) $sp_c['icon'] : 'check';
										$sp_title    = isset( $sp_c['title'] ) ? (string) $sp_c['title'] : '';
										$sp_body     = isset( $sp_c['body'] ) ? (string) $sp_c['body'] : '';
										$sp_url      = isset( $sp_c['url'] ) ? (string) $sp_c['url'] : '#';
										if ( '' === $sp_title && '' === $sp_body ) { continue; }
										?>
										<a href="<?php echo esc_url( $sp_url ); ?>" class="items-center bg-[linear-gradient(to_right_bottom,rgb(249,250,251),rgb(255,255,255))] box-border gap-x-5 flex break-words gap-y-5 border border-gray-100 p-6 rounded-2xl border-solid hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_20px_25px_-5px,rgba(0,0,0,0.1)_0px_8px_10px_-6px] hover:border-teal-500 transition-all duration-300">
											<div class="relative box-border shrink-0 break-words">
												<div class="relative items-center bg-[linear-gradient(to_right_bottom,rgba(59,155,159,0.1),rgba(44,122,126,0.1))] box-border flex h-16 justify-center break-words w-16 rounded-2xl">
													<?php echo sp_icon( $sp_icon_key, 'w-8 h-8 text-teal-500' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
												</div>
											</div>
											<div class="box-border basis-[0%] grow break-words">
												<?php if ( $sp_title ) : ?>
													<p class="text-neutral-900 text-lg font-black box-border leading-7 break-words mb-1"><?php echo esc_html( $sp_title ); ?></p>
												<?php endif; ?>
												<?php if ( $sp_body ) : ?>
													<p class="text-neutral-600 text-sm box-border leading-5 break-words"><?php echo esc_html( $sp_body ); ?></p>
												<?php endif; ?>
											</div>
											<svg class="text-neutral-400 box-border shrink-0 h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
										</a>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>

					<!-- GPhC badge card (pulls from I1 Compliance options) -->
					<?php if ( $tx_gphc_number ) : ?>
						<div class="relative text-white bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.25)_0px_25px_50px_-12px] box-border break-words overflow-hidden mt-6 p-8 rounded-3xl">
							<div class="absolute box-border opacity-10 break-words inset-0" aria-hidden="true">
								<div class="absolute bg-[radial-gradient(circle_at_2px_2px,rgb(255,255,255)_1px,rgba(0,0,0,0)_0px)] bg-[length:30px_30px] box-border break-words inset-0"></div>
							</div>

							<div class="relative box-border break-words z-10">
								<div class="items-center box-border gap-x-4 flex break-words gap-y-4 mb-6">
									<div class="items-center backdrop-blur-sm bg-white/20 box-border flex h-16 justify-center break-words w-16 border rounded-2xl border-solid border-white/30">
										<?php echo sp_icon( 'shield', 'w-8 h-8 text-white' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
									<div class="box-border break-words">
										<h4 class="text-2xl font-black box-border leading-8 break-words"><?php esc_html_e( 'GPhC Registered', 'smart-pharmacy' ); ?></h4>
										<p class="text-white/90 text-sm box-border leading-5 break-words"><?php esc_html_e( 'Fully regulated UK pharmacy', 'smart-pharmacy' ); ?></p>
									</div>
								</div>
								<p class="text-white text-lg box-border leading-[1.6] break-words"><?php esc_html_e( 'Registration number:', 'smart-pharmacy' ); ?> <strong class="font-black"><?php echo esc_html( $tx_gphc_number ); ?></strong></p>
							</div>

							<div class="absolute bg-white/10 box-border blur-3xl h-32 break-words w-32 rounded-full right-0 top-0" aria-hidden="true"></div>
						</div>
					<?php endif; ?>

				</div>
			</div>

		</div>
	</div>
</section>
