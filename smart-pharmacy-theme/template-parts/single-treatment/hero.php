<?php
/**
 * Single Treatment — Hero.
 *
 * Two-column: content left (category eyebrow, tri-part headline, subheading,
 * CTAs, trust row), hero image right with 2 overlay stat cards.
 *
 * Editable via the Treatment post edit screen → B1 — Treatment Hero.
 *
 * @package SmartPharmacy
 */

$tx_category = sp_field( 'tx_hero_category', '' );
$tx_h_pre    = sp_field( 'tx_hero_heading_pre', '' );
$tx_h_hi     = sp_field( 'tx_hero_heading_highlight', '' );
$tx_h_post   = sp_field( 'tx_hero_heading_post', '' );
$tx_sub      = sp_field( 'tx_hero_subheading', '' );
$tx_cta1_l   = sp_field( 'tx_hero_primary_cta_label', 'Start Consultation' );
/*
 * Smart default for the "Start Consultation" CTA so a treatment page never
 * dead-ends when its CTA field is left blank: weight-loss treatments go to
 * the BMI assessment, everything else to the consultation form. An explicit
 * ACF value still wins.
 */
$sp_tx_cta_default = '#consultation';
if ( class_exists( 'SPE_Admin' ) ) {
	$sp_tx_is_weight = ( 'weight-loss' === get_post_field( 'post_name', get_the_ID() ) )
		|| ( false !== stripos( (string) get_the_title(), 'weight' ) );
	if ( $sp_tx_is_weight && method_exists( 'SPE_Admin', 'get_checker_url' ) ) {
		$sp_tx_cta_default = SPE_Admin::get_checker_url();
	} elseif ( method_exists( 'SPE_Admin', 'get_consultation_url' ) ) {
		$sp_tx_cta_default = SPE_Admin::get_consultation_url();
	}
}
$tx_cta1_u   = sp_field( 'tx_hero_primary_cta_url', $sp_tx_cta_default );
$tx_cta2_l   = sp_field( 'tx_hero_secondary_cta_label', 'Learn More' );
$tx_cta2_u   = sp_field( 'tx_hero_secondary_cta_url', '#how-it-works' );

// Hero image (ACF → fallback to featured image → prototype Unsplash URL).
$tx_img_url = sp_image_url( 'tx_hero_image', '' );
$tx_img_alt = sp_image_alt( 'tx_hero_image', get_the_title() );
if ( '' === $tx_img_url ) {
	$tx_img_url = get_the_post_thumbnail_url( null, 'large' );
}
if ( '' === $tx_img_url ) {
	$tx_img_url = 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=800&h=900&fit=crop';
}

$tx_trust = sp_field( 'tx_hero_trust_items', array() );
if ( empty( $tx_trust ) || ! is_array( $tx_trust ) ) {
	$tx_trust = array(
		array( 'icon' => 'check_circle', 'label' => 'NHS-approved medication' ),
		array( 'icon' => 'person',       'label' => 'Expert prescribers' ),
		array( 'icon' => 'check',        'label' => 'FREE Delivery' ),
	);
}

$tx_stats = sp_field( 'tx_hero_stats', array() );
?>

