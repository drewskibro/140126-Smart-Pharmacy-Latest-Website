<?php
/**
 * Photo ID upload for consultation orders (GPhC POM dispensing).
 *
 * Flow:
 *   - After a consultation order is placed, the customer is shown an
 *     "Upload your ID" form on the order-received and view-order pages
 *     (works for guests via the order key, and logged-in customers via
 *     ownership).
 *   - The file is validated, stored under an unguessable random name in
 *     a dedicated uploads dir, and linked to the order (meta) so it
 *     never merges into the media library.
 *   - The pharmacist sees the ID status + a gated "View ID" link in the
 *     clinical review panel, and an "Eligibility → ID Uploads" admin
 *     screen tracks which consultation orders have / haven't uploaded.
 *
 * SECURITY NOTE (Kinsta = Nginx): files are stored with a 40-char random
 * filename and only ever served through the capability-gated download
 * handler — the raw URL is never exposed. The bundled .htaccess only
 * helps on Apache; on Nginx the uploads dir should ALSO be denied at the
 * server level. This is flagged for Drew/Kinsta in the README. ID
 * documents are special-category data: 5-year retention + a documented
 * deletion process are a pre-launch compliance task, not done here.
 *
 * @package SmartPharmacyEligibility
 */

defined( 'ABSPATH' ) || exit;

/**
 * SPE_ID_Upload.
 */
class SPE_ID_Upload {

	const ORDER_META = '_spe_id_upload';
	const SUBDIR     = 'spe-id-uploads';
	const NONCE      = 'spe_id_upload';
	const UP_ACTION  = 'spe_id_upload';
	const DL_ACTION  = 'spe_id_download';
	const MAX_BYTES  = 10485760; // 10 MB.
	const CAPABILITY = 'manage_woocommerce';

	/** ext => mime allow-list. */
	const ALLOWED = array(
		'jpg'  => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'png'  => 'image/png',
		'pdf'  => 'application/pdf',
		'heic' => 'image/heic',
	);

	/**
	 * Wire hooks.
	 */
	public static function register() {
		// Customer-facing upload form on order-received + view-order.
		add_action( 'woocommerce_order_details_after_order_table', array( __CLASS__, 'render_customer_form' ), 20 );

		// Upload + download handlers.
		add_action( 'admin_post_' . self::UP_ACTION, array( __CLASS__, 'handle_upload' ) );
		add_action( 'admin_post_nopriv_' . self::UP_ACTION, array( __CLASS__, 'handle_upload' ) );
		add_action( 'admin_post_' . self::DL_ACTION, array( __CLASS__, 'handle_download' ) );

		// Pharmacist: ID status in the review panel + tracking screen.
		add_action( 'spe_after_consultation_review', array( __CLASS__, 'render_panel_status' ), 5, 2 );
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ), 25 );
	}

	/* ----------------------------------------------------------------
	 * Customer upload
	 * ---------------------------------------------------------------- */

	/**
	 * Show the upload form (or "received" note) under the order table on
	 * the order-received and view-order pages — consultation orders only.
	 *
	 * @param WC_Order $order
	 */
	public static function render_customer_form( $order ) {
		if ( ! is_a( $order, 'WC_Order' ) || ! self::is_consultation_order( $order ) ) {
			return;
		}

		$notice = isset( $_GET['spe_id'] ) ? sanitize_key( wp_unslash( $_GET['spe_id'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		echo '<section class="spe-id-upload" style="margin:24px 0;padding:16px 18px;border:1px solid #99f6e4;border-radius:8px;background:#f0fdfa;">';
		echo '<h2 style="margin-top:0;">' . esc_html__( 'Photo ID required', 'smart-pharmacy-eligibility' ) . '</h2>';

		if ( self::get_record( $order ) ) {
			echo '<p>' . esc_html__( 'Thank you — we have received your ID. A pharmacist will review your consultation.', 'smart-pharmacy-eligibility' ) . '</p>';
			echo '</section>';
			return;
		}

		if ( 'error' === $notice ) {
			echo '<p style="color:#991b1b;">' . esc_html__( 'Sorry, that file could not be uploaded. Please use a JPG, PNG or PDF under 10 MB.', 'smart-pharmacy-eligibility' ) . '</p>';
		}

		echo '<p>' . esc_html__( 'Before a pharmacist can approve your order, please upload a photo of your ID (passport or driving licence).', 'smart-pharmacy-eligibility' ) . '</p>';
		?>
		<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="<?php echo esc_attr( self::UP_ACTION ); ?>" />
			<input type="hidden" name="order_id" value="<?php echo esc_attr( $order->get_id() ); ?>" />
			<input type="hidden" name="order_key" value="<?php echo esc_attr( $order->get_order_key() ); ?>" />
			<?php wp_nonce_field( self::NONCE . '_' . $order->get_id() ); ?>
			<p>
				<input type="file" name="spe_id_file" accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf" required />
			</p>
			<button type="submit" class="button"><?php esc_html_e( 'Upload ID', 'smart-pharmacy-eligibility' ); ?></button>
			<p style="font-size:13px;color:#115e59;margin-top:10px;"><?php esc_html_e( 'Your ID is stored securely and only seen by our pharmacy team.', 'smart-pharmacy-eligibility' ); ?></p>
		</form>
		<?php
		echo '</section>';
	}

	/**
	 * Handle a customer ID upload.
	 */
	public static function handle_upload() {
		$order_id = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
		check_admin_referer( self::NONCE . '_' . $order_id );

		$order = function_exists( 'wc_get_order' ) ? wc_get_order( $order_id ) : null;
		$key   = isset( $_POST['order_key'] ) ? sanitize_text_field( wp_unslash( $_POST['order_key'] ) ) : '';

		if ( ! $order || ! self::can_access( $order, $key ) ) {
			wp_die( esc_html__( 'You are not allowed to upload to this order.', 'smart-pharmacy-eligibility' ) );
		}

		$stored = self::store_file( $order );
		$status = $stored ? 'ok' : 'error';

		$redirect = wp_get_referer();
		if ( ! $redirect ) {
			$redirect = $order->get_view_order_url();
		}
		wp_safe_redirect( add_query_arg( 'spe_id', $status, $redirect ) );
		exit;
	}

	/**
	 * Validate + move the uploaded file into the protected dir and link
	 * it to the order.
	 *
	 * @param WC_Order $order
	 * @return bool
	 */
	protected static function store_file( $order ) {
		if ( empty( $_FILES['spe_id_file'] ) || ! isset( $_FILES['spe_id_file']['tmp_name'] ) ) {
			return false;
		}
		$file = $_FILES['spe_id_file']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- individual members validated below.

		if ( ! isset( $file['error'] ) || UPLOAD_ERR_OK !== (int) $file['error'] ) {
			return false;
		}
		if ( (int) $file['size'] <= 0 || (int) $file['size'] > self::MAX_BYTES ) {
			return false;
		}
		if ( ! is_uploaded_file( $file['tmp_name'] ) ) {
			return false;
		}

		// Real type check (reads the file, not just the name).
		$original = sanitize_file_name( wp_unslash( $file['name'] ) );
		$check    = wp_check_filetype_and_ext( $file['tmp_name'], $original );
		$ext      = $check['ext'] ? strtolower( $check['ext'] ) : '';
		$type     = $check['type'];

		if ( ! $ext || ! isset( self::ALLOWED[ $ext ] ) || self::ALLOWED[ $ext ] !== $type ) {
			return false;
		}

		$dir = self::ensure_dir();
		if ( ! $dir ) {
			return false;
		}

		$name = bin2hex( random_bytes( 20 ) ) . '.' . $ext;
		$dest = trailingslashit( $dir ) . $name;

		if ( ! move_uploaded_file( $file['tmp_name'], $dest ) ) {
			return false;
		}
		@chmod( $dest, 0640 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

		$order->update_meta_data(
			self::ORDER_META,
			array(
				'file'        => $name,
				'original'    => $original,
				'mime'        => $type,
				'uploaded_at' => current_time( 'mysql' ),
			)
		);
		$order->add_order_note( __( 'Customer uploaded photo ID.', 'smart-pharmacy-eligibility' ) );
		$order->save();

		return true;
	}

	/* ----------------------------------------------------------------
	 * Pharmacist download + status
	 * ---------------------------------------------------------------- */

	/**
	 * Stream the stored ID to an authorised staff member only.
	 */
	public static function handle_download() {
		$order_id = isset( $_GET['order_id'] ) ? absint( $_GET['order_id'] ) : 0;
		check_admin_referer( self::DL_ACTION . '_' . $order_id );

		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die( esc_html__( 'You do not have permission to view this file.', 'smart-pharmacy-eligibility' ) );
		}

		$order  = function_exists( 'wc_get_order' ) ? wc_get_order( $order_id ) : null;
		$record = $order ? self::get_record( $order ) : null;
		if ( ! $record ) {
			wp_die( esc_html__( 'ID file not found.', 'smart-pharmacy-eligibility' ) );
		}

		$path = trailingslashit( self::dir_path() ) . $record['file'];
		if ( ! is_readable( $path ) || ! self::path_is_inside_dir( $path ) ) {
			wp_die( esc_html__( 'ID file not found.', 'smart-pharmacy-eligibility' ) );
		}

		nocache_headers();
		header( 'Content-Type: ' . $record['mime'] );
		header( 'Content-Disposition: inline; filename="' . sanitize_file_name( $record['original'] ) . '"' );
		header( 'Content-Length: ' . filesize( $path ) );
		readfile( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile
		exit;
	}

	/**
	 * Show ID status + view link inside the review panel, above the
	 * Approve/Reject buttons.
	 *
	 * @param WC_Order $order
	 * @param object   $row
	 */
	public static function render_panel_status( $order, $row ) {
		if ( ! is_a( $order, 'WC_Order' ) ) {
			return;
		}
		$record = self::get_record( $order );
		echo '<p style="margin:12px 0 0;"><strong>' . esc_html__( 'Photo ID:', 'smart-pharmacy-eligibility' ) . '</strong> ';
		if ( $record ) {
			echo '<span style="color:#166534;">' . esc_html__( 'uploaded', 'smart-pharmacy-eligibility' ) . '</span> — ';
			echo '<a href="' . esc_url( self::download_url( $order->get_id() ) ) . '" target="_blank" rel="noopener">' . esc_html__( 'View ID', 'smart-pharmacy-eligibility' ) . '</a>';
			echo ' <span style="color:#6b7280;">(' . esc_html( $record['original'] ) . ')</span>';
		} else {
			echo '<span style="color:#991b1b;">' . esc_html__( 'not uploaded yet', 'smart-pharmacy-eligibility' ) . '</span>';
		}
		echo '</p>';
	}

	/* ----------------------------------------------------------------
	 * Admin tracking screen
	 * ---------------------------------------------------------------- */

	/**
	 * "Eligibility → ID Uploads" submenu.
	 */
	public static function register_menu() {
		add_submenu_page(
			'spe-assessments',
			__( 'ID Uploads', 'smart-pharmacy-eligibility' ),
			__( 'ID Uploads', 'smart-pharmacy-eligibility' ),
			self::CAPABILITY,
			'spe-id-uploads',
			array( __CLASS__, 'render_admin' )
		);
	}

	/**
	 * List recent consultation orders and whether ID has been uploaded.
	 */
	public static function render_admin() {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die( esc_html__( 'You do not have permission to view this page.', 'smart-pharmacy-eligibility' ) );
		}
		if ( ! function_exists( 'wc_get_orders' ) ) {
			echo '<div class="wrap"><h1>' . esc_html__( 'ID Uploads', 'smart-pharmacy-eligibility' ) . '</h1><p>' . esc_html__( 'WooCommerce is not active.', 'smart-pharmacy-eligibility' ) . '</p></div>';
			return;
		}

		$orders = wc_get_orders(
			array(
				'limit'   => 200,
				'orderby' => 'date',
				'order'   => 'DESC',
			)
		);
		$rows = array();
		foreach ( (array) $orders as $order ) {
			if ( self::is_consultation_order( $order ) ) {
				$rows[] = $order;
			}
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'ID Uploads', 'smart-pharmacy-eligibility' ); ?></h1>
			<p><?php esc_html_e( 'Consultation orders and whether the customer has uploaded their photo ID.', 'smart-pharmacy-eligibility' ); ?></p>
			<table class="wp-list-table widefat fixed striped">
				<thead><tr>
					<th><?php esc_html_e( 'Order', 'smart-pharmacy-eligibility' ); ?></th>
					<th><?php esc_html_e( 'Customer', 'smart-pharmacy-eligibility' ); ?></th>
					<th><?php esc_html_e( 'Date', 'smart-pharmacy-eligibility' ); ?></th>
					<th><?php esc_html_e( 'ID uploaded?', 'smart-pharmacy-eligibility' ); ?></th>
					<th></th>
				</tr></thead>
				<tbody>
				<?php if ( empty( $rows ) ) : ?>
					<tr><td colspan="5"><?php esc_html_e( 'No consultation orders yet.', 'smart-pharmacy-eligibility' ); ?></td></tr>
				<?php else : ?>
					<?php foreach ( $rows as $order ) : ?>
						<?php $record = self::get_record( $order ); ?>
						<tr>
							<td><a href="<?php echo esc_url( $order->get_edit_order_url() ); ?>">#<?php echo (int) $order->get_id(); ?></a></td>
							<td><?php echo esc_html( trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ) ); ?></td>
							<td><?php echo esc_html( $order->get_date_created() ? wc_format_datetime( $order->get_date_created() ) : '' ); ?></td>
							<td>
								<?php if ( $record ) : ?>
									<span style="color:#166534;font-weight:600;"><?php esc_html_e( 'Yes', 'smart-pharmacy-eligibility' ); ?></span>
								<?php else : ?>
									<span style="color:#991b1b;font-weight:600;"><?php esc_html_e( 'Waiting', 'smart-pharmacy-eligibility' ); ?></span>
								<?php endif; ?>
							</td>
							<td>
								<?php if ( $record ) : ?>
									<a href="<?php echo esc_url( self::download_url( $order->get_id() ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'View ID', 'smart-pharmacy-eligibility' ); ?></a>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
			</table>
			<p style="margin-top:12px;color:#6b7280;"><?php esc_html_e( 'Showing the 200 most recent orders.', 'smart-pharmacy-eligibility' ); ?></p>
		</div>
		<?php
	}

	/* ----------------------------------------------------------------
	 * Helpers
	 * ---------------------------------------------------------------- */

	/**
	 * @param WC_Order $order
	 * @return bool
	 */
	protected static function is_consultation_order( $order ) {
		return (bool) $order->get_meta( '_spe_consultation_id' );
	}

	/**
	 * @param WC_Order $order
	 * @return array|null Stored record, or null.
	 */
	protected static function get_record( $order ) {
		$record = $order->get_meta( self::ORDER_META );
		return ( is_array( $record ) && ! empty( $record['file'] ) ) ? $record : null;
	}

	/**
	 * Guest-or-owner authorisation, mirroring how WooCommerce gates
	 * guest order views.
	 *
	 * @param WC_Order $order
	 * @param string   $key Provided order key.
	 * @return bool
	 */
	protected static function can_access( $order, $key ) {
		if ( get_current_user_id() && (int) $order->get_customer_id() === get_current_user_id() ) {
			return true;
		}
		return $key && hash_equals( (string) $order->get_order_key(), (string) $key );
	}

	/**
	 * Admin-post download URL with nonce.
	 *
	 * @param int $order_id
	 * @return string
	 */
	protected static function download_url( $order_id ) {
		return wp_nonce_url(
			admin_url( 'admin-post.php?action=' . self::DL_ACTION . '&order_id=' . (int) $order_id ),
			self::DL_ACTION . '_' . $order_id
		);
	}

	/**
	 * Absolute path to the protected upload dir (no trailing slash).
	 *
	 * @return string
	 */
	protected static function dir_path() {
		$up = wp_upload_dir();
		return trailingslashit( $up['basedir'] ) . self::SUBDIR;
	}

	/**
	 * Create the protected dir + drop-in guards on first use.
	 *
	 * @return string|false Dir path or false on failure.
	 */
	protected static function ensure_dir() {
		$dir = self::dir_path();
		if ( ! wp_mkdir_p( $dir ) ) {
			return false;
		}
		$index = trailingslashit( $dir ) . 'index.html';
		if ( ! file_exists( $index ) ) {
			@file_put_contents( $index, '' ); // phpcs:ignore
		}
		$ht = trailingslashit( $dir ) . '.htaccess';
		if ( ! file_exists( $ht ) ) {
			// Apache only; Nginx needs a server-level deny (see README).
			@file_put_contents( $ht, "Require all denied\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n" ); // phpcs:ignore
		}
		return $dir;
	}

	/**
	 * Guard against path traversal on download.
	 *
	 * @param string $path
	 * @return bool
	 */
	protected static function path_is_inside_dir( $path ) {
		$real = realpath( $path );
		$base = realpath( self::dir_path() );
		return $real && $base && 0 === strpos( $real, $base );
	}
}
