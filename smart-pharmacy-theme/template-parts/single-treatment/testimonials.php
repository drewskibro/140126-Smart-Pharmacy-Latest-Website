<?php
/**
 * Single Treatment — Results / Testimonials.
 *
 * Social-proof section: rating summary pill, gradient heading, 3-card
 * testimonial grid (image, per-testimonial star rating, italic editorial
 * quote, name, outcome meta). Matches the prototype "Real results. Real
 * people." section of weight-loss.html.
 *
 * Editable via the Treatment post edit screen → C2 — Treatment Results / Testimonials.
 *
 * @package SmartPharmacy
 */

if ( ! (bool) sp_field( 'tx_tst_enabled', 1 ) ) {
	return;
}

$tx_rating_score = sp_field( 'tx_tst_rating_score', '4.9/5' );
$tx_rating_count = sp_field( 'tx_tst_rating_count', 'from 3,847 reviews' );
$tx_h_pre        = sp_field( 'tx_tst_heading_pre', 'Real results.' );
$tx_h_hi         = sp_field( 'tx_tst_heading_highlight', 'Real people.' );
$tx_sub          = sp_field( 'tx_tst_subheading', 'See what our patients have achieved with Smart Pharmacy' );

$tx_items = sp_field( 'tx_tst_items', array() );
if ( empty( $tx_items ) || ! is_array( $tx_items ) ) {
	// Graceful fallback: 3 placeholder cards so the section renders
	// before editors upload real patient photos + quotes.
	$tx_items = array(
		array(
			'image'  => null,
			'rating' => 5,
			'quote'  => 'Lost 32 lbs in 4 months with Wegovy. The support from Smart Pharmacy has been incredible throughout my journey.',
			'name'   => 'Sarah M.',
			'meta'   => 'Lost 32 lbs • 4 months',
		),
		array(
			'image'  => null,
			'rating' => 5,
			'quote'  => 'Mounjaro changed my life. Down 45 lbs and feeling healthier than ever. The process was so easy and discreet.',
			'name'   => 'James T.',
			'meta'   => 'Lost 45 lbs • 6 months',
		),
		array(
			'image'  => null,
			'rating' => 5,
			'quote'  => 'Finally found something that works! Lost 28 lbs with Wegovy. The doctors were so supportive and understanding.',
			'name'   => 'Emma L.',
			'meta'   => 'Lost 28 lbs • 3 months',
		),
	);
}

// Responsive column count: 1 → single, 2 → 2-col, 3+ → 3-col (same
// heuristic used by B3 and C1).
$sp_tst_count = count( $tx_items );
if ( 1 === $sp_tst_count ) {
	$sp_tst_cols = '';
} elseif ( 2 === $sp_tst_count ) {
	$sp_tst_cols = 'md:grid-cols-[repeat(2,minmax(0px,1fr))]';
} else {
	$sp_tst_cols = 'md:grid-cols-[repeat(3,minmax(0px,1fr))]';
}

// Rating shown in the eyebrow pill: default 5 stars (prototype shows
// a solid 5-star row regardless of the numeric rating).
$sp_tst_header_stars = 5;
?>

