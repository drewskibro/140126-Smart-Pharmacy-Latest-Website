<?php
/**
 * Off-canvas mobile menu panel.
 *
 * Toggled by #sp-mobile-menu-toggle in the site header. The open/close
 * behaviour lives in assets/js/main.js.
 *
 * @package SmartPharmacy
 */

$sp_mm_primary = sp_field( 'nav_primary', array() );
if ( empty( $sp_mm_primary ) || ! is_array( $sp_mm_primary ) ) {
	$sp_mm_primary = array(
		array( 'label' => 'Weight Loss', 'url' => '/treatments/weight-loss/' ),
		array( 'label' => "Men's Health", 'url' => '/treatments/mens-health/' ),
		array( 'label' => "Women's Health", 'url' => '/treatments/womens-health/' ),
		array( 'label' => 'Shop', 'url' => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : '/shop/' ),
		array( 'label' => 'About', 'url' => '/about/' ),
	);
}

$sp_mm_nhs_enabled = (bool) sp_field( 'nav_nhs_enabled', 1 );
$sp_mm_nhs_label   = sp_field( 'nav_nhs_label', 'NHS Prescriptions' );
$sp_mm_nhs_url     = sp_field( 'nav_nhs_url', '/nhs-prescriptions/' );
$sp_mm_placeholder = sp_field( 'nav_search_placeholder', 'Search medicines...' );
?>

<!-- Mobile menu backdrop -->
<div id="sp-mobile-menu-backdrop" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[90] hidden md:hidden" aria-hidden="true"></div>

<!-- Mobile menu panel -->
<div id="sp-mobile-menu" class="fixed top-0 right-0 bottom-0 w-[85%] max-w-[360px] bg-white shadow-2xl z-[95] translate-x-full transition-transform duration-300 md:hidden flex flex-col" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Site menu', 'smart-pharmacy' ); ?>" aria-hidden="true">

	<!-- Close button -->
	<div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
		<span class="text-neutral-900 text-lg font-bold tracking-[-0.02em]"><?php esc_html_e( 'Menu', 'smart-pharmacy' ); ?></span>
		<button type="button" id="sp-mobile-menu-close" aria-label="<?php esc_attr_e( 'Close menu', 'smart-pharmacy' ); ?>" class="bg-transparent p-2 rounded-xl hover:bg-gray-100 transition-all duration-200">
			<svg class="w-6 h-6 text-neutral-700" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
				<path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
		</button>
	</div>

	<!-- Search -->
	<div class="px-6 py-4 border-b border-gray-100">
		<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<div class="relative flex items-center">
				<button type="submit" aria-label="<?php esc_attr_e( 'Search', 'smart-pharmacy' ); ?>" class="absolute text-neutral-400 bg-transparent p-0 left-4 hover:text-neutral-900 transition-colors duration-200">
					<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</button>
				<input type="search" name="s" placeholder="<?php echo esc_attr( $sp_mm_placeholder ); ?>" class="text-[14px] font-medium bg-gray-50 block h-12 leading-5 w-full border border-gray-200/60 pl-11 pr-4 rounded-xl focus:border-teal-500 focus:bg-white focus:ring-2 focus:ring-teal-500/20 transition-all duration-200" />
			</div>
		</form>
	</div>

	<!-- Menu items -->
	<nav class="flex-1 overflow-y-auto px-4 py-4" aria-label="<?php esc_attr_e( 'Mobile primary', 'smart-pharmacy' ); ?>">
		<ul class="list-none pl-0 space-y-1">
			<?php foreach ( $sp_mm_primary as $sp_item ) :
				$sp_label = isset( $sp_item['label'] ) ? (string) $sp_item['label'] : '';
				$sp_url   = isset( $sp_item['url'] ) ? (string) $sp_item['url'] : '#';
				?>
				<li>
					<a href="<?php echo esc_url( $sp_url ); ?>" class="text-neutral-800 text-[15px] font-semibold flex items-center justify-between px-4 py-3 rounded-xl hover:text-teal-600 hover:bg-teal-50/50 transition-all duration-200">
						<span><?php echo esc_html( $sp_label ); ?></span>
						<svg class="w-4 h-4 text-neutral-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
							<path d="M9 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>

	<!-- NHS button footer -->
	<?php if ( $sp_mm_nhs_enabled ) : ?>
		<div class="px-6 py-4 border-t border-gray-100">
			<a href="<?php echo esc_url( $sp_mm_nhs_url ); ?>" class="w-full flex items-center justify-center gap-2.5 bg-[#005EB8] text-white text-[14px] font-bold tracking-[-0.01em] px-6 py-3 rounded-xl hover:bg-[#003D7A] transition-all duration-300">
				<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				<span><?php echo esc_html( $sp_mm_nhs_label ); ?></span>
			</a>
		</div>
	<?php endif; ?>
</div>
