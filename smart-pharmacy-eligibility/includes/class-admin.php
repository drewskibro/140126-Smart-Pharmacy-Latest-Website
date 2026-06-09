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

	/**
	 * Wire admin hooks.
	 */
	public static function register() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
	}

	/**
	 * Top-level menu + Settings submenu.
	 */
	public static function register_menu() {
		add_menu_page(
			__( 'Eligibility', 'smart-pharmacy-eligibility' ),
			__( 'Eligibility', 'smart-pharmacy-eligibility' ),
			self::CAPABILITY,
			'spe-assessments',
			array( __CLASS__, 'render_list' ),
			'dashicons-clipboard',
			56
		);

		add_submenu_page(
			'spe-assessments',
			__( 'Assessments', 'smart-pharmacy-eligibility' ),
			__( 'Assessments', 'smart-pharmacy-eligibility' ),
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
	 * Register the spe_product_map option.
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
			$key   = sanitize_key( $key );
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
						<th><?php esc_html_e( 'Order', 'smart-pharmacy-eligibility' ); ?></th>
						<th><?php esc_html_e( 'Reason', 'smart-pharmacy-eligibility' ); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php if ( empty( $rows ) ) : ?>
					<tr><td colspan="8"><?php esc_html_e( 'No assessments yet.', 'smart-pharmacy-eligibility' ); ?></td></tr>
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
								<?php if ( $row->order_id ) : ?>
									<a href="<?php echo esc_url( admin_url( 'post.php?post=' . $row->order_id . '&action=edit' ) ); ?>">#<?php echo (int) $row->order_id; ?></a>
								<?php else : ?>
									—
								<?php endif; ?>
							</td>
							<td><?php echo esc_html( $row->ineligible_reason ); ?></td>
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
			<p><?php esc_html_e( "Map each treatment + dose to the matching WooCommerce product. The checker uses this map to add the right SKU to the basket once a patient finishes the assessment. If a row is left blank, the plugin falls back to SKU pattern matching (SP-WL-WEG-0.25mg, SP-WL-MOUN-2.5mg) and finally to a product-name search.", 'smart-pharmacy-eligibility' ); ?></p>

			<form method="post" action="options.php">
				<?php settings_fields( 'spe_settings' ); ?>

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
