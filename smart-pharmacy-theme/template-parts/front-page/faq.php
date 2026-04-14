<?php
/**
 * Homepage — FAQ accordion + contact sidebar.
 *
 * Uses native <details>/<summary> elements for the accordion so it works
 * without JavaScript (progressive enhancement). The plus icon rotates to
 * an X when the item is open via a CSS-only [open] selector.
 *
 * Editable via Theme Settings → Homepage → A10 — FAQ.
 *
 * @package SmartPharmacy
 */

if ( ! (bool) sp_field( 'faq_enabled', 1 ) ) {
	return;
}

$faq_badge        = sp_field( 'faq_badge_label', '24/7 Support Available' );
$faq_h_pre        = sp_field( 'faq_heading_pre', 'Ask us' );
$faq_h_hi         = sp_field( 'faq_heading_highlight', 'anything' );
$faq_sub          = sp_field( 'faq_subheading', 'Get instant answers to common questions, or speak with our pharmacy team for personalized support.' );
$faq_view_all_l   = sp_field( 'faq_view_all_label', 'View All Questions' );
$faq_view_all_u   = sp_field( 'faq_view_all_url', '/faqs/' );
$faq_side_h       = sp_field( 'faq_sidebar_heading', 'Need more help?' );
$faq_side_b       = sp_field( 'faq_sidebar_body', 'Our pharmacy team is here to answer your questions and provide expert guidance on your healthcare journey.' );
$faq_gphc_number  = sp_field( 'comp_gphc_number', '9012842' );

$faq_items = sp_field( 'faq_items', array() );
if ( empty( $faq_items ) || ! is_array( $faq_items ) ) {
	$faq_items = array(
		array( 'question' => 'Is Smart Pharmacy a registered pharmacy?', 'answer' => 'Yes. Smart Pharmacy is operated by OVIO Ltd, a UK-based distance-selling pharmacy. All medicines are dispensed from our registered premises under the supervision of a GPhC-registered pharmacist. We also work with UK-registered prescribers for all private treatments.' ),
		array( 'question' => 'How does the online consultation process work?', 'answer' => "When you choose a treatment, you'll complete a secure medical questionnaire. This is reviewed by a qualified prescriber or pharmacist, who may contact you if more information is needed. If your treatment is approved, your medicine will be dispensed and delivered directly to your address." ),
		array( 'question' => 'How do you make sure medicines are safe for me?', 'answer' => 'Every order is checked against your medical history and the answers you provide in your consultation. We use ID and age verification, review for medicine interactions, and follow strict GPhC and MHRA guidance. If anything needs clarification, our team will contact you before dispensing.' ),
		array( 'question' => 'How long does delivery take?', 'answer' => 'Once your order has been approved, it is normally dispatched the same or next working day. Delivery times may vary depending on the option you choose at checkout, but most patients receive their medicines within 1–3 working days.' ),
		array( 'question' => 'Who can use Smart Pharmacy?', 'answer' => 'Our services are available to adults aged 18 and over who are resident in the UK. Some treatments may have specific age restrictions or medical requirements.' ),
		array( 'question' => "What's your delivery policy?", 'answer' => 'We offer free delivery on orders over £30. Standard delivery takes 1-3 working days. Express delivery options are also available at checkout. All packages are sent in plain, discreet packaging.' ),
		array( 'question' => 'Are consultations really free?', 'answer' => 'Yes, all online consultations are completely free. You only pay for your medication if your treatment is approved. There are no hidden consultation fees or charges.' ),
	);
}

$faq_contacts = sp_field( 'faq_contacts', array() );
if ( empty( $faq_contacts ) || ! is_array( $faq_contacts ) ) {
	$faq_contacts = array(
		array( 'icon' => 'document', 'title' => 'Email Us', 'body' => 'Get a response within 24 hours', 'url' => '/contact/' ),
		array( 'icon' => 'check_circle', 'title' => 'Call Us', 'body' => 'Mon-Fri, 9am-5pm', 'url' => 'tel://+441234567890' ),
	);
}
?>

