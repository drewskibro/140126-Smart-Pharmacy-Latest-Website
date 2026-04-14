<?php
/**
 * Homepage — Most Trusted Treatments.
 *
 * Asymmetric 12-column grid: first card = 7 columns (tall, left) with full
 * testimonial + price. Next two cards stack on the right (5 columns,
 * shorter) with abbreviated testimonials.
 *
 * Editable via Theme Settings → Homepage → A7 — Most Trusted Treatments.
 *
 * @package SmartPharmacy
 */

if ( ! (bool) sp_field( 'mt_enabled', 1 ) ) {
	return;
}

$mt_score       = sp_field( 'mt_rating_score', '4.8/5' );
$mt_count       = (int) sp_field( 'mt_rating_count', 6093 );
$mt_h_pre       = sp_field( 'mt_heading_pre', 'Our most' );
$mt_h_hi        = sp_field( 'mt_heading_highlight', 'trusted' );
$mt_h_post      = sp_field( 'mt_heading_post', 'treatments' );
$mt_sub         = sp_field( 'mt_subheading', 'Dispensed by UK-based Pharmacists, delivered with care' );
$mt_bottom_l    = sp_field( 'mt_bottom_cta_label', 'Browse All Treatments' );
$mt_bottom_u    = sp_field( 'mt_bottom_cta_url', '/shop/' );

$mt_cards = sp_field( 'mt_cards', array() );
if ( empty( $mt_cards ) || ! is_array( $mt_cards ) ) {
	$mt_cards = array(
		array(
			'image' => null, 'badge' => 'Most Popular', 'category' => 'Weight Management', 'title' => 'Latest Weight Loss Products',
			'quote' => 'Lost 15kg in 3 months. Life-changing results with professional support throughout.',
			'quote_author' => 'Sarah M.', 'price' => '£199', 'price_unit' => '/month',
			'cta_label' => 'Get Started →', 'cta_url' => '/treatments/weight-loss/',
		),
		array(
			'image' => null, 'badge' => 'Best Value', 'category' => 'Hair Loss', 'title' => 'Hair Loss Treatment',
			'quote' => 'Visible regrowth in 4 months. Confidence restored. Couldn\'t be happier with the service.',
			'quote_author' => '', 'price' => '£45', 'price_unit' => '/month',
			'cta_label' => 'View →', 'cta_url' => '/treatments/hair-loss/',
		),
		array(
			'image' => null, 'badge' => 'Bestseller', 'category' => "Men's Health", 'title' => 'Erectile Dysfunction Treatment',
			'quote' => 'Discreet, fast delivery. Works perfectly. The consultation was professional and easy.',
			'quote_author' => '', 'price' => '£2.50', 'price_unit' => '/tablet',
			'cta_label' => 'View →', 'cta_url' => '/treatments/erectile-dysfunction/',
		),
	);
}

// Split: first card is the large one, remaining cards stack.
$mt_big  = array_shift( $mt_cards );
$mt_rest = $mt_cards;

$sp_render_card_bg = function( $card ) {
	$img = isset( $card['image'] ) ? $card['image'] : null;
	if ( is_array( $img ) && ! empty( $img['url'] ) ) {
		$alt = ! empty( $img['alt'] ) ? $img['alt'] : ( $card['title'] ?? '' );
		return '<img src="' . esc_url( $img['url'] ) . '" alt="' . esc_attr( $alt ) . '" class="absolute box-border h-full max-w-full object-cover break-words w-full inset-0 transition-transform duration-700 group-hover:scale-110" />';
	}
	return '<div class="absolute inset-0 bg-[linear-gradient(to_bottom_right,rgb(59,155,159),rgb(44,122,126))]" aria-hidden="true"></div>';
};
?>

