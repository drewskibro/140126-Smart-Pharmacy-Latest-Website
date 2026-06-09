<?php
/**
 * Treatment-selection cards for screen 21.
 *
 * Pulls live data from WooCommerce instead of hardcoding price /
 * title in the template. Reads the spe_product_map option to find
 * the configured starter-dose product per treatment, then falls
 * back to the same SKU + name search that the WC integration uses.
 *
 * If WC isn't active or no product is configured, returns a
 * sensible default so the screen still renders.
 *
 * @package SmartPharmacyEligibility
 */

defined( 'ABSPATH' ) || exit;

/**
 * SPE_Treatment_Cards.
 */
class SPE_Treatment_Cards {

	/**
	 * Two cards: Wegovy + Mounjaro at their starter doses.
	 *
	 * Returns an array of card structs:
	 *   key:         "wegovy" | "mounjaro"
	 *   dose:        "0.25mg" | "2.5mg"
	 *   title:       WC product title or default
	 *   price_html:  WC formatted price string or fallback "£—"
	 *   tag:         "Popular" / "Advanced"
	 *   tag_style:   inline style for the tag pill
	 *   description: short pitch
	 *
	 * @return array<int, array<string, string>>
	 */
	public static function starter_cards() {
		$starters = array(
			array(
				'key'         => 'wegovy',
				'dose'        => '0.25mg',
				'default_title' => 'Wegovy',
				'tag'         => __( 'Popular', 'smart-pharmacy-eligibility' ),
				'tag_style'   => 'background-color: #e6f7f5; color: #13b8a7;',
				'description' => __( 'Clinically proven semaglutide injection for significant weight loss.', 'smart-pharmacy-eligibility' ),
			),
			array(
				'key'         => 'mounjaro',
				'dose'        => '2.5mg',
				'default_title' => 'Mounjaro',
				'tag'         => __( 'Advanced', 'smart-pharmacy-eligibility' ),
				'tag_style'   => 'background-color: #dbeafe; color: #1e40af;',
				'description' => __( 'Dual-action tirzepatide formula for maximum weight loss results.', 'smart-pharmacy-eligibility' ),
			),
		);

		$out = array();
		foreach ( $starters as $s ) {
			$out[] = self::resolve_card( $s );
		}
		return $out;
	}

	/**
	 * Resolve a single card by looking up the matching WC product.
	 *
	 * @param array $seed Card seed with key / dose / default_title / tag / etc.
	 * @return array Full card struct ready for the template.
	 */
	protected static function resolve_card( array $seed ) {
		$title      = $seed['default_title'];
		$price_html = '<span>£—</span>';

		if ( function_exists( 'WC' ) ) {
			$product_id = SPE_WooCommerce_Integration::resolve_product_id( $seed['key'], $seed['dose'] );
			if ( $product_id ) {
				$product = wc_get_product( $product_id );
				if ( $product ) {
					$title      = $product->get_name();
					$price_html = $product->get_price_html();
				}
			}
		}

		return array(
			'key'         => $seed['key'],
			'dose'        => $seed['dose'],
			'title'       => $title,
			'price_html'  => $price_html,
			'tag'         => $seed['tag'],
			'tag_style'   => $seed['tag_style'],
			'description' => $seed['description'],
		);
	}
}
