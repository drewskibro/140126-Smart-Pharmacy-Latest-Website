<?php
/**
 * Site header markup — logo, desktop nav, search, NHS button, account, cart.
 *
 * All editable strings / URLs come from ACF (Theme Settings → Branding /
 * Navigation). Defaults mirror the static prototype so the site renders
 * sanely before a client populates the options.
 *
 * @package SmartPharmacy
 */

// Logo (ACF image array or null). Fall back to the prototype logo URL.
$sp_logo     = function_exists( 'get_field' ) ? get_field( 'brand_logo', 'option' ) : null;
$sp_logo_url = ( is_array( $sp_logo ) && ! empty( $sp_logo['url'] ) )
	? $sp_logo['url']
	: 'https://c.animaapp.com/mhiyf2riFan5kf/assets/new-updated-logo-smart-pharmacy-(1).png';
$sp_logo_alt = sp_field( 'brand_logo_alt', 'Smart Pharmacy' );

// Primary menu: fall back to the prototype's five items if ACF is empty.
$sp_primary_menu = sp_field( 'nav_primary', array() );
if ( empty( $sp_primary_menu ) || ! is_array( $sp_primary_menu ) ) {
	$sp_primary_menu = array(
		array( 'label' => 'Weight Loss', 'url' => '/treatments/weight-loss/', 'has_caret' => false ),
		// Men's / Women's Health point to the treatment archive until
		// the client publishes dedicated landing posts; better than
		// the original '#' which was a dead link.
		array( 'label' => "Men's Health", 'url' => '/treatments/mens-health/', 'has_caret' => false ),
		array( 'label' => "Women's Health", 'url' => '/treatments/womens-health/', 'has_caret' => false ),
		array( 'label' => 'Shop', 'url' => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : '/shop/', 'has_caret' => true ),
		array( 'label' => 'About', 'url' => '/about/', 'has_caret' => false ),
	);
}

$sp_search_placeholder = sp_field( 'nav_search_placeholder', 'Search medicines...' );
$sp_nhs_enabled        = (bool) sp_field( 'nav_nhs_enabled', 1 );
$sp_nhs_label          = sp_field( 'nav_nhs_label', 'NHS Prescriptions' );
$sp_nhs_url            = sp_field( 'nav_nhs_url', '/nhs-prescriptions/' );

