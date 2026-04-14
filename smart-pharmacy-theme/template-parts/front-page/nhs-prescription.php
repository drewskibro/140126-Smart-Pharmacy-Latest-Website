<?php
/**
 * Homepage — NHS Prescription section.
 *
 * Two-column: feature bullets left, sticky "Get Started" + "Need Help" cards right.
 *
 * Editable via Theme Settings → Homepage → A5 — NHS Prescription.
 *
 * @package SmartPharmacy
 */

if ( ! (bool) sp_field( 'nhs_enabled', 1 ) ) {
	return;
}

$nhs_logo     = sp_image_url( 'nhs_logo', 'https://c.animaapp.com/mhiyf2riFan5kf/img/screenshot-2025-11-04-140700.png', 'option' );
$nhs_logo_alt = sp_image_alt( 'nhs_logo', 'NHS - Providing NHS services', 'option' );
$nhs_h_pre    = sp_field( 'nhs_heading_pre', 'Order your' );
$nhs_h_hi     = sp_field( 'nhs_heading_highlight', 'NHS prescription' );
$nhs_sub      = sp_field( 'nhs_subheading', 'Fast, free NHS prescriptions delivered to your door' );

$nhs_features = sp_field( 'nhs_features', array() );
if ( empty( $nhs_features ) || ! is_array( $nhs_features ) ) {
	$nhs_features = array(
		array( 'title' => 'Free NHS prescription delivery', 'body' => 'No delivery charges on NHS prescriptions' ),
		array( 'title' => 'Order repeat prescriptions online', 'body' => 'Quick and easy repeat prescription service' ),
		array( 'title' => 'Speak to our pharmacists anytime', 'body' => 'Expert advice when you need it' ),
	);
}

$nhs_card_h       = sp_field( 'nhs_card_heading', 'Get Started' );
$nhs_card_b       = sp_field( 'nhs_card_body', 'Order your NHS prescription or login to your account' );
$nhs_primary_l    = sp_field( 'nhs_primary_label', 'Order Prescription' );
$nhs_primary_u    = sp_field( 'nhs_primary_url', '/nhs-prescriptions/' );
$nhs_secondary_l  = sp_field( 'nhs_secondary_label', 'Login to Account' );
$nhs_secondary_u  = sp_field( 'nhs_secondary_url', '/my-account/' );
$nhs_help_h       = sp_field( 'nhs_help_heading', 'Need Help?' );
$nhs_help_b       = sp_field( 'nhs_help_body', 'Contact our pharmacy team' );
?>

