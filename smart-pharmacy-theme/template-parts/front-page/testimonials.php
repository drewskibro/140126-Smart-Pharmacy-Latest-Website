<?php
/**
 * Homepage — Testimonials.
 *
 * Asymmetric 12-col grid: 1 big card (7 cols, 2 large stats) + 2 stacked
 * cards (5 cols, 2 small stats each). Each card is a dark image overlay
 * with a frosted white quote block + stat tiles.
 *
 * Editable via Theme Settings → Homepage → A9 — Testimonials.
 *
 * @package SmartPharmacy
 */

if ( ! (bool) sp_field( 'ts_enabled', 1 ) ) {
	return;
}

$ts_count  = (int) sp_field( 'ts_patient_count', 50000 );
$ts_h_pre  = sp_field( 'ts_heading_pre', 'Experience the' );
$ts_h_hi   = sp_field( 'ts_heading_highlight', 'difference' );
$ts_sub    = sp_field( 'ts_subheading', 'Real stories from real patients who chose Smart Pharmacy for their healthcare journey' );

$ts_cards = sp_field( 'ts_cards', array() );
if ( empty( $ts_cards ) || ! is_array( $ts_cards ) ) {
	$ts_cards = array(
		array(
			'image' => null, 'title' => 'Expert Care, Your Way',
			'quote' => 'The consultation was thorough yet quick. My doctor took time to understand my needs and prescribed the perfect treatment.',
			'author' => 'David L.',
			'stats' => array(
				array( 'value' => '100', 'animate' => 1, 'label' => 'UK Registered Doctors' ),
				array( 'value' => '5',   'animate' => 1, 'label' => 'min Average Consultation' ),
			),
		),
		array(
			'image' => null, 'title' => 'Delivered with Discretion',
			'quote' => "Plain packaging, next-day delivery. Nobody knows what's inside. Exactly what I needed.",
			'author' => 'Anonymous',
			'stats' => array(
				array( 'value' => '24hrs', 'animate' => 0, 'label' => 'Next Day Delivery' ),
				array( 'value' => '100',   'animate' => 1, 'label' => '% Discreet Packaging' ),
			),
		),
		array(
			'image' => null, 'title' => 'Trusted & Transparent',
			'quote' => 'No hidden fees, clear pricing, genuine medications. Finally, a pharmacy I can trust completely.',
			'author' => 'Rachel M.',
			'stats' => array(
				array( 'value' => '£0', 'animate' => 0, 'label' => 'Hidden Fees' ),
				array( 'value' => '50',  'animate' => 1, 'label' => 'k+ Happy Patients' ),
			),
		),
	);
}

// Split big card (first) vs stacked right (rest).
$ts_big  = array_shift( $ts_cards );
$ts_rest = $ts_cards;

$sp_render_ts_bg = function( $card ) {
	$img = isset( $card['image'] ) ? $card['image'] : null;
	if ( is_array( $img ) && ! empty( $img['url'] ) ) {
		$alt = ! empty( $img['alt'] ) ? $img['alt'] : ( $card['title'] ?? '' );
		return '<img src="' . esc_url( $img['url'] ) . '" alt="' . esc_attr( $alt ) . '" class="absolute box-border h-full max-w-full object-cover break-words w-full inset-0" />';
	}
	return '<div class="absolute inset-0 bg-[linear-gradient(to_bottom_right,rgb(59,155,159),rgb(44,122,126))]" aria-hidden="true"></div>';
};

