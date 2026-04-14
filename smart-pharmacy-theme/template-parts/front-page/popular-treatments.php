<?php
/**
 * Homepage — Popular Treatments carousel.
 *
 * A 5-column (2-column on mobile) grid of treatment cards with image,
 * title, hover CTA, and an animated progress bar at the bottom.
 *
 * Editable via Theme Settings → Homepage → A2 — Popular Treatments.
 *
 * @package SmartPharmacy
 */

if ( ! (bool) sp_field( 'pt_enabled', 1 ) ) {
	return;
}

$sp_pt_prefix  = sp_field( 'pt_eyebrow_prefix', 'Trusted by' );
$sp_pt_count   = (int) sp_field( 'pt_eyebrow_count', 50000 );
$sp_pt_suffix  = sp_field( 'pt_eyebrow_suffix', '+ patients' );
$sp_pt_heading = sp_field( 'pt_heading', 'Our most popular treatments' );

$sp_pt_cards = sp_field( 'pt_cards', array() );
if ( empty( $sp_pt_cards ) || ! is_array( $sp_pt_cards ) ) {
	// Placeholder defaults — use a neutral teal gradient so a missing image
	// doesn't show a broken <img>. Real images come from ACF.
	$sp_pt_cards = array(
		array( 'image' => null, 'title' => 'Weight loss',            'url' => '/treatments/weight-loss/',           'cta' => 'View Treatment' ),
		array( 'image' => null, 'title' => 'Hair loss',              'url' => '/treatments/hair-loss/',             'cta' => 'View Treatment' ),
		array( 'image' => null, 'title' => 'ED',                     'url' => '/treatments/erectile-dysfunction/',  'cta' => 'View Treatment' ),
		array( 'image' => null, 'title' => 'Acne',                   'url' => '/treatments/acne/',                  'cta' => 'View Treatment' ),
		array( 'image' => null, 'title' => 'Premature Ejaculation',  'url' => '/treatments/premature-ejaculation/', 'cta' => 'View Treatment' ),
	);
}

// Alternate the overlay gradient between the two teal shades, matching the
// prototype. Inline styles (not Tailwind classes) because JIT can't see
// dynamically interpolated arbitrary values at build time.
$sp_pt_overlay_colours = array(
	'linear-gradient(to top, rgba(59,155,159,0.9), rgba(0,0,0,0), rgba(0,0,0,0))',
	'linear-gradient(to top, rgba(44,122,126,0.9), rgba(0,0,0,0), rgba(0,0,0,0))',
);
?>

