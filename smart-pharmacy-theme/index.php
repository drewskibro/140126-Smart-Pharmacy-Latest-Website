<?php
/**
 * Fallback template.
 *
 * Used whenever WordPress cannot find a more specific template
 * (e.g. single.php, page.php, archive.php). Intentionally minimal
 * until stages 2+ add specific templates.
 *
 * @package SmartPharmacy
 */

get_header();
?>

<div class="container mx-auto px-4 py-12">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class(); ?>>
				<h1 class="text-3xl font-bold mb-4"><?php the_title(); ?></h1>
				<div class="prose max-w-none">
					<?php the_content(); ?>
				</div>
			</article>
			<?php
		endwhile;
	else :
		?>
		<p><?php esc_html_e( 'Nothing found.', 'smart-pharmacy' ); ?></p>
		<?php
	endif;
	?>
</div>

<?php
get_footer();