<section class="relative bg-[linear-gradient(to_right_bottom,rgb(253,252,250),rgb(248,246,243),rgb(245,243,240))] box-border break-words w-full overflow-hidden">
	<div class="absolute bg-teal-500/10 box-border blur-3xl h-96 break-words w-96 rounded-full -left-20 -top-20" aria-hidden="true"></div>
	<div class="absolute bg-teal-700/10 box-border blur-3xl h-[500px] break-words w-[500px] rounded-full -right-20 bottom-0" aria-hidden="true"></div>

	<div class="relative box-border break-words w-full px-8 py-12 md:px-24 md:py-20">
		<div class="items-center box-border gap-x-12 grid grid-cols-[repeat(1,minmax(0px,1fr))] max-w-[1600px] break-words gap-y-12 mx-auto md:gap-x-16 md:grid-cols-[55fr_45fr] md:gap-y-16">

			<!-- LEFT -->
			<div class="box-border break-words">

				<?php if ( $tx_category ) : ?>
					<div class="items-center bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.05)_0px_1px_2px_0px] box-border gap-x-2 inline-flex break-words gap-y-2 border border-gray-100 mb-6 px-4 py-2 rounded-full border-solid">
						<div class="bg-teal-500 box-border h-2 break-words w-2 rounded-full"></div>
						<span class="text-teal-600 text-sm font-bold box-border block leading-5 break-words uppercase tracking-[1.5px]"><?php echo esc_html( $tx_category ); ?></span>
					</div>
				<?php endif; ?>

				<h1 class="text-neutral-900 text-4xl font-black box-border tracking-[-0.96px] leading-[1.2] break-words mb-6 md:text-6xl md:tracking-[-1.44px] md:leading-[1.2]">
					<?php if ( $tx_h_pre ) : ?><?php echo esc_html( $tx_h_pre ); ?> <?php endif; ?>
					<?php if ( $tx_h_hi ) : ?>
						<span class="relative inline-block">
							<span class="text-transparent bg-clip-text bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))]"><?php echo esc_html( $tx_h_hi ); ?></span>
						</span>
					<?php endif; ?>
					<?php if ( $tx_h_post ) : ?> <?php echo esc_html( $tx_h_post ); ?><?php endif; ?>
				</h1>

				<?php if ( $tx_sub ) : ?>
					<p class="text-neutral-600 text-lg box-border leading-[1.6] break-words mb-8 md:text-xl md:leading-[1.7]"><?php echo esc_html( $tx_sub ); ?></p>
				<?php endif; ?>

				<div class="box-border gap-x-4 flex flex-col break-words gap-y-4 mb-8 md:flex-row">
					<a href="<?php echo esc_url( $tx_cta1_u ); ?>" class="text-white text-base font-bold items-center bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border gap-x-2 flex justify-center leading-[1.4] break-words gap-y-2 px-8 py-4 rounded-full hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.25)_0px_25px_50px_-12px] transition-all duration-300 md:text-lg">
						<?php echo esc_html( $tx_cta1_l ); ?>
						<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
					</a>
					<a href="<?php echo esc_url( $tx_cta2_u ); ?>" class="text-neutral-900 text-base font-bold items-center bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.05)_0px_1px_2px_0px] box-border gap-x-2 flex justify-center leading-[1.4] break-words gap-y-2 border-2 border-solid border-teal-500 px-8 py-4 rounded-full hover:bg-teal-50 hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] transition-all duration-300 md:text-lg">
						<?php echo esc_html( $tx_cta2_l ); ?>
					</a>
				</div>

				<?php if ( ! empty( $tx_trust ) ) : ?>
					<div class="text-neutral-600 text-sm items-center box-border gap-x-6 flex justify-start leading-5 break-words gap-y-6 flex-wrap">
						<?php foreach ( $tx_trust as $sp_i => $sp_t ) :
							$sp_label = isset( $sp_t['label'] ) ? (string) $sp_t['label'] : '';
							$sp_icon  = isset( $sp_t['icon'] ) ? (string) $sp_t['icon'] : 'check';
							if ( '' === $sp_label ) { continue; }
							?>
							<?php if ( $sp_i > 0 ) : ?>
								<div class="bg-gray-300 box-border h-4 break-words w-px" aria-hidden="true"></div>
							<?php endif; ?>
							<div class="items-center box-border gap-x-2 flex break-words gap-y-2">
								<?php echo sp_icon( $sp_icon, 'text-teal-500 w-5 h-5' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<span class="font-medium box-border block break-words"><?php echo esc_html( $sp_label ); ?></span>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<!-- RIGHT: image + stats -->
			<div class="relative box-border break-words">
				<div class="relative box-border break-words rounded-3xl overflow-hidden shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.25)_0px_25px_50px_-12px]">
					<img src="<?php echo esc_url( $tx_img_url ); ?>" alt="<?php echo esc_attr( $tx_img_alt ); ?>" class="box-border w-full h-auto max-w-full object-cover break-words" />
					<div class="absolute bg-[linear-gradient(to_top,rgba(0,0,0,0.3),rgba(0,0,0,0))] box-border break-words inset-0" aria-hidden="true"></div>
				</div>

				<?php if ( ! empty( $tx_stats ) && is_array( $tx_stats ) ) : ?>
					<div class="absolute box-border gap-x-4 grid grid-cols-[repeat(2,minmax(0px,1fr))] break-words gap-y-4 -bottom-8 left-4 right-4 md:left-8 md:right-8">
						<?php foreach ( $tx_stats as $sp_stat ) :
							$sp_value = isset( $sp_stat['value'] ) ? (string) $sp_stat['value'] : '';
							$sp_label = isset( $sp_stat['label'] ) ? (string) $sp_stat['label'] : '';
							if ( '' === $sp_value ) { continue; }
							?>
							<div class="backdrop-blur-xl bg-white/95 shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.25)_0px_25px_50px_-12px] box-border break-words border border-gray-100 p-6 rounded-2xl border-solid">
								<div class="text-transparent text-4xl font-black bg-clip-text bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))] box-border leading-10 break-words mb-2"><?php echo esc_html( $sp_value ); ?></div>
								<div class="text-sm font-bold box-border tracking-[0.35px] leading-5 break-words uppercase text-neutral-600"><?php echo esc_html( $sp_label ); ?></div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

		</div>
	</div>
</section>
