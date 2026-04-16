<?php
/**
 * Single Treatment — Eligibility / Calculator (Stage 3b stub).
 *
 * Teal-gradient "Check Your Eligibility" callout: 3 criteria tiles +
 * "Start Free Assessment" CTA. This is the anchor target for B3's
 * `#consultation` pricing-card CTAs (the `id="consultation"` attribute
 * on the <section> must be preserved).
 *
 * STAGE 3b NOTE: this is the static UI shell. Stage 5 replaces the
 * criteria tiles with a live BMI calculator form and ties the CTA to
 * the real consultation flow + eligibility gating logic.
 *
 * Editable via the Treatment post edit screen → C3 — Treatment Eligibility.
 *
 * @package SmartPharmacy
 */

if ( ! (bool) sp_field( 'tx_elig_enabled', 1 ) ) {
	return;
}

$tx_badge_text = sp_field( 'tx_elig_badge_text', 'Check Your Eligibility' );
$tx_badge_icon = sp_field( 'tx_elig_badge_icon', 'check_circle' );
$tx_heading    = sp_field( 'tx_elig_heading', 'Calculate Your Weight Loss Journey' );
$tx_sub        = sp_field( 'tx_elig_subheading', 'Find out if weight loss medication is right for you with our quick eligibility checker' );
$tx_cta_label  = sp_field( 'tx_elig_cta_label', 'Start Free Assessment' );
$tx_cta_url    = sp_field( 'tx_elig_cta_url', '#consultation' );
$tx_footer     = sp_field( 'tx_elig_footer_note', 'Takes less than 5 minutes • No payment required' );

$tx_criteria = sp_field( 'tx_elig_criteria', array() );
if ( empty( $tx_criteria ) || ! is_array( $tx_criteria ) ) {
	// Graceful fallback matching the prototype.
	$tx_criteria = array(
		array( 'icon' => 'person',       'title' => 'BMI Over 30', 'description' => 'Or BMI 27+ with health conditions' ),
		array( 'icon' => 'check_circle', 'title' => 'Age 18-75',   'description' => 'Suitable for most adults' ),
		array( 'icon' => 'sparkle',      'title' => 'Committed',   'description' => 'Ready for lifestyle changes' ),
	);
}

// Column heuristic: 1 = single centred, 2 = 2-col, 3+ = 3-col.
$sp_elig_count = count( $tx_criteria );
if ( 1 === $sp_elig_count ) {
	$sp_elig_cols = '';
} elseif ( 2 === $sp_elig_count ) {
	$sp_elig_cols = 'md:grid-cols-[repeat(2,minmax(0px,1fr))]';
} else {
	$sp_elig_cols = 'md:grid-cols-[repeat(3,minmax(0px,1fr))]';
}
?>