$sp_render_stat = function( $stat, $is_big ) {
	$value   = isset( $stat['value'] ) ? (string) $stat['value'] : '';
	$animate = ! empty( $stat['animate'] ) && is_numeric( $value );
	$label   = isset( $stat['label'] ) ? (string) $stat['label'] : '';
	$vsize   = $is_big ? 'text-4xl leading-10 mb-2' : 'text-2xl leading-8 mb-1';
	$lsize   = $is_big ? 'text-sm tracking-[0.35px] leading-5' : 'text-xs tracking-[0.3px] leading-4';
	$padding = $is_big ? 'p-6 rounded-2xl' : 'p-4 rounded-xl';
	$shadow  = $is_big ? 'shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_20px_25px_-5px,rgba(0,0,0,0.1)_0px_8px_10px_-6px]' : 'shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px]';
	ob_start();
	?>
	<div class="bg-white <?php echo esc_attr( $shadow ); ?> box-border break-words border border-gray-100 <?php echo esc_attr( $padding ); ?> border-solid">
		<div class="text-transparent <?php echo esc_attr( $vsize ); ?> font-black bg-clip-text bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))] box-border break-words antialiased <?php echo $animate ? 'counter' : ''; ?>" <?php if ( $animate ) { echo 'data-target="' . esc_attr( (int) $value ) . '"'; } ?>><?php echo $animate ? '0' : esc_html( $value ); ?></div>
		<div class="<?php echo esc_attr( $lsize ); ?> font-bold box-border break-words uppercase antialiased"><?php echo esc_html( $label ); ?></div>
	</div>
	<?php
	return ob_get_clean();
};
?>

