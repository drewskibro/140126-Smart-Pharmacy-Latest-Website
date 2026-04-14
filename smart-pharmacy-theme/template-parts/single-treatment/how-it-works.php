<?php
/**
 * Single Treatment — How It Works.
 *
 * Three numbered step cards (large circle with step number, title,
 * meta pill, description). Repeater-driven; 3 is the design default.
 *
 * Editable via the Treatment post edit screen → B2 — Treatment How It Works.
 *
 * @package SmartPharmacy
 */

if ( ! (bool) sp_field( 'tx_how_enabled', 1 ) ) {
	return;
}

$tx_badge_pre = sp_field( 'tx_how_badge_pre', 'Simple' );
$tx_badge_hi  = sp_field( 'tx_how_badge_highlight', '3-Step' );
$tx_badge_suf = sp_field( 'tx_how_badge_suffix', 'Process' );
$tx_h_pre     = sp_field( 'tx_how_heading_pre', 'How It' );
$tx_h_hi      = sp_field( 'tx_how_heading_highlight', 'Works' );
$tx_sub       = sp_field( 'tx_how_subheading', 'Start your journey in 3 simple steps' );

$tx_steps = sp_field( 'tx_how_steps', array() );
if ( empty( $tx_steps ) || ! is_array( $tx_steps ) ) {
	$tx_steps = array(
		array( 'title' => 'Complete Assessment', 'meta_label' => '2-5 Minutes', 'meta_icon' => 'document',    'description' => 'Fill out our simple online health questionnaire about your goals and medical history.' ),
		array( 'title' => 'Expert Review',       'meta_label' => 'UK Doctors', 'meta_icon' => 'person',      'description' => 'A UK-registered doctor reviews your assessment and prescribes the most suitable treatment if appropriate.' ),
		array( 'title' => 'Fast Delivery',       'meta_label' => 'Next Day',   'meta_icon' => 'truck',       'description' => 'Your medication is delivered discreetly to your door in plain packaging with free next-day delivery.' ),
	);
}
?>

<section id="how-it-works" class="relative bg-white box-border break-words overflow-hidden py-16 md:py-24">
	<div class="absolute bg-teal-500/10 box-border blur-3xl h-96 break-words w-96 rounded-full -left-20 -top-20" aria-hidden="true"></div>
	<div class="absolute bg-teal-700/10 box-border blur-3xl h-[500px] break-words w-[500px] rounded-full -right-20 bottom-0" aria-hidden="true"></div>

	<div class="relative box-border max-w-[1400px] break-words z-10 mx-auto px-8 md:px-24">
		<div class="box-border break-words text-center mb-16">
			<div class="items-center backdrop-blur-sm bg-white/80 shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border gap-x-3 inline-flex break-words gap-y-3 border border-gray-100 mb-8 px-6 py-3 rounded-full border-solid">
				<div class="items-center bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] box-border flex h-10 justify-center break-words w-10 rounded-full">
					<?php echo sp_icon( 'check_circle', 'w-5 h-5 text-white' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<span class="text-neutral-900 text-base font-bold box-border block leading-6 break-words">
					<?php echo esc_html( $tx_badge_pre ); ?>
					<?php if ( $tx_badge_hi ) : ?><span class="text-teal-500"><?php echo esc_html( $tx_badge_hi ); ?></span><?php endif; ?>
					<?php echo esc_html( $tx_badge_suf ); ?>
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

			<div class="items-center box-border gap-x-3 flex justify-center break-words gap-y-3 mt-8" aria-hidden="true">
				<div class="bg-[linear-gradient(to_right,rgba(0,0,0,0),rgba(59,155,159,0.3))] box-border h-px break-words w-16"></div>
				<div class="bg-teal-500/30 box-border h-2 break-words w-2 rounded-full"></div>
				<div class="bg-[linear-gradient(to_left,rgba(0,0,0,0),rgba(59,155,159,0.3))] box-border h-px break-words w-16"></div>
			</div>
		</div>

		<!-- Step cards -->
		<div class="box-border gap-x-8 grid grid-cols-[repeat(1,minmax(0px,1fr))] break-words gap-y-8 md:grid-cols-[repeat(3,minmax(0px,1fr))]">
			<?php foreach ( $tx_steps as $sp_i => $sp_step ) :
				$sp_title = isset( $sp_step['title'] ) ? (string) $sp_step['title'] : '';
				$sp_desc  = isset( $sp_step['description'] ) ? (string) $sp_step['description'] : '';
				$sp_meta  = isset( $sp_step['meta_label'] ) ? (string) $sp_step['meta_label'] : '';
				$sp_icon  = isset( $sp_step['meta_icon'] ) ? (string) $sp_step['meta_icon'] : 'check';
				?>
				<div class="relative bg-gradient-to-br from-white to-gray-50/50 shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border flex flex-col break-words border border-gray-100 overflow-hidden p-10 rounded-3xl border-solid hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.25)_0px_25px_50px_-12px] hover:border-teal-200 transition-all duration-500 group">
					<div class="absolute bg-[linear-gradient(to_right_bottom,rgba(59,155,159,0.08),rgba(0,0,0,0))] box-border h-32 break-words w-32 rounded-bl-full right-0 top-0" aria-hidden="true"></div>

					<div class="relative box-border break-words z-10">
						<div class="flex flex-col items-center text-center mb-8">
							<div class="relative box-border mb-6 break-words">
								<div class="absolute bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] box-border blur-2xl opacity-40 break-words rounded-full inset-0 group-hover:opacity-60 transition-opacity duration-500" aria-hidden="true"></div>
								<div class="relative items-center bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_20px_25px_-5px,rgba(0,0,0,0.1)_0px_8px_10px_-6px] box-border flex h-24 justify-center break-words w-24 rounded-full border-4 border-teal-100 group-hover:border-teal-300 transition-all duration-500 group-hover:scale-110">
									<div class="text-transparent text-4xl font-black bg-clip-text bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))] box-border leading-10 break-words"><?php echo (int) ( $sp_i + 1 ); ?></div>
								</div>
							</div>

							<div class="mb-6">
								<h4 class="text-neutral-900 text-3xl font-black box-border leading-9 break-words mb-3 group-hover:text-teal-600 transition-colors duration-300"><?php echo esc_html( $sp_title ); ?></h4>
								<?php if ( $sp_meta ) : ?>
									<div class="inline-flex items-center gap-2 bg-teal-50 px-4 py-2 rounded-full">
										<?php echo sp_icon( $sp_icon, 'w-4 h-4 text-teal-500' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										<span class="text-teal-700 text-xs font-bold uppercase tracking-wider"><?php echo esc_html( $sp_meta ); ?></span>
									</div>
								<?php endif; ?>
							</div>
						</div>

						<?php if ( $sp_desc ) : ?>
							<p class="text-neutral-600 text-lg font-medium box-border leading-[1.8] break-words text-center"><?php echo esc_html( $sp_desc ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
