<?php
/**
 * Single post — plain Classic Editor content with a small meta line.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="section">
	<div class="wrap" style="max-width:760px">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
				<?php bocp_the_breadcrumbs(); ?>
			<h1 class="h-display"><?php the_title(); ?></h1>
			<p class="lead muted" style="margin-top:12px">
				<?php echo esc_html( get_the_date() ); ?>
				<?php if ( get_the_author() ) : ?> · <?php the_author(); ?><?php endif; ?>
			</p>
			<?php if ( has_post_thumbnail() ) : ?>
				<div style="margin-top:28px"><?php the_post_thumbnail( 'large' ); ?></div>
			<?php endif; ?>
			<div class="entry-content" style="margin-top:28px">
				<?php the_content(); ?>
			</div>
			<?php
		endwhile;
		?>
	</div>
</section>

<?php
get_footer();