<section class="relative bg-[linear-gradient(to_right_bottom,rgb(253,252,250),rgb(248,246,243),rgb(245,243,240))] box-border break-words w-full overflow-hidden">
	<div class="relative box-border break-words w-full px-6 pb-16 md:px-16 md:pb-20">
		<div class="box-border max-w-[1600px] break-words mx-auto">

			<!-- Eyebrow + heading -->
			<div class="box-border break-words text-center mb-16">
				<div class="relative items-center box-border gap-x-6 flex justify-center break-words gap-y-6 mb-6">
					<div class="bg-[linear-gradient(to_right,rgba(59,155,159,0),rgba(59,155,159,0.8))] box-border h-[3px] break-words w-32" aria-hidden="true"></div>
					<div class="relative box-border break-words px-6 py-2">
						<p class="relative text-teal-500 text-sm font-bold box-border tracking-[2px] leading-5 break-words uppercase z-10 md:text-base md:tracking-[2.4px] md:leading-6">
							<?php echo esc_html( $sp_pt_prefix ); ?>
							<span class="counter" data-target="<?php echo (int) $sp_pt_count; ?>">0</span><?php echo esc_html( $sp_pt_suffix ); ?>
						</p>
					</div>
					<div class="bg-[linear-gradient(to_left,rgba(59,155,159,0),rgba(59,155,159,0.8))] box-border h-[3px] break-words w-32" aria-hidden="true"></div>
				</div>

				<h2 class="ultra-heading text-neutral-900 text-4xl font-black box-border leading-[1.1] break-words mb-4 md:text-6xl"><?php echo esc_html( $sp_pt_heading ); ?></h2>

				<!-- Decorative divider -->
				<div class="items-center box-border gap-x-6 flex justify-center break-words gap-y-6 mt-8" aria-hidden="true">
					<div class="bg-[linear-gradient(to_right,rgba(59,155,159,0),rgba(59,155,159,0.6))] box-border h-[2px] break-words w-24"></div>
					<div class="relative box-border break-words">
						<div class="relative items-center bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] box-border flex h-4 justify-center break-words w-4 rounded-full z-10">
							<div class="bg-white box-border h-2 break-words w-2 rounded-full"></div>
						</div>
					</div>
					<div class="bg-[linear-gradient(to_left,rgba(59,155,159,0),rgba(59,155,159,0.6))] box-border h-[2px] break-words w-24"></div>
				</div>
			</div>

			<!-- Card grid -->
			<div class="box-border gap-x-4 grid grid-cols-[repeat(2,minmax(0px,1fr))] break-words gap-y-4 md:gap-x-6 md:grid-cols-[repeat(5,minmax(0px,1fr))] md:gap-y-6">
				<?php foreach ( $sp_pt_cards as $sp_i => $sp_card ) :
					$sp_title   = isset( $sp_card['title'] ) ? (string) $sp_card['title'] : '';
					$sp_url     = isset( $sp_card['url'] ) ? (string) $sp_card['url'] : '#';
					$sp_cta     = isset( $sp_card['cta'] ) && $sp_card['cta'] !== '' ? (string) $sp_card['cta'] : 'View Treatment';
					$sp_image   = isset( $sp_card['image'] ) ? $sp_card['image'] : null;
					$sp_img_url = ( is_array( $sp_image ) && ! empty( $sp_image['url'] ) ) ? $sp_image['url'] : '';
					$sp_img_alt = ( is_array( $sp_image ) && ! empty( $sp_image['alt'] ) ) ? $sp_image['alt'] : $sp_title;
					$sp_overlay = $sp_pt_overlay_colours[ $sp_i % 2 ];
					?>
					<a href="<?php echo esc_url( $sp_url ); ?>" class="treatment-card-hover relative aspect-[4_/_5] bg-gray-100 box-border block break-words overflow-hidden rounded-3xl group">
						<div class="relative box-border h-full break-words overflow-hidden rounded-3xl">

							<!-- Image -->
							<div class="absolute box-border break-words overflow-hidden inset-0">
								<?php if ( $sp_img_url ) : ?>
									<img src="<?php echo esc_url( $sp_img_url ); ?>" alt="<?php echo esc_attr( $sp_img_alt ); ?>" class="box-border h-full max-w-full object-cover break-words w-full transition-transform duration-500 group-hover:scale-110" />
								<?php else : ?>
									<!-- Placeholder gradient when no image is uploaded -->
									<div class="w-full h-full bg-[linear-gradient(to_bottom_right,rgb(59,155,159),rgb(44,122,126))]" aria-hidden="true"></div>
								<?php endif; ?>
							</div>

							<!-- Tint overlay (inline style because gradient is dynamic) -->
							<div class="absolute box-border opacity-60 break-words inset-0 transition-opacity duration-300 group-hover:opacity-80" style="background-image: <?php echo esc_attr( $sp_overlay ); ?>;" aria-hidden="true"></div>

							<!-- Hover CTA overlay -->
							<div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 flex items-center justify-center" aria-hidden="true">
								<div class="transform scale-90 opacity-0 group-hover:scale-100 group-hover:opacity-100 transition-all duration-500">
									<span class="bg-white text-teal-600 font-black text-lg px-8 py-4 rounded-full shadow-2xl hover:shadow-[0_20px_50px_rgba(0,0,0,0.3)] transition-all duration-300 hover:scale-110 inline-block">
										<?php echo esc_html( $sp_cta ); ?> &rarr;
									</span>
								</div>
							</div>

							<!-- Bottom label + progress bar -->
							<div class="absolute box-border break-words bottom-0 inset-x-0">
								<div class="absolute backdrop-blur-md bg-white/10 box-border break-words border-t border-solid border-white/20 inset-0" aria-hidden="true"></div>
								<div class="relative box-border break-words p-4 md:p-6">
									<h3 class="text-white text-xl font-bold box-border leading-7 break-words origin-[0%_50%] mb-2 md:text-2xl md:leading-8 transition-transform duration-300 group-hover:translate-x-2"><?php echo esc_html( $sp_title ); ?></h3>
									<div class="progress-bar bg-white box-border h-1 break-words w-0 rounded-full transition-all duration-[400ms] group-hover:w-full"></div>
								</div>
							</div>
						</div>
					</a>
				<?php endforeach; ?>
			</div>

			<!-- Bottom decorative divider -->
			<div class="items-center box-border gap-x-6 flex justify-center break-words gap-y-6 mt-12" aria-hidden="true">
				<div class="bg-[linear-gradient(to_right,rgba(59,155,159,0),rgba(59,155,159,0.5))] box-border h-[2px] break-words w-32"></div>
				<div class="relative box-border break-words">
					<div class="relative items-center bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] box-border flex h-4 justify-center break-words w-4 rounded-full z-10">
						<div class="bg-white box-border h-2 break-words w-2 rounded-full"></div>
					</div>
				</div>
				<div class="bg-[linear-gradient(to_left,rgba(59,155,159,0),rgba(59,155,159,0.5))] box-border h-[2px] break-words w-32"></div>
			</div>

		</div>
	</div>
</section>
