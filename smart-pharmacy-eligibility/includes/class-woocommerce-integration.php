<?php
/**
 * WooCommerce hooks: product lookup + order-meta stamping.
 *
 * Three responsibilities:
 *   1. Map a (treatment, dose) tuple to a WC product ID using the
 *      admin-configured spe_product_map option, with SKU pattern +
 *      title-search fallbacks so the demo works before an admin has
 *      mapped anything.
 *   2. Add the resolved product to the cart and stash the linked
 *      assessment_id in the cart item meta so it survives onto the
 *      eventual order line item.
 *   3. On checkout, flatten that assessment_id + a JSON copy of the
 *      full payload onto the WC order meta so clinicians can see the
 *      full picture from the order admin without bouncing tabs.
 *
 * @package SmartPharmacyEligibility
 */

defined( 'ABSPATH' ) || exit;

/**
 * SPE_WooCommerce_Integration.
 */
class SPE_WooCommerce_Integration {

	const CART_META_KEY     = '_spe_assessment_id';
	const ORDER_META_RAW    = '_tc_eligibility_raw';
	const ORDER_META_PREFIX = '_tc_elig_';
	const SESSION_KEY       = 'spe_completed_products';

	/**
	 * Wire the hooks.
	 */
	public static function register() {
		// Persist cart-item assessment_id onto order line items.
		add_action( 'woocommerce_checkout_create_order_line_item', array( __CLASS__, 'persist_line_item_meta' ), 10, 4 );

		// On order placement: write the assessment payload onto the
		// order itself + back-link the assessment row to the order.
		add_action( 'woocommerce_checkout_order_processed', array( __CLASS__, 'stamp_order_meta' ), 20, 3 );

		// Make the cart honour our hidden item meta so identical
		// products with different assessments don't merge into one
		// line.
		add_filter( 'woocommerce_add_cart_item_data', array( __CLASS__, 'inject_cart_item_data' ), 10, 3 );
	}

	/**
	 * Look up the WC product to add to cart for (treatment, dose).
	 *
	 * Resolution order:
	 *   1. Exact mapping in spe_product_map (slug "wegovy-0.25mg" => 123)
	 *   2. SKU pattern match (e.g. SP-WL-WEG-0.25mg, SP-WL-MOUN-2.5mg)
	 *   3. Product-name LIKE fallback so the demo works pre-mapping
	 *
	 * Returns 0 when nothing matches.
	 *
	 * @param string $treatment "wegovy" | "mounjaro" (case-insensitive).
	 * @param string $dose      Numeric+unit, e.g. "0.25mg".
	 * @return int Product ID or 0.
	 */
	public static function resolve_product_id( $treatment, $dose ) {
		$treatment = strtolower( sanitize_text_field( $treatment ) );
		$dose      = strtolower( sanitize_text_field( $dose ) );
		if ( '' === $treatment ) {
			return 0;
		}

		$key = $treatment . '-' . $dose;
		$map = (array) spe_option( 'product_map', array() );
		if ( ! empty( $map[ $key ] ) ) {
			$id = (int) $map[ $key ];
			if ( $id > 0 && get_post_type( $id ) === 'product' ) {
				return $id;
			}
		}

		// SKU-pattern fallback. We deliberately don't enforce a
		// strict convention -- two patterns covers the seed catalogue.
		$sku_prefix = 'wegovy' === $treatment ? 'SP-WL-WEG-' : 'SP-WL-MOUN-';
		$sku_guess  = $sku_prefix . $dose;
		if ( function_exists( 'wc_get_product_id_by_sku' ) ) {
			$id = (int) wc_get_product_id_by_sku( $sku_guess );
			if ( $id > 0 ) {
				return $id;
			}
		}

		// Name-LIKE fallback (e.g. "Wegovy 0.25mg" or "Mounjaro 2.5mg").
		$results = get_posts(
			array(
				'post_type'      => 'product',
				'posts_per_page' => 1,
				's'              => $treatment . ' ' . $dose,
				'fields'         => 'ids',
				'post_status'    => 'publish',
			)
		);
		if ( ! empty( $results ) ) {
			return (int) $results[0];
		}

		return 0;
	}

	/**
	 * Add a product to the cart with the assessment_id attached.
	 *
	 * Marks the product as eligibility-completed in the WC session
	 * BEFORE calling add_to_cart so the theme's POM gating (which
	 * normally blocks POM products from being purchased directly)
	 * yields and lets this purchase through.
	 *
	 * @param int    $product_id    WC product ID.
	 * @param string $assessment_id UUID of the assessment.
	 * @return string|bool          Cart item key on success, false otherwise.
	 */
	public static function add_to_cart_with_assessment( $product_id, $assessment_id ) {
		if ( ! function_exists( 'WC' ) ) {
			return false;
		}

		// AJAX context may not have the cart bootstrapped yet --
		// wc_load_cart() handles both cart + session for guests.
		if ( ! WC()->cart && function_exists( 'wc_load_cart' ) ) {
			wc_load_cart();
		}
		if ( ! WC()->cart ) {
			return false;
		}

		// Empty the cart so we don't pile multiple treatments together.
		// POM products are 1-per-cart in normal use.
		WC()->cart->empty_cart();

		// Unlock the POM gate for this product before WC validates it.
		self::mark_eligibility_completed( $product_id, $assessment_id );

		return WC()->cart->add_to_cart(
			(int) $product_id,
			1,
			0,
			array(),
			array( self::CART_META_KEY => sanitize_text_field( $assessment_id ) )
		);
	}

