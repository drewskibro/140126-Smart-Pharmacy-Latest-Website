<?php
/**
 * Homepage hero section.
 *
 * Left column: rating badge, two-line headline, subheading, hero search,
 * trust badges, treatment pills.
 * Right column: 3 info cards (icon + title + body).
 *
 * All content editable via Theme Settings → Homepage → A1 — Homepage Hero.
 *
 * @package SmartPharmacy
 */

// Rating badge.
$sp_rating_enabled = (bool) sp_field( 'hero_rating_enabled', 1 );
$sp_rating_stars   = (int) sp_field( 'hero_rating_stars', 5 );
$sp_rating_score   = sp_field( 'hero_rating_score', '4.8/5' );
$sp_rating_count   = (int) sp_field( 'hero_rating_count', 10392 );
$sp_rating_label   = sp_field( 'hero_rating_label', 'reviews' );

// Reviewer avatars: renders ONLY when real photos are uploaded in
// admin — there is deliberately no stock/placeholder fallback, so
// nothing generic can ever appear here by default.
$sp_rating_avatars = sp_field( 'hero_rating_avatars', array() );
if ( ! is_array( $sp_rating_avatars ) ) {
	$sp_rating_avatars = array();
}

// Hero photo: when set, the right column switches from the classic
// three-card stack to a single photo with the benefit cards floating
// over it as compact glass chips. Empty = classic layout unchanged.
$sp_visual         = sp_field( 'hero_visual_image', null );
$sp_visual_url     = ( is_array( $sp_visual ) && ! empty( $sp_visual['url'] ) ) ? (string) $sp_visual['url'] : '';
$sp_visual_alt     = ( is_array( $sp_visual ) && ! empty( $sp_visual['alt'] ) ) ? (string) $sp_visual['alt'] : '';
$sp_visual_caption = (string) sp_field( 'hero_visual_caption', '' );

// Headline + subheading.
$sp_heading_1     = sp_field( 'hero_heading_1', 'Prescriptions sorted.' );
$sp_heading_2     = sp_field( 'hero_heading_2', 'Privacy protected. Delivered.' );
$sp_subheading    = sp_field( 'hero_subheading', 'Expert healthcare on your terms. NHS-approved, prescription-perfect, delivered tomorrow.' );
$sp_hero_search   = sp_field( 'hero_search_placeholder', 'Search for trending treatments, medications or health concerns...' );

// Trust badges.
$sp_trust_badges = sp_field( 'hero_trust_badges', array() );
if ( empty( $sp_trust_badges ) || ! is_array( $sp_trust_badges ) ) {
	$sp_trust_badges = array(
		array( 'icon' => 'shield', 'label' => 'UK Regulated' ),
		array( 'icon' => 'lock',   'label' => '100% Secure' ),
		array( 'icon' => 'check',  'label' => 'FREE Delivery' ),
	);
}

// Treatment pills.
$sp_pills = sp_field( 'hero_pills', array() );
if ( empty( $sp_pills ) || ! is_array( $sp_pills ) ) {
	$sp_pills = array(
		array( 'icon' => 'person',    'label' => 'Weight Management', 'url' => '/treatments/weight-loss/' ),
		array( 'icon' => 'clipboard', 'label' => 'HRT',               'url' => '/treatments/hrt/' ),
		array( 'icon' => 'men',       'label' => "Men's Health",      'url' => '/treatments/mens-health/' ),
		array( 'icon' => 'hair',      'label' => 'Hair Loss',         'url' => '/treatments/hair-loss/' ),
		array( 'icon' => 'women',     'label' => "Women's Health",    'url' => '/treatments/womens-health/' ),
	);
}

