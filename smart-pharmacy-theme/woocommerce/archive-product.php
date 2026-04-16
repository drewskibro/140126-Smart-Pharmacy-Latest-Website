<?php
/**
 * Shop archive (Stage 4a-2 brand override).
 *
 * Drives /shop/ and /product-category/{slug}/.  Replaces WC's default
 * archive-product.php with a brand layout: gradient page header on
 * top, 3-col card grid in the middle, pagination at the bottom.
 *
 * Hooks preserved (intentional):
 *   - woocommerce_before_main_content  → sp_wc_wrapper_open()
 *   - woocommerce_archive_description  → category descriptions, etc.
 *   - woocommerce_before_shop_loop     → notices (priority 10)
 *   - woocommerce_after_shop_loop      → pagination (priority 10)
 *   - woocommerce_no_products_found    → empty-state template
 *   - woocommerce_after_main_content   → sp_wc_wrapper_close()
 *
 * Hooks intentionally NOT re-added here:
 *   - woocommerce_result_count   (Stage 4a-1 stripped it; Stage 4b
 *                                  will re-add a brand-styled version)
 *   - woocommerce_catalog_ordering (same — sort dropdown lands in 4b)
 *
 * The ul.products / li.product wrapper that WC normally emits via
 * woocommerce_product_loop_start/_end is replaced with an explicit
 * Tailwind <div> grid here; that lets us match the rest of the site
 * (3-col responsive grid identical to A8/E1) instead of fighting WC's
 * .products flex CSS.
 *
 * @package SmartPharmacy
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked sp_wc_wrapper_open - 10  (inc/woocommerce.php)
 */
do_action( 'woocommerce_before_main_content' );

/* ---------------------------------------------------------------
 * Page header — eyebrow pill + gradient h1 + subheading
 * Same visual rhythm as Stage 3 section headers (B2/C1/etc).
 * Falls back gracefully when on the main shop page (no specific
 * category / search / tag context).
 * ------------------------------------------------------------- */
$sp_archive_title = woocommerce_page_title( false );
$sp_archive_desc  = '';

if ( is_product_category() ) {
	$sp_term         = get_queried_object();
	$sp_archive_desc = $sp_term && ! empty( $sp_term->description )
		? wp_strip_all_tags( $sp_term->description )
		: '';
} elseif ( is_shop() ) {
	$sp_archive_desc = __( 'Browse our full pharmacy range. Genuine UK-licensed products, dispatched discreetly with free delivery on orders over £30.', 'smart-pharmacy' );
}
?>
<header class="box-border break-words text-center mb-12 md:mb-16">
	<div class="items-center backdrop-blur-sm bg-white/80 shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border gap-x-3 inline-flex break-words gap-y-3 border border-gray-100 mb-6 px-6 py-3 rounded-full border-solid">
		<div class="items-center bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] box-border flex h-10 justify-center break-words w-10 rounded-full">
			<?php echo sp_icon( 'truck', 'w-5 h-5 text-white' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<span class="text-neutral-900 text-base font-bold box-border block leading-6 break-words">
			<?php esc_html_e( 'Smart Pharmacy', 'smart-pharmacy' ); ?>
			<span class="text-teal-500"> <?php esc_html_e( 'Shop', 'smart-pharmacy' ); ?></span>
		</span>
		<div class="bg-teal-500 box-border h-2 break-words w-2 rounded-full"></div>
	</div>

	<h1 class="text-neutral-900 text-4xl font-black box-border leading-[1.1] break-words mb-4 md:text-6xl">
		<span class="text-transparent bg-clip-text bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))]"><?php echo esc_html( $sp_archive_title ); ?></span>
	</h1>

	<?php if ( $sp_archive_desc ) : ?>
		<p class="text-neutral-600 text-lg box-border leading-[1.6] break-words max-w-3xl mx-auto md:text-xl"><?php echo esc_html( $sp_archive_desc ); ?></p>
	<?php endif; ?>

	<?php
	/**
	 * Hook: woocommerce_archive_description.
	 *
	 * Category archive description (the long-form one set on the term
	 * editor).  Already-styled HTML — let WC render it inside our header.
	 *
	 * @hooked woocommerce_taxonomy_archive_description - 10
	 * @hooked woocommerce_product_archive_description  - 10
	 */
	if ( is_product_category() && ! is_paged() ) {
		echo '<div class="prose mx-auto mt-6 text-neutral-600">';
		do_action( 'woocommerce_archive_description' );
		echo '</div>';
	}
	?>
</header>

<?php
/**
 * Hook: woocommerce_before_shop_loop.
 *
 * @hooked woocommerce_output_all_notices - 10  (kept)
 * @hooked woocommerce_result_count       - 20  (removed in Stage 4a-1)
 * @hooked woocommerce_catalog_ordering   - 30  (removed in Stage 4a-1)
 */
do_action( 'woocommerce_before_shop_loop' );

if ( woocommerce_product_loop() ) :
	?>
	<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
		<?php
		while ( have_posts() ) :
			the_post();
			/**
			 * Each iteration loads woocommerce/content-product.php
			 * (our brand override at the same path).
			 */
			wc_get_template_part( 'content', 'product' );
		endwhile;
		?>
	</div>
	<?php
	/**
	 * Hook: woocommerce_after_shop_loop.
	 *
	 * @hooked woocommerce_pagination - 10  (kept; default WC pagination
	 *                                       markup, restyled in 4b)
	 */
	do_action( 'woocommerce_after_shop_loop' );
else :
	/**
	 * Hook: woocommerce_no_products_found.
	 *
	 * @hooked wc_no_products_found - 10  (renders woocommerce/loop/
	 *                                     no-products-found.php — our
	 *                                     branded empty state)
	 */
	do_action( 'woocommerce_no_products_found' );
endif;

/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked sp_wc_wrapper_close - 10  (inc/woocommerce.php)
 */
do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );
