<?php
/**
 * Admin: assessments list + (treatment, dose) -> WC product mapping.
 *
 * Two screens, both under a top-level "Eligibility" admin menu so
 * pharmacy staff can find assessments without diving into the WC
 * orders area for partial / ineligible drop-offs.
 *
 *   Eligibility                 -> SPE_Admin::render_list()
 *   Eligibility > Settings      -> SPE_Admin::render_settings()
 *
 * Capability: manage_woocommerce so shop managers can review but
 * not edit theme / plugin code.
 *
 * @package SmartPharmacyEligibility
 */

defined( 'ABSPATH' ) || exit;

/**
 * SPE_Admin.
 */
class SPE_Admin {

	const CAPABILITY = 'manage_woocommerce';

	/** Pharmacist review statuses: slug => label. */
	const REVIEW_STATUSES = array(
		'new'       => 'New',
		'in_review' => 'In review',
		'approved'  => 'Approved',
		'rejected'  => 'Denied',
	);

	/**
	 * Wire admin hooks.
	 */
	public static function register() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'admin_init', array( __CLASS__, 'maybe_handle_detail_post' ) );
	}

	/**
	 * Top-level menu + Settings submenu.
	 */
	public static function register_menu() {
		$needs  = class_exists( 'SPE_Assessment_Repo' ) ? SPE_Assessment_Repo::count_needs_review() : 0;
		$bubble = $needs > 0 ? ' <span class="awaiting-mod"><span class="pending-count">' . (int) $needs . '</span></span>' : '';

		add_menu_page(
			__( 'Eligibility', 'smart-pharmacy-eligibility' ),
			__( 'Eligibility', 'smart-pharmacy-eligibility' ) . $bubble,
			self::CAPABILITY,
			'spe-assessments',
			array( __CLASS__, 'render_list' ),
			'dashicons-clipboard',
			56
		);

		add_submenu_page(
			'spe-assessments',
			__( 'Assessments', 'smart-pharmacy-eligibility' ),
			__( 'Assessments', 'smart-pharmacy-eligibility' ) . $bubble,
			self::CAPABILITY,
			'spe-assessments',
			array( __CLASS__, 'render_list' )
		);

		add_submenu_page(
			'spe-assessments',
			__( 'Settings', 'smart-pharmacy-eligibility' ),
			__( 'Settings', 'smart-pharmacy-eligibility' ),
			self::CAPABILITY,
			'spe-settings',
			array( __CLASS__, 'render_settings' )
		);
	}

	/**
	 * Register the plugin's options.
	 */
	public static function register_settings() {
		register_setting(
			'spe_settings',
			'spe_product_map',
			array(
				'sanitize_callback' => array( __CLASS__, 'sanitise_product_map' ),
				'default'           => array(),
			)
		);
		register_setting(
			'spe_settings',
			'spe_checker_url',
			array(
				'sanitize_callback' => 'esc_url_raw',
				'default'           => '',
			)
		);
		register_setting(
			'spe_settings',
			'spe_consultation_url',
			array(
				'sanitize_callback' => 'esc_url_raw',
				'default'           => '',
			)
		);
	}

	/**
	 * URL of the P-med consultation form page (the lighter form, for the
	 * wider prescription range). The theme routes general P-medicines
	 * here (with the product id), while GLP-1 / weight-loss products go
	 * to the in-depth checker instead. Empty if not configured.
	 *
	 * @return string
	 */
	public static function get_consultation_url() {
		$url = (string) spe_option( 'consultation_url', '' );
		if ( $url ) {
			return $url;
		}
		// Self-heal: if the admin never set the page, find the published page
		// that hosts the consultation shortcode so the CTA can't dead-end.
		return self::discover_page_url( 'smart_pharmacy_consultation' );
	}

	/**
	 * Find the published page hosting a given plugin shortcode.
	 *
	 * Used as a fallback when the admin has not selected the consultation /
	 * checker page in settings, so a "Start Consultation" CTA never lands on
	 * a blank "Nothing found" page. Result is cached for an hour.
	 *
	 * @param string $shortcode Shortcode tag, without brackets.
	 * @return string Permalink, or '' if no such page exists.
	 */
	protected static function discover_page_url( $shortcode ) {
		$cache_key = 'spe_page_url_' . $shortcode;
		$cached    = get_transient( $cache_key );
		if ( false !== $cached ) {
			return (string) $cached;
		}

		global $wpdb;
		$page_id = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts}
				 WHERE post_type = 'page' AND post_status = 'publish'
				 AND post_content LIKE %s
				 ORDER BY ID ASC LIMIT 1",
				'%[' . $wpdb->esc_like( $shortcode ) . '%'
			)
		);
		$url = $page_id ? (string) get_permalink( $page_id ) : '';
		set_transient( $cache_key, $url, HOUR_IN_SECONDS );
		return $url;
	}

	/**
	 * Resolve the public-facing URL of the eligibility checker page.
	 *
	 * The theme's "Start Consultation" CTAs on POM products link to
	 * this URL so the customer goes straight to the consultation form
	 * instead of bouncing via the treatment landing page.
	 *
	 * Falls back to the homepage if the admin hasn't set it yet --
	 * better than a broken link.
	 *
	 * @return string Absolute URL.
	 */
	public static function get_checker_url() {
		$url = (string) spe_option( 'checker_url', '' );
		if ( $url ) {
			return $url;
		}
		// Self-heal via the checker shortcode page before falling back home.
		$discovered = self::discover_page_url( 'smart_pharmacy_eligibility' );
		return $discovered ? $discovered : home_url( '/' );
	}

	/**
	 * Sanitise the product map submitted from the settings form.
	 *
	 * @param mixed $input
	 * @return array
	 */
	public static function sanitise_product_map( $input ) {
		if ( ! is_array( $input ) ) {
			return array();
		}
		$clean = array();
		foreach ( $input as $key => $value ) {
			// NOT sanitize_key() — that strips the "." from decimal doses
			// (mounjaro-2.5mg -> mounjaro-25mg), so those never round-trip.
			// Keep lowercase letters, digits, dot, underscore and hyphen.
			$key   = preg_replace( '/[^a-z0-9._-]/', '', strtolower( (string) $key ) );
			$value = (int) $value;
			if ( $key && $value > 0 ) {
				$clean[ $key ] = $value;
			}
		}
		return $clean;
	}

	/**
	 * Render the assessments list table.
	 */
	public static function render_list() {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die( esc_html__( 'You do not have permission to view this page.', 'smart-pharmacy-eligibility' ) );
		}

		$view = isset( $_GET['assessment'] ) ? sanitize_text_field( wp_unslash( $_GET['assessment'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( $view ) {
			self::render_detail( $view );
			return;
		}

		$status = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$rows = SPE_Assessment_Repo::list_recent(
			array(
				'status' => $status,
				'search' => $search,
				'limit'  => 100,
			)
		);
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php esc_html_e( 'Eligibility Assessments', 'smart-pharmacy-eligibility' ); ?></h1>

			<form method="get" style="margin: 16px 0 8px;">
				<input type="hidden" name="page" value="spe-assessments" />
				<select name="status">
					<option value=""><?php esc_html_e( 'All statuses', 'smart-pharmacy-eligibility' ); ?></option>
					<option value="partial" <?php selected( 'partial', $status ); ?>><?php esc_html_e( 'Partial (in progress)', 'smart-pharmacy-eligibility' ); ?></option>
					<option value="complete" <?php selected( 'complete', $status ); ?>><?php esc_html_e( 'Complete', 'smart-pharmacy-eligibility' ); ?></option>
					<option value="ineligible" <?php selected( 'ineligible', $status ); ?>><?php esc_html_e( 'Ineligible', 'smart-pharmacy-eligibility' ); ?></option>
				</select>
				<input type="search" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'Email, name, or assessment ID', 'smart-pharmacy-eligibility' ); ?>" />
				<button class="button"><?php esc_html_e( 'Filter', 'smart-pharmacy-eligibility' ); ?></button>
			</form>

			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Created', 'smart-pharmacy-eligibility' ); ?></th>
						<th><?php esc_html_e( 'Name', 'smart-pharmacy-eligibility' ); ?></th>
						<th><?php esc_html_e( 'Email', 'smart-pharmacy-eligibility' ); ?></th>
						<th><?php esc_html_e( 'BMI', 'smart-pharmacy-eligibility' ); ?></th>
						<th><?php esc_html_e( 'Treatment', 'smart-pharmacy-eligibility' ); ?></th>
						<th><?php esc_html_e( 'Status', 'smart-pharmacy-eligibility' ); ?></th>
						<th><?php esc_html_e( 'Review', 'smart-pharmacy-eligibility' ); ?></th>
						<th><?php esc_html_e( 'Order', 'smart-pharmacy-eligibility' ); ?></th>
						<th><?php esc_html_e( 'Reason', 'smart-pharmacy-eligibility' ); ?></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				<?php if ( empty( $rows ) ) : ?>
					<tr><td colspan="10"><?php esc_html_e( 'No assessments yet.', 'smart-pharmacy-eligibility' ); ?></td></tr>
				<?php else : ?>
					<?php foreach ( $rows as $row ) : ?>
						<tr>
							<td><?php echo esc_html( mysql2date( 'Y-m-d H:i', $row->created_at ) ); ?></td>
							<td><?php echo esc_html( trim( $row->first_name . ' ' . $row->last_name ) ); ?></td>
							<td><?php echo esc_html( $row->email ); ?></td>
							<td><?php echo esc_html( $row->bmi ); ?></td>
							<td>
								<?php
								if ( $row->selected_treatment ) {
									echo esc_html( ucfirst( $row->selected_treatment ) );
									if ( $row->selected_dose ) {
										echo ' ' . esc_html( $row->selected_dose );
									}
								}
								?>
							</td>
							<td>
								<span class="spe-status spe-status--<?php echo esc_attr( $row->status ); ?>">
									<?php echo esc_html( ucfirst( $row->status ) ); ?>
								</span>
							</td>
							<td>
								<?php
								$rev = $row->review_status ? $row->review_status : ( 'complete' === $row->status ? 'new' : '' );
								if ( isset( self::REVIEW_STATUSES[ $rev ] ) ) {
									$rc = array( 'new' => '#fef3c7;color:#92400e', 'in_review' => '#dbeafe;color:#1e40af', 'approved' => '#dcfce7;color:#166534', 'rejected' => '#fee2e2;color:#991b1b' );
									echo '<span style="display:inline-block;padding:2px 9px;border-radius:999px;font-size:12px;font-weight:600;background:' . esc_attr( $rc[ $rev ] ) . ';">' . esc_html( self::REVIEW_STATUSES[ $rev ] ) . '</span>';
								} else {
									echo '&mdash;';
								}
								?>
							</td>
							<td>
								<?php if ( $row->order_id ) : ?>
									<a href="<?php echo esc_url( admin_url( 'post.php?post=' . $row->order_id . '&action=edit' ) ); ?>">#<?php echo (int) $row->order_id; ?></a>
								<?php else : ?>
									—
								<?php endif; ?>
							</td>
							<td><?php echo esc_html( $row->ineligible_reason ); ?></td>
							<td><a href="<?php echo esc_url( add_query_arg( array( 'page' => 'spe-assessments', 'assessment' => $row->assessment_id ), admin_url( 'admin.php' ) ) ); ?>"><?php esc_html_e( 'View', 'smart-pharmacy-eligibility' ); ?></a></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
			</table>

			<p style="margin-top: 16px; color: #6b7280;">
				<?php esc_html_e( 'Showing the most recent 100 assessments. Filter by status or search by name / email / assessment ID.', 'smart-pharmacy-eligibility' ); ?>
			</p>
		</div>

		<style>
			.spe-status { padding: 3px 8px; border-radius: 999px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; }
			.spe-status--partial    { background: #fef3c7; color: #92400e; }
			.spe-status--complete   { background: #dcfce7; color: #166534; }
			.spe-status--ineligible { background: #fee2e2; color: #991b1b; }
		</style>
		<?php
	}

	/**
	 * Handle review-status / note / order-link actions from an
	 * assessment detail page (admin_init, so we can redirect-after-POST).
	 */
	public static function maybe_handle_detail_post() {
		if ( empty( $_POST['spe_action'] ) || empty( $_POST['spe_assessment_uuid'] ) ) {
			return;
		}
		$uuid = sanitize_text_field( wp_unslash( $_POST['spe_assessment_uuid'] ) );
		check_admin_referer( 'spe_assessment_detail_' . $uuid );
		if ( ! current_user_can( self::CAPABILITY ) || ! class_exists( 'SPE_Assessment_Repo' ) ) {
			return;
		}

		$action = sanitize_key( wp_unslash( $_POST['spe_action'] ) );

		if ( 'review_status' === $action ) {
			$status = isset( $_POST['review_status'] ) ? sanitize_key( wp_unslash( $_POST['review_status'] ) ) : '';
			if ( isset( self::REVIEW_STATUSES[ $status ] ) ) {
				SPE_Assessment_Repo::set_review_status( $uuid, $status );
			}
		} elseif ( 'note' === $action ) {
			$note = isset( $_POST['note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['note'] ) ) : '';
			if ( '' !== trim( $note ) ) {
				SPE_Assessment_Repo::add_note( $uuid, $note, get_current_user_id() );
			}
		} elseif ( 'order' === $action ) {
			$order_id = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
			if ( $order_id && function_exists( 'wc_get_order' ) && wc_get_order( $order_id ) ) {
				SPE_Assessment_Repo::set_order_id( $uuid, $order_id );
			}
		}

		wp_safe_redirect( add_query_arg( array( 'page' => 'spe-assessments', 'assessment' => $uuid, 'spe_done' => $action ), admin_url( 'admin.php' ) ) );
		exit;
	}

	/**
	 * Single-assessment detail: data + pharmacist review workflow
	 * (status, notes, order link).
	 *
	 * @param string $uuid
	 */
	protected static function render_detail( $uuid ) {
		$row  = SPE_Assessment_Repo::find_by_assessment_id( $uuid );
		$back = add_query_arg( array( 'page' => 'spe-assessments' ), admin_url( 'admin.php' ) );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Assessment', 'smart-pharmacy-eligibility' ); ?></h1>
			<p><a href="<?php echo esc_url( $back ); ?>">&larr; <?php esc_html_e( 'Back to all assessments', 'smart-pharmacy-eligibility' ); ?></a></p>
			<?php if ( isset( $_GET['spe_done'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Saved.', 'smart-pharmacy-eligibility' ); ?></p></div>
			<?php endif; ?>
			<?php if ( ! $row ) : ?>
				<p><?php esc_html_e( 'Assessment not found.', 'smart-pharmacy-eligibility' ); ?></p>
			<?php else : ?>
				<?php
				$nonce_action = 'spe_assessment_detail_' . $row->assessment_id;
				$fields = array(
					__( 'Submitted', 'smart-pharmacy-eligibility' )     => mysql2date( 'Y-m-d H:i', $row->created_at ),
					__( 'Name', 'smart-pharmacy-eligibility' )          => trim( $row->first_name . ' ' . $row->last_name ),
					__( 'Email', 'smart-pharmacy-eligibility' )         => $row->email,
					__( 'Phone', 'smart-pharmacy-eligibility' )         => $row->phone,
					__( 'Date of birth', 'smart-pharmacy-eligibility' ) => $row->dob,
					__( 'Sex', 'smart-pharmacy-eligibility' )           => $row->sex,
					__( 'Ethnicity', 'smart-pharmacy-eligibility' )     => $row->ethnicity,
					__( 'Height (cm)', 'smart-pharmacy-eligibility' )   => $row->height_cm,
					__( 'Weight (kg)', 'smart-pharmacy-eligibility' )   => $row->weight_kg,
					__( 'BMI', 'smart-pharmacy-eligibility' )           => $row->bmi,
					__( 'Diabetes', 'smart-pharmacy-eligibility' )      => $row->diabetes,
					__( 'Treatment', 'smart-pharmacy-eligibility' )     => trim( ucfirst( (string) $row->selected_treatment ) . ' ' . $row->selected_dose ),
					__( 'GP', 'smart-pharmacy-eligibility' )            => trim( $row->gp_name . ' ' . $row->gp_postcode ),
					__( 'Eligibility', 'smart-pharmacy-eligibility' )   => ucfirst( (string) $row->status ),
				);
				if ( $row->ineligible_reason ) {
					$fields[ __( 'Ineligible reason', 'smart-pharmacy-eligibility' ) ] = $row->ineligible_reason;
				}
				?>
				<table class="widefat striped" style="max-width:800px;">
					<tbody>
						<?php foreach ( $fields as $label => $val ) : ?>
							<?php if ( '' === trim( (string) $val ) ) { continue; } ?>
							<tr><th style="width:200px;text-align:left;"><?php echo esc_html( $label ); ?></th><td><?php echo esc_html( $val ); ?></td></tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<?php $decoded = ! empty( $row->raw_payload ) ? json_decode( $row->raw_payload ) : null; ?>
				<?php if ( $decoded ) : ?>
					<details style="margin-top:12px;max-width:800px;">
						<summary style="cursor:pointer;"><?php esc_html_e( 'Full submitted data', 'smart-pharmacy-eligibility' ); ?></summary>
						<pre style="white-space:pre-wrap;background:#fff;border:1px solid #dcdcde;padding:12px;border-radius:6px;overflow:auto;"><?php echo esc_html( wp_json_encode( $decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) ); ?></pre>
					</details>
				<?php endif; ?>

				<div style="display:flex;gap:32px;flex-wrap:wrap;margin-top:24px;max-width:800px;">
					<div>
						<h2><?php esc_html_e( 'Review status', 'smart-pharmacy-eligibility' ); ?></h2>
						<form method="post">
							<?php wp_nonce_field( $nonce_action ); ?>
							<input type="hidden" name="spe_assessment_uuid" value="<?php echo esc_attr( $row->assessment_id ); ?>" />
							<input type="hidden" name="spe_action" value="review_status" />
							<?php $current_rev = $row->review_status ? $row->review_status : 'new'; ?>
							<select name="review_status">
								<?php foreach ( self::REVIEW_STATUSES as $slug => $label ) : ?>
									<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $current_rev, $slug ); ?>><?php echo esc_html( $label ); ?></option>
								<?php endforeach; ?>
							</select>
							<button class="button button-primary"><?php esc_html_e( 'Update', 'smart-pharmacy-eligibility' ); ?></button>
						</form>
					</div>
					<div>
						<h2><?php esc_html_e( 'Linked order', 'smart-pharmacy-eligibility' ); ?></h2>
						<?php if ( $row->order_id ) : ?>
							<p><?php printf( /* translators: %d: order id. */ esc_html__( 'Linked to order #%d.', 'smart-pharmacy-eligibility' ), (int) $row->order_id ); ?>
								<a href="<?php echo esc_url( admin_url( 'post.php?post=' . (int) $row->order_id . '&action=edit' ) ); ?>"><?php esc_html_e( 'View', 'smart-pharmacy-eligibility' ); ?></a></p>
						<?php endif; ?>
						<form method="post">
							<?php wp_nonce_field( $nonce_action ); ?>
							<input type="hidden" name="spe_assessment_uuid" value="<?php echo esc_attr( $row->assessment_id ); ?>" />
							<input type="hidden" name="spe_action" value="order" />
							<input type="number" name="order_id" min="1" placeholder="<?php esc_attr_e( 'Order ID', 'smart-pharmacy-eligibility' ); ?>" class="small-text" />
							<button class="button"><?php esc_html_e( 'Link order', 'smart-pharmacy-eligibility' ); ?></button>
						</form>
					</div>
				</div>

				<h2 style="margin-top:24px;"><?php esc_html_e( 'Notes', 'smart-pharmacy-eligibility' ); ?></h2>
				<div style="max-width:800px;">
					<?php $log = SPE_Assessment_Repo::notes_log( $row ); ?>
					<?php if ( empty( $log ) ) : ?>
						<p style="color:#6b7280;"><?php esc_html_e( 'No notes yet.', 'smart-pharmacy-eligibility' ); ?></p>
					<?php else : ?>
						<?php foreach ( array_reverse( $log ) as $note ) : ?>
							<?php $author = ! empty( $note['user'] ) ? get_userdata( (int) $note['user'] ) : null; ?>
							<div style="background:#fff;border:1px solid #dcdcde;border-radius:6px;padding:10px 12px;margin-bottom:8px;">
								<p style="margin:0 0 4px;color:#646970;font-size:12px;">
									<?php echo esc_html( isset( $note['time'] ) ? mysql2date( 'Y-m-d H:i', $note['time'] ) : '' ); ?>
									<?php echo $author ? ' &middot; ' . esc_html( $author->display_name ) : ''; ?>
								</p>
								<p style="margin:0;white-space:pre-wrap;"><?php echo esc_html( isset( $note['text'] ) ? $note['text'] : '' ); ?></p>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
					<form method="post" style="margin-top:8px;">
						<?php wp_nonce_field( $nonce_action ); ?>
						<input type="hidden" name="spe_assessment_uuid" value="<?php echo esc_attr( $row->assessment_id ); ?>" />
						<input type="hidden" name="spe_action" value="note" />
						<textarea name="note" rows="2" class="large-text" placeholder="<?php esc_attr_e( 'Add a note…', 'smart-pharmacy-eligibility' ); ?>"></textarea>
						<p><button class="button"><?php esc_html_e( 'Add note', 'smart-pharmacy-eligibility' ); ?></button></p>
					</form>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render the (treatment, dose) -> WC product mapping form.
	 */
	public static function render_settings() {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die( esc_html__( 'You do not have permission to view this page.', 'smart-pharmacy-eligibility' ) );
		}

		$current_map = (array) spe_option( 'product_map', array() );

		$treatments = array(
			'wegovy'   => array( '0.25mg', '0.5mg', '1mg', '1.7mg', '2.4mg' ),
			'mounjaro' => array( '2.5mg', '5mg', '7.5mg', '10mg', '12.5mg', '15mg' ),
		);
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Eligibility Settings', 'smart-pharmacy-eligibility' ); ?></h1>

			<form method="post" action="options.php">
				<?php settings_fields( 'spe_settings' ); ?>

				<h2 style="margin-top: 16px;"><?php esc_html_e( 'Consultation Page URLs', 'smart-pharmacy-eligibility' ); ?></h2>
				<p><?php esc_html_e( 'Two pages drive the "Start Consultation" buttons. Weight-loss products (linked treatment / Weight Management) go to the BMI assessment; all other prescription products go to the lighter consultation form (with the product id attached).', 'smart-pharmacy-eligibility' ); ?></p>
				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row"><label for="spe_checker_url"><?php esc_html_e( 'Weight-loss assessment URL', 'smart-pharmacy-eligibility' ); ?></label></th>
							<td>
								<input type="url"
									id="spe_checker_url"
									name="spe_checker_url"
									value="<?php echo esc_attr( spe_option( 'checker_url', '' ) ); ?>"
									class="regular-text"
									placeholder="<?php echo esc_attr( home_url( '/weight-loss-assessment/' ) ); ?>" />
								<p class="description"><?php esc_html_e( 'Page with the [smart_pharmacy_eligibility] shortcode.', 'smart-pharmacy-eligibility' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="spe_consultation_url"><?php esc_html_e( 'Consultation form URL', 'smart-pharmacy-eligibility' ); ?></label></th>
							<td>
								<input type="url"
									id="spe_consultation_url"
									name="spe_consultation_url"
									value="<?php echo esc_attr( spe_option( 'consultation_url', '' ) ); ?>"
									class="regular-text"
									placeholder="<?php echo esc_attr( home_url( '/start-consultation/' ) ); ?>" />
								<p class="description"><?php esc_html_e( 'Page with the [smart_pharmacy_consultation] shortcode.', 'smart-pharmacy-eligibility' ); ?></p>
							</td>
						</tr>
					</tbody>
				</table>

				<h2 style="margin-top: 32px;"><?php esc_html_e( 'Product Mapping', 'smart-pharmacy-eligibility' ); ?></h2>
				<p><?php esc_html_e( "Map each treatment + dose to the matching WooCommerce product. The checker uses this map to add the right SKU to the basket once a patient finishes the assessment. If a row is left blank, the plugin falls back to SKU pattern matching (SP-WL-WEG-0.25mg, SP-WL-MOUN-2.5mg) and finally to a product-name search.", 'smart-pharmacy-eligibility' ); ?></p>


				<?php foreach ( $treatments as $treatment => $doses ) : ?>
					<h2 style="margin-top: 32px;"><?php echo esc_html( ucfirst( $treatment ) ); ?></h2>
					<table class="form-table" role="presentation">
						<tbody>
						<?php foreach ( $doses as $dose ) : ?>
							<?php $key = $treatment . '-' . $dose; ?>
							<tr>
								<th scope="row"><label for="spe_pm_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $dose ); ?></label></th>
								<td>
									<input type="number" min="0"
										id="spe_pm_<?php echo esc_attr( $key ); ?>"
										name="spe_product_map[<?php echo esc_attr( $key ); ?>]"
										value="<?php echo isset( $current_map[ $key ] ) ? (int) $current_map[ $key ] : ''; ?>"
										class="small-text"
										placeholder="WC product ID" />
									<?php if ( isset( $current_map[ $key ] ) && (int) $current_map[ $key ] > 0 ) : ?>
										<?php $linked = get_post( (int) $current_map[ $key ] ); ?>
										<?php if ( $linked ) : ?>
											<a href="<?php echo esc_url( get_edit_post_link( $linked->ID ) ); ?>" target="_blank" rel="noopener">
												<?php echo esc_html( $linked->post_title ); ?>
											</a>
										<?php endif; ?>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				<?php endforeach; ?>

				<?php submit_button(); ?>
			</form>

			<h2 style="margin-top: 48px;"><?php esc_html_e( 'How to embed the checker', 'smart-pharmacy-eligibility' ); ?></h2>
			<p><?php esc_html_e( 'Create a new WP page (suggested slug: /start-consultation/) and add this shortcode to its content:', 'smart-pharmacy-eligibility' ); ?></p>
			<pre style="background: #f3f4f6; padding: 12px; border-radius: 6px;">[smart_pharmacy_eligibility]</pre>
			<p><?php esc_html_e( 'Then point the "Start Consultation" CTAs on Mounjaro / Wegovy product cards at that page.', 'smart-pharmacy-eligibility' ); ?></p>
		</div>
		<?php
	}
}
