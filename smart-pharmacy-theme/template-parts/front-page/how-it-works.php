<?php
/**
 * Homepage — How It Works.
 *
 * Three service cards (NHS Prescriptions / Private Services / Shop Products).
 * Middle card is visually "featured" via a thicker teal border + "Most Popular" pill.
 *
 * Editable via Theme Settings → Homepage → A4 — How It Works.
 *
 * @package SmartPharmacy
 */

if ( ! (bool) sp_field( 'hw_enabled', 1 ) ) {
	return;
}

$hw_badge_label = sp_field( 'hw_badge_label', 'Fast & Simple Process' );
$hw_h_pre       = sp_field( 'hw_heading_pre', 'How it' );
$hw_h_hi        = sp_field( 'hw_heading_highlight', 'Works' );
$hw_sub         = sp_field( 'hw_subheading', 'Three simple ways to access expert healthcare on your terms' );

$hw_cards = sp_field( 'hw_cards', array() );
if ( empty( $hw_cards ) || ! is_array( $hw_cards ) ) {
	$hw_cards = array(
		array( 'icon' => 'document', 'title' => 'NHS Prescriptions', 'subtitle' => 'Order NHS prescriptions online', 'tagline' => 'FREE delivery • Repeat prescriptions', 'cta' => 'Get Started',       'url' => '/nhs-prescriptions/', 'featured' => 0, 'featured_label' => 'Most Popular' ),
		array( 'icon' => 'person',   'title' => 'Private Services',  'subtitle' => 'Book private treatment online',   'tagline' => "Weight loss • HRT • Men's health", 'cta' => 'Browse Treatments', 'url' => '/treatments/',         'featured' => 1, 'featured_label' => 'Most Popular' ),
		array( 'icon' => 'clipboard','title' => 'Shop Products',     'subtitle' => 'Buy health essentials online',     'tagline' => 'Pain relief • Vitamins • Baby care', 'cta' => 'Shop Now',         'url' => '/shop/',               'featured' => 0, 'featured_label' => 'Most Popular' ),
	);
}
?>

