<?php
/**
 * Empty state shown when a shop archive query returns no products.
 *
 * Stage 4a-2 brand override.  WC's default is a flat `<p>` notice;
 * ours is a centred card with a soft icon, headline, helper copy,
 * and a CTA back to the main shop.  Same visual language as the
 * "Need more help?" sidebar in D1 FAQ.
 *
 * @package SmartPharmacy
 */

defined( 'ABSPATH' ) || exit;

$sp_shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
?>

<div class="box-border break-words text-center mx-auto max-w-2xl py-16">
	<div class="relative box-border inline-block break-words mb-8">
		<div class="absolute bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] box-border blur-xl opacity-20 break-words rounded-3xl inset-0" aria-hidden="true"></div>
		<div class="relative items-center bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_20px_25px_-5px,rgba(0,0,0,0.1)_0px_8px_10px_-6px] box-border flex h-20 justify-center break-words w-20 rounded-3xl mx-auto">
			<?php echo sp_icon( 'eye', 'w-10 h-10 text-white' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>

	<h2 class="text-neutral-900 text-3xl font-black box-border leading-[1.2] break-words mb-4 md:text-4xl">
		<?php esc_html_e( 'No products found', 'smart-pharmacy' ); ?>
	</h2>

	<p class="text-neutral-600 text-lg box-border leading-[1.6] break-words mb-8">
		<?php esc_html_e( 'We couldn\'t find any products matching that view. Try a different category, or browse our full pharmacy range.', 'smart-pharmacy' ); ?>
	</p>

	<a href="<?php echo esc_url( $sp_shop_url ); ?>" class="text-white text-base font-bold items-center bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border gap-x-2 inline-flex leading-6 break-words gap-y-2 px-8 py-4 rounded-full hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.25)_0px_25px_50px_-12px] transition-all duration-300">
		<?php esc_html_e( 'Browse all products', 'smart-pharmacy' ); ?>
		<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
	</a>
</div>