<section class="relative bg-[linear-gradient(to_right_bottom,rgb(245,243,240),rgb(250,248,245),rgb(240,237,232))] box-border break-words overflow-hidden py-20 md:py-32">
	<div class="absolute bg-teal-500/10 box-border blur-3xl h-96 break-words w-96 rounded-full -left-20 -top-20" aria-hidden="true"></div>
	<div class="absolute bg-teal-700/10 box-border blur-3xl h-[500px] break-words w-[500px] rounded-full -right-20 top-40" aria-hidden="true"></div>

	<div class="relative box-border max-w-[1400px] break-words z-10 mx-auto px-8 md:px-24">
		<div class="box-border max-w-4xl break-words text-center mb-20 mx-auto">
			<?php if ( $tx_rating_score || $tx_rating_count ) : ?>
				<div class="items-center backdrop-blur-sm bg-white/80 shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border gap-x-3 inline-flex break-words gap-y-3 border border-gray-100 mb-8 px-6 py-3 rounded-full border-solid">
					<div class="box-border flex break-words" aria-hidden="true">
						<?php for ( $sp_s = 0; $sp_s < $sp_tst_header_stars; $sp_s++ ) :
							echo sp_icon( 'star', 'text-teal-500 h-4 w-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						endfor; ?>
					</div>
					<span class="text-neutral-900 text-base font-bold box-border block leading-6 break-words ml-2">
						<?php if ( $tx_rating_score ) : ?><span class="text-teal-500"><?php echo esc_html( $tx_rating_score ); ?></span> <?php endif; ?>
						<?php echo esc_html( $tx_rating_count ); ?>
					</span>
					<div class="bg-teal-500 box-border h-2 break-words w-2 rounded-full"></div>
				</div>
			<?php endif; ?>

			<h2 class="text-neutral-900 text-5xl font-black box-border tracking-[-1.2px] leading-[1.1] break-words mb-6 md:text-7xl md:tracking-[-1.8px]">
				<?php echo esc_html( $tx_h_pre ); ?>
				<?php if ( $tx_h_hi ) : ?>
					<span class="relative inline-block">
						<span class="text-transparent bg-clip-text bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))]"> <?php echo esc_html( $tx_h_hi ); ?></span>
					</span>
				<?php endif; ?>
			</h2>

			<?php if ( $tx_sub ) : ?>
				<p class="text-neutral-600 text-xl box-border leading-[32.5px] break-words md:text-2xl md:leading-8"><?php echo esc_html( $tx_sub ); ?></p>
			<?php endif; ?>

			<div class="items-center box-border gap-x-3 flex justify-center break-words gap-y-3 mt-8" aria-hidden="true">
				<div class="bg-[linear-gradient(to_right,rgba(0,0,0,0),rgba(59,155,159,0.3))] box-border h-px break-words w-16"></div>
				<div class="bg-teal-500/30 box-border h-2 break-words w-2 rounded-full"></div>
				<div class="bg-[linear-gradient(to_left,rgba(0,0,0,0),rgba(59,155,159,0.3))] box-border h-px break-words w-16"></div>
			</div>
		</div>

		<!-- Testimonial cards -->
		<div class="box-border gap-x-8 grid grid-cols-[repeat(1,minmax(0px,1fr))] break-words gap-y-8 <?php echo esc_attr( $sp_tst_cols ); ?>">
			<?php foreach ( $tx_items as $sp_t ) :
				$sp_img     = isset( $sp_t['image'] ) ? $sp_t['image'] : null;
				$sp_img_id  = ( is_array( $sp_img ) && ! empty( $sp_img['ID'] ) ) ? (int) $sp_img['ID'] : 0;
				$sp_img_url = ( is_array( $sp_img ) && ! empty( $sp_img['url'] ) ) ? $sp_img['url'] : '';
				$sp_quote   = isset( $sp_t['quote'] ) ? (string) $sp_t['quote'] : '';
				$sp_name    = isset( $sp_t['name'] ) ? (string) $sp_t['name'] : '';
				$sp_meta    = isset( $sp_t['meta'] ) ? (string) $sp_t['meta'] : '';
				$sp_rating  = isset( $sp_t['rating'] ) ? (int) $sp_t['rating'] : 5;
				if ( $sp_rating < 0 ) { $sp_rating = 0; }
				if ( $sp_rating > 5 ) { $sp_rating = 5; }
				$sp_img_alt = $sp_name ? $sp_name : '';
				?>
				<div class="relative bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_20px_25px_-5px,rgba(0,0,0,0.1)_0px_8px_10px_-6px] box-border flex flex-col break-words border border-gray-100 overflow-hidden rounded-3xl border-solid hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.25)_0px_25px_50px_-12px] transition-all duration-300 group">

					<?php if ( $sp_img_id || $sp_img_url ) : ?>
						<!-- Image banner with dark bottom overlay for depth -->
						<div class="relative box-border h-64 break-words overflow-hidden">
							<?php
							$sp_img_class = 'box-border w-full h-full object-cover break-words transition-transform duration-500 group-hover:scale-[1.03]';
							if ( $sp_img_id ) {
								echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									$sp_img_id,
									'large',
									false,
									array(
										'class'    => $sp_img_class,
										'alt'      => $sp_img_alt,
										'sizes'    => '(min-width: 768px) 33vw, 100vw',
										'loading'  => 'lazy',
										'decoding' => 'async',
									)
								);
							} else { ?>
								<img src="<?php echo esc_url( $sp_img_url ); ?>" alt="<?php echo esc_attr( $sp_img_alt ); ?>" class="<?php echo esc_attr( $sp_img_class ); ?>" loading="lazy" decoding="async" />
							<?php } ?>
							<div class="absolute bg-[linear-gradient(to_top,rgba(0,0,0,0.6),rgba(0,0,0,0))] box-border break-words inset-0" aria-hidden="true"></div>
						</div>
					<?php endif; ?>

					<div class="box-border break-words p-8">
						<?php if ( $sp_rating > 0 ) : ?>
							<div class="box-border flex break-words mb-4" aria-label="<?php echo esc_attr( sprintf( '%d out of 5 stars', $sp_rating ) ); ?>">
								<?php for ( $sp_i = 0; $sp_i < $sp_rating; $sp_i++ ) :
									echo sp_icon( 'star', 'text-teal-500 h-5 w-5' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								endfor; ?>
							</div>
						<?php endif; ?>

						<?php if ( $sp_quote ) : ?>
							<p class="text-neutral-800 text-lg italic font-serif box-border leading-[1.6] break-words mb-4">&ldquo;<?php echo esc_html( $sp_quote ); ?>&rdquo;</p>
						<?php endif; ?>

						<?php if ( $sp_name || $sp_meta ) : ?>
							<div class="box-border break-words border-gray-100 pt-4 border-t border-solid">
								<?php if ( $sp_name ) : ?>
									<p class="text-neutral-900 text-base font-bold box-border leading-6 break-words"><?php echo esc_html( $sp_name ); ?></p>
								<?php endif; ?>
								<?php if ( $sp_meta ) : ?>
									<p class="text-teal-600 text-sm font-bold box-border leading-5 break-words"><?php echo esc_html( $sp_meta ); ?></p>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
