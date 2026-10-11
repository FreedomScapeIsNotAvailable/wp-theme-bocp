<?php
/**
 * Search results — same card-list treatment as archive.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="section">
	<div class="wrap">
		<div class="reveal in" style="max-width:720px">
			<h1 class="h-display">
				<?php
				printf(
					/* translators: %s: search query */
					esc_html__( 'Rezultate pentru: %s', 'bocp' ),
					'<span style="color:var(--color-primary)">' . esc_html( get_search_query() ) . '</span>'
				);
				?>
			</h1>
		</div>

		<?php if ( have_posts() ) : ?>
			<div class="grid g-3" style="margin-top:32px">
				<?php while ( have_posts() ) : the_post(); ?>
					<a class="card card-lift mod" href="<?php the_permalink(); ?>">
						<div class="mod-title"><?php the_title(); ?></div>
						<div class="mod-desc"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></div>
						<span class="mod-more"><?php esc_html_e( 'Citește mai mult', 'bocp' ); ?> <?php echo bocp_icon( 'arrowRight' ); ?></span>
					</a>
				<?php endwhile; ?>
			</div>
			<div style="margin-top:32px">
				<?php the_posts_pagination(); ?>
			</div>
		<?php else : ?>
			<p class="lead muted" style="margin-top:20px"><?php esc_html_e( 'Nu am găsit niciun rezultat.', 'bocp' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
