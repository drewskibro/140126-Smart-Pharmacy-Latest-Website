<?php
/**
 * Consultation → checkout → order handoff.
 *
 * When a consultation is submitted for a product, send the customer to
 * checkout with that product in the basket and the consultation linked,
 * then — once the order is placed (payment authorised by the configured
 * Stripe gateway in manual-capture mode) — drop it into Awaiting
 * Clinical Review via SPE_Consultation_Order::attach().
 *
 * Mirrors the GLP-1 assessment cart flow in
 * SPE_WooCommerce_Integration (empty cart → unlock POM gate → add with
 * hidden meta → stamp onto order), but for the lighter P-med
 * consultation and keyed by consultation_id.
 *
 * IMPORTANT: the exact post-authorisation order status with Stripe
 * manual capture must be verified on staging once Stripe is configured.
 * The pharmacist actions treat awaiting-review / on-hold / pending
 * consultation orders alike, so review still works if the gateway lands
 * the order on-hold first.
 *
 * @package SmartPharmacyEligibility
 */

defined( 'ABSPATH' ) || exit;

/**
 * SPE_Consultation_Checkout.
 */
class SPE_Consultation_Checkout {

	const CART_META  = '_spe_consultation_id';
	const ITEM_META  = '_spe_consultation_id';

	/**
	 * Wire hooks.
	 */
	public static function register() {
		add_filter( 'spe_consultation_redirect', array( __CLASS__, 'redirect_to_checkout' ), 10, 3 );
		add_action( 'woocommerce_checkout_create_order_line_item', array( __CLASS__, 'persist_line_item_meta' ), 10, 4 );
		add_action( 'woocommerce_checkout_order_processed', array( __CLASS__, 'on_order_processed' ), 20, 3 );
	}

	/**
	 * After a consultation is saved, put its product in the basket and
	 * send the customer to checkout. Falls back to the on-page
	 * confirmation (empty $url) if there's no product or no WooCommerce.
	 *
	 * @param string $url
	 * @param string $consultation_id
	 * @param int    $product_id
	 * @return string
	 */
	public static function redirect_to_checkout( $url, $consultation_id, $product_id ) {
		$product_id = (int) $product_id;
		if ( $product_id <= 0 || ! function_exists( 'WC' ) ) {
			return $url;
		}
		if ( ! self::add_to_cart( $product_id, $consultation_id ) ) {
			return $url;
		}
		return function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : $url;
	}

	/**
	 * Empty the cart, unlock the POM gate for this product, and add it
	 * with the consultation id in hidden cart-item meta.
	 *
	 * @param int    $product_id
	 * @param string $consultation_id
	 * @return bool
	 */
	protected static function add_to_cart( $product_id, $consultation_id ) {
		if ( ! function_exists( 'WC' ) ) {
			return false;
		}
		if ( ! WC()->cart && function_exists( 'wc_load_cart' ) ) {
			wc_load_cart();
		}
		if ( ! WC()->cart ) {
			return false;
		}

		WC()->cart->empty_cart();

		// Unlock the theme's POM purchase gate for this product, reusing
		// the engine's existing session flag. Guarded per the hard rule.
		if ( class_exists( 'SPE_WooCommerce_Integration' ) && method_exists( 'SPE_WooCommerce_Integration', 'mark_eligibility_completed' ) ) {
			SPE_WooCommerce_Integration::mark_eligibility_completed( $product_id, $consultation_id );
		}

		$added = WC()->cart->add_to_cart(
			$product_id,
			1,
			0,
			array(),
			array( self::CART_META => sanitize_text_field( $consultation_id ) )
		);

		return (bool) $added;
	}

	/**
	 * Carry the consultation id from the cart item onto the order line
	 * item so it survives onto the order.
	 *
	 * @param WC_Order_Item_Product $item
	 * @param string                $cart_item_key
	 * @param array                 $values
	 * @param WC_Order              $order
	 */
	public static function persist_line_item_meta( $item, $cart_item_key, $values, $order ) {
		if ( ! empty( $values[ self::CART_META ] ) ) {
			$item->add_meta_data( self::ITEM_META, sanitize_text_field( $values[ self::CART_META ] ) );
		}
	}

	/**
	 * On order placement, link the consultation and move the order into
	 * Awaiting Clinical Review.
	 *
	 * @param int      $order_id
	 * @param array    $posted_data
	 * @param WC_Order $order
	 */
	public static function on_order_processed( $order_id, $posted_data, $order ) {
		if ( ! is_a( $order, 'WC_Order' ) && function_exists( 'wc_get_order' ) ) {
			$order = wc_get_order( $order_id );
		}
		if ( ! is_a( $order, 'WC_Order' ) ) {
			return;
		}

		$consultation_id = '';
		foreach ( $order->get_items() as $item ) {
			$maybe = $item->get_meta( self::ITEM_META );
			if ( $maybe ) {
				$consultation_id = $maybe;
				break;
			}
		}
		if ( ! $consultation_id ) {
			return;
		}

		if ( class_exists( 'SPE_Consultation_Order' ) && method_exists( 'SPE_Consultation_Order', 'attach' ) ) {
			SPE_Consultation_Order::attach( $order, $consultation_id );
		}
	}
}
