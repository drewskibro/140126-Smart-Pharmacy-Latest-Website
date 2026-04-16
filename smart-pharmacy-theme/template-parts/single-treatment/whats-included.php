<?php
/**
 * Single Treatment — What's Included.
 *
 * Three feature cards (large gradient icon square, title, bullet benefits,
 * teal callout tagline). Repeater-driven; 3 is the design default. Matches
 * the prototype "What's Included" section of weight-loss.html.
 *
 * Editable via the Treatment post edit screen → B5 — Treatment What's Included.
 *
 * @package SmartPharmacy
 */

if ( ! (bool) sp_field( 'tx_incl_enabled', 1 ) ) {
	return;
}

$tx_badge_pre = sp_field( 'tx_incl_badge_pre', 'Complete' );
$tx_badge_hi  = sp_field( 'tx_incl_badge_highlight', 'Package' );
$tx_h_pre     = sp_field( 'tx_incl_heading_pre', "What's" );
$tx_h_hi      = sp_field( 'tx_incl_heading_highlight', 'Included' );
$tx_sub       = sp_field( 'tx_incl_subheading', 'Everything you need for successful weight loss' );

$tx_items = sp_field( 'tx_incl_items', array() );
if ( empty( $tx_items ) || ! is_array( $tx_items ) ) {
	// Degrade gracefully: give the page a sensible 3-card default so the
	// section still looks complete before editors customise it.
	$tx_items = array(
		array(
			'icon'    => 'document',
			'title'   => 'Prescription Medication',
			'bullets' => array(
				array( 'text' => 'Genuine UK-licensed medication' ),
				array( 'text' => 'Pre-filled injection pens' ),
				array( 'text' => 'Discreet packaging' ),
			),
			'tagline' => 'Available treatments: Wegovy, Mounjaro, Saxenda',
		),
		array(
			'icon'    => 'person',
			'title'   => 'Expert Support',
			'bullets' => array(
				array( 'text' => 'Ongoing clinical support' ),
				array( 'text' => 'Free follow-up consultations' ),
				array( 'text' => 'Dosage adjustments as needed' ),
			),
			'tagline' => '24/7 access to our pharmacy team',
		),
		array(
			'icon'    => 'sparkle',
			'title'   => 'Lifestyle Guidance',
			'bullets' => array(
				array( 'text' => 'Nutrition advice' ),
				array( 'text' => 'Exercise tips' ),
				array( 'text' => 'Weight tracking tools' ),
			),
			'tagline' => 'Personalized support for lasting results',
		),
	);
}

// Column count heuristic: 1 = centred single column, 2 = 2-col, 3+ = 3-col.
$sp_incl_count = count( $tx_items );
if ( 1 === $sp_incl_count ) {
	$sp_incl_cols = '';
} elseif ( 2 === $sp_incl_count ) {
	$sp_incl_cols = 'md:grid-cols-[repeat(2,minmax(0px,1fr))]';
} else {
	$sp_incl_cols = 'md:grid-cols-[repeat(3,minmax(0px,1fr))]';
}
?>

<section class="relative bg-white box-border break-words overflow-hidden py-16 md:py-24">
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
					<?php if ( $tx_badge_hi ) : ?><span class="text-teal-500"> <?php echo esc_html( $tx_badge_hi ); ?></span><?php endif; ?>
				</span>
				<div class="bg-teal-500 box-border h-2 break-words w-2 rounded-full"></div>
			</div>

			<h2 class="text-neutral-900 text-5xl font-black box-border tracking-[-1.2px] leading-[1.1] break-words mb-6 md:text-7xl md:tracking-[-1.8px]">
				<?php echo esc_html( $tx_h_pre ); ?>
				<?php if ( $tx_h_hi ) : ?>
					<span class="relative inline-block">
						<span class="text-transparent bg-clip-text bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))]"> <?php echo esc_html( $tx_h_hi ); ?></span>
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

		<!-- Feature cards -->
		<div class="box-border gap-x-8 grid grid-cols-[repeat(1,minmax(0px,1fr))] break-words gap-y-8 <?php echo esc_attr( $sp_incl_cols ); ?>">
			<?php foreach ( $tx_items as $sp_item ) :
				$sp_icon_key = isset( $sp_item['icon'] ) ? (string) $sp_item['icon'] : '';
				$sp_title    = isset( $sp_item['title'] ) ? (string) $sp_item['title'] : '';
				$sp_tagline  = isset( $sp_item['tagline'] ) ? (string) $sp_item['tagline'] : '';
				$sp_bullets  = isset( $sp_item['bullets'] ) && is_array( $sp_item['bullets'] ) ? $sp_item['bullets'] : array();
				?>
				<div class="relative bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border flex flex-col break-words border border-gray-100 overflow-hidden p-10 rounded-3xl border-solid hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.25)_0px_25px_50px_-12px] hover:border-teal-500/30 transition-all duration-300 group">
					<div class="absolute bg-[linear-gradient(to_right_bottom,rgba(59,155,159,0.05),rgba(0,0,0,0))] box-border h-32 break-words w-32 rounded-bl-full right-0 top-0" aria-hidden="true"></div>

					<div class="relative box-border break-words z-10">
						<?php if ( $sp_icon_key ) : ?>
							<div class="relative box-border inline-block break-words mb-8">
								<div class="absolute bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] box-border blur-xl opacity-30 break-words rounded-3xl inset-0" aria-hidden="true"></div>
								<div class="relative items-center bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_20px_25px_-5px,rgba(0,0,0,0.1)_0px_8px_10px_-6px] box-border flex h-20 justify-center break-words w-20 rounded-3xl transition-transform duration-300 group-hover:scale-110">
									<?php echo sp_icon( $sp_icon_key, 'w-10 h-10 text-white' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( $sp_title ) : ?>
							<h3 class="text-neutral-900 text-2xl font-black box-border leading-8 break-words mb-4"><?php echo esc_html( $sp_title ); ?></h3>
						<?php endif; ?>

						<?php if ( ! empty( $sp_bullets ) ) : ?>
							<div class="box-border break-words space-y-3 mb-6">
								<?php foreach ( $sp_bullets as $sp_b ) :
									$sp_text = isset( $sp_b['text'] ) ? (string) $sp_b['text'] : '';
									if ( '' === $sp_text ) { continue; }
									?>
									<div class="items-start box-border gap-x-3 flex break-words gap-y-3">
										<?php echo sp_icon( 'check', 'text-teal-500 h-5 w-5 shrink-0 mt-0.5' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										<p class="text-neutral-600 text-base box-border leading-[1.6] break-words"><?php echo esc_html( $sp_text ); ?></p>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<?php if ( $sp_tagline ) : ?>
							<div class="backdrop-blur-sm bg-teal-50/50 box-border break-words border border-teal-100 p-4 rounded-xl border-solid">
								<p class="text-teal-700 text-sm font-bold box-border leading-5 break-words"><?php echo esc_html( $sp_tagline ); ?></p>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
