<?php
/**
 * Homepage — Shop Bestsellers.
 *
 * Stage 2d ports this as a STATIC product grid driven by an ACF repeater.
 * In Stage 4 we swap the repeater for a real WC_Query against WooCommerce
 * bestsellers (either "best selling products" or a curated product
 * category). Keeping the current markup stable means stage 4 only has to
 * replace the data source, not the design.
 *
 * Editable via Theme Settings → Homepage → A8 — Shop Bestsellers.
 *
 * @package SmartPharmacy
 */

if ( ! (bool) sp_field( 'bs_enabled', 1 ) ) {
	return;
}

$bs_badge  = sp_field( 'bs_badge_label', 'Shop Bestsellers' );
$bs_h_pre  = sp_field( 'bs_heading_pre', 'Top Pharmacy' );
$bs_h_hi   = sp_field( 'bs_heading_highlight', 'Essentials' );
$bs_sub    = sp_field( 'bs_subheading', 'Fast delivery on everyday health essentials. Trusted brands, unbeatable prices.' );
$bs_cta_l  = sp_field( 'bs_bottom_cta_label', 'View All Products' );
$bs_cta_u  = sp_field( 'bs_bottom_cta_url', '/shop/' );

$bs_products = sp_field( 'bs_products', array() );
if ( empty( $bs_products ) || ! is_array( $bs_products ) ) {
	$bs_products = array(
		array(
			'image' => array( 'url' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=400&h=400&fit=crop', 'alt' => 'Paracetamol tablets' ),
			'bg' => 'teal', 'category' => 'Pain Relief', 'category_colour' => 'teal',
			'name' => 'Paracetamol 500mg', 'description' => 'Fast-acting pain relief tablets',
			'rating' => 5, 'reviews' => 2847, 'price' => '£2.99',
			'badge_text' => 'Bestseller', 'badge_colour' => 'red',
			'cta_label' => 'Add to Cart', 'cta_url' => '/shop/',
		),
		array(
			'image' => array( 'url' => 'https://images.unsplash.com/photo-1607619056574-7b8d3ee536b2?w=400&h=400&fit=crop', 'alt' => 'Vitamin D3 supplements' ),
			'bg' => 'purple', 'category' => 'Vitamins', 'category_colour' => 'purple',
			'name' => 'Vitamin D3 1000IU', 'description' => 'Essential daily supplement',
			'rating' => 5, 'reviews' => 2847, 'price' => '£2.99',
			'badge_text' => '40% OFF', 'badge_colour' => 'orange',
			'cta_label' => 'Add to Cart', 'cta_url' => '/shop/',
		),
		array(
			'image' => array( 'url' => 'https://images.unsplash.com/photo-1603398938378-e54eab446dde?w=500&h=500&fit=crop', 'alt' => 'First aid bandages and plasters' ),
			'bg' => 'green', 'category' => 'First Aid', 'category_colour' => 'green',
			'name' => 'First Aid Kit', 'description' => 'Complete emergency kit',
			'rating' => 4, 'reviews' => 1456, 'price' => '£12.99',
			'badge_text' => 'In Stock', 'badge_colour' => 'green',
			'cta_label' => 'Add to Cart', 'cta_url' => '/shop/',
		),
	);
}

// Colour maps — Tailwind classes must appear as literal strings for JIT
// to pick them up; we look them up here by key.
$sp_bs_bg_class = array(
	'teal'   => 'bg-gradient-to-br from-teal-50 to-white',
	'purple' => 'bg-gradient-to-br from-purple-50 to-white',
	'green'  => 'bg-gradient-to-br from-green-50 to-white',
	'orange' => 'bg-gradient-to-br from-orange-50 to-white',
	'red'    => 'bg-gradient-to-br from-red-50 to-white',
	'blue'   => 'bg-gradient-to-br from-blue-50 to-white',
);
$sp_bs_cat_class = array(
	'teal'   => 'text-teal-600',
	'purple' => 'text-purple-600',
	'green'  => 'text-green-600',
	'orange' => 'text-orange-600',
	'red'    => 'text-red-600',
	'blue'   => 'text-blue-600',
);
$sp_bs_badge_class = array(
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
			<div class="items-center backdrop-blur-sm bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border gap-x-3 inline-flex break-words gap-y-3 border border-gray-100 mb-8 px-6 py-3 rounded-full border-solid">
				<div class="items-center bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] box-border flex h-10 justify-center break-words w-10 rounded-full">
					<?php echo sp_icon( 'truck', 'w-5 h-5 text-white' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<span class="text-neutral-900 text-base font-bold box-border block leading-6 break-words"><?php echo esc_html( $bs_badge ); ?></span>
				<div class="bg-teal-500 box-border h-2 break-words w-2 rounded-full"></div>
			</div>

			<h2 class="ultra-heading text-neutral-900 text-4xl font-black box-border leading-[1.1] break-words mb-4 md:text-6xl">
				<?php echo esc_html( $bs_h_pre ); ?>
				<?php if ( $bs_h_hi ) : ?>
					<span class="relative inline-block">
						<span class="text-transparent bg-clip-text bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))]"><?php echo esc_html( $bs_h_hi ); ?></span>
					</span>
				<?php endif; ?>
			</h2>

			<p class="text-neutral-600 text-lg box-border leading-[1.6] break-words mb-10 md:text-xl md:leading-[1.6] font-medium"><?php echo esc_html( $bs_sub ); ?></p>

			<div class="items-center box-border gap-x-6 flex justify-center break-words gap-y-6 mt-8" aria-hidden="true">
				<div class="bg-[linear-gradient(to_right,rgba(59,155,159,0),rgba(59,155,159,0.6))] box-border h-[2px] break-words w-24"></div>
				<div class="relative items-center bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] box-border flex h-4 justify-center break-words w-4 rounded-full">
					<div class="bg-white box-border h-2 break-words w-2 rounded-full"></div>
				</div>
				<div class="bg-[linear-gradient(to_left,rgba(59,155,159,0),rgba(59,155,159,0.6))] box-border h-[2px] break-words w-24"></div>
			</div>
		</div>

		<!-- Product grid -->
		<div class="box-border gap-x-6 grid grid-cols-[repeat(1,minmax(0px,1fr))] break-words gap-y-6 mb-12 md:grid-cols-[repeat(2,minmax(0px,1fr))] lg:grid-cols-[repeat(3,minmax(0px,1fr))]">
			<?php foreach ( $bs_products as $sp_p ) :
				$bg_class     = $sp_bs_bg_class[ $sp_p['bg'] ?? 'teal' ] ?? $sp_bs_bg_class['teal'];
				$cat_class    = $sp_bs_cat_class[ $sp_p['category_colour'] ?? 'teal' ] ?? $sp_bs_cat_class['teal'];
				$badge_class  = $sp_bs_badge_class[ $sp_p['badge_colour'] ?? 'red' ] ?? $sp_bs_badge_class['red'];
				$img_url      = is_array( $sp_p['image'] ?? null ) && ! empty( $sp_p['image']['url'] ) ? $sp_p['image']['url'] : '';
				$img_alt      = is_array( $sp_p['image'] ?? null ) && ! empty( $sp_p['image']['alt'] ) ? $sp_p['image']['alt'] : ( $sp_p['name'] ?? '' );
				$rating       = (int) ( $sp_p['rating'] ?? 5 );
				$reviews      = (int) ( $sp_p['reviews'] ?? 0 );
				?>
				<div class="product-card relative bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.05)_0px_1px_2px_0px] box-border flex flex-col break-words border border-gray-200 overflow-hidden rounded-2xl border-solid hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_20px_25px_-5px,rgba(0,0,0,0.1)_0px_8px_10px_-6px] hover:border-teal-500/30 group transition-all duration-300">

					<?php if ( ! empty( $sp_p['badge_text'] ) ) : ?>
						<div class="absolute box-border break-words z-10 left-4 top-4">
							<span class="text-white text-xs font-bold <?php echo esc_attr( $badge_class ); ?> shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border inline-block leading-4 break-words px-3 py-1.5 rounded-full uppercase tracking-wider"><?php echo esc_html( $sp_p['badge_text'] ); ?></span>
						</div>
					<?php endif; ?>

					<div class="relative <?php echo esc_attr( $bg_class ); ?> box-border flex items-center justify-center break-words h-64 p-8 overflow-hidden">
						<?php if ( $img_url ) : ?>
							<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
						<?php endif; ?>
					</div>

					<div class="box-border break-words p-6">
						<?php if ( ! empty( $sp_p['category'] ) ) : ?>
							<p class="<?php echo esc_attr( $cat_class ); ?> text-xs font-bold box-border tracking-[0.6px] leading-4 break-words uppercase mb-2"><?php echo esc_html( $sp_p['category'] ); ?></p>
						<?php endif; ?>
						<h3 class="ultra-heading text-neutral-900 text-xl font-black box-border leading-[1.3] break-words mb-2"><?php echo esc_html( $sp_p['name'] ?? '' ); ?></h3>
						<?php if ( ! empty( $sp_p['description'] ) ) : ?>
							<p class="text-neutral-600 text-sm box-border leading-[1.5] break-words mb-4"><?php echo esc_html( $sp_p['description'] ); ?></p>
						<?php endif; ?>

						<div class="flex items-center gap-1 mb-4" aria-hidden="true">
							<?php for ( $i = 0; $i < 5; $i++ ) {
								$colour_class = $i < $rating ? 'text-yellow-400' : 'text-gray-300';
								echo sp_icon( 'star', 'w-4 h-4 ' . $colour_class ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							} ?>
							<?php if ( $reviews ) : ?>
								<span class="text-neutral-600 text-sm ml-2">(<?php echo esc_html( number_format_i18n( $reviews ) ); ?> reviews)</span>
							<?php endif; ?>
						</div>

						<div class="flex items-center justify-between">
							<div>
								<?php if ( ! empty( $sp_p['price'] ) ) : ?>
									<span class="text-neutral-900 text-2xl font-black antialiased"><?php echo esc_html( $sp_p['price'] ); ?></span>
								<?php endif; ?>
							</div>
							<a href="<?php echo esc_url( $sp_p['cta_url'] ?? '#' ); ?>" class="text-white text-sm font-semibold bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] px-6 py-3 rounded-full hover:shadow-lg transition-all inline-block"><?php echo esc_html( $sp_p['cta_label'] ?? 'Add to Cart' ); ?></a>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="box-border break-words text-center">
			<a href="<?php echo esc_url( $bs_cta_u ); ?>" class="text-teal-500 text-lg font-bold shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.05)_0px_1px_2px_0px] box-border inline-block leading-7 break-words border-teal-500 px-10 py-5 rounded-full border-2 border-solid hover:text-white hover:bg-teal-500 hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] transition-all duration-300"><?php echo esc_html( $bs_cta_l ); ?></a>
		</div>
	</div>
</section>