<section class="relative bg-[linear-gradient(to_right_bottom,rgb(253,252,250),rgb(248,246,243),rgb(245,243,240))] box-border break-words overflow-hidden py-12 md:py-16">
	<div class="absolute bg-teal-500/10 box-border blur-3xl h-96 break-words w-96 rounded-full -left-20 -top-20" aria-hidden="true"></div>
	<div class="absolute bg-teal-700/10 box-border blur-3xl h-[500px] break-words w-[500px] rounded-full -right-20 bottom-0" aria-hidden="true"></div>

	<div class="relative box-border max-w-[1400px] break-words z-10 mx-auto px-6 md:px-16">

		<!-- Eyebrow + heading -->
		<div class="box-border break-words text-center mb-20">
			<div class="items-center backdrop-blur-sm bg-white/80 shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border gap-x-3 inline-flex break-words gap-y-3 border border-gray-100 mb-8 px-6 py-3 rounded-full border-solid">
				<div class="items-center bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] box-border flex h-10 justify-center break-words w-10 rounded-full">
					<?php echo sp_icon( 'check_circle', 'w-5 h-5 text-white' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<span class="text-neutral-900 text-base font-bold box-border block leading-6 break-words"><?php echo esc_html( $hw_badge_label ); ?></span>
				<div class="bg-teal-500 box-border h-2 break-words w-2 rounded-full"></div>
			</div>

			<h2 class="ultra-heading text-neutral-900 text-5xl font-black box-border leading-[1.1] break-words mb-6 md:text-7xl">
				<?php echo esc_html( $hw_h_pre ); ?>
				<?php if ( $hw_h_hi ) : ?>
					<span class="relative inline-block">
						<span class="text-transparent bg-clip-text bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))]"><?php echo esc_html( $hw_h_hi ); ?></span>
					</span>
				<?php endif; ?>
			</h2>

			<p class="text-neutral-600 text-xl box-border leading-[1.6] break-words max-w-3xl mx-auto md:text-2xl md:leading-[1.65] font-medium"><?php echo esc_html( $hw_sub ); ?></p>

			<div class="items-center box-border gap-x-3 flex justify-center break-words gap-y-3 mt-8" aria-hidden="true">
				<div class="bg-[linear-gradient(to_right,rgba(0,0,0,0),rgba(59,155,159,0.3))] box-border h-px break-words w-16"></div>
				<div class="bg-teal-500/30 box-border h-2 break-words w-2 rounded-full"></div>
				<div class="bg-[linear-gradient(to_left,rgba(0,0,0,0),rgba(59,155,159,0.3))] box-border h-px break-words w-16"></div>
			</div>
		</div>

		<!-- Cards -->
		<div class="box-border gap-x-8 grid grid-cols-[repeat(1,minmax(0px,1fr))] break-words gap-y-8 mb-8 md:grid-cols-[repeat(3,minmax(0px,1fr))]">
			<?php foreach ( $hw_cards as $sp_card ) :
				$sp_icon     = isset( $sp_card['icon'] ) ? (string) $sp_card['icon'] : '';
				$sp_title    = isset( $sp_card['title'] ) ? (string) $sp_card['title'] : '';
				$sp_subtitle = isset( $sp_card['subtitle'] ) ? (string) $sp_card['subtitle'] : '';
				$sp_tagline  = isset( $sp_card['tagline'] ) ? (string) $sp_card['tagline'] : '';
				$sp_cta      = isset( $sp_card['cta'] ) ? (string) $sp_card['cta'] : 'Get Started';
				$sp_url      = isset( $sp_card['url'] ) ? (string) $sp_card['url'] : '#';
				$sp_featured = ! empty( $sp_card['featured'] );
				$sp_f_label  = isset( $sp_card['featured_label'] ) ? (string) $sp_card['featured_label'] : 'Most Popular';
				$sp_border   = $sp_featured ? 'border-2 border-teal-500' : 'border border-gray-100';
				?>
				<a href="<?php echo esc_url( $sp_url ); ?>" class="service-card relative bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border flex flex-col break-words <?php echo esc_attr( $sp_border ); ?> overflow-hidden p-10 rounded-3xl border-solid hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.25)_0px_25px_50px_-12px] transition-all duration-300 group">
					<div class="absolute bg-[linear-gradient(to_right_bottom,rgba(59,155,159,0.05),rgba(0,0,0,0))] box-border h-20 break-words w-20 rounded-bl-full right-0 top-0" aria-hidden="true"></div>

					<?php if ( $sp_featured ) : ?>
						<div class="absolute box-border break-words z-10 right-6 top-6">
							<span class="text-white text-sm font-black bg-[#10c0a9] shadow-[rgba(16,192,169,0.4)_0px_8px_24px] box-border inline-block leading-4 break-words px-4 py-2 rounded-full uppercase tracking-wider"><?php echo esc_html( $sp_f_label ); ?></span>
						</div>
					<?php endif; ?>

					<div class="relative box-border break-words z-10 mb-8">
						<div class="relative box-border inline-block break-words mb-8">
							<div class="absolute bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] box-border blur-xl opacity-30 break-words rounded-3xl inset-0" aria-hidden="true"></div>
							<div class="relative items-center bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_20px_25px_-5px,rgba(0,0,0,0.1)_0px_8px_10px_-6px] box-border flex h-24 justify-center break-words w-24 rounded-3xl transition-transform duration-300 group-hover:scale-110">
								<?php echo sp_icon( $sp_icon, 'w-14 h-14 text-white' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
						</div>
						<h4 class="ultra-heading text-neutral-900 text-3xl font-black box-border leading-[1.2] break-words mb-4"><?php echo esc_html( $sp_title ); ?></h4>
						<p class="text-neutral-600 text-lg font-semibold box-border leading-[1.7] break-words mb-3"><?php echo esc_html( $sp_subtitle ); ?></p>
						<p class="text-teal-600 text-base font-black box-border leading-6 break-words"><?php echo esc_html( $sp_tagline ); ?></p>
					</div>

					<div class="mt-auto">
						<div class="flex items-center gap-3 text-teal-500 text-lg font-black group-hover:gap-4 transition-all">
							<span><?php echo esc_html( $sp_cta ); ?></span>
							<svg class="w-6 h-6 transition-transform duration-300 group-hover:translate-x-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
							</svg>
						</div>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
