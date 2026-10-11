<?php
/**
 * Default page template — plain Classic Editor content, for any page that
 * isn't using one of the BOCP page templates (e.g. Privacy Policy, Terms).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="section">
	<div class="wrap" style="max-width:820px">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<h1 class="h-display"><?php the_title(); ?></h1>
			<div class="entry-content" style="margin-top:24px">
				<?php the_content(); ?>
			</div>
			<?php
		endwhile;
		?>
	</div>
</section>

<?php
get_footer();