// Info cards (right column).
$sp_info_cards = sp_field( 'hero_info_cards', array() );
if ( empty( $sp_info_cards ) || ! is_array( $sp_info_cards ) ) {
	// NB: "Prescribed online" (not "No prescription needed") — POMs
	// legally always need a prescription; ours are issued online. The
	// previous wording risked reading as misleading for a GPhC-
	// regulated pharmacy.
	$sp_info_cards = array(
		array( 'image' => null, 'icon' => 'document', 'title' => 'Prescribed online',       'body' => 'No GP visit needed — assessed by UK prescribers' ),
		array( 'image' => null, 'icon' => 'eye',      'title' => 'Discreet service',        'body' => 'Plain packaging, confidential delivery' ),
		array( 'image' => null, 'icon' => 'lock',     'title' => 'Confidential and secure', 'body' => 'Your data is protected and private' ),
	);
}
?>
<section class="relative bg-[linear-gradient(to_right_bottom,rgb(253,252,250),rgb(248,246,243),rgb(245,243,240))] box-border break-words w-full overflow-hidden">
	<div class="relative box-border break-words w-full px-6 py-12 md:px-16 md:py-16">
		<div class="items-start box-border gap-x-8 grid grid-cols-[repeat(1,minmax(0px,1fr))] max-w-[1600px] break-words gap-y-8 mb-16 mx-auto md:gap-x-16 md:grid-cols-[1fr_0.6fr] md:gap-y-16">

			<!-- LEFT COLUMN -->
			<div class="box-border break-words">

				<!-- Rating badge -->
				<?php if ( $sp_rating_enabled ) : ?>
					<div class="items-center bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.05)_0px_1px_2px_0px] box-border gap-x-2 inline-flex break-words gap-y-2 border border-gray-100 mb-6 px-4 py-2 rounded-full border-solid">
						<?php if ( ! empty( $sp_rating_avatars ) ) : ?>
							<div class="flex -space-x-2 mr-1" aria-hidden="true">
								<?php foreach ( $sp_rating_avatars as $sp_av ) :
									$sp_av_img = isset( $sp_av['image'] ) && is_array( $sp_av['image'] ) ? $sp_av['image'] : null;
									if ( ! $sp_av_img || empty( $sp_av_img['url'] ) ) { continue; }
									$sp_av_src = ! empty( $sp_av_img['sizes']['thumbnail'] ) ? $sp_av_img['sizes']['thumbnail'] : $sp_av_img['url'];
									?>
									<img src="<?php echo esc_url( $sp_av_src ); ?>" alt="" loading="lazy" class="w-7 h-7 rounded-full object-cover ring-2 ring-white shadow-sm" />
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
						<div class="box-border flex break-words">
							<?php for ( $sp_i = 0; $sp_i < 5; $sp_i++ ) : ?>
								<?php echo sp_icon( 'star', 'w-4 h-4 ' . ( $sp_i < $sp_rating_stars ? 'text-teal-500' : 'text-gray-200' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sp_icon returns trusted static SVG. ?>
							<?php endfor; ?>
						</div>
						<span class="text-neutral-900 text-sm font-semibold box-border block leading-5 break-words">
							<?php echo esc_html( $sp_rating_score ); ?> from <span class="counter" data-target="<?php echo (int) $sp_rating_count; ?>">0</span> <?php echo esc_html( $sp_rating_label ); ?>
						</span>
					</div>
				<?php endif; ?>

				<!-- Headline -->
				<h1 class="ultra-heading text-neutral-900 text-5xl font-black box-border leading-[1.05] break-words mb-6 md:text-7xl">
					<?php echo esc_html( $sp_heading_1 ); ?>
					<?php if ( $sp_heading_2 ) : ?>
						<span class="text-teal-500 text-5xl box-border block leading-[1.05] break-words mt-1 md:text-7xl"><?php echo esc_html( $sp_heading_2 ); ?></span>
					<?php endif; ?>
				</h1>

				<!-- Subheading -->
				<p class="text-neutral-600 text-lg box-border leading-[1.6] max-w-2xl break-words mb-8 md:text-2xl md:leading-[1.65] font-medium"><?php echo esc_html( $sp_subheading ); ?></p>

				<!-- Hero search + trust badges + pills -->
				<div class="box-border break-words">

					<!-- Search input (rotating placeholder powered by search-animation.js) -->
					<div class="relative box-border break-words">
						<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
							<div class="relative box-border break-words">
								<div class="absolute text-teal-500 box-border break-words pointer-events-none translate-y-[-50.0%] left-6 top-2/4">
									<svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
										<circle cx="11" cy="11" r="7" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M21 21l-4.35-4.35" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</div>
								<input type="search" id="hero-search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php echo esc_attr( $sp_hero_search ); ?>" class="text-lg shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.05)_0px_1px_2px_0px] box-border h-16 leading-7 break-words w-full border-neutral-900 pl-16 pr-6 py-0 rounded-2xl border-2 border-solid focus:border-neutral-900 focus:outline-none transition-all duration-300" />
							</div>
						</form>
					</div>

					<!-- Trust badges row -->
					<?php if ( ! empty( $sp_trust_badges ) ) : ?>
						<div class="text-neutral-600 text-sm items-center box-border gap-x-6 flex justify-center leading-5 break-words gap-y-6 mt-4 flex-wrap">
							<?php foreach ( $sp_trust_badges as $sp_i => $sp_badge ) :
								$sp_label = isset( $sp_badge['label'] ) ? (string) $sp_badge['label'] : '';
								$sp_icon  = isset( $sp_badge['icon'] ) ? (string) $sp_badge['icon'] : '';
								?>
								<?php if ( $sp_i > 0 ) : ?>
									<div class="bg-gray-300 box-border h-4 break-words w-px" aria-hidden="true"></div>
								<?php endif; ?>
								<div class="items-center box-border gap-x-2 flex break-words gap-y-2">
									<?php if ( $sp_icon ) : ?>
										<?php echo sp_icon( $sp_icon, 'text-teal-500 w-5 h-5' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<?php endif; ?>
									<span class="font-medium box-border block break-words"><?php echo esc_html( $sp_label ); ?></span>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<!-- Treatment pills -->
					<?php if ( ! empty( $sp_pills ) ) : ?>
						<div class="box-border gap-x-2 flex flex-wrap break-words gap-y-2 mt-4">
							<?php foreach ( $sp_pills as $sp_pill ) :
								$sp_label = isset( $sp_pill['label'] ) ? (string) $sp_pill['label'] : '';
								$sp_url   = isset( $sp_pill['url'] ) ? (string) $sp_pill['url'] : '#';
								$sp_icon  = isset( $sp_pill['icon'] ) ? (string) $sp_pill['icon'] : '';
								if ( '' === $sp_label ) { continue; }
								?>
								<a href="<?php echo esc_url( $sp_url ); ?>" class="treatment-pill text-sm font-semibold items-center bg-white gap-x-2.5 flex leading-5 break-words gap-y-2 text-center border border-gray-200 shadow-sm px-5 py-3 rounded-full border-solid hover:text-white hover:bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))] hover:border-transparent hover:shadow-lg transition-all duration-300 group">
									<?php if ( $sp_icon ) : ?>
										<?php echo sp_icon( $sp_icon, 'w-4 h-4 transition-transform duration-300 group-hover:scale-110' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<?php endif; ?>
									<?php echo esc_html( $sp_label ); ?>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

				</div>
			</div>

			<?php if ( $sp_visual_url ) : ?>
				<!-- RIGHT COLUMN: hero photo, deliberately clean.
				     Benefits render as an editorial strip BELOW the hero grid
				     (see sp-hero-benefit-strip), never as overlays on the
				     photo — the photo carries the human/trust signal on its
				     own, with at most one small credential caption. -->
				<div class="box-border break-words pt-0 md:pt-8">
					<div class="relative overflow-hidden rounded-[2rem] shadow-[rgba(0,0,0,0.16)_0px_26px_50px_-18px]">
						<img
							src="<?php echo esc_url( $sp_visual_url ); ?>"
							alt="<?php echo esc_attr( $sp_visual_alt ? $sp_visual_alt : ( $sp_visual_caption ? $sp_visual_caption : __( 'Smart Pharmacy', 'smart-pharmacy' ) ) ); ?>"
							class="w-full aspect-[4/5] object-cover"
						/>
						<?php if ( $sp_visual_caption ) : ?>
							<!-- Grounding gradient only when a caption needs legibility. -->
							<div class="absolute inset-x-0 bottom-0 h-28 bg-gradient-to-t from-neutral-900/40 to-transparent pointer-events-none" aria-hidden="true"></div>
							<div class="absolute bottom-4 left-4 text-white/95 text-xs font-medium tracking-wide">
								<?php echo esc_html( $sp_visual_caption ); ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php else : ?>
			<!-- RIGHT COLUMN: info cards -->
			<div class="box-border break-words pt-0 md:pt-12">
				<?php foreach ( $sp_info_cards as $sp_i => $sp_card ) :
					$sp_title = isset( $sp_card['title'] ) ? (string) $sp_card['title'] : '';
					$sp_body  = isset( $sp_card['body'] ) ? (string) $sp_card['body'] : '';
					$sp_icon  = isset( $sp_card['icon'] ) ? (string) $sp_card['icon'] : '';
					$sp_image = isset( $sp_card['image'] ) ? $sp_card['image'] : null;
					$sp_has_image = is_array( $sp_image ) && ! empty( $sp_image['url'] );
					?>
					<div class="info-card items-start backdrop-blur-sm bg-white/80 shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.05)_0px_1px_2px_0px] box-border gap-x-4 flex break-words gap-y-4 border border-gray-100 p-6 rounded-2xl border-solid <?php echo $sp_i > 0 ? 'mt-4' : ''; ?> hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_20px_25px_-5px,rgba(0,0,0,0.1)_0px_8px_10px_-6px] hover:border-teal-500/30 cursor-pointer transition-all duration-300 group">
						<div class="box-border shrink-0 break-words">
							<div class="text-white items-center bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border flex h-16 justify-center break-words w-16 rounded-xl transition-transform duration-300 group-hover:scale-110">
								<?php if ( $sp_has_image ) : ?>
									<img src="<?php echo esc_url( $sp_image['url'] ); ?>" alt="<?php echo esc_attr( ! empty( $sp_image['alt'] ) ? $sp_image['alt'] : $sp_title ); ?>" class="box-border h-9 w-9" />
								<?php elseif ( $sp_icon ) : ?>
									<?php echo sp_icon( $sp_icon, 'h-9 w-9' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php endif; ?>
							</div>
						</div>
						<div class="box-border basis-[0%] grow break-words">
							<h3 class="text-neutral-900 text-lg font-bold box-border leading-7 break-words mb-1"><?php echo esc_html( $sp_title ); ?></h3>
							<p class="text-neutral-600 text-sm box-border leading-[22.75px] break-words"><?php echo esc_html( $sp_body ); ?></p>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

		</div>

		<?php if ( $sp_visual_url && ! empty( $sp_info_cards ) ) : ?>
			<!-- Editorial benefit strip (photo layout only) — the pattern the
			     strongest healthcare DTC sites use: benefits as quiet
			     typography with hairline dividers, never boxed cards or
			     overlays. Reuses the same hero_info_cards data. -->
			<div class="sp-hero-benefit-strip max-w-[1600px] mx-auto -mt-8 mb-2 border-t border-neutral-900/10">
				<div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-neutral-900/10">
					<?php foreach ( $sp_info_cards as $sp_i => $sp_card ) :
						$sp_title = isset( $sp_card['title'] ) ? (string) $sp_card['title'] : '';
						$sp_body  = isset( $sp_card['body'] ) ? (string) $sp_card['body'] : '';
						$sp_icon  = isset( $sp_card['icon'] ) ? (string) $sp_card['icon'] : '';
						if ( '' === $sp_title ) { continue; }
						?>
						<div class="flex items-start gap-x-3 py-5 md:px-10 <?php echo 0 === $sp_i ? 'md:pl-0' : ''; ?>">
							<?php if ( $sp_icon ) : ?>
								<?php echo sp_icon( $sp_icon, 'w-5 h-5 text-teal-600 shrink-0 mt-0.5' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php endif; ?>
							<div class="min-w-0">
								<h3 class="text-neutral-900 text-sm font-semibold tracking-tight leading-5"><?php echo esc_html( $sp_title ); ?></h3>
								<?php if ( $sp_body ) : ?>
									<p class="text-neutral-500 text-sm leading-snug mt-0.5"><?php echo esc_html( $sp_body ); ?></p>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

	</div>
</section>