	/**
	 * Record that the customer has completed eligibility for a product.
	 *
	 * Stored on the WC session so the theme's POM gating (or any
	 * other purchase-restriction filter) can consult it and yield.
	 * Session persists across requests for the duration of the
	 * customer's visit.
	 *
	 * @param int    $product_id
	 * @param string $assessment_id UUID for audit linkage.
	 * @return void
	 */
	public static function mark_eligibility_completed( $product_id, $assessment_id ) {
		if ( ! function_exists( 'WC' ) ) {
			return;
		}
		if ( ! WC()->session && function_exists( 'wc_load_cart' ) ) {
			wc_load_cart();
		}
		if ( ! WC()->session ) {
			return;
		}
		// Force a session cookie for guests so the flag survives the
		// redirect from /start-consultation/ to /checkout/.
		if ( ! WC()->session->has_session() ) {
			WC()->session->set_customer_session_cookie( true );
		}
		$completed = (array) WC()->session->get( self::SESSION_KEY, array() );
		$completed[ (int) $product_id ] = sanitize_text_field( $assessment_id );
		WC()->session->set( self::SESSION_KEY, $completed );
	}

	/**
	 * Has the current visitor completed eligibility for this product?
	 *
	 * Theme code (or anyone gating POM purchases) calls this from
	 * its woocommerce_is_purchasable filter to allow eligibility-
	 * completed purchases through.
	 *
	 * @param int $product_id
	 * @return bool
	 */
	public static function has_completed_eligibility( $product_id ) {
		if ( ! function_exists( 'WC' ) || ! WC()->session ) {
			return false;
		}
		$completed = (array) WC()->session->get( self::SESSION_KEY, array() );
		return ! empty( $completed[ (int) $product_id ] );
	}

	/**
	 * Carry our hidden cart-item meta into the canonical cart item
	 * data so WC doesn't strip it.
	 *
	 * @param array $cart_item_data Existing cart item meta.
	 * @param int   $product_id     Product being added.
	 * @param int   $variation_id   Variation ID.
	 * @return array
	 */
	public static function inject_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
		// add_to_cart() may pass cart_item_data via the 5th arg --
		// already populated. Nothing extra to do here, but the filter
		// is registered for future expansion (e.g. coupon-from-checker).
		return $cart_item_data;
	}

	/**
	 * Persist the assessment_id onto the order line item so it shows
	 * in the admin order view + survives any post-order workflows.
	 *
	 * @param WC_Order_Item_Product $item          Order item being created.
	 * @param string                $cart_item_key Cart item key.
	 * @param array                 $values        Cart item values.
	 * @param WC_Order              $order         Order being assembled.
	 */
	public static function persist_line_item_meta( $item, $cart_item_key, $values, $order ) {
		if ( ! empty( $values[ self::CART_META_KEY ] ) ) {
			$item->add_meta_data( __( 'Assessment ID', 'smart-pharmacy-eligibility' ), sanitize_text_field( $values[ self::CART_META_KEY ] ) );
		}
	}

	/**
	 * Stamp the full assessment payload onto the order + link the
	 * assessment row back to the order.
	 *
	 * @param int      $order_id    Order ID.
	 * @param array    $posted_data Posted checkout data (unused).
	 * @param WC_Order $order       Order object.
	 */
	public static function stamp_order_meta( $order_id, $posted_data, $order ) {
		$assessment_id = '';
		foreach ( $order->get_items() as $item ) {
			$maybe = $item->get_meta( __( 'Assessment ID', 'smart-pharmacy-eligibility' ) );
			if ( $maybe ) {
				$assessment_id = $maybe;
				break;
			}
		}

		if ( ! $assessment_id ) {
			return;
		}

		$row = SPE_Assessment_Repo::find_by_assessment_id( $assessment_id );
		if ( ! $row ) {
			return;
		}

		// Flat copy onto the order so admins can query / filter on
		// individual fields without parsing the JSON blob.
		$flat = array(
			'first_name'        => $row->first_name,
			'last_name'         => $row->last_name,
			'email'             => $row->email,
			'phone'             => $row->phone,
			'dob'               => $row->dob,
			'sex'               => $row->sex,
			'ethnicity'         => $row->ethnicity,
			'height_cm'         => $row->height_cm,
			'weight_kg'         => $row->weight_kg,
			'bmi'               => $row->bmi,
			'diabetes'          => $row->diabetes,
			'gp_name'           => $row->gp_name,
			'gp_postcode'       => $row->gp_postcode,
			'selected_treatment'=> $row->selected_treatment,
			'selected_dose'     => $row->selected_dose,
			'assessment_id'     => $row->assessment_id,
		);
		foreach ( $flat as $k => $v ) {
			$order->update_meta_data( self::ORDER_META_PREFIX . $k, $v );
		}

		// Raw payload (full JSON blob) for clinical review.
		if ( ! empty( $row->raw_payload ) ) {
			$order->update_meta_data( self::ORDER_META_RAW, $row->raw_payload );
		}

		$order->save();

		// Back-link: assessment row knows which order completed it.
		SPE_Assessment_Repo::set_order_id( $assessment_id, $order_id );
	}
}
