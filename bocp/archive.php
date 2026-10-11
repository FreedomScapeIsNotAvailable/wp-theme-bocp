<?php
/**
 * Archive (category/tag/date/author) — simple card list of excerpts.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="section">
	<div class="wrap">
		<div class="reveal in" style="max-width:720px">
			<h1 class="h-display"><?php the_archive_title(); ?></h1>
			<?php $desc = get_the_archive_description(); ?>
			<?php if ( $desc ) : ?>
				<div class="lead muted" style="margin-top:12px"><?php echo wp_kses_post( $desc ); ?></div>
			<?php endif; ?>
		</div>

		<div class="grid g-3" style="margin-top:32px">
			<?php while ( have_posts() ) : the_post(); ?>
				<a class="card card-lift mod" href="<?php the_permalink(); ?>">
					<?php if ( has_post_thumbnail() ) : ?>
						<div style="margin:-22px -22px 0;border-radius:var(--radius-md) var(--radius-md) 0 0;overflow:hidden">
							<?php the_post_thumbnail( 'medium_large' ); ?>
						</div>
					<?php endif; ?>
					<div class="mod-title"><?php the_title(); ?></div>
					<div class="mod-desc"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></div>
					<span class="mod-more"><?php esc_html_e( 'Citește mai mult', 'bocp' ); ?> <?php echo bocp_icon( 'arrowRight' ); ?></span>
				</a>
			<?php endwhile; ?>
		</div>

		<div style="margin-top:32px">
			<?php the_posts_pagination(); ?>
		</div>
	</div>
</section>

<?php
get_footer();
