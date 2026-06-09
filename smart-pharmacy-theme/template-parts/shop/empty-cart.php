<?php
/**
 * Branded empty-cart treatment.
 *
 * Injected before WC's own empty-cart block on /cart/ via the
 * the_content filter in inc/woocommerce.php. Replaces the
 * sad-face emoji + grey text with a brand panel and a clear
 * routing CTA back to the shop or the start-consultation page.
 *
 * The WC blocks below still render -- but the CSS in styles.css
 * hides the default empty cart heading + emoji so the only
 * visible empty state is this branded panel.
 *
 * @package SmartPharmacy
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="sp-empty-cart">
	<div class="sp-empty-cart__icon">
		<?php echo sp_icon( 'check_circle', 'w-12 h-12 text-teal-500' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
	<h2 class="sp-empty-cart__title"><?php esc_html_e( "Your basket's empty — let's fix that", 'smart-pharmacy' ); ?></h2>
	<p class="sp-empty-cart__body"><?php esc_html_e( 'Browse our pharmacy range or start a free consultation if you need a prescription treatment.', 'smart-pharmacy' ); ?></p>

	<div class="sp-empty-cart__ctas">
		<a class="sp-empty-cart__cta sp-empty-cart__cta--primary" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ?: home_url( '/shop/' ) ); ?>">
			<?php esc_html_e( 'Browse the shop', 'smart-pharmacy' ); ?>
			<?php echo sp_icon( 'check', 'w-4 h-4 ml-1' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>
		<?php
		// Offer the consultation CTA only when the eligibility plugin is wired up.
		$sp_ec_checker_url = class_exists( 'SPE_Admin' ) ? SPE_Admin::get_checker_url() : '';
		if ( $sp_ec_checker_url && home_url( '/' ) !== $sp_ec_checker_url ) :
			?>
			<a class="sp-empty-cart__cta sp-empty-cart__cta--secondary" href="<?php echo esc_url( $sp_ec_checker_url ); ?>">
				<?php esc_html_e( 'Start a consultation', 'smart-pharmacy' ); ?>
			</a>
		<?php endif; ?>
	</div>

	<ul class="sp-empty-cart__assurances">
		<li><?php echo sp_icon( 'truck', 'w-4 h-4 text-teal-500' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Free UK delivery on orders over £30', 'smart-pharmacy' ); ?></span></li>
		<li><?php echo sp_icon( 'shield', 'w-4 h-4 text-teal-500' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'GPhC-registered UK pharmacy', 'smart-pharmacy' ); ?></span></li>
		<li><?php echo sp_icon( 'lock', 'w-4 h-4 text-teal-500' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Discreet packaging, secure checkout', 'smart-pharmacy' ); ?></span></li>
	</ul>
</section>
