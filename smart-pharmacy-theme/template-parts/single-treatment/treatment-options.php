<?php
/**
 * Single Treatment — Treatment Options / Pricing.
 *
 * Grid of pricing cards (best with 1-2, max 4). Each card: large gradient
 * icon square, name, optional top-right badge, bullet benefits, price,
 * full-width CTA button.
 *
 * STAGE 3a NOTE: data comes from an ACF repeater. Stage 4 will swap this
 * source for a real WC_Query (or an ACF Relationship to WC product IDs)
 * without changing the markup.
 *
 * Editable via the Treatment post edit screen → B3 — Treatment Options.
 *
 * @package SmartPharmacy
 */

if ( ! (bool) sp_field( 'tx_opts_enabled', 1 ) ) {
	return;
}

$tx_badge_pre = sp_field( 'tx_opts_badge_pre', 'Prescription' );
$tx_badge_hi  = sp_field( 'tx_opts_badge_highlight', 'Treatments' );
$tx_h_pre     = sp_field( 'tx_opts_heading_pre', '' );
$tx_h_hi      = sp_field( 'tx_opts_heading_highlight', '' );
$tx_sub       = sp_field( 'tx_opts_subheading', '' );

$tx_options = sp_field( 'tx_opts_items', array() );

// If no options yet, render nothing (treatment might not be ready to show pricing).
if ( empty( $tx_options ) || ! is_array( $tx_options ) ) {
	return;
}

// Map badge text keywords → background colour class, so the first badge
// matches the prototype ("Most Popular" orange, "New" green) without
// forcing editors to pick a colour.
$sp_tx_badge_colour = function( $text ) {
	$text = strtolower( trim( (string) $text ) );
	if ( str_contains( $text, 'popular' ) ) { return 'bg-orange-500'; }
	if ( str_contains( $text, 'new' ) )     { return 'bg-green-500'; }
	if ( str_contains( $text, 'best' ) )    { return 'bg-teal-500'; }
	if ( str_contains( $text, 'sale' ) )    { return 'bg-red-500'; }
	return 'bg-teal-500';
};

// Column count heuristic: 1 option = centered 1-col, 2+ = 2-col grid.
$sp_tx_cols = ( count( $tx_options ) === 1 ) ? '' : 'md:grid-cols-[repeat(2,minmax(0px,1fr))]';
?>

