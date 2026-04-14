<?php
/**
 * Homepage — Featured Treatment showcase (Weight Loss block).
 *
 * Asymmetric two-column layout: content left (badge, headline, subheading,
 * stats, CTA, testimonial), image collage right (4 overlapping images +
 * floating success-rate badge).
 *
 * Editable via Theme Settings → Homepage → A3 — Featured Treatment.
 *
 * @package SmartPharmacy
 */

if ( ! (bool) sp_field( 'ft_enabled', 1 ) ) {
	return;
}

$ft_badge_label = sp_field( 'ft_badge_label', 'Medical Weight Loss' );
$ft_badge_icon  = sp_field( 'ft_badge_icon', 'person' );
$ft_h_pre       = sp_field( 'ft_heading_pre', 'Transform your life with' );
$ft_h_hi        = sp_field( 'ft_heading_highlight', 'prescription weight loss' );
$ft_sub         = sp_field( 'ft_subheading', 'Clinically-proven treatments delivered discreetly to your door. Expert medical support every step of the way.' );
$ft_cta_label   = sp_field( 'ft_cta_label', 'Start Your Journey' );
$ft_cta_url     = sp_field( 'ft_cta_url', '/treatments/weight-loss/' );
$ft_quote       = sp_field( 'ft_testimonial_quote', '' );
$ft_author      = sp_field( 'ft_testimonial_author', 'Sarah M.' );
$ft_meta        = sp_field( 'ft_testimonial_meta', 'Lost 32 lbs' );
$ft_age         = sp_field( 'ft_testimonial_age', '4 months ago' );

$ft_stats = sp_field( 'ft_stats', array() );
if ( empty( $ft_stats ) || ! is_array( $ft_stats ) ) {
	$ft_stats = array(
		array( 'value' => '15', 'animated' => 1, 'label' => '% Avg. Weight Loss' ),
		array( 'value' => '24', 'animated' => 1, 'label' => 'h Fast Delivery' ),
		array( 'value' => '4.9★', 'animated' => 0, 'label' => 'Patient Rating' ),
	);
}

