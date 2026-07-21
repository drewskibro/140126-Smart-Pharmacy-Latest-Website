<?php
/**
 * "Consultation Emails" admin tab.
 *
 * Lets staff edit the wording (subject / heading / body) of the plugin's
 * consultation emails and see a live, fully-branded preview of each,
 * rendered through the same WooCommerce email wrapper that actually
 * sends them.
 *
 *   Eligibility > Consultation Emails
 *
 * The email senders (SPE_Email_Consultation_Received and the rejection
 * notice) read their copy from here via the get_*() helpers, falling
 * back to the defaults below. Order/dispatch emails are WooCommerce's
 * own and are edited under WooCommerce > Settings > Emails.
 *
 * @package SmartPharmacyEligibility
 */

defined( 'ABSPATH' ) || exit;

/**
 * SPE_Consultation_Emails_Admin.
 */
class SPE_Consultation_Emails_Admin {

	const CAPABILITY    = 'manage_woocommerce';
	const SETTINGS_PAGE = 'spe_consultation_emails';

	/**
	 * The editable emails + their default copy.
	 *
	 * @return array
	 */
	public static function emails() {
		return array(
			'received' => array(
				'label'   => __( 'Consultation received (pending review)', 'smart-pharmacy-eligibility' ),
				'when'    => __( 'Sent to the customer when their order enters Awaiting Clinical Review.', 'smart-pharmacy-eligibility' ),
				'subject' => __( '[{site_title}] We have your consultation', 'smart-pharmacy-eligibility' ),
				'heading' => __( "Thanks — we've received your consultation", 'smart-pharmacy-eligibility' ),
				'body'    => __( 'Thanks for completing your consultation. One of our pharmacists will review your answers and may contact you with a few further questions before anything is dispatched. You have not been charged yet — payment is only taken once a pharmacist approves your order.', 'smart-pharmacy-eligibility' ),
			),
			'rejected' => array(
				'label'   => __( 'Consultation not approved', 'smart-pharmacy-eligibility' ),
				'when'    => __( 'Sent to the customer when a pharmacist rejects the consultation (payment voided).', 'smart-pharmacy-eligibility' ),
				'subject' => __( '[{site_title}] An update on your consultation', 'smart-pharmacy-eligibility' ),
				'heading' => __( 'About your recent consultation', 'smart-pharmacy-eligibility' ),
				'body'    => __( 'Thank you for completing your consultation. After review, our pharmacist was unable to approve this treatment for you on this occasion, so your order has been cancelled and you have not been charged. If you have any questions, please reply to this email or contact us.', 'smart-pharmacy-eligibility' ),
			),
		);
	}

	/* ----------------------------------------------------------------
	 * Copy getters — used by the email senders.
	 * ---------------------------------------------------------------- */

	public static function get_subject( $key ) {
		return self::get_part( $key, 'subject' );
	}

	public static function get_heading( $key ) {
		return self::get_part( $key, 'heading' );
	}

	public static function get_body( $key ) {
		return self::get_part( $key, 'body' );
	}

	/**
	 * @param string $key  Email key.
	 * @param string $part subject|heading|body.
	 * @return string
	 */
	protected static function get_part( $key, $part ) {
		$emails = self::emails();
		$default = isset( $emails[ $key ][ $part ] ) ? $emails[ $key ][ $part ] : '';
		$stored  = spe_option( 'email_' . $key . '_' . $part, null );
		return ( null === $stored || '' === $stored ) ? $default : (string) $stored;
	}

	/* ---------------------------------------------------------------- */