<section class="relative bg-[#F8FAFB] box-border break-words overflow-hidden py-16 md:py-24">
	<div class="absolute bg-teal-500/10 box-border blur-3xl h-96 break-words w-96 rounded-full -left-20 -top-20" aria-hidden="true"></div>
	<div class="absolute bg-teal-700/10 box-border blur-3xl h-[500px] break-words w-[500px] rounded-full -right-20 bottom-0" aria-hidden="true"></div>

	<div class="relative box-border max-w-[1400px] break-words z-10 mx-auto px-8 md:px-24">
		<div class="box-border break-words text-center mb-16">
			<div class="items-center backdrop-blur-sm bg-white/80 shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border gap-x-3 inline-flex break-words gap-y-3 border border-gray-100 mb-8 px-6 py-3 rounded-full border-solid">
				<div class="items-center bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] box-border flex h-10 justify-center break-words w-10 rounded-full">
					<?php echo sp_icon( 'document', 'w-5 h-5 text-white' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<span class="text-neutral-900 text-base font-bold box-border block leading-6 break-words">
					<?php echo esc_html( $tx_badge_pre ); ?>
					<?php if ( $tx_badge_hi ) : ?><span class="text-teal-500"><?php echo esc_html( $tx_badge_hi ); ?></span><?php endif; ?>
				</span>
				<div class="bg-teal-500 box-border h-2 break-words w-2 rounded-full"></div>
			</div>

			<h2 class="text-neutral-900 text-5xl font-black box-border tracking-[-1.2px] leading-[1.1] break-words mb-6 md:text-7xl md:tracking-[-1.8px]">
				<?php echo esc_html( $tx_h_pre ); ?>
				<?php if ( $tx_h_hi ) : ?>
					<span class="relative inline-block">
						<span class="text-transparent bg-clip-text bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))]"><?php echo esc_html( $tx_h_hi ); ?></span>
					</span>
				<?php endif; ?>
			</h2>

			<?php if ( $tx_sub ) : ?>
				<p class="text-neutral-600 text-xl box-border leading-[32.5px] break-words max-w-3xl mx-auto md:text-2xl md:leading-8"><?php echo esc_html( $tx_sub ); ?></p>
			<?php endif; ?>
		</div>

		<div class="box-border gap-x-8 grid grid-cols-[repeat(1,minmax(0px,1fr))] break-words gap-y-8 <?php echo esc_attr( $sp_tx_cols ); ?>">
			<?php foreach ( $tx_options as $sp_opt ) :
				$sp_name     = isset( $sp_opt['name'] ) ? (string) $sp_opt['name'] : '';
				$sp_badge    = isset( $sp_opt['badge'] ) ? (string) $sp_opt['badge'] : '';
				$sp_price    = isset( $sp_opt['price'] ) ? (string) $sp_opt['price'] : '';
				$sp_unit     = isset( $sp_opt['price_unit'] ) ? (string) $sp_opt['price_unit'] : '';
				$sp_cta_l    = isset( $sp_opt['cta_label'] ) ? (string) $sp_opt['cta_label'] : 'Learn More';
				$sp_cta_u    = isset( $sp_opt['cta_url'] ) ? (string) $sp_opt['cta_url'] : '#consultation';
				$sp_benefits = isset( $sp_opt['benefits'] ) && is_array( $sp_opt['benefits'] ) ? $sp_opt['benefits'] : array();

				// Product image (replaces the old decorative icon box).
				$sp_img      = isset( $sp_opt['image'] ) ? $sp_opt['image'] : null;
				$sp_img_id   = ( is_array( $sp_img ) && ! empty( $sp_img['ID'] ) ) ? (int) $sp_img['ID'] : 0;
				$sp_img_url  = ( is_array( $sp_img ) && ! empty( $sp_img['url'] ) ) ? $sp_img['url'] : '';
				$sp_img_alt  = ( is_array( $sp_img ) && ! empty( $sp_img['alt'] ) ) ? $sp_img['alt'] : $sp_name;
				$sp_has_img  = ( $sp_img_id || $sp_img_url );

				// Product photo renders full-width-of-card, upscaled to fit the banner,
				// with a radial "studio lighting" backdrop + soft drop shadow so the
				// product reads as a showcased object rather than a floating icon.
				$sp_img_class = 'relative w-full h-full object-contain transition-transform duration-500 ease-out group-hover:scale-[1.03] drop-shadow-[0_12px_24px_rgba(15,42,44,0.10)]';
				?>
				<div class="relative bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_20px_25px_-5px,rgba(0,0,0,0.1)_0px_8px_10px_-6px] box-border flex flex-col break-words border border-gray-100 overflow-hidden rounded-3xl border-solid hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.25)_0px_25px_50px_-12px] hover:border-teal-500/30 transition-all duration-300 group">

					<!-- Product image banner: full-width of card, radial "studio lighting" backdrop,
					     image fills to the banner edges (w-full h-full) so varied source aspect
					     ratios all read as a showcase rather than a floating thumbnail.  Hairline
					     bottom border softly separates the banner from the card body. -->
					<?php if ( $sp_has_img ) : ?>
						<div class="relative overflow-hidden h-64 md:h-72 flex items-center justify-center p-6 md:p-8 bg-[radial-gradient(ellipse_at_center,_rgb(255,255,255)_0%,_rgb(242,247,248)_75%)] border-b border-gray-100">
							<?php if ( $sp_badge ) : ?>
								<div class="absolute z-10 right-6 top-6">
									<span class="text-white text-xs font-bold <?php echo esc_attr( $sp_tx_badge_colour( $sp_badge ) ); ?> shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] inline-block leading-4 px-4 py-2 rounded-full uppercase tracking-wider"><?php echo esc_html( $sp_badge ); ?></span>
								</div>
							<?php endif; ?>
							<?php
							if ( $sp_img_id ) {
								// Prefer wp_get_attachment_image() — emits srcset + sizes so the
								// browser picks a file sharp enough for the ~240-288px banner,
								// and adds native lazy / async decoding.
								echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									$sp_img_id,
									'large',
									false,
									array(
										'class'    => $sp_img_class,
										'alt'      => $sp_img_alt,
										'sizes'    => '(min-width: 768px) 40vw, 90vw',
										'loading'  => 'lazy',
										'decoding' => 'async',
									)
								);
							} else { ?>
								<img src="<?php echo esc_url( $sp_img_url ); ?>" alt="<?php echo esc_attr( $sp_img_alt ); ?>" class="<?php echo esc_attr( $sp_img_class ); ?>" loading="lazy" decoding="async" />
							<?php } ?>
						</div>
					<?php elseif ( $sp_badge ) : ?>
						<!-- No image: keep the badge anchored to the card top-right -->
						<div class="absolute z-10 right-6 top-6">
							<span class="text-white text-xs font-bold <?php echo esc_attr( $sp_tx_badge_colour( $sp_badge ) ); ?> shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] inline-block leading-4 px-4 py-2 rounded-full uppercase tracking-wider"><?php echo esc_html( $sp_badge ); ?></span>
						</div>
					<?php endif; ?>

					<!-- Card body -->
					<div class="relative p-10 flex flex-col flex-grow">
						<div class="absolute bg-[linear-gradient(to_right_bottom,rgba(59,155,159,0.05),rgba(0,0,0,0))] box-border h-32 break-words w-32 rounded-bl-full right-0 top-0" aria-hidden="true"></div>

						<div class="relative box-border break-words z-10">
							<h3 class="text-neutral-900 text-3xl font-black box-border leading-9 break-words mb-4"><?php echo esc_html( $sp_name ); ?></h3>

						<?php if ( ! empty( $sp_benefits ) ) : ?>
							<div class="box-border break-words space-y-4 mb-8">
								<?php foreach ( $sp_benefits as $sp_b ) :
									$sp_text = isset( $sp_b['text'] ) ? (string) $sp_b['text'] : '';
									if ( '' === $sp_text ) { continue; }
									?>
									<div class="items-start box-border gap-x-3 flex break-words gap-y-3">
										<?php echo sp_icon( 'check', 'text-teal-500 h-6 w-6 shrink-0 mt-0.5' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										<p class="text-neutral-600 text-base box-border leading-[1.6] break-words"><?php echo esc_html( $sp_text ); ?></p>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<div class="box-border break-words border-gray-100 pt-6 border-t border-solid">
							<?php if ( $sp_price ) : ?>
								<div class="items-center box-border flex justify-between break-words mb-6">
									<div class="box-border break-words">
										<span class="text-neutral-900 text-4xl font-black box-border leading-10 break-words"><?php echo esc_html( $sp_price ); ?></span>
										<?php if ( $sp_unit ) : ?>
											<span class="text-neutral-600 text-lg box-border leading-7 break-words"><?php echo esc_html( $sp_unit ); ?></span>
										<?php endif; ?>
									</div>
								</div>
							<?php endif; ?>
							<a href="<?php echo esc_url( $sp_cta_u ); ?>" class="text-white text-base font-bold items-center bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border gap-x-2 flex justify-center leading-[1.4] break-words gap-y-2 w-full px-6 py-4 rounded-2xl hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.25)_0px_25px_50px_-12px] transition-all duration-300">
								<?php echo esc_html( $sp_cta_l ); ?>
								<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
							</a>
						</div>
					</div><!-- /.z-10 inner content -->
					</div><!-- /.card body -->
				</div><!-- /.card wrapper -->
			<?php endforeach; ?>
		</div>
	</div>
</section>