<section id="consultation" class="relative bg-white box-border break-words overflow-hidden py-16 md:py-24">
	<div class="absolute bg-teal-500/10 box-border blur-3xl h-96 break-words w-96 rounded-full -left-20 -top-20" aria-hidden="true"></div>
	<div class="absolute bg-teal-700/10 box-border blur-3xl h-[500px] break-words w-[500px] rounded-full -right-20 bottom-0" aria-hidden="true"></div>

	<div class="relative box-border max-w-[1200px] break-words z-10 mx-auto px-8 md:px-24">
		<div class="relative bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.25)_0px_25px_50px_-12px] box-border break-words overflow-hidden p-12 rounded-3xl md:p-16">
			<!-- Subtle dot pattern overlay -->
			<div class="absolute box-border opacity-10 break-words inset-0" aria-hidden="true">
				<div class="absolute bg-[radial-gradient(circle_at_2px_2px,rgb(255,255,255)_1px,rgba(0,0,0,0)_0px)] bg-[length:40px_40px] box-border break-words inset-0"></div>
			</div>

			<div class="relative box-border break-words z-10 text-center">
				<?php if ( $tx_badge_text ) : ?>
					<div class="items-center backdrop-blur-sm bg-white/20 box-border gap-x-3 inline-flex break-words gap-y-3 border px-5 py-3 rounded-2xl border-solid border-white/30 mb-8">
						<?php if ( $tx_badge_icon ) : ?>
							<?php echo sp_icon( $tx_badge_icon, 'w-6 h-6 text-white' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php endif; ?>
						<span class="text-white text-base font-bold box-border block leading-6 break-words"><?php echo esc_html( $tx_badge_text ); ?></span>
					</div>
				<?php endif; ?>

				<?php if ( $tx_heading ) : ?>
					<h2 class="text-white text-4xl font-black box-border tracking-[-1.2px] leading-[1.2] break-words mb-6 md:text-6xl md:tracking-[-1.5px]"><?php echo esc_html( $tx_heading ); ?></h2>
				<?php endif; ?>

				<?php if ( $tx_sub ) : ?>
					<p class="text-white/95 text-xl box-border leading-[1.6] break-words mb-12 max-w-3xl mx-auto md:text-2xl md:leading-[1.7]"><?php echo esc_html( $tx_sub ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $tx_criteria ) ) : ?>
					<div class="backdrop-blur-xl bg-white/10 box-border break-words border p-8 rounded-2xl border-solid border-white/30 mb-10 md:p-12">
						<div class="box-border gap-x-8 grid grid-cols-[repeat(1,minmax(0px,1fr))] break-words gap-y-8 <?php echo esc_attr( $sp_elig_cols ); ?>">
							<?php foreach ( $tx_criteria as $sp_c ) :
								$sp_c_icon  = isset( $sp_c['icon'] ) ? (string) $sp_c['icon'] : '';
								$sp_c_title = isset( $sp_c['title'] ) ? (string) $sp_c['title'] : '';
								$sp_c_desc  = isset( $sp_c['description'] ) ? (string) $sp_c['description'] : '';
								?>
								<div class="box-border break-words text-center">
									<?php if ( $sp_c_icon ) : ?>
										<div class="items-center bg-white/20 box-border flex h-20 justify-center break-words w-20 rounded-2xl mx-auto mb-4">
											<?php echo sp_icon( $sp_c_icon, 'w-10 h-10 text-white' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										</div>
									<?php endif; ?>
									<?php if ( $sp_c_title ) : ?>
										<p class="text-white text-lg font-bold box-border leading-7 break-words mb-2"><?php echo esc_html( $sp_c_title ); ?></p>
									<?php endif; ?>
									<?php if ( $sp_c_desc ) : ?>
										<p class="text-white/80 text-sm box-border leading-5 break-words"><?php echo esc_html( $sp_c_desc ); ?></p>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $tx_cta_label ) : ?>
					<a href="<?php echo esc_url( $tx_cta_url ); ?>" class="text-neutral-900 text-lg font-bold items-center bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_20px_25px_-5px,rgba(0,0,0,0.1)_0px_8px_10px_-6px] box-border gap-x-3 inline-flex leading-7 break-words gap-y-3 px-12 py-6 rounded-full hover:bg-gray-100 hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.25)_0px_25px_50px_-12px] transition-all duration-300">
						<?php echo esc_html( $tx_cta_label ); ?>
						<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
					</a>
				<?php endif; ?>

				<?php if ( $tx_footer ) : ?>
					<p class="text-white/80 text-sm box-border leading-5 break-words mt-6"><?php echo esc_html( $tx_footer ); ?></p>
				<?php endif; ?>
			</div>

			<!-- Corner light blobs -->
			<div class="absolute bg-white/10 box-border blur-3xl h-64 break-words w-64 rounded-full -right-20 -top-20" aria-hidden="true"></div>
			<div class="absolute bg-white/10 box-border blur-3xl h-64 break-words w-64 rounded-full -left-20 -bottom-20" aria-hidden="true"></div>
		</div>
	</div>
</section>