// Image collage defaults (Unsplash, same as prototype). Replace in admin.
$ft_img_main = sp_image_url( 'ft_image_main', 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=600&h=700&fit=crop&q=80', 'option' );
$ft_img_tr   = sp_image_url( 'ft_image_tr',   'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=500&h=400&fit=crop&q=80', 'option' );
$ft_img_bl   = sp_image_url( 'ft_image_bl',   'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=500&h=400&fit=crop&q=80', 'option' );
$ft_img_br   = sp_image_url( 'ft_image_br',   'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=400&h=350&fit=crop&q=80', 'option' );

$ft_main_val  = sp_field( 'ft_main_badge_value', '32' );
$ft_main_lbl  = sp_field( 'ft_main_badge_label', 'lbs Lost' );
$ft_corner    = sp_field( 'ft_corner_badge_value', '-45 lbs' );
$ft_float_val = sp_field( 'ft_floating_value', '84' );
$ft_float_lbl = sp_field( 'ft_floating_label', '% Success Rate' );
?>

<section class="relative bg-white box-border break-words overflow-hidden py-12 md:py-16">
	<div class="absolute bg-teal-500/5 box-border blur-3xl h-96 break-words w-96 rounded-full -left-20 -top-20" aria-hidden="true"></div>
	<div class="absolute bg-teal-700/5 box-border blur-3xl h-[500px] break-words w-[500px] rounded-full -right-20 bottom-0" aria-hidden="true"></div>

	<div class="relative box-border break-words w-full px-6 md:px-16 max-w-[1600px] mx-auto">
		<div class="items-center box-border gap-x-16 grid grid-cols-[repeat(1,minmax(0px,1fr))] max-w-[1600px] break-words gap-y-12 mx-auto md:gap-x-20 md:grid-cols-[50fr_50fr] md:gap-y-16">

			<!-- LEFT: content -->
			<div class="box-border break-words order-2 md:order-1">
				<!-- Badge -->
				<div class="items-center backdrop-blur-sm bg-white/80 shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border gap-x-3 inline-flex break-words gap-y-3 border border-gray-100 mb-6 px-6 py-3 rounded-full border-solid">
					<div class="items-center bg-[#10c0a9] box-border flex h-10 justify-center break-words w-10 rounded-full">
						<?php echo sp_icon( $ft_badge_icon, 'w-5 h-5 text-white' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<span class="text-neutral-900 text-base font-bold box-border block leading-6 break-words"><?php echo esc_html( $ft_badge_label ); ?></span>
					<div class="bg-[#10c0a9] box-border h-2 break-words w-2 rounded-full"></div>
				</div>

				<!-- Heading -->
				<h2 class="ultra-heading text-neutral-900 text-[2.75rem] font-black box-border leading-[1.1] break-words mb-6 md:text-[4rem] md:leading-[1.05] antialiased">
					<?php echo esc_html( $ft_h_pre ); ?>
					<?php if ( $ft_h_hi ) : ?>
						<span class="relative inline-block">
							<span class="relative text-[#10c0a9] antialiased"><?php echo esc_html( $ft_h_hi ); ?></span>
							<div class="absolute -bottom-1 left-0 w-full h-[3px] bg-[#10c0a9] opacity-70 rounded-full" aria-hidden="true"></div>
						</span>
					<?php endif; ?>
				</h2>

				<p class="text-neutral-600 text-xl box-border leading-[1.7] break-words mb-8 md:text-2xl md:leading-[1.75] font-medium antialiased"><?php echo esc_html( $ft_sub ); ?></p>

				<!-- Stats -->
				<?php if ( ! empty( $ft_stats ) ) : ?>
					<div class="grid grid-cols-3 gap-4 mb-8">
						<?php foreach ( $ft_stats as $sp_i => $sp_stat ) :
							$sp_value    = isset( $sp_stat['value'] ) ? (string) $sp_stat['value'] : '';
							$sp_label    = isset( $sp_stat['label'] ) ? (string) $sp_stat['label'] : '';
							$sp_animated = ! empty( $sp_stat['animated'] );
							$sp_border   = ( $sp_i === 1 ) ? 'border-x border-gray-200' : '';
							?>
							<div class="text-center <?php echo esc_attr( $sp_border ); ?>">
								<?php if ( $sp_animated && is_numeric( $sp_value ) ) : ?>
									<div class="text-[#10c0a9] text-4xl font-black mb-1 md:text-5xl antialiased counter" data-target="<?php echo (int) $sp_value; ?>">0</div>
								<?php else : ?>
									<div class="text-[#10c0a9] text-4xl font-black mb-1 md:text-5xl antialiased"><?php echo esc_html( $sp_value ); ?></div>
								<?php endif; ?>
								<p class="text-neutral-600 text-xs font-bold uppercase tracking-wider md:text-sm antialiased"><?php echo esc_html( $sp_label ); ?></p>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<!-- CTA -->
				<div class="box-border gap-x-4 flex flex-col break-words gap-y-4 mb-8 md:flex-row">
					<a href="<?php echo esc_url( $ft_cta_url ); ?>" class="premium-button group relative flex items-center justify-center gap-3 bg-[#10c0a9] hover:bg-[#0da592] text-white text-[17px] md:text-[19px] font-black px-10 py-5 rounded-[16px] shadow-[0_20px_50px_-12px_rgba(16,192,169,0.35)] hover:shadow-[0_25px_60px_-15px_rgba(16,192,169,0.5)] transition-all duration-300 overflow-hidden transform hover:scale-[1.02] antialiased">
						<div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 translate-x-[-200%] group-hover:translate-x-[200%] transition-transform duration-1000" aria-hidden="true"></div>
						<span class="relative flex items-center gap-3 tracking-[-0.01em]">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
							<?php echo esc_html( $ft_cta_label ); ?>
							<svg class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path d="M17 8l4 4m0 0l-4 4m4-4H3" stroke-linecap="round" stroke-linejoin="round"/></svg>
						</span>
					</a>
				</div>

				<!-- Testimonial card -->
				<?php if ( $ft_quote ) : ?>
					<div class="relative bg-white rounded-3xl p-8 shadow-[0_20px_50px_-12px_rgba(0,0,0,0.12)] border border-gray-100 overflow-hidden group hover:shadow-[0_25px_60px_-15px_rgba(0,0,0,0.18)] transition-all duration-500">
						<div class="absolute -top-20 -right-20 w-40 h-40 bg-[#10c0a9]/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700" aria-hidden="true"></div>
						<div class="relative z-10">
							<div class="flex items-start gap-4 mb-5">
								<div class="relative shrink-0">
									<div class="absolute inset-0 bg-[#10c0a9] rounded-2xl blur-md opacity-30" aria-hidden="true"></div>
									<div class="relative w-16 h-16 bg-[#10c0a9] rounded-2xl flex items-center justify-center shadow-lg">
										<svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 8c0-.55.45-1 1-1h10c.55 0 1 .45 1 1v2c0 .55-.45 1-1 1H7c-.55 0-1-.45-1-1V8zm0 6c0-.55.45-1 1-1h10c.55 0 1 .45 1 1v2c0 .55-.45 1-1 1H7c-.55 0-1-.45-1-1v-2z"/></svg>
									</div>
								</div>
								<div class="flex-1">
									<div class="flex gap-1 mb-3" aria-hidden="true">
										<?php for ( $i = 0; $i < 5; $i++ ) { echo sp_icon( 'star', 'w-5 h-5 text-amber-400' ); } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
									<p class="text-neutral-900 text-lg font-black tracking-tight antialiased"><?php echo esc_html( $ft_author ); ?> <?php if ( $ft_meta ) : ?><span class="text-neutral-400 font-medium">• <?php echo esc_html( $ft_meta ); ?></span><?php endif; ?></p>
								</div>
							</div>
							<blockquote class="serif-italic text-neutral-700 text-[19px] leading-[1.65] break-words mb-5 antialiased">&ldquo;<?php echo esc_html( $ft_quote ); ?>&rdquo;</blockquote>
							<div class="flex items-center gap-3 pt-5 border-t border-gray-100">
								<div class="flex items-center gap-2 bg-[#10c0a9]/10 px-4 py-2 rounded-full border border-[#10c0a9]/20">
									<?php echo sp_icon( 'check', 'w-4 h-4 text-[#10c0a9]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<span class="text-[#10c0a9] text-sm font-bold antialiased"><?php esc_html_e( 'Verified Purchase', 'smart-pharmacy' ); ?></span>
								</div>
								<?php if ( $ft_age ) : ?>
									<span class="text-neutral-500 text-sm font-medium antialiased"><?php echo esc_html( $ft_age ); ?></span>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<!-- RIGHT: image collage -->
			<div class="relative box-border break-words order-1 md:order-2">
				<div class="relative h-[500px] md:h-[600px]">

					<!-- Main large image + "lbs Lost" badge -->
					<div class="absolute top-0 left-0 w-[60%] h-[55%] rounded-3xl overflow-hidden shadow-[0_25px_60px_-15px_rgba(0,0,0,0.3)] transform hover:scale-105 hover:rotate-1 transition-all duration-700 z-20 image-float">
						<div class="absolute inset-0 bg-[#10c0a9]/15 z-10 mix-blend-overlay" aria-hidden="true"></div>
						<img src="<?php echo esc_url( $ft_img_main ); ?>" alt="" class="w-full h-full object-cover" />
						<div class="absolute bottom-4 left-4 right-4 z-20">
							<div class="backdrop-blur-xl bg-white/95 rounded-2xl p-4 shadow-lg">
								<div class="flex items-center gap-3">
									<div class="w-12 h-12 bg-[#10c0a9] rounded-xl flex items-center justify-center shadow-md">
										<?php echo sp_icon( 'check', 'w-6 h-6 text-white' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
									<div>
										<p class="text-neutral-900 text-sm font-black <?php echo is_numeric( $ft_main_val ) ? 'counter' : ''; ?>" <?php if ( is_numeric( $ft_main_val ) ) : ?>data-target="<?php echo (int) $ft_main_val; ?>"<?php endif; ?>><?php echo is_numeric( $ft_main_val ) ? '0' : esc_html( $ft_main_val ); ?></p>
										<p class="text-neutral-600 text-xs font-semibold"><?php echo esc_html( $ft_main_lbl ); ?></p>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Top-right image -->
					<div class="absolute top-[10%] right-0 w-[45%] h-[35%] rounded-3xl overflow-hidden shadow-[0_20px_50px_-12px_rgba(0,0,0,0.25)] transform hover:scale-105 hover:-rotate-1 transition-all duration-700 z-10 image-float-delayed">
						<div class="absolute inset-0 bg-[#10c0a9]/15 z-10 mix-blend-overlay" aria-hidden="true"></div>
						<img src="<?php echo esc_url( $ft_img_tr ); ?>" alt="" class="w-full h-full object-cover" />
					</div>

					<!-- Bottom-left image -->
					<div class="absolute bottom-[5%] left-[5%] w-[45%] h-[35%] rounded-3xl overflow-hidden shadow-[0_20px_50px_-12px_rgba(0,0,0,0.25)] transform hover:scale-105 hover:rotate-2 transition-all duration-700 z-10 image-float-slow">
						<div class="absolute inset-0 bg-[#10c0a9]/15 z-10 mix-blend-overlay" aria-hidden="true"></div>
						<img src="<?php echo esc_url( $ft_img_bl ); ?>" alt="" class="w-full h-full object-cover" />
					</div>

					<!-- Bottom-right accent + corner badge -->
					<div class="absolute bottom-0 right-[8%] w-[38%] h-[30%] rounded-3xl overflow-hidden shadow-[0_20px_50px_-12px_rgba(0,0,0,0.25)] transform hover:scale-105 hover:-rotate-2 transition-all duration-700 z-30 image-float-fast">
						<div class="absolute inset-0 bg-[#10c0a9]/15 z-10 mix-blend-overlay" aria-hidden="true"></div>
						<img src="<?php echo esc_url( $ft_img_br ); ?>" alt="" class="w-full h-full object-cover" />
						<?php if ( $ft_corner ) : ?>
							<div class="absolute top-3 right-3 z-20">
								<div class="backdrop-blur-xl bg-white/95 rounded-full px-3 py-1.5 shadow-lg">
									<p class="text-[#10c0a9] text-xs font-black"><?php echo esc_html( $ft_corner ); ?></p>
								</div>
							</div>
						<?php endif; ?>
					</div>

					<!-- Floating badge -->
					<div class="absolute top-[45%] right-[15%] z-40 image-float-badge">
						<div class="relative">
							<div class="absolute inset-0 bg-[#10c0a9] rounded-2xl blur-xl opacity-40" aria-hidden="true"></div>
							<div class="relative backdrop-blur-xl bg-white/95 rounded-2xl px-6 py-4 shadow-[0_20px_50px_-12px_rgba(0,0,0,0.3)] border border-white">
								<div class="text-center">
									<div class="text-[#10c0a9] text-3xl font-black mb-1 <?php echo is_numeric( $ft_float_val ) ? 'counter' : ''; ?>" <?php if ( is_numeric( $ft_float_val ) ) : ?>data-target="<?php echo (int) $ft_float_val; ?>"<?php endif; ?>><?php echo is_numeric( $ft_float_val ) ? '0' : esc_html( $ft_float_val ); ?></div>
									<p class="text-neutral-600 text-xs font-black uppercase tracking-wider"><?php echo esc_html( $ft_float_lbl ); ?></p>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>

		</div>
	</div>
</section>
