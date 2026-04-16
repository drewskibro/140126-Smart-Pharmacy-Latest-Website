<?php
/**
 * Single product — simple-product Add-to-Cart form (Stage 4a-3 brand override).
 *
 * Replaces WC's default add-to-cart/simple.php with a branded qty +
 * button row matching the rest of the site's CTA treatment (gradient
 * teal pill).  Variable / grouped / external products use their own
 * templates and are NOT affected by this file -- a future stage adds
 * branded versions of those.
 *
 * Renders nothing if the product isn't purchasable (out of stock with
 * stock-management on, draft, password-protected, etc.) -- the
 * wc_get_stock_html() call above the form gives the user a clear
 * "Out of stock" message instead.
 *
 * Hooks preserved (intentional for plugin compatibility):
 *   - woocommerce_before_add_to_cart_form
 *   - woocommerce_before_add_to_cart_button
 *   - woocommerce_before_add_to_cart_quantity
 *   - woocommerce_after_add_to_cart_quantity
 *   - woocommerce_after_add_to_cart_button
 *   - woocommerce_after_add_to_cart_form
 *
 * @package SmartPharmacy
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product->is_purchasable() ) {
	return;
}

// Stock html ("In stock" / "Only X left" / "Out of stock"). Styled in
// styles.css under .sp-product-summary .stock.
echo wc_get_stock_html( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
?>

<?php if ( $product->is_in_stock() ) : ?>

	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<form class="cart sp-cart-form mt-6" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>

		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		<div class="flex items-stretch gap-3 flex-col sm:flex-row">

			<?php do_action( 'woocommerce_before_add_to_cart_quantity' ); ?>

			<div class="sp-qty-wrap">
				<?php
				woocommerce_quantity_input(
					array(
						'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
						'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
						'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // phpcs:ignore WordPress.Security.NonceVerification.Missing
					)
				);
				?>
			</div>

			<?php do_action( 'woocommerce_after_add_to_cart_quantity' ); ?>

			<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button text-white text-base font-bold items-center bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] gap-x-2 inline-flex justify-center leading-6 break-words gap-y-2 grow px-8 py-4 rounded-full hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.25)_0px_25px_50px_-12px] transition-all duration-300">
				<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
					<path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				<?php echo esc_html( $product->single_add_to_cart_text() ); ?>
			</button>

		</div>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

	</form>

	<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

<?php endif; ?>
