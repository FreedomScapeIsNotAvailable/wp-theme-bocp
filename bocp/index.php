<?php
/**
 * Fallback template (required by WordPress). In practice archive.php,
 * single.php, page.php etc. handle every real case; this only fires for
 * template hierarchy levels not otherwise covered.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="section">
	<div class="wrap">
		<?php if ( have_posts() ) : ?>
			<div class="grid g-3">
				<?php while ( have_posts() ) : the_post(); ?>
					<a class="card card-lift mod" href="<?php the_permalink(); ?>">
						<div class="mod-title"><?php the_title(); ?></div>
						<div class="mod-desc"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></div>
					</a>
				<?php endwhile; ?>
			</div>
		<?php else : ?>
			<p class="lead muted"><?php esc_html_e( 'Nimic de afișat.', 'bocp' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
