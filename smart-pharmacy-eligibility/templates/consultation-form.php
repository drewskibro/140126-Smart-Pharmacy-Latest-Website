<?php
/**
 * P-med consultation form markup, rendered by
 * [smart_pharmacy_consultation].
 *
 * Expects (from SPE_Consultation_Shortcode::render):
 *   $questions  array[]  Ordered, enabled question records.
 *   $intro      string   Editable intro copy.
 *   $disclaimer string   Editable pharmacist disclaimer.
 *   $product_id int      Product the consultation is for (0 if none).
 *
 * @package SmartPharmacyEligibility
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="spe-consult">
	<div class="spe-consult__body">
		<?php if ( '' !== trim( (string) $intro ) ) : ?>
			<p class="spe-consult__intro"><?php echo esc_html( $intro ); ?></p>
		<?php endif; ?>

		<?php if ( empty( $questions ) ) : ?>
			<p class="spe-consult__intro"><?php esc_html_e( 'No consultation questions are configured yet.', 'smart-pharmacy-eligibility' ); ?></p>
		<?php else : ?>

		<form class="spe-consult__form" novalidate>
			<?php foreach ( $questions as $q ) : ?>
				<?php
				$key      = $q['key'];
				$type     = $q['type'];
				$id       = 'spe-q-' . sanitize_html_class( $key );
				$name     = 'spe-q-' . $key;
				$required = ! empty( $q['required'] );
				?>
				<div class="spe-consult__field"
					data-key="<?php echo esc_attr( $key ); ?>"
					data-type="<?php echo esc_attr( $type ); ?>"
					data-required="<?php echo $required ? '1' : '0'; ?>">

					<label class="spe-consult__label" for="<?php echo esc_attr( $id ); ?>">
						<?php echo esc_html( $q['label'] ); ?>
						<?php if ( $required ) : ?>
							<span class="spe-consult__req" aria-hidden="true">*</span>
						<?php endif; ?>
					</label>

					<?php if ( '' !== trim( (string) $q['help'] ) ) : ?>
						<p class="spe-consult__help" id="<?php echo esc_attr( $id . '-help' ); ?>"><?php echo esc_html( $q['help'] ); ?></p>
					<?php endif; ?>

					<?php
					switch ( $type ) :
						case 'textarea':
							?>
							<textarea id="<?php echo esc_attr( $id ); ?>"
								class="spe-consult__textarea"
								rows="3"
								<?php echo $required ? 'aria-required="true"' : ''; ?>></textarea>
							<?php
							break;

						case 'date':
							?>
							<input type="date" id="<?php echo esc_attr( $id ); ?>"
								class="spe-consult__input"
								max="<?php echo esc_attr( gmdate( 'Y-m-d' ) ); ?>"
								<?php echo $required ? 'aria-required="true"' : ''; ?> />
							<?php
							break;

						case 'select':
							?>
							<select id="<?php echo esc_attr( $id ); ?>" class="spe-consult__select" <?php echo $required ? 'aria-required="true"' : ''; ?>>
								<option value=""><?php esc_html_e( 'Please choose…', 'smart-pharmacy-eligibility' ); ?></option>
								<?php foreach ( (array) $q['options'] as $opt ) : ?>
									<option value="<?php echo esc_attr( $opt ); ?>"><?php echo esc_html( $opt ); ?></option>
								<?php endforeach; ?>
							</select>
							<?php
							break;

						case 'radio':
						case 'checkbox':
							$input_type = ( 'radio' === $type ) ? 'radio' : 'checkbox';
							?>
							<div class="spe-consult__options" role="group" aria-labelledby="<?php echo esc_attr( $id ); ?>">
								<?php foreach ( (array) $q['options'] as $i => $opt ) : ?>
									<label class="spe-consult__option">
										<input type="<?php echo esc_attr( $input_type ); ?>"
											name="<?php echo esc_attr( $name ); ?>"
											value="<?php echo esc_attr( $opt ); ?>"
											<?php echo ( 0 === $i ) ? 'id="' . esc_attr( $id ) . '"' : ''; ?> />
										<span><?php echo esc_html( $opt ); ?></span>
									</label>
								<?php endforeach; ?>
							</div>
							<?php
							break;

						default: // text and anything else.
							?>
							<input type="text" id="<?php echo esc_attr( $id ); ?>"
								class="spe-consult__input"
								<?php echo $required ? 'aria-required="true"' : ''; ?> />
							<?php
					endswitch;
					?>

					<p class="spe-consult__error" hidden aria-live="polite"></p>
				</div>
			<?php endforeach; ?>

			<?php if ( '' !== trim( (string) $disclaimer ) ) : ?>
				<div class="spe-consult__disclaimer">
					<p><?php echo esc_html( $disclaimer ); ?></p>
				</div>
			<?php endif; ?>

			<input type="hidden" class="spe-consult__product" value="<?php echo esc_attr( (int) $product_id ); ?>" />

			<p class="spe-consult__form-error" role="alert" hidden></p>

			<button type="submit" class="spe-consult__submit">
				<?php esc_html_e( 'Submit consultation', 'smart-pharmacy-eligibility' ); ?>
			</button>
		</form>

		<div class="spe-consult__confirmation" hidden>
			<div class="spe-consult__confirmation-icon" aria-hidden="true">✓</div>
			<h2><?php esc_html_e( 'Consultation submitted', 'smart-pharmacy-eligibility' ); ?></h2>
			<p class="spe-consult__confirmation-msg"></p>
		</div>

		<?php endif; ?>
	</div>
</div>
