<?php
/**
 * Single Treatment — Final CTA.
 *
 * Full-width teal-gradient closing-CTA band: eyebrow pill, h2, sub-
 * heading, paired primary (solid white) + secondary (glass) buttons,
 * and a row of trust signals. Primary CTA anchors to #consultation
 * (the C3 Eligibility section); secondary CTA routes to the contact
 * page by default.
 *
 * Editable via the Treatment post edit screen → D2 — Treatment Final CTA.
 *
 * @package SmartPharmacy
 */

if ( ! (bool) sp_field( 'tx_cta_enabled', 1 ) ) {
	return;
}

$tx_badge_text      = sp_field( 'tx_cta_badge_text', 'Start Your Journey Today' );
$tx_badge_icon      = sp_field( 'tx_cta_badge_icon', 'sparkle' );
$tx_heading         = sp_field( 'tx_cta_heading', 'Start Your Weight Loss Journey Online' );
$tx_sub             = sp_field( 'tx_cta_subheading', 'Get started in under 5 minutes with our simple online assessment' );
$tx_primary_label   = sp_field( 'tx_cta_primary_label', 'Start Consultation' );
$tx_primary_url     = sp_field( 'tx_cta_primary_url', '#consultation' );
$tx_secondary_label = sp_field( 'tx_cta_secondary_label', 'Speak to a Pharmacist' );
$tx_secondary_url   = sp_field( 'tx_cta_secondary_url', '/contact/' );

$tx_trust = sp_field( 'tx_cta_trust', array() );
if ( empty( $tx_trust ) || ! is_array( $tx_trust ) ) {
	$tx_trust = array(
		array( 'text' => 'No payment required' ),
		array( 'text' => 'Takes 5 minutes' ),
		array( 'text' => '100% confidential' ),
	);
}
?>

<section class="relative bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] box-border break-words overflow-hidden py-20 md:py-32">
	<!-- Dot-pattern overlay -->
	<div class="absolute box-border opacity-10 break-words inset-0" aria-hidden="true">
		<div class="absolute bg-[radial-gradient(circle_at_2px_2px,rgb(255,255,255)_1px,rgba(0,0,0,0)_0px)] bg-[length:40px_40px] box-border break-words inset-0"></div>
	</div>

	<div class="relative box-border max-w-[1400px] break-words z-10 mx-auto px-8 md:px-24">
		<div class="box-border break-words text-center">
			<?php if ( $tx_badge_text ) : ?>
				<div class="items-center backdrop-blur-sm bg-white/20 box-border gap-x-3 inline-flex break-words gap-y-3 border px-5 py-3 rounded-2xl border-solid border-white/30 mb-8">
					<?php if ( $tx_badge_icon ) : ?>
						<?php echo sp_icon( $tx_badge_icon, 'w-6 h-6 text-white' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php endif; ?>
					<span class="text-white text-base font-bold box-border block leading-6 break-words"><?php echo esc_html( $tx_badge_text ); ?></span>
				</div>
			<?php endif; ?>

			<?php if ( $tx_heading ) : ?>
				<h2 class="text-white text-5xl font-black box-border tracking-[-1.2px] leading-[1.2] break-words mb-6 md:text-7xl md:tracking-[-1.8px]"><?php echo esc_html( $tx_heading ); ?></h2>
			<?php endif; ?>

			<?php if ( $tx_sub ) : ?>
				<p class="text-white/95 text-xl box-border leading-[1.6] break-words mb-12 max-w-3xl mx-auto md:text-2xl md:leading-[1.7]"><?php echo esc_html( $tx_sub ); ?></p>
			<?php endif; ?>

			<?php if ( $tx_primary_label || $tx_secondary_label ) : ?>
				<div class="box-border gap-x-4 flex flex-col break-words gap-y-4 items-center justify-center mb-8 md:flex-row">
					<?php if ( $tx_primary_label ) : ?>
						<a href="<?php echo esc_url( $tx_primary_url ); ?>" class="text-neutral-900 text-lg font-bold items-center bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_20px_25px_-5px,rgba(0,0,0,0.1)_0px_8px_10px_-6px] box-border gap-x-3 inline-flex leading-7 break-words gap-y-3 px-12 py-6 rounded-full hover:bg-gray-100 hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.25)_0px_25px_50px_-12px] transition-all duration-300">
							<?php echo esc_html( $tx_primary_label ); ?>
							<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
						</a>
					<?php endif; ?>

					<?php if ( $tx_secondary_label ) : ?>
						<a href="<?php echo esc_url( $tx_secondary_url ); ?>" class="text-white text-lg font-bold items-center backdrop-blur-sm bg-white/20 shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border gap-x-3 inline-flex leading-7 break-words gap-y-3 border-2 border-solid border-white/40 px-12 py-6 rounded-full hover:bg-white/30 hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_20px_25px_-5px,rgba(0,0,0,0.1)_0px_8px_10px_-6px] transition-all duration-300">
							<?php echo esc_html( $tx_secondary_label ); ?>
							<!-- Phone icon (inline — not in sp_icon palette) -->
							<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $tx_trust ) ) : ?>
				<div class="text-white/80 text-sm items-center box-border gap-x-6 flex justify-center leading-5 break-words gap-y-6 flex-wrap">
					<?php
					$sp_last = count( $tx_trust ) - 1;
					foreach ( $tx_trust as $sp_i => $sp_t ) :
						$sp_text = isset( $sp_t['text'] ) ? (string) $sp_t['text'] : '';
						if ( '' === $sp_text ) { continue; }
						?>
						<div class="items-center box-border gap-x-2 flex break-words gap-y-2">
							<?php echo sp_icon( 'check', 'text-white h-5 w-5' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span class="font-medium box-border block break-words"><?php echo esc_html( $sp_text ); ?></span>
						</div>
						<?php if ( $sp_i < $sp_last ) : ?>
							<div class="bg-white/30 box-border h-4 break-words w-px" aria-hidden="true"></div>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<!-- Corner light blobs -->
	<div class="absolute bg-white/10 box-border blur-3xl h-96 break-words w-96 rounded-full -left-20 -bottom-20" aria-hidden="true"></div>
	<div class="absolute bg-white/10 box-border blur-3xl h-96 break-words w-96 rounded-full -right-20 -top-20" aria-hidden="true"></div>
</section>