<section class="relative bg-[linear-gradient(to_right_bottom,rgb(253,252,250),rgb(248,246,243),rgb(245,243,240))] box-border break-words overflow-hidden py-12 md:py-16">
	<div class="absolute bg-teal-500/10 box-border blur-3xl h-96 break-words w-96 rounded-full -left-20 -top-20" aria-hidden="true"></div>
	<div class="absolute bg-teal-700/10 box-border blur-3xl h-[500px] break-words w-[500px] rounded-full -right-20 bottom-40" aria-hidden="true"></div>

	<div class="relative box-border max-w-[1400px] break-words z-10 mx-auto px-6 md:px-16">
		<div class="box-border gap-x-12 grid grid-cols-[repeat(1,minmax(0px,1fr))] break-words gap-y-12 md:grid-cols-[repeat(12,minmax(0px,1fr))]">

			<!-- LEFT — accordion -->
			<div class="box-border break-words md:col-end-[span_7] md:col-start-[span_7]">
				<div class="box-border break-words mb-12">
					<div class="items-center backdrop-blur-sm bg-white/80 shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border gap-x-3 inline-flex break-words gap-y-3 border border-gray-100 mb-8 px-6 py-3 rounded-full border-solid">
						<div class="items-center bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] box-border flex h-10 justify-center break-words w-10 rounded-full">
							<?php echo sp_icon( 'check_circle', 'w-5 h-5 text-white' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
						<span class="text-neutral-900 text-base font-bold box-border block leading-6 break-words"><?php echo esc_html( $faq_badge ); ?></span>
						<div class="bg-green-500 box-border h-2 break-words w-2 rounded-full"></div>
					</div>

					<h2 class="ultra-heading text-neutral-900 text-5xl font-black box-border leading-[1.1] break-words mb-6 md:text-7xl">
						<?php echo esc_html( $faq_h_pre ); ?>
						<?php if ( $faq_h_hi ) : ?>
							<span class="relative inline-block">
								<span class="text-transparent bg-clip-text bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))]"><?php echo esc_html( $faq_h_hi ); ?></span>
							</span>
						<?php endif; ?>
					</h2>

					<p class="text-neutral-600 text-xl box-border leading-[1.6] break-words md:text-2xl md:leading-[1.65] font-medium"><?php echo esc_html( $faq_sub ); ?></p>

					<div class="items-center box-border gap-x-3 flex break-words gap-y-3 mt-6" aria-hidden="true">
						<div class="bg-[linear-gradient(to_right,rgba(0,0,0,0),rgba(59,155,159,0.3))] box-border h-px break-words w-16"></div>
						<div class="bg-teal-500/30 box-border h-2 break-words w-2 rounded-full"></div>
						<div class="bg-[linear-gradient(to_left,rgba(0,0,0,0),rgba(59,155,159,0.3))] box-border h-px break-words w-16"></div>
					</div>
				</div>

				<!-- Accordion -->
				<div class="box-border break-words mb-10 space-y-4 sp-faq">
					<?php foreach ( $faq_items as $sp_qna ) :
						$sp_q = isset( $sp_qna['question'] ) ? (string) $sp_qna['question'] : '';
						$sp_a = isset( $sp_qna['answer'] ) ? (string) $sp_qna['answer'] : '';
						if ( '' === $sp_q ) { continue; }
						?>
						<details class="sp-faq-item relative bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border break-words border border-gray-100 overflow-hidden rounded-2xl border-solid premium-hover">
							<summary class="items-center box-border flex justify-between list-none break-words p-6 md:p-8 cursor-pointer">
								<span class="ultra-heading text-neutral-900 text-lg font-bold box-border block leading-7 break-words pr-4 md:text-xl"><?php echo esc_html( $sp_q ); ?></span>
								<div class="relative box-border shrink-0 break-words">
									<div class="relative items-center bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border flex h-10 justify-center break-words w-10 rounded-full md:h-12 md:w-12">
										<!-- Plus icon: rotates to X via CSS when <details> is open -->
										<svg class="sp-faq-chevron w-6 h-6 text-white transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
											<path d="M12 5v14M5 12h14" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
									</div>
								</div>
							</summary>
							<div class="box-border break-words pb-6 px-6 md:pb-8 md:px-8">
								<div class="box-border break-words border-gray-100 pt-4 border-t border-solid">
									<p class="text-neutral-600 text-base box-border leading-[1.6] break-words md:text-lg md:leading-[1.65]"><?php echo esc_html( $sp_a ); ?></p>
								</div>
							</div>
						</details>
					<?php endforeach; ?>
				</div>

				<div class="box-border break-words mt-10">
					<a href="<?php echo esc_url( $faq_view_all_u ); ?>" class="premium-button text-white text-lg font-bold items-center bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border gap-x-3 inline-flex leading-7 break-words gap-y-3 px-10 py-5 rounded-full hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.25)_0px_25px_50px_-12px] transition-all duration-300 antialiased">
						<?php echo esc_html( $faq_view_all_l ); ?>
						<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
					</a>
				</div>
			</div>

			<!-- RIGHT — sticky sidebar -->
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
							<h3 class="ultra-heading text-neutral-900 text-3xl font-black box-border leading-[1.2] break-words mb-4 md:text-4xl"><?php echo esc_html( $faq_side_h ); ?></h3>
							<p class="text-neutral-600 text-lg box-border leading-[1.6] break-words mb-8"><?php echo esc_html( $faq_side_b ); ?></p>

							<div class="box-border break-words space-y-3">
								<?php foreach ( $faq_contacts as $sp_c ) :
									$sp_icon_key = isset( $sp_c['icon'] ) ? (string) $sp_c['icon'] : 'check';
									$sp_title    = isset( $sp_c['title'] ) ? (string) $sp_c['title'] : '';
									$sp_body     = isset( $sp_c['body'] ) ? (string) $sp_c['body'] : '';
									$sp_url      = isset( $sp_c['url'] ) ? (string) $sp_c['url'] : '#';
									?>
									<a href="<?php echo esc_url( $sp_url ); ?>" class="items-center bg-[linear-gradient(to_right_bottom,rgb(249,250,251),rgb(255,255,255))] box-border gap-x-5 flex break-words gap-y-5 border border-gray-100 p-6 rounded-2xl border-solid hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_20px_25px_-5px,rgba(0,0,0,0.1)_0px_8px_10px_-6px] hover:border-teal-500 transition-all duration-300">
										<div class="relative box-border shrink-0 break-words">
											<div class="relative items-center bg-[linear-gradient(to_right_bottom,rgba(59,155,159,0.1),rgba(44,122,126,0.1))] box-border flex h-16 justify-center break-words w-16 rounded-2xl">
												<?php echo sp_icon( $sp_icon_key, 'w-8 h-8 text-teal-500' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
											</div>
										</div>
										<div class="box-border basis-[0%] grow break-words">
											<p class="text-neutral-900 text-lg font-black box-border leading-7 break-words mb-1 antialiased"><?php echo esc_html( $sp_title ); ?></p>
											<p class="text-neutral-600 text-sm box-border leading-5 break-words"><?php echo esc_html( $sp_body ); ?></p>
										</div>
										<svg class="text-neutral-400 box-border shrink-0 h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
									</a>
								<?php endforeach; ?>
							</div>
						</div>
					</div>

					<!-- GPhC badge card -->
					<div class="relative text-white bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.25)_0px_25px_50px_-12px] box-border break-words overflow-hidden mt-6 p-8 rounded-3xl">
						<div class="relative box-border break-words z-10">
							<div class="items-center box-border gap-x-4 flex break-words gap-y-4 mb-6">
								<div class="items-center backdrop-blur-sm bg-white/20 box-border flex h-16 justify-center break-words w-16 border rounded-2xl border-solid border-white/30">
									<?php echo sp_icon( 'shield', 'w-8 h-8 text-white' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
								<div class="box-border break-words">
									<h4 class="text-2xl font-black box-border leading-8 break-words antialiased"><?php esc_html_e( 'GPhC Registered', 'smart-pharmacy' ); ?></h4>
									<p class="text-white/90 text-sm box-border leading-5 break-words"><?php esc_html_e( 'Fully regulated UK pharmacy', 'smart-pharmacy' ); ?></p>
								</div>
							</div>
							<p class="text-white text-lg box-border leading-[1.6] break-words"><?php esc_html_e( 'Registration number:', 'smart-pharmacy' ); ?> <strong class="font-black antialiased"><?php echo esc_html( $faq_gphc_number ); ?></strong></p>
						</div>
					</div>

				</div>
			</div>

		</div>
	</div>
</section>