// Cart count — safe when WC is not active.
$sp_cart_count = 0;
$sp_cart_url   = '/cart/';
if ( function_exists( 'WC' ) && WC()->cart ) {
	$sp_cart_count = WC()->cart->get_cart_contents_count();
	$sp_cart_url   = wc_get_cart_url();
}
?>
<header class="sticky bg-white/95 backdrop-blur-xl shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.08)_0px_2px_8px_0px] box-border break-words z-50 top-0 border-b border-gray-100/50">
	<div class="box-border max-w-[1600px] break-words mx-auto px-6 lg:px-12">
		<div class="items-center box-border gap-x-8 flex justify-between break-words gap-y-6 py-3">

			<!-- Logo. shrink-0 + w-auto are load-bearing: without them the
			     flex row squeezes the image's width against its fixed
			     height and the logo renders horizontally squashed. -->
			<div class="items-center box-border flex shrink-0 break-words">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( $sp_logo_alt ); ?>" class="box-border block break-words transition-transform duration-300 hover:scale-105">
					<img src="<?php echo esc_url( $sp_logo_url ); ?>" alt="<?php echo esc_attr( $sp_logo_alt ); ?>" class="box-border h-16 w-auto object-contain break-words md:h-20" />
				</a>
			</div>

			<!-- Desktop primary nav -->
			<div class="box-border flex basis-[0%] grow justify-center break-words">
				<nav class="items-center box-border gap-x-2 hidden min-h-0 min-w-0 break-words gap-y-2 md:flex md:min-h-[auto] md:min-w-[auto]" aria-label="<?php esc_attr_e( 'Primary', 'smart-pharmacy' ); ?>">
					<?php foreach ( $sp_primary_menu as $sp_item ) :
						$sp_has_caret = ! empty( $sp_item['has_caret'] );
						$sp_label     = isset( $sp_item['label'] ) ? (string) $sp_item['label'] : '';
						$sp_url       = isset( $sp_item['url'] ) ? (string) $sp_item['url'] : '#';
						?>
						<a href="<?php echo esc_url( $sp_url ); ?>" class="text-neutral-800 text-[15px] font-semibold items-center box-border gap-x-1.5 flex leading-[1.4] tracking-[-0.01em] break-words px-4 py-2.5 rounded-xl hover:text-teal-600 hover:bg-teal-50/50 transition-all duration-200 relative group">
							<?php echo esc_html( $sp_label ); ?>
							<?php if ( $sp_has_caret ) : ?>
								<svg class="w-4 h-4 transition-transform duration-200 group-hover:rotate-180" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
									<path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							<?php endif; ?>
							<span class="absolute bottom-0 left-4 right-4 h-0.5 bg-teal-500 scale-x-0 group-hover:scale-x-100 transition-transform duration-300 rounded-full"></span>
						</a>
					<?php endforeach; ?>
				</nav>
			</div>

			<!-- Right-side actions -->
			<div class="items-center box-border gap-x-3 flex break-words gap-y-3">

				<!-- Mobile hamburger -->
				<button type="button" id="sp-mobile-menu-toggle" aria-label="<?php esc_attr_e( 'Open menu', 'smart-pharmacy' ); ?>" aria-controls="sp-mobile-menu" aria-expanded="false" class="items-center bg-transparent box-border flex h-11 justify-center min-h-[auto] min-w-[auto] break-words w-11 rounded-xl md:hidden hover:bg-gray-100 transition-all duration-200">
					<svg class="w-6 h-6 text-neutral-700" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</button>

				<div class="items-center box-border gap-x-3 flex break-words gap-y-3">

					<!-- Desktop header search -->
					<div class="box-border hidden min-h-0 min-w-0 break-words md:block md:min-h-[auto] md:min-w-[auto]">
						<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="box-border break-words">
							<div class="relative items-center box-border flex break-words">
								<button type="submit" aria-label="<?php esc_attr_e( 'Search', 'smart-pharmacy' ); ?>" class="absolute text-neutral-400 bg-transparent box-border block break-words text-center p-0 left-4 hover:text-neutral-900 transition-colors duration-200">
									<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
										<path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</button>
								<input type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php echo esc_attr( $sp_search_placeholder ); ?>" class="text-[14px] font-medium bg-gray-50/80 box-border block h-11 leading-5 break-words w-[260px] border border-gray-200/60 pl-11 pr-4 py-0 rounded-xl border-solid focus:border-teal-500 focus:bg-white focus:ring-2 focus:ring-teal-500/20 transition-all duration-200" />
							</div>
						</form>
					</div>

					<!-- NHS Prescriptions button -->
					<?php if ( $sp_nhs_enabled ) : ?>
						<a href="<?php echo esc_url( $sp_nhs_url ); ?>" class="hidden md:flex items-center gap-2.5 bg-[#005EB8] text-white text-[14px] font-bold tracking-[-0.01em] px-6 py-3 rounded-xl hover:bg-[#003D7A] transition-all duration-300 shadow-[rgba(0,94,184,0.25)_0px_4px_16px] hover:shadow-[rgba(0,94,184,0.4)_0px_8px_24px] hover:translate-y-[-1px] whitespace-nowrap">
							<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
								<path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
							<span><?php echo esc_html( $sp_nhs_label ); ?></span>
						</a>
					<?php endif; ?>

					<!-- Account -->
					<a href="<?php echo esc_url( function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'dashboard' ) : wp_login_url() ); ?>" aria-label="<?php esc_attr_e( 'My account', 'smart-pharmacy' ); ?>" class="bg-transparent box-border block break-words text-center p-2.5 rounded-xl hover:bg-gray-100 transition-all duration-200">
						<svg class="w-6 h-6 text-neutral-700" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
							<path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</a>

					<!-- Cart -->
					<a href="<?php echo esc_url( $sp_cart_url ); ?>" aria-label="<?php esc_attr_e( 'Cart', 'smart-pharmacy' ); ?>" class="relative box-border block break-words p-2.5 rounded-xl hover:bg-gray-100 transition-all duration-200">
						<svg class="w-6 h-6 text-neutral-700" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
							<path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
						<span class="sp-cart-count absolute text-white text-[11px] font-bold items-center bg-red-500 box-border flex h-5 justify-center leading-4 break-words w-5 rounded-full -right-1 -top-1 shadow-sm"><?php echo (int) $sp_cart_count; ?></span>
					</a>
				</div>
			</div>

		</div>
	</div>
</header>
