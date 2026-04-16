<?php
/**
 * Cart page (Stage 4a-4 brand override).
 *
 * Replaces WC's default cart/cart.php with a two-column brand layout:
 *
 *   - Left (lg:col-span-8)  : line items list -- one card per product,
 *                             image + name + price + qty + remove.
 *   - Right (lg:col-span-4) : sticky summary with subtotal, shipping,
 *                             total, coupon field, and "Proceed to
 *                             checkout" gradient CTA.
 *
 * On mobile everything stacks and the summary sits underneath the items.
 *
 * All WC hooks preserved for plugin compatibility:
 *   - woocommerce_before_cart
 *   - woocommerce_before_cart_table
 *   - woocommerce_before_cart_contents
 *   - woocommerce_cart_contents
 *   - woocommerce_cart_item_class / removed / thumbnail / name /
 *     price / quantity / subtotal / remove_link
 *   - woocommerce_after_cart_contents
 *   - woocommerce_cart_coupon
 *   - woocommerce_cart_actions
 *   - woocommerce_after_cart_table
 *   - woocommerce_cart_collaterals
 *   - woocommerce_after_cart
 *
 * The classic "items table" markup is replaced with a stacked card
 * layout because WC's default <table> is hostile to responsive design
 * and the site has no other table UI to match.  WC's JS (quantity
 * update, remove_from_cart) still binds cleanly because we preserve
 * the class names (cart_item, product-remove, product-quantity, etc.)
 * and the input names (cart[hash][qty]) that the handler looks for.
 *
 * @package SmartPharmacy
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>

<form class="woocommerce-cart-form sp-cart-page" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
	<?php do_action( 'woocommerce_before_cart_table' ); ?>

	<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

		<?php /* ---------- Items list (left, 8 of 12) ---------- */ ?>
		<div class="lg:col-span-8">

			<header class="mb-6">
				<h1 class="text-neutral-900 text-3xl font-black leading-[1.2] md:text-4xl">
					<span class="text-transparent bg-clip-text bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))]"><?php esc_html_e( 'Your basket', 'smart-pharmacy' ); ?></span>
				</h1>
				<p class="text-neutral-600 text-base leading-[1.6] mt-2">
					<?php
					$sp_cart_count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
					/* translators: %d: number of items */
					echo esc_html( sprintf( _n( '%d item ready for checkout.', '%d items ready for checkout.', $sp_cart_count, 'smart-pharmacy' ), $sp_cart_count ) );
					?>
				</p>
			</header>

			<div class="sp-cart-items space-y-4">
				<?php do_action( 'woocommerce_before_cart_contents' ); ?>

				<?php
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
					$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
					$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

					if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 || ! apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
						continue;
					}

					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );

					// Thumbnail -- WC filter lets plugins override the
					// HTML, so we pass through instead of calling
					// get_image() directly.
					$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail' ), $cart_item, $cart_item_key );
					?>
					<article class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item sp-cart-item bg-white border border-neutral-200 rounded-2xl p-4 md:p-6 flex flex-col sm:flex-row gap-4 sm:gap-6', $cart_item, $cart_item_key ) ); ?>">

						<div class="sp-cart-item__thumb w-full sm:w-28 md:w-32 aspect-square bg-neutral-50 rounded-xl overflow-hidden flex items-center justify-center flex-shrink-0">
							<?php
							if ( ! $product_permalink ) {
								echo wp_kses_post( $thumbnail );
							} else {
								printf( '<a href="%s" class="block w-full h-full">%s</a>', esc_url( $product_permalink ), wp_kses_post( $thumbnail ) );
							}
							?>
						</div>

						<div class="sp-cart-item__body flex-1 flex flex-col sm:flex-row gap-4 sm:items-center">

							<div class="flex-1 min-w-0">
								<h3 class="text-neutral-900 text-lg font-bold leading-snug mb-1">
									<?php
									if ( ! $product_permalink ) {
										echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
									} else {
										echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s" class="hover:text-teal-600 transition-colors">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
									}

									// Item data (variations, add-ons).
									wc_cart_item_data( $cart_item, array( 'echo' => true ) );

									// Meta (e.g. backordered).
									echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

									// Stock-status / backorder notice.
									if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
										echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="text-amber-600 text-sm mt-1">' . esc_html__( 'Available on backorder', 'smart-pharmacy' ) . '</p>', $product_id ) );
									}
									?>
								</h3>
								<p class="text-neutral-600 text-sm">
									<?php
									/* translators: %s: product price */
									echo wp_kses_post( apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ) );
									?>
									<span class="text-neutral-400 mx-1">·</span>
									<span class="text-neutral-500"><?php echo esc_html( $_product->get_sku() ); ?></span>
								</p>
							</div>

							<div class="flex items-center gap-4 sm:gap-6">
								<div class="product-quantity sp-qty-wrap">
									<?php
									if ( $_product->is_sold_individually() ) {
										$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
									} else {
										$product_quantity = woocommerce_quantity_input(
											array(
												'input_name'   => "cart[{$cart_item_key}][qty]",
												'input_value'  => $cart_item['quantity'],
												'max_value'    => $_product->get_max_purchase_quantity(),
												'min_value'    => '0',
												'product_name' => $_product->get_name(),
											),
											$_product,
											false
										);
									}
									echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									?>
								</div>

								<div class="product-subtotal text-right min-w-[80px]">
									<span class="text-neutral-900 text-lg font-bold">
										<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</span>
								</div>

								<div class="product-remove">
									<?php
									echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										'woocommerce_cart_item_remove_link',
										sprintf(
											'<a href="%s" class="remove text-neutral-400 hover:text-red-500 transition-colors inline-flex items-center justify-center w-9 h-9 rounded-full hover:bg-red-50" aria-label="%s" data-product_id="%s" data-product_sku="%s"><svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></a>',
											esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
											/* translators: %s: product name */
											esc_attr( sprintf( __( 'Remove %s from cart', 'smart-pharmacy' ), wp_strip_all_tags( $_product->get_name() ) ) ),
											esc_attr( $product_id ),
											esc_attr( $_product->get_sku() )
										),
										$cart_item_key
									);
									?>
								</div>
							</div>

						</div>
					</article>
					<?php
				}
				?>

				<?php do_action( 'woocommerce_cart_contents' ); ?>

				<?php /* Coupon + update-cart actions row. */ ?>
				<div class="sp-cart-actions bg-neutral-50 border border-neutral-200 rounded-2xl p-4 md:p-6 flex flex-col sm:flex-row gap-4 sm:items-center sm:justify-between mt-2">
					<?php if ( wc_coupons_enabled() ) { ?>
						<div class="coupon flex flex-col sm:flex-row gap-2 flex-1 max-w-lg">
							<label for="coupon_code" class="sr-only"><?php esc_html_e( 'Coupon', 'smart-pharmacy' ); ?></label>
							<input type="text" name="coupon_code" class="input-text sp-input flex-1" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Discount code', 'smart-pharmacy' ); ?>" />
							<button type="submit" class="button sp-btn-secondary" name="apply_coupon" value="<?php esc_attr_e( 'Apply', 'smart-pharmacy' ); ?>"><?php esc_html_e( 'Apply', 'smart-pharmacy' ); ?></button>
							<?php do_action( 'woocommerce_cart_coupon' ); ?>
						</div>
					<?php } ?>

					<button type="submit" class="button sp-btn-ghost" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'smart-pharmacy' ); ?>"><?php esc_html_e( 'Update cart', 'smart-pharmacy' ); ?></button>

					<?php do_action( 'woocommerce_cart_actions' ); ?>

					<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
				</div>

				<?php do_action( 'woocommerce_after_cart_contents' ); ?>
			</div>

			<?php do_action( 'woocommerce_after_cart_table' ); ?>
		</div>

		<?php /* ---------- Summary (right, 4 of 12) ---------- */ ?>
		<aside class="lg:col-span-4">
			<div class="cart-collaterals sp-cart-summary lg:sticky lg:top-8">
				<?php
				/**
				 * Renders cart totals + cross-sells + shipping calculator.
				 * WC's default cart-totals.php is template-overridden
				 * below (woocommerce/cart/cart-totals.php) with branded
				 * markup so this hook drops our version straight in.
				 */
				do_action( 'woocommerce_cart_collaterals' );
				?>
			</div>
		</aside>

	</div>
</form>

<?php do_action( 'woocommerce_after_cart' ); ?>