<section class="relative bg-white box-border break-words overflow-hidden py-12 md:py-16">
	<div class="absolute bg-teal-500/10 box-border blur-3xl h-96 break-words w-96 rounded-full -left-20 -top-20" aria-hidden="true"></div>
	<div class="absolute bg-teal-700/10 box-border blur-3xl h-[500px] break-words w-[500px] rounded-full -right-20 bottom-40" aria-hidden="true"></div>

	<div class="relative box-border max-w-[1600px] break-words z-10 mx-auto px-6 md:px-16">

		<!-- Heading -->
		<div class="box-border max-w-4xl break-words text-center mb-16 mx-auto">
			<div class="items-center backdrop-blur-sm bg-white/80 shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border gap-x-3 inline-flex break-words gap-y-3 border border-gray-100 mb-8 px-6 py-3 rounded-full border-solid">
				<div class="box-border flex break-words" aria-hidden="true">
					<?php for ( $i = 0; $i < 5; $i++ ) { echo sp_icon( 'star', 'text-teal-500 h-4 w-4' ); } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<span class="text-neutral-900 text-base font-bold box-border block leading-6 break-words ml-2"><span class="text-teal-500"><?php echo esc_html( $mt_score ); ?></span> from <span class="counter" data-target="<?php echo (int) $mt_count; ?>">0</span> reviews</span>
				<div class="bg-teal-500 box-border h-2 break-words w-2 rounded-full"></div>
			</div>

			<h2 class="ultra-heading text-neutral-900 text-5xl font-black box-border leading-[1.1] break-words mb-6 md:text-7xl">
				<?php echo esc_html( $mt_h_pre ); ?>
				<?php if ( $mt_h_hi ) : ?>
					<span class="relative inline-block">
						<span class="text-transparent bg-clip-text bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))]"><?php echo esc_html( $mt_h_hi ); ?></span>
					</span>
				<?php endif; ?>
				<?php if ( $mt_h_post ) : ?> <?php echo esc_html( $mt_h_post ); ?><?php endif; ?>
			</h2>

			<p class="text-neutral-600 text-xl box-border leading-[1.6] break-words md:text-2xl md:leading-[1.65] font-medium"><?php echo esc_html( $mt_sub ); ?></p>

			<div class="items-center box-border gap-x-3 flex justify-center break-words gap-y-3 mt-6" aria-hidden="true">
				<div class="bg-[linear-gradient(to_right,rgba(0,0,0,0),rgba(59,155,159,0.3))] box-border h-px break-words w-16"></div>
				<div class="bg-teal-500/30 box-border h-2 break-words w-2 rounded-full"></div>
				<div class="bg-[linear-gradient(to_left,rgba(0,0,0,0),rgba(59,155,159,0.3))] box-border h-px break-words w-16"></div>
			</div>
		</div>

		<!-- Asymmetric grid -->
		<div class="box-border gap-x-8 grid grid-cols-[repeat(1,minmax(0px,1fr))] break-words gap-y-8 mb-12 md:grid-cols-[repeat(12,minmax(0px,1fr))]">

			<!-- BIG CARD (left, 7 cols) -->
			<?php if ( $mt_big ) :
				$big_url = isset( $mt_big['cta_url'] ) && $mt_big['cta_url'] ? $mt_big['cta_url'] : '#';
				?>
				<div class="box-border break-words md:col-end-[span_7] md:col-start-[span_7]">
					<a href="<?php echo esc_url( $big_url ); ?>" class="treatment-card-hover relative shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.25)_0px_25px_50px_-12px] box-border h-full min-h-[600px] break-words border border-gray-100 overflow-hidden rounded-3xl border-solid md:min-h-[700px] group block">
						<?php echo $sp_render_card_bg( $mt_big ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<div class="absolute bg-[linear-gradient(to_top,rgba(0,0,0,0.9),rgba(0,0,0,0.5),rgba(0,0,0,0.2))] box-border break-words inset-0" aria-hidden="true"></div>
						<div class="absolute inset-0 bg-gradient-to-t from-teal-600/95 via-teal-500/60 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 flex items-end justify-center pb-12" aria-hidden="true">
							<div class="transform translate-y-8 group-hover:translate-y-0 transition-transform duration-500">
								<span class="bg-white text-teal-600 font-black text-lg px-10 py-4 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 antialiased inline-block"><?php esc_html_e( 'View Treatment →', 'smart-pharmacy' ); ?></span>
							</div>
						</div>

						<?php if ( ! empty( $mt_big['badge'] ) ) : ?>
							<div class="absolute box-border break-words left-6 top-6">
								<span class="text-white text-sm font-bold bg-[#10c0a9] shadow-[rgba(16,192,169,0.4)_0px_8px_24px] box-border inline-block leading-5 break-words px-4 py-2 rounded-full antialiased"><?php echo esc_html( $mt_big['badge'] ); ?></span>
							</div>
						<?php endif; ?>

						<div class="absolute box-border break-words p-8 bottom-0 inset-x-0 md:p-12">
							<?php if ( ! empty( $mt_big['category'] ) ) : ?>
								<div class="backdrop-blur-sm bg-white/20 box-border inline-block break-words border mb-4 px-4 py-2 rounded-full border-solid border-white/30">
									<p class="text-white text-sm font-bold box-border tracking-[0.7px] leading-5 break-words uppercase antialiased"><?php echo esc_html( $mt_big['category'] ); ?></p>
								</div>
							<?php endif; ?>

							<h3 class="ultra-heading text-white text-4xl font-black box-border leading-[1.1] break-words mb-6 md:text-5xl"><?php echo esc_html( $mt_big['title'] ?? '' ); ?></h3>

							<?php if ( ! empty( $mt_big['quote'] ) ) : ?>
								<div class="backdrop-blur-xl bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.25)_0px_25px_50px_-12px] box-border break-words border mb-8 p-8 rounded-2xl border-solid border-white/30">
									<div class="box-border flex break-words mb-3" aria-hidden="true">
										<?php for ( $i = 0; $i < 5; $i++ ) { echo sp_icon( 'star', 'text-teal-500 h-4 w-4' ); } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
									<p class="serif-italic text-neutral-800 text-lg box-border leading-[1.65] break-words mb-2 antialiased">&ldquo;<?php echo esc_html( $mt_big['quote'] ); ?>&rdquo;</p>
									<?php if ( ! empty( $mt_big['quote_author'] ) ) : ?>
										<p class="text-sm font-bold box-border leading-5 break-words antialiased">— <?php echo esc_html( $mt_big['quote_author'] ); ?></p>
									<?php endif; ?>
								</div>
							<?php endif; ?>

							<div class="items-center box-border flex justify-between break-words">
								<div class="box-border break-words">
									<?php if ( ! empty( $mt_big['price'] ) ) : ?>
										<span class="text-white text-4xl font-black box-border leading-10 break-words antialiased"><?php echo esc_html( $mt_big['price'] ); ?></span>
										<span class="text-white/80 text-lg box-border leading-7 break-words antialiased"><?php echo esc_html( $mt_big['price_unit'] ?? '' ); ?></span>
									<?php endif; ?>
								</div>
								<span class="text-neutral-900 font-bold bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] block break-words text-center px-8 py-4 rounded-full hover:bg-gray-100 transition-all duration-300 antialiased"><?php echo esc_html( $mt_big['cta_label'] ?? 'View →' ); ?></span>
							</div>
						</div>
					</a>
				</div>
			<?php endif; ?>

			<!-- STACKED CARDS (right, 5 cols) -->
			<div class="box-border gap-x-8 flex flex-col break-words gap-y-8 md:col-end-[span_5] md:col-start-[span_5]">
				<?php foreach ( $mt_rest as $sp_card ) :
					$sp_url = isset( $sp_card['cta_url'] ) && $sp_card['cta_url'] ? $sp_card['cta_url'] : '#';
					?>
					<div class="box-border basis-[0%] grow break-words">
						<a href="<?php echo esc_url( $sp_url ); ?>" class="treatment-card-hover relative shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_20px_25px_-5px,rgba(0,0,0,0.1)_0px_8px_10px_-6px] box-border h-full min-h-80 break-words border border-gray-100 overflow-hidden rounded-3xl border-solid group block">
							<?php echo $sp_render_card_bg( $sp_card ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<div class="absolute bg-[linear-gradient(to_top,rgba(0,0,0,0.9),rgba(0,0,0,0.5),rgba(0,0,0,0.2))] box-border break-words inset-0" aria-hidden="true"></div>

							<div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 flex items-center justify-center" aria-hidden="true">
								<div class="transform scale-90 opacity-0 group-hover:scale-100 group-hover:opacity-100 transition-all duration-500">
									<span class="bg-white text-teal-600 font-black text-lg px-8 py-4 rounded-full shadow-2xl hover:shadow-[0_20px_50px_rgba(0,0,0,0.3)] transition-all duration-300 hover:scale-110 antialiased inline-block"><?php esc_html_e( 'View Treatment →', 'smart-pharmacy' ); ?></span>
								</div>
							</div>

							<?php if ( ! empty( $sp_card['badge'] ) ) : ?>
								<div class="absolute box-border break-words left-4 top-4">
									<span class="text-white text-xs font-bold bg-[#10c0a9] shadow-[rgba(16,192,169,0.4)_0px_8px_24px] box-border inline-block leading-4 break-words px-3 py-1.5 rounded-full antialiased"><?php echo esc_html( $sp_card['badge'] ); ?></span>
								</div>
							<?php endif; ?>

							<div class="absolute box-border break-words p-6 bottom-0 inset-x-0">
								<?php if ( ! empty( $sp_card['category'] ) ) : ?>
									<div class="backdrop-blur-sm bg-white/20 box-border inline-block break-words border mb-3 px-3 py-1.5 rounded-full border-solid border-white/30">
										<p class="text-white text-xs font-bold box-border tracking-[0.6px] leading-4 break-words uppercase antialiased"><?php echo esc_html( $sp_card['category'] ); ?></p>
									</div>
								<?php endif; ?>

								<h3 class="ultra-heading text-white text-2xl font-black box-border leading-[1.2] break-words mb-4"><?php echo esc_html( $sp_card['title'] ?? '' ); ?></h3>

								<?php if ( ! empty( $sp_card['quote'] ) ) : ?>
									<div class="backdrop-blur-xl bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_20px_25px_-5px,rgba(0,0,0,0.1)_0px_8px_10px_-6px] box-border break-words border mb-4 p-5 rounded-xl border-solid border-white/30">
										<div class="box-border flex break-words mb-2" aria-hidden="true">
											<?php for ( $i = 0; $i < 5; $i++ ) { echo sp_icon( 'star', 'text-teal-500 h-3 w-3' ); } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										</div>
										<p class="serif-italic text-neutral-800 text-sm box-border leading-[1.65] break-words antialiased">&ldquo;<?php echo esc_html( $sp_card['quote'] ); ?>&rdquo;</p>
									</div>
								<?php endif; ?>

								<div class="items-center box-border flex justify-between break-words">
									<div class="box-border break-words">
										<?php if ( ! empty( $sp_card['price'] ) ) : ?>
											<span class="text-white text-2xl font-black box-border leading-8 break-words antialiased"><?php echo esc_html( $sp_card['price'] ); ?></span>
											<span class="text-white/80 text-sm box-border leading-5 break-words antialiased"><?php echo esc_html( $sp_card['price_unit'] ?? '' ); ?></span>
										<?php endif; ?>
									</div>
									<span class="text-neutral-900 text-sm font-semibold bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] block leading-5 break-words text-center px-6 py-3 rounded-full hover:bg-gray-100 transition-all duration-300 antialiased"><?php echo esc_html( $sp_card['cta_label'] ?? 'View →' ); ?></span>
								</div>
							</div>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- Bottom CTA -->
		<div class="box-border break-words text-center">
			<a href="<?php echo esc_url( $mt_bottom_u ); ?>" class="premium-button text-white text-lg font-bold items-center bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_20px_25px_-5px,rgba(0,0,0,0.1)_0px_8px_10px_-6px] box-border gap-x-3 inline-flex leading-7 break-words gap-y-3 px-12 py-6 rounded-full hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.25)_0px_25px_50px_-12px] transition-all duration-300 antialiased">
				<?php echo esc_html( $mt_bottom_l ); ?>
				<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</a>
		</div>
	</div>
</section>
