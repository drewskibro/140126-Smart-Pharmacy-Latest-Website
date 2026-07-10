<?php
/**
 * Page template.
 *
 * Renders standard WP pages (About, Contact, FAQ, NHS, policies) inside the
 * same brand frame the WooCommerce pages use: an eyebrow pill, a gradient
 * title, an optional subheading (the page excerpt), then the editor content
 * in a rounded white card.
 *
 * Without this file, pages fell through to index.php, which renders a bare
 * <h1> + the_content() in a `.prose` div. The theme does NOT ship
 * @tailwindcss/typography (tailwind.config.js has `plugins: []`), so `.prose`
 * is a no-op and Tailwind's preflight strips heading sizes and list bullets.
 * Editor HTML is therefore styled explicitly by `.sp-prose` in
 * assets/css/styles.css.
 *
 * @package SmartPharmacy
 */

get_header();

while ( have_posts() ) :
	the_post();

	// Only use a real, hand-written excerpt as the subheading -- never the
	// auto-generated one, which would dump the first 55 words of the page.
	$sp_page_sub = has_excerpt() ? get_the_excerpt() : '';
	?>

	<div class="box-border break-words w-full px-6 py-16 md:px-16 md:py-20">
		<div class="box-border max-w-4xl break-words mx-auto">

			<header class="box-border break-words text-center mb-12 md:mb-16">
				<div class="items-center backdrop-blur-sm bg-white/80 shadow-[rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border gap-x-3 inline-flex break-words gap-y-3 border border-gray-100 mb-6 px-6 py-3 rounded-full border-solid">
					<div class="items-center bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] box-border flex h-10 justify-center break-words w-10 rounded-full">
						<?php echo sp_icon( 'document', 'w-5 h-5 text-white' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<span class="text-neutral-900 text-base font-bold box-border block leading-6 break-words">
						<?php esc_html_e( 'Smart', 'smart-pharmacy' ); ?>
						<span class="text-teal-500"> <?php esc_html_e( 'Pharmacy', 'smart-pharmacy' ); ?></span>
					</span>
					<div class="bg-teal-500 box-border h-2 break-words w-2 rounded-full"></div>
				</div>

				<h1 class="text-neutral-900 text-4xl font-black box-border leading-[1.1] break-words mb-4 md:text-6xl">
					<span class="text-transparent bg-clip-text bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))]"><?php the_title(); ?></span>
				</h1>

				<?php if ( $sp_page_sub ) : ?>
					<p class="text-neutral-600 text-lg box-border leading-[1.6] break-words max-w-3xl mx-auto md:text-xl"><?php echo esc_html( $sp_page_sub ); ?></p>
				<?php endif; ?>
			</header>

			<article <?php post_class( 'sp-prose backdrop-blur-sm bg-white/80 box-border break-words border border-gray-100 border-solid rounded-3xl shadow-[rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] p-8 md:p-12' ); ?>>
				<?php the_content(); ?>
			</article>

		</div>
	</div>

	<?php
endwhile;

get_footer();
