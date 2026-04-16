<?php
/**
 * Single Treatment — Related Products (Stage 3c stub).
 *
 * Companion product grid shown at the bottom of a treatment page --
 * other treatments or supplements the patient may also be interested
 * in. Mirrors the visual pattern of A8 Shop Bestsellers (homepage) so
 * Stage 4 can share its eventual WC_Query data loader between both
 * sections.
 *
 * STAGE 3c NOTE: data comes from an ACF repeater for now. Stage 4
 * swaps the source for a real WC_Query -- either honouring an editor-
 * picked ACF Relationship to WC_Product IDs, or auto-deriving related
 * products from the treatment_category taxonomy. Editors still get to
 * override per-treatment when they want to.
 *
 * Because the section has no meaningful default content (related
 * products are treatment-specific), this template returns early when
 * the repeater is empty rather than render placeholder cards. The
 * section only appears once an editor has populated it.
 *
 * Editable via the Treatment post edit screen → E1 — Treatment Related.
 *
 * @package SmartPharmacy
 */

if ( ! (bool) sp_field( 'tx_rel_enabled', 1 ) ) {
	return;
}

$tx_products = sp_field( 'tx_rel_items', array() );
if ( empty( $tx_products ) || ! is_array( $tx_products ) ) {
	return;
}

$tx_badge_pre = sp_field( 'tx_rel_badge_pre', 'Recommended' );
$tx_badge_hi  = sp_field( 'tx_rel_badge_highlight', 'For You' );
$tx_h_pre     = sp_field( 'tx_rel_heading_pre', 'You May Also' );
$tx_h_hi      = sp_field( 'tx_rel_heading_highlight', 'Like' );
$tx_sub       = sp_field( 'tx_rel_subheading', 'Other treatments and products that may support your journey' );
$tx_cta_l     = sp_field( 'tx_rel_cta_label', 'View All Treatments' );
$tx_cta_u     = sp_field( 'tx_rel_cta_url', '/treatments/' );

// Colour maps — Tailwind classes must appear as literal strings for
// JIT to pick them up; we look them up here by key. Shape deliberately
// matches A8 Shop Bestsellers (front-page/bestsellers.php:58-82) so
// Stage 4 can share the WC-integration code.
$sp_rel_bg_class = array(
	'teal'   => 'bg-gradient-to-br from-teal-50 to-white',
	'purple' => 'bg-gradient-to-br from-purple-50 to-white',
	'green'  => 'bg-gradient-to-br from-green-50 to-white',
	'orange' => 'bg-gradient-to-br from-orange-50 to-white',
	'red'    => 'bg-gradient-to-br from-red-50 to-white',
	'blue'   => 'bg-gradient-to-br from-blue-50 to-white',
);
$sp_rel_cat_class = array(
	'teal'   => 'text-teal-600',
	'purple' => 'text-purple-600',
	'green'  => 'text-green-600',
	'orange' => 'text-orange-600',
	'red'    => 'text-red-600',
	'blue'   => 'text-blue-600',
);
$sp_rel_badge_class = array(
	'teal'   => 'bg-teal-500',
	'purple' => 'bg-purple-500',
	'green'  => 'bg-green-500',
	'orange' => 'bg-orange-500',
	'red'    => 'bg-red-500',
	'blue'   => 'bg-blue-500',
);
?>