<section class="relative bg-[linear-gradient(to_right_bottom,rgb(253,252,250),rgb(248,246,243),rgb(245,243,240))] box-border break-words overflow-hidden py-12 md:py-16">
	<div class="absolute bg-teal-500/10 box-border blur-3xl h-96 break-words w-96 rounded-full -left-20 -top-20" aria-hidden="true"></div>
	<div class="absolute bg-teal-700/10 box-border blur-3xl h-[500px] break-words w-[500px] rounded-full -right-20 bottom-0" aria-hidden="true"></div>

	<div class="relative box-border max-w-[1400px] break-words z-10 mx-auto px-6 md:px-16">
		<div class="items-start box-border gap-x-16 grid grid-cols-[repeat(1,minmax(0px,1fr))] break-words gap-y-16 md:grid-cols-[60fr_40fr]">

			<!-- LEFT -->
			<div class="box-border break-words">
				<div class="mb-10">
					<img src="<?php echo esc_url( $nhs_logo ); ?>" alt="<?php echo esc_attr( $nhs_logo_alt ); ?>" class="box-border h-auto max-w-full break-words w-[280px] mb-6 md:w-[320px]" />
				</div>

				<h2 class="ultra-heading text-neutral-900 text-4xl font-black box-border leading-[1.1] break-words mb-6 md:text-6xl">
					<?php echo esc_html( $nhs_h_pre ); ?>
					<?php if ( $nhs_h_hi ) : ?>
						<span class="relative inline-block">
							<span class="text-transparent bg-clip-text bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))]"><?php echo esc_html( $nhs_h_hi ); ?></span>
						</span>
					<?php endif; ?>
				</h2>

				<p class="text-neutral-600 text-lg box-border leading-[1.6] break-words mb-10 md:text-xl md:leading-[1.6] font-medium"><?php echo esc_html( $nhs_sub ); ?></p>

				<div class="items-center box-border gap-x-3 flex break-words gap-y-3 mb-10" aria-hidden="true">
					<div class="bg-[linear-gradient(to_right,rgba(0,0,0,0),rgba(59,155,159,0.3))] box-border h-px break-words w-20"></div>
					<div class="bg-teal-500/30 box-border h-2 break-words w-2 rounded-full"></div>
					<div class="bg-[linear-gradient(to_left,rgba(0,0,0,0),rgba(59,155,159,0.3))] box-border h-px break-words w-20"></div>
				</div>

				<div class="box-border break-words space-y-6">
					<?php foreach ( $nhs_features as $sp_feat ) :
						$sp_title = isset( $sp_feat['title'] ) ? (string) $sp_feat['title'] : '';
						$sp_body  = isset( $sp_feat['body'] ) ? (string) $sp_feat['body'] : '';
						?>
						<div class="items-start box-border gap-x-6 flex break-words gap-y-6 group">
							<div class="box-border shrink-0 break-words">
								<div class="relative box-border break-words">
									<div class="absolute bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] box-border blur-xl opacity-30 break-words rounded-2xl inset-0" aria-hidden="true"></div>
									<div class="relative items-center bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border flex h-16 justify-center break-words w-16 rounded-2xl transition-transform duration-300 group-hover:scale-110">
										<?php echo sp_icon( 'check', 'w-8 h-8 text-white' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
								</div>
							</div>
							<div class="box-border break-words">
								<p class="text-neutral-900 text-xl font-black box-border leading-[1.3] break-words mb-2 md:text-2xl antialiased"><?php echo esc_html( $sp_title ); ?></p>
								<p class="text-neutral-600 text-base box-border leading-[1.6] break-words md:text-lg"><?php echo esc_html( $sp_body ); ?></p>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<!-- RIGHT: sticky cards -->
			<div class="box-border break-words">
				<div class="sticky box-border break-words top-24">

					<!-- Primary card -->
					<div class="relative bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.25)_0px_25px_50px_-12px] box-border break-words border border-gray-100 overflow-hidden p-8 rounded-3xl border-solid md:p-10">
						<div class="absolute bg-[linear-gradient(to_right_bottom,rgba(59,155,159,0.05),rgba(0,0,0,0))] box-border blur-2xl h-32 break-words w-32 rounded-full right-0 top-0" aria-hidden="true"></div>

						<div class="relative box-border break-words z-10">
							<div class="relative box-border inline-block break-words mb-8">
								<div class="absolute bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] box-border blur-xl opacity-30 break-words rounded-3xl inset-0" aria-hidden="true"></div>
								<div class="relative items-center bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_20px_25px_-5px,rgba(0,0,0,0.1)_0px_8px_10px_-6px] box-border flex h-20 justify-center break-words w-20 rounded-3xl">
									<?php echo sp_icon( 'document', 'w-10 h-10 text-white' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
							</div>

							<h3 class="ultra-heading text-neutral-900 text-2xl font-black box-border leading-[1.3] break-words mb-3 md:text-3xl md:leading-[1.3]"><?php echo esc_html( $nhs_card_h ); ?></h3>
							<p class="text-neutral-600 text-base box-border leading-[1.5] break-words mb-6 md:text-lg md:leading-[1.5]"><?php echo esc_html( $nhs_card_b ); ?></p>

							<div class="box-border break-words space-y-3">
								<a href="<?php echo esc_url( $nhs_primary_u ); ?>" class="premium-button text-white text-base font-bold items-center bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border gap-x-2 flex justify-center leading-[1.4] break-words gap-y-2 w-full px-6 py-4 rounded-2xl hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.25)_0px_25px_50px_-12px] transition-all duration-300 md:text-lg md:px-8 md:py-5">
									<?php echo esc_html( $nhs_primary_l ); ?>
									<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
								</a>
								<a href="<?php echo esc_url( $nhs_secondary_u ); ?>" class="text-neutral-900 text-base font-bold items-center bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.05)_0px_1px_2px_0px] box-border gap-x-2 flex justify-center leading-[1.4] break-words gap-y-2 w-full border-2 border-solid border-teal-500 px-6 py-4 rounded-2xl hover:bg-teal-50 hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] transition-all duration-300 md:text-lg md:px-8 md:py-5">
									<?php echo esc_html( $nhs_secondary_l ); ?>
									<svg class="w-5 h-5 text-teal-500" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
								</a>
							</div>
						</div>
					</div>

					<!-- Help card -->
					<div class="relative text-white bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.25)_0px_25px_50px_-12px] box-border break-words overflow-hidden mt-6 p-8 rounded-3xl group hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.35)_0px_35px_60px_-15px] transition-all duration-300">
						<div class="relative box-border break-words z-10">
							<div class="items-center box-border gap-x-4 flex break-words gap-y-4 mb-2">
								<div class="relative box-border shrink-0 break-words">
									<div class="absolute bg-white box-border blur-xl opacity-30 break-words rounded-2xl inset-0" aria-hidden="true"></div>
									<div class="relative items-center backdrop-blur-sm bg-white/20 box-border flex h-16 justify-center break-words w-16 border-2 rounded-2xl border-solid border-white/40 transition-transform duration-300 group-hover:scale-110">
										<svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" aria-hidden="true">
											<path d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
									</div>
								</div>
								<div class="box-border break-words">
									<h4 class="text-xl font-black box-border leading-[1.3] break-words mb-1 md:text-2xl antialiased"><?php echo esc_html( $nhs_help_h ); ?></h4>
									<p class="text-white/95 text-base box-border leading-[1.4] break-words font-semibold"><?php echo esc_html( $nhs_help_b ); ?></p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
</section>
