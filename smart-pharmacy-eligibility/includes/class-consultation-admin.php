<?php
/**
 * Admin: "Consultation Form" editor.
 *
 * Lets Murtaza reword every base question, toggle each on/off, mark it
 * required, reorder it, and edit the choice options — plus the intro and
 * pharmacist disclaimer copy. This is the "editable, not hardcoded" half
 * of the card: question keys + input types stay in code, everything the
 * client would want to change lives here.
 *
 *   Eligibility > Consultation Form -> SPE_Consultation_Admin::render()
 *
 * @package SmartPharmacyEligibility
 */

defined( 'ABSPATH' ) || exit;

/**
 * SPE_Consultation_Admin.
 */
class SPE_Consultation_Admin {

	const CAPABILITY    = 'manage_woocommerce';
	const SETTINGS_PAGE = 'spe_consultation_settings';

	/**
	 * Wire admin hooks.
	 */
	public static function register() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ), 20 );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
	}

	/**
	 * Add the submenu under the existing "Eligibility" top-level menu.
	 */
	public static function register_menu() {
		// The submissions list — where P-med consultation submissions land
		// (distinct from GLP-1 "Assessments").
		add_submenu_page(
			'spe-assessments',
			__( 'Consultations', 'smart-pharmacy-eligibility' ),
			__( 'Consultations', 'smart-pharmacy-eligibility' ),
			self::CAPABILITY,
			'spe-consultations',
			array( __CLASS__, 'render_list' )
		);
		add_submenu_page(
			'spe-assessments',
			__( 'Consultation Form', 'smart-pharmacy-eligibility' ),
			__( 'Consultation Form', 'smart-pharmacy-eligibility' ),
			self::CAPABILITY,
			'spe-consultation-form',
			array( __CLASS__, 'render' )
		);
	}

	/**
	 * The consultation submissions list (and single-submission detail
	 * when ?consultation=<uuid> is present).
	 */
	public static function render_list() {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die( esc_html__( 'You do not have permission to view this page.', 'smart-pharmacy-eligibility' ) );
		}

		$uuid = isset( $_GET['consultation'] ) ? sanitize_text_field( wp_unslash( $_GET['consultation'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( $uuid ) {
			self::render_detail( $uuid );
			return;
		}

		$rows = class_exists( 'SPE_Consultation_Repo' ) ? SPE_Consultation_Repo::list_recent( array( 'limit' => 100 ) ) : array();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Consultations', 'smart-pharmacy-eligibility' ); ?></h1>
			<p><?php esc_html_e( 'P-medicine consultation form submissions. (The GLP-1 weight-loss checker is under "Assessments".)', 'smart-pharmacy-eligibility' ); ?></p>
			<table class="wp-list-table widefat fixed striped">
				<thead><tr>
					<th><?php esc_html_e( 'Submitted', 'smart-pharmacy-eligibility' ); ?></th>
					<th><?php esc_html_e( 'For', 'smart-pharmacy-eligibility' ); ?></th>
					<th><?php esc_html_e( 'Date of birth', 'smart-pharmacy-eligibility' ); ?></th>
					<th><?php esc_html_e( 'Product', 'smart-pharmacy-eligibility' ); ?></th>
					<th><?php esc_html_e( 'Order', 'smart-pharmacy-eligibility' ); ?></th>
					<th></th>
				</tr></thead>
				<tbody>
				<?php if ( empty( $rows ) ) : ?>
					<tr><td colspan="6"><?php esc_html_e( 'No consultation submissions yet.', 'smart-pharmacy-eligibility' ); ?></td></tr>
				<?php else : ?>
					<?php foreach ( $rows as $row ) : ?>
						<tr>
							<td><?php echo esc_html( mysql2date( 'Y-m-d H:i', $row->created_at ) ); ?></td>
							<td><?php echo esc_html( $row->who_for ); ?></td>
							<td><?php echo esc_html( $row->dob ); ?></td>
							<td>
								<?php
								if ( $row->product_id ) {
									echo esc_html( get_the_title( (int) $row->product_id ) );
								} else {
									echo '&mdash;';
								}
								?>
							</td>
							<td>
								<?php if ( $row->order_id ) : ?>
									<a href="<?php echo esc_url( get_edit_post_link( (int) $row->order_id ) ?: admin_url( 'post.php?post=' . (int) $row->order_id . '&action=edit' ) ); ?>">#<?php echo (int) $row->order_id; ?></a>
								<?php else : ?>
									&mdash;
								<?php endif; ?>
							</td>
							<td><a href="<?php echo esc_url( add_query_arg( array( 'page' => 'spe-consultations', 'consultation' => $row->consultation_id ), admin_url( 'admin.php' ) ) ); ?>"><?php esc_html_e( 'View answers', 'smart-pharmacy-eligibility' ); ?></a></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
			</table>
			<p style="margin-top:12px;color:#6b7280;"><?php esc_html_e( 'Showing the 100 most recent. Once payment is wired, these also attach to their order and show in the Clinical Review panel there.', 'smart-pharmacy-eligibility' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Single submission: all answers, labelled.
	 *
	 * @param string $uuid
	 */
	protected static function render_detail( $uuid ) {
		$row = class_exists( 'SPE_Consultation_Repo' ) ? SPE_Consultation_Repo::find( $uuid ) : null;
		$back = add_query_arg( array( 'page' => 'spe-consultations' ), admin_url( 'admin.php' ) );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Consultation', 'smart-pharmacy-eligibility' ); ?></h1>
			<p><a href="<?php echo esc_url( $back ); ?>">&larr; <?php esc_html_e( 'Back to all consultations', 'smart-pharmacy-eligibility' ); ?></a></p>
			<?php if ( ! $row ) : ?>
				<p><?php esc_html_e( 'Consultation not found.', 'smart-pharmacy-eligibility' ); ?></p>
			<?php else : ?>
				<?php
				$answers = SPE_Consultation_Repo::answers( $row );
				$labels  = array();
				$questions = SPE_Consultation_Questions::get_questions( array( 'include_disabled' => true, 'product_id' => (int) $row->product_id ) );
				foreach ( $questions as $q ) {
					$labels[ $q['key'] ] = $q['label'];
				}
				?>
				<table class="widefat striped" style="max-width:800px;">
					<tbody>
						<tr><th style="width:220px;"><?php esc_html_e( 'Submitted', 'smart-pharmacy-eligibility' ); ?></th><td><?php echo esc_html( mysql2date( 'Y-m-d H:i', $row->created_at ) ); ?></td></tr>
						<?php if ( $row->product_id ) : ?><tr><th><?php esc_html_e( 'Product', 'smart-pharmacy-eligibility' ); ?></th><td><?php echo esc_html( get_the_title( (int) $row->product_id ) ); ?></td></tr><?php endif; ?>
						<?php foreach ( $answers as $key => $value ) : ?>
							<tr>
								<th style="text-align:left;"><?php echo esc_html( isset( $labels[ $key ] ) ? $labels[ $key ] : $key ); ?></th>
								<td><?php echo esc_html( is_array( $value ) ? implode( ', ', $value ) : (string) $value ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Register the three options the editor saves.
	 */
	public static function register_settings() {
		register_setting(
			self::SETTINGS_PAGE,
			'spe_consultation_questions',
			array(
				'sanitize_callback' => array( __CLASS__, 'sanitise_questions' ),
				'default'           => array(),
			)
		);
		register_setting(
			self::SETTINGS_PAGE,
			'spe_consultation_intro',
			array( 'sanitize_callback' => 'sanitize_textarea_field', 'default' => '' )
		);
		register_setting(
			self::SETTINGS_PAGE,
			'spe_consultation_disclaimer',
			array( 'sanitize_callback' => 'sanitize_textarea_field', 'default' => '' )
		);
	}

	/**
	 * Sanitise the posted question overrides.
	 *
	 * Iterates the CODE default keys (never trusts arbitrary keys from
	 * the request) and keeps only the editable fields. Unchecked
	 * checkboxes simply aren't posted, so they resolve to 0.
	 *
	 * @param mixed $input
	 * @return array
	 */
	public static function sanitise_questions( $input ) {
		$defaults = SPE_Consultation_Questions::defaults();
		if ( ! is_array( $input ) ) {
			return array();
		}

		$clean = array();
		foreach ( $defaults as $key => $def ) {
			$in  = isset( $input[ $key ] ) && is_array( $input[ $key ] ) ? $input[ $key ] : array();
			$row = array(
				'label'    => isset( $in['label'] ) && '' !== trim( $in['label'] )
					? sanitize_text_field( $in['label'] )
					: $def['label'],
				'help'     => isset( $in['help'] ) ? sanitize_textarea_field( $in['help'] ) : $def['help'],
				'required' => empty( $in['required'] ) ? 0 : 1,
				'enabled'  => empty( $in['enabled'] ) ? 0 : 1,
				'order'    => isset( $in['order'] ) ? (int) $in['order'] : (int) $def['order'],
			);

			if ( in_array( $def['type'], array( 'radio', 'select', 'checkbox' ), true ) ) {
				$opts = isset( $in['options'] ) ? self::parse_options( $in['options'] ) : array();
				// A choice question must always have options — fall back
				// to the code defaults rather than save an unusable field.
				$row['options'] = $opts ? $opts : $def['options'];
			}

			$clean[ $key ] = $row;
		}

		return $clean;
	}

	/**
	 * Split a textarea (one option per line) into a clean array.
	 *
	 * @param string $raw
	 * @return string[]
	 */
	protected static function parse_options( $raw ) {
		$lines = preg_split( '/\r\n|\r|\n/', (string) $raw );
		$out   = array();
		foreach ( $lines as $line ) {
			$line = sanitize_text_field( trim( $line ) );
			if ( '' !== $line ) {
				$out[] = $line;
			}
		}
		return $out;
	}

	/**
	 * Render the editor screen.
	 */
	public static function render() {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die( esc_html__( 'You do not have permission to view this page.', 'smart-pharmacy-eligibility' ) );
		}

		$questions = SPE_Consultation_Questions::get_questions( array( 'include_disabled' => true ) );
		?>
		<div class="wrap spe-consult-admin">
			<h1><?php esc_html_e( 'Consultation Form', 'smart-pharmacy-eligibility' ); ?></h1>
			<p><?php esc_html_e( 'The standard questions every P-medicine consultation asks. Reword them, turn any off, mark them required, or reorder them. Product-specific questions are added per product (on the product edit screen).', 'smart-pharmacy-eligibility' ); ?></p>

			<form method="post" action="options.php">
				<?php settings_fields( self::SETTINGS_PAGE ); ?>

				<h2><?php esc_html_e( 'Intro text', 'smart-pharmacy-eligibility' ); ?></h2>
				<textarea name="spe_consultation_intro" class="large-text" rows="2"><?php echo esc_textarea( SPE_Consultation_Questions::get_copy( 'intro' ) ); ?></textarea>

				<h2 style="margin-top:24px;"><?php esc_html_e( 'Questions', 'smart-pharmacy-eligibility' ); ?></h2>

				<?php foreach ( $questions as $q ) : ?>
					<?php
					$key      = $q['key'];
					$base     = 'spe_consultation_questions[' . $key . ']';
					$is_choice = in_array( $q['type'], array( 'radio', 'select', 'checkbox' ), true );
					?>
					<div class="spe-q-card" style="background:#fff;border:1px solid #dcdcde;border-radius:6px;padding:16px 18px;margin-bottom:14px;">
						<p style="margin:0 0 10px;color:#646970;">
							<code><?php echo esc_html( $key ); ?></code>
							&middot; <?php echo esc_html( $q['type'] ); ?>
						</p>

						<table class="form-table" role="presentation" style="margin-top:0;">
							<tr>
								<th scope="row"><label><?php esc_html_e( 'Question text', 'smart-pharmacy-eligibility' ); ?></label></th>
								<td><input type="text" name="<?php echo esc_attr( $base . '[label]' ); ?>" value="<?php echo esc_attr( $q['label'] ); ?>" class="large-text" /></td>
							</tr>
							<tr>
								<th scope="row"><label><?php esc_html_e( 'Help text', 'smart-pharmacy-eligibility' ); ?></label></th>
								<td><textarea name="<?php echo esc_attr( $base . '[help]' ); ?>" class="large-text" rows="2"><?php echo esc_textarea( $q['help'] ); ?></textarea></td>
							</tr>
							<?php if ( $is_choice ) : ?>
								<tr>
									<th scope="row"><label><?php esc_html_e( 'Options (one per line)', 'smart-pharmacy-eligibility' ); ?></label></th>
									<td><textarea name="<?php echo esc_attr( $base . '[options]' ); ?>" class="large-text" rows="3"><?php echo esc_textarea( implode( "\n", (array) $q['options'] ) ); ?></textarea></td>
								</tr>
							<?php endif; ?>
							<tr>
								<th scope="row"><?php esc_html_e( 'Settings', 'smart-pharmacy-eligibility' ); ?></th>
								<td>
									<label style="margin-right:18px;">
										<input type="checkbox" name="<?php echo esc_attr( $base . '[enabled]' ); ?>" value="1" <?php checked( ! empty( $q['enabled'] ) ); ?> />
										<?php esc_html_e( 'Show this question', 'smart-pharmacy-eligibility' ); ?>
									</label>
									<label style="margin-right:18px;">
										<input type="checkbox" name="<?php echo esc_attr( $base . '[required]' ); ?>" value="1" <?php checked( ! empty( $q['required'] ) ); ?> />
										<?php esc_html_e( 'Required', 'smart-pharmacy-eligibility' ); ?>
									</label>
									<label>
										<?php esc_html_e( 'Order', 'smart-pharmacy-eligibility' ); ?>
										<input type="number" name="<?php echo esc_attr( $base . '[order]' ); ?>" value="<?php echo esc_attr( $q['order'] ); ?>" class="small-text" />
									</label>
								</td>
							</tr>
						</table>
					</div>
				<?php endforeach; ?>

				<h2 style="margin-top:24px;"><?php esc_html_e( 'Pharmacist disclaimer', 'smart-pharmacy-eligibility' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Shown at the end of the form, above the submit button.', 'smart-pharmacy-eligibility' ); ?></p>
				<textarea name="spe_consultation_disclaimer" class="large-text" rows="2"><?php echo esc_textarea( SPE_Consultation_Questions::get_copy( 'disclaimer' ) ); ?></textarea>

				<?php submit_button(); ?>
			</form>

			<h2 style="margin-top:40px;"><?php esc_html_e( 'How to embed the form', 'smart-pharmacy-eligibility' ); ?></h2>
			<p><?php esc_html_e( 'Create a page (suggested slug /start-consultation/) and add this shortcode:', 'smart-pharmacy-eligibility' ); ?></p>
			<pre style="background:#f3f4f6;padding:12px;border-radius:6px;">[smart_pharmacy_consultation]</pre>
			<p><?php esc_html_e( 'The "Start Consultation" button on a P-medicine product can pass its product id with ?product=123 so the consultation is linked to that product.', 'smart-pharmacy-eligibility' ); ?></p>
		</div>
		<?php
	}
}