	/**
	 * Wire hooks.
	 */
	public static function register() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ), 30 );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
	}

	/**
	 * Submenu under Eligibility.
	 */
	public static function register_menu() {
		add_submenu_page(
			'spe-assessments',
			__( 'Consultation Emails', 'smart-pharmacy-eligibility' ),
			__( 'Consultation Emails', 'smart-pharmacy-eligibility' ),
			self::CAPABILITY,
			'spe-consultation-emails',
			array( __CLASS__, 'render' )
		);
	}

	/**
	 * Register the editable-copy options.
	 */
	public static function register_settings() {
		foreach ( array_keys( self::emails() ) as $key ) {
			foreach ( array( 'subject', 'heading', 'body' ) as $part ) {
				register_setting(
					self::SETTINGS_PAGE,
					'spe_email_' . $key . '_' . $part,
					array(
						'sanitize_callback' => 'body' === $part ? 'sanitize_textarea_field' : 'sanitize_text_field',
						'default'           => '',
					)
				);
			}
		}
	}

	/**
	 * Render the editor + previews.
	 */
	public static function render() {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die( esc_html__( 'You do not have permission to view this page.', 'smart-pharmacy-eligibility' ) );
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Consultation Emails', 'smart-pharmacy-eligibility' ); ?></h1>
			<p><?php esc_html_e( 'Edit the wording of the consultation emails and preview the branded design. Order confirmation and dispatch emails are WooCommerce\'s — edit those under WooCommerce → Settings → Emails.', 'smart-pharmacy-eligibility' ); ?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=email' ) ); ?>"><?php esc_html_e( 'Open WooCommerce email settings', 'smart-pharmacy-eligibility' ); ?></a>
			</p>

			<form method="post" action="options.php">
				<?php settings_fields( self::SETTINGS_PAGE ); ?>

				<?php foreach ( self::emails() as $key => $email ) : ?>
					<div style="background:#fff;border:1px solid #dcdcde;border-radius:6px;padding:16px 18px;margin:16px 0;">
						<h2 style="margin-top:0;"><?php echo esc_html( $email['label'] ); ?></h2>
						<p class="description"><?php echo esc_html( $email['when'] ); ?></p>
						<div style="display:flex;gap:24px;flex-wrap:wrap;align-items:flex-start;">
							<div style="flex:1;min-width:320px;">
								<table class="form-table" role="presentation">
									<tr>
										<th scope="row"><label><?php esc_html_e( 'Subject', 'smart-pharmacy-eligibility' ); ?></label></th>
										<td><input type="text" name="<?php echo esc_attr( 'spe_email_' . $key . '_subject' ); ?>" value="<?php echo esc_attr( self::get_subject( $key ) ); ?>" class="large-text" /></td>
									</tr>
									<tr>
										<th scope="row"><label><?php esc_html_e( 'Heading', 'smart-pharmacy-eligibility' ); ?></label></th>
										<td><input type="text" name="<?php echo esc_attr( 'spe_email_' . $key . '_heading' ); ?>" value="<?php echo esc_attr( self::get_heading( $key ) ); ?>" class="large-text" /></td>
									</tr>
									<tr>
										<th scope="row"><label><?php esc_html_e( 'Body', 'smart-pharmacy-eligibility' ); ?></label></th>
										<td><textarea name="<?php echo esc_attr( 'spe_email_' . $key . '_body' ); ?>" rows="6" class="large-text"><?php echo esc_textarea( self::get_body( $key ) ); ?></textarea></td>
									</tr>
								</table>
								<p class="description"><?php esc_html_e( 'Placeholder: {site_title}. The preview updates when you save.', 'smart-pharmacy-eligibility' ); ?></p>
							</div>
							<div style="flex:1;min-width:320px;">
								<p style="font-weight:600;margin:0 0 6px;"><?php esc_html_e( 'Preview', 'smart-pharmacy-eligibility' ); ?></p>
								<iframe title="<?php echo esc_attr( $email['label'] ); ?>" style="width:100%;height:520px;border:1px solid #dcdcde;border-radius:6px;background:#fff;" srcdoc="<?php echo esc_attr( self::preview_html( $key ) ); ?>"></iframe>
							</div>
						</div>
					</div>
				<?php endforeach; ?>

				<?php submit_button( __( 'Save emails', 'smart-pharmacy-eligibility' ) ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render a fully-branded preview of an email through the WC wrapper.
	 *
	 * @param string $key
	 * @return string HTML.
	 */
	protected static function preview_html( $key ) {
		$heading = self::replace_tokens( self::get_heading( $key ) );
		$body    = self::replace_tokens( self::get_body( $key ) );

		if ( function_exists( 'WC' ) && WC()->mailer() ) {
			$html = WC()->mailer()->wrap_message( $heading, wpautop( wptexturize( $body ) ) );
			// Replace any remaining tokens (e.g. in the footer text).
			return self::replace_tokens( $html );
		}

		// WooCommerce inactive — plain fallback so the box isn't empty.
		return '<div style="font-family:sans-serif;padding:20px;"><h2>' . esc_html( $heading ) . '</h2>' . wpautop( esc_html( $body ) ) . '</div>';
	}

	/**
	 * Swap {site_title}/{site_url} for the live values in previews.
	 *
	 * @param string $text
	 * @return string
	 */
	protected static function replace_tokens( $text ) {
		return str_replace(
			array( '{site_title}', '{site_url}' ),
			array( wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ), home_url( '/' ) ),
			(string) $text
		);
	}
}