<section class="relative bg-white box-border break-words overflow-hidden py-12 md:py-16">
	<div class="relative box-border max-w-[1400px] break-words z-10 mx-auto px-6 md:px-16">

		<div class="box-border break-words text-center mb-16">
			<?php if ( $tx_badge_pre || $tx_badge_hi ) : ?>
				<div class="items-center backdrop-blur-sm bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border gap-x-3 inline-flex break-words gap-y-3 border border-gray-100 mb-8 px-6 py-3 rounded-full border-solid">
					<div class="items-center bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] box-border flex h-10 justify-center break-words w-10 rounded-full">
						<?php echo sp_icon( 'sparkle', 'w-5 h-5 text-white' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<span class="text-neutral-900 text-base font-bold box-border block leading-6 break-words">
						<?php echo esc_html( $tx_badge_pre ); ?>
						<?php if ( $tx_badge_hi ) : ?><span class="text-teal-500"> <?php echo esc_html( $tx_badge_hi ); ?></span><?php endif; ?>
					</span>
					<div class="bg-teal-500 box-border h-2 break-words w-2 rounded-full"></div>
				</div>
			<?php endif; ?>

			<h2 class="text-neutral-900 text-4xl font-black box-border leading-[1.1] break-words mb-4 md:text-6xl">
				<?php echo esc_html( $tx_h_pre ); ?>
				<?php if ( $tx_h_hi ) : ?>
					<span class="relative inline-block">
						<span class="text-transparent bg-clip-text bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))]"> <?php echo esc_html( $tx_h_hi ); ?></span>
					</span>
				<?php endif; ?>
			</h2>

			<?php if ( $tx_sub ) : ?>
				<p class="text-neutral-600 text-lg box-border leading-[1.6] break-words mb-10 md:text-xl md:leading-[1.6] font-medium"><?php echo esc_html( $tx_sub ); ?></p>
			<?php endif; ?>

			<div class="items-center box-border gap-x-6 flex justify-center break-words gap-y-6 mt-8" aria-hidden="true">
				<div class="bg-[linear-gradient(to_right,rgba(59,155,159,0),rgba(59,155,159,0.6))] box-border h-[2px] break-words w-24"></div>
				<div class="relative items-center bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] box-border flex h-4 justify-center break-words w-4 rounded-full">
					<div class="bg-white box-border h-2 break-words w-2 rounded-full" aria-hidden="true"></div>
				</div>
				<div class="bg-[linear-gradient(to_left,rgba(59,155,159,0),rgba(59,155,159,0.6))] box-border h-[2px] break-words w-24"></div>
			</div>
		</div>

		<!-- Product grid -->
		<div class="box-border gap-x-6 grid grid-cols-[repeat(1,minmax(0px,1fr))] break-words gap-y-6 mb-12 md:grid-cols-[repeat(2,minmax(0px,1fr))] lg:grid-cols-[repeat(3,minmax(0px,1fr))]">
			<?php foreach ( $tx_products as $sp_p ) :
				$sp_bg_class    = $sp_rel_bg_class[ $sp_p['bg'] ?? 'teal' ] ?? $sp_rel_bg_class['teal'];
				$sp_cat_class   = $sp_rel_cat_class[ $sp_p['category_colour'] ?? 'teal' ] ?? $sp_rel_cat_class['teal'];
				$sp_badge_class = $sp_rel_badge_class[ $sp_p['badge_colour'] ?? 'teal' ] ?? $sp_rel_badge_class['teal'];

				$sp_img        = $sp_p['image'] ?? null;
				$sp_img_id     = ( is_array( $sp_img ) && ! empty( $sp_img['ID'] ) ) ? (int) $sp_img['ID'] : 0;
				$sp_img_url    = ( is_array( $sp_img ) && ! empty( $sp_img['url'] ) ) ? $sp_img['url'] : '';
				$sp_img_alt    = ( is_array( $sp_img ) && ! empty( $sp_img['alt'] ) ) ? $sp_img['alt'] : ( $sp_p['name'] ?? '' );

				$sp_rating  = (int) ( $sp_p['rating'] ?? 0 );
				if ( $sp_rating < 0 ) { $sp_rating = 0; }
				if ( $sp_rating > 5 ) { $sp_rating = 5; }
				$sp_reviews = (int) ( $sp_p['reviews'] ?? 0 );
				?>
				<div class="relative bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.05)_0px_1px_2px_0px] box-border flex flex-col break-words border border-gray-200 overflow-hidden rounded-2xl border-solid hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_20px_25px_-5px,rgba(0,0,0,0.1)_0px_8px_10px_-6px] hover:border-teal-500/30 group transition-all duration-300">

					<?php if ( ! empty( $sp_p['badge_text'] ) ) : ?>
						<div class="absolute box-border break-words z-10 left-4 top-4">
							<span class="text-white text-xs font-bold <?php echo esc_attr( $sp_badge_class ); ?> shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border inline-block leading-4 break-words px-3 py-1.5 rounded-full uppercase tracking-wider"><?php echo esc_html( $sp_p['badge_text'] ); ?></span>
						</div>
					<?php endif; ?>

					<div class="relative <?php echo esc_attr( $sp_bg_class ); ?> box-border flex items-center justify-center break-words h-64 p-8 overflow-hidden">
						<?php
						if ( $sp_img_id ) {
							echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								$sp_img_id,
								'large',
								false,
								array(
									'class'    => 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-110',
									'alt'      => $sp_img_alt,
									'sizes'    => '(min-width: 1024px) 33vw, (min-width: 768px) 50vw, 100vw',
									'loading'  => 'lazy',
									'decoding' => 'async',
								)
							);
						} elseif ( $sp_img_url ) { ?>
							<img src="<?php echo esc_url( $sp_img_url ); ?>" alt="<?php echo esc_attr( $sp_img_alt ); ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" loading="lazy" decoding="async" />
						<?php } ?>
					</div>

					<div class="box-border break-words p-6">
						<?php if ( ! empty( $sp_p['category'] ) ) : ?>
							<p class="<?php echo esc_attr( $sp_cat_class ); ?> text-xs font-bold box-border tracking-[0.6px] leading-4 break-words uppercase mb-2"><?php echo esc_html( $sp_p['category'] ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $sp_p['name'] ) ) : ?>
							<h3 class="text-neutral-900 text-xl font-black box-border leading-[1.3] break-words mb-2"><?php echo esc_html( $sp_p['name'] ); ?></h3>
						<?php endif; ?>
						<?php if ( ! empty( $sp_p['description'] ) ) : ?>
							<p class="text-neutral-600 text-sm box-border leading-[1.5] break-words mb-4"><?php echo esc_html( $sp_p['description'] ); ?></p>
						<?php endif; ?>

						<?php if ( $sp_rating > 0 || $sp_reviews > 0 ) : ?>
							<div class="flex items-center gap-1 mb-4" aria-label="<?php echo esc_attr( sprintf( '%d out of 5 stars', $sp_rating ) ); ?>">
								<?php for ( $sp_i = 0; $sp_i < 5; $sp_i++ ) {
									$sp_star_class = $sp_i < $sp_rating ? 'text-yellow-400' : 'text-gray-300';
									echo sp_icon( 'star', 'w-4 h-4 ' . $sp_star_class ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								} ?>
								<?php if ( $sp_reviews > 0 ) : ?>
									<span class="text-neutral-600 text-sm ml-2">(<?php echo esc_html( number_format_i18n( $sp_reviews ) ); ?> reviews)</span>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<div class="flex items-center justify-between">
							<div>
								<?php if ( ! empty( $sp_p['price'] ) ) : ?>
									<span class="text-neutral-900 text-2xl font-black"><?php echo esc_html( $sp_p['price'] ); ?></span>
								<?php endif; ?>
							</div>
							<?php if ( ! empty( $sp_p['cta_label'] ) ) : ?>
								<a href="<?php echo esc_url( $sp_p['cta_url'] ?? '#' ); ?>" class="text-white text-sm font-semibold bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] px-6 py-3 rounded-full hover:shadow-lg transition-all inline-block"><?php echo esc_html( $sp_p['cta_label'] ); ?></a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<?php if ( $tx_cta_l && $tx_cta_u ) : ?>
			<div class="box-border break-words text-center">
				<a href="<?php echo esc_url( $tx_cta_u ); ?>" class="text-teal-500 text-lg font-bold shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.05)_0px_1px_2px_0px] box-border inline-block leading-7 break-words border-teal-500 px-10 py-5 rounded-full border-2 border-solid hover:text-white hover:bg-teal-500 hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] transition-all duration-300"><?php echo esc_html( $tx_cta_l ); ?></a>
			</div>
		<?php endif; ?>
	</div>
</section>