<section class="relative bg-white box-border break-words overflow-hidden py-12 md:py-16">
	<div class="absolute bg-teal-500/10 box-border blur-3xl h-96 break-words w-96 rounded-full -left-20 -top-20" aria-hidden="true"></div>
	<div class="absolute bg-teal-700/10 box-border blur-3xl h-[500px] break-words w-[500px] rounded-full -right-20 top-40" aria-hidden="true"></div>

	<div class="relative box-border max-w-[1400px] break-words z-10 mx-auto px-6 md:px-16">

		<!-- Eyebrow + heading -->
		<div class="box-border max-w-4xl break-words text-center mb-20 mx-auto">
			<div class="items-center backdrop-blur-sm bg-white/80 shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border gap-x-3 inline-flex break-words gap-y-3 border border-gray-100 mb-8 px-6 py-3 rounded-full border-solid">
				<div class="items-center bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] box-border flex h-10 justify-center break-words w-10 rounded-full">
					<?php echo sp_icon( 'sparkle', 'w-5 h-5 text-white' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<span class="text-neutral-900 text-base font-bold box-border block leading-6 break-words">Trusted by <span class="text-teal-500 counter" data-target="<?php echo (int) $ts_count; ?>">0</span>+ patients</span>
				<div class="bg-teal-500 box-border h-2 break-words w-2 rounded-full"></div>
			</div>

			<h2 class="ultra-heading text-neutral-900 text-5xl font-black box-border leading-[1.1] break-words mb-6 md:text-7xl">
				<?php echo esc_html( $ts_h_pre ); ?>
				<?php if ( $ts_h_hi ) : ?>
					<span class="relative inline-block">
						<span class="text-transparent bg-clip-text bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))]"><?php echo esc_html( $ts_h_hi ); ?></span>
					</span>
				<?php endif; ?>
			</h2>

			<p class="text-neutral-600 text-xl box-border leading-[1.6] break-words md:text-2xl md:leading-[1.65] font-medium"><?php echo esc_html( $ts_sub ); ?></p>

			<div class="items-center box-border gap-x-3 flex justify-center break-words gap-y-3 mt-8" aria-hidden="true">
				<div class="bg-[linear-gradient(to_right,rgba(0,0,0,0),rgba(59,155,159,0.3))] box-border h-px break-words w-16"></div>
				<div class="bg-teal-500/30 box-border h-2 break-words w-2 rounded-full"></div>
				<div class="bg-[linear-gradient(to_left,rgba(0,0,0,0),rgba(59,155,159,0.3))] box-border h-px break-words w-16"></div>
			</div>
		</div>

		<!-- Asymmetric grid -->
		<div class="box-border gap-x-8 grid grid-cols-[repeat(1,minmax(0px,1fr))] break-words gap-y-8 md:grid-cols-[repeat(12,minmax(0px,1fr))]">

			<!-- BIG CARD -->
			<?php if ( $ts_big ) : ?>
				<div class="box-border break-words md:col-end-[span_7] md:col-start-[span_7]">
					<div class="relative shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.25)_0px_25px_50px_-12px] box-border h-full min-h-[600px] break-words overflow-hidden rounded-3xl md:min-h-[700px]">
						<?php echo $sp_render_ts_bg( $ts_big ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<div class="absolute bg-[linear-gradient(to_top,rgba(0,0,0,0.9),rgba(0,0,0,0.5),rgba(0,0,0,0.2))] box-border break-words inset-0" aria-hidden="true"></div>

						<div class="absolute box-border break-words p-8 bottom-0 inset-x-0 md:p-12">
							<h3 class="ultra-heading text-white text-4xl font-black box-border leading-[1.1] break-words mb-6 md:text-5xl"><?php echo esc_html( $ts_big['title'] ?? '' ); ?></h3>

							<?php if ( ! empty( $ts_big['quote'] ) ) : ?>
								<div class="backdrop-blur-xl bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.25)_0px_25px_50px_-12px] box-border break-words border mb-8 p-8 rounded-2xl border-solid border-white/30">
									<div class="box-border flex break-words mb-4" aria-hidden="true">
										<?php for ( $i = 0; $i < 5; $i++ ) { echo sp_icon( 'star', 'text-teal-500 h-5 w-5' ); } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
									<p class="serif-italic text-neutral-800 text-xl box-border leading-[1.65] break-words mb-4 md:text-2xl antialiased">&ldquo;<?php echo esc_html( $ts_big['quote'] ); ?>&rdquo;</p>
									<?php if ( ! empty( $ts_big['author'] ) ) : ?>
										<p class="text-lg font-bold box-border leading-7 break-words antialiased">— <?php echo esc_html( $ts_big['author'] ); ?></p>
									<?php endif; ?>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $ts_big['stats'] ) && is_array( $ts_big['stats'] ) ) : ?>
								<div class="box-border gap-x-6 grid grid-cols-[repeat(2,minmax(0px,1fr))] break-words gap-y-6">
									<?php foreach ( $ts_big['stats'] as $sp_stat ) {
										echo $sp_render_stat( $sp_stat, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									} ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<!-- STACKED SMALL CARDS -->
			<div class="box-border gap-x-8 flex flex-col break-words gap-y-8 md:col-end-[span_5] md:col-start-[span_5]">
				<?php foreach ( $ts_rest as $sp_card ) : ?>
					<div class="box-border basis-[0%] grow break-words">
						<div class="relative shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_20px_25px_-5px,rgba(0,0,0,0.1)_0px_8px_10px_-6px] box-border h-full min-h-80 break-words overflow-hidden rounded-3xl">
							<?php echo $sp_render_ts_bg( $sp_card ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<div class="absolute bg-[linear-gradient(to_top,rgba(0,0,0,0.9),rgba(0,0,0,0.5),rgba(0,0,0,0.2))] box-border break-words inset-0" aria-hidden="true"></div>

							<div class="absolute box-border break-words p-6 bottom-0 inset-x-0">
								<h3 class="ultra-heading text-white text-2xl font-black box-border leading-[1.2] break-words mb-4"><?php echo esc_html( $sp_card['title'] ?? '' ); ?></h3>

								<?php if ( ! empty( $sp_card['quote'] ) ) : ?>
									<div class="backdrop-blur-xl bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_20px_25px_-5px,rgba(0,0,0,0.1)_0px_8px_10px_-6px] box-border break-words border mb-4 p-5 rounded-xl border-solid border-white/30">
										<div class="box-border flex break-words mb-2" aria-hidden="true">
											<?php for ( $i = 0; $i < 5; $i++ ) { echo sp_icon( 'star', 'text-teal-500 h-4 w-4' ); } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										</div>
										<p class="serif-italic text-neutral-800 text-base box-border leading-[1.65] break-words mb-2 antialiased">&ldquo;<?php echo esc_html( $sp_card['quote'] ); ?>&rdquo;</p>
										<?php if ( ! empty( $sp_card['author'] ) ) : ?>
											<p class="text-sm font-bold box-border leading-5 break-words antialiased">— <?php echo esc_html( $sp_card['author'] ); ?></p>
										<?php endif; ?>
									</div>
								<?php endif; ?>

								<?php if ( ! empty( $sp_card['stats'] ) && is_array( $sp_card['stats'] ) ) : ?>
									<div class="box-border gap-x-3 grid grid-cols-[repeat(2,minmax(0px,1fr))] break-words gap-y-3">
										<?php foreach ( $sp_card['stats'] as $sp_stat ) {
											echo $sp_render_stat( $sp_stat, false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										} ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

		</div>
	</div>
</section>
