<?php
/**
 * Posts page (Settings › Reading › Posts page) — same card list as archive.php,
 * with the posts page title as the single H1 and its excerpt as lead.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$bocp_posts_page = (int) get_option( 'page_for_posts' );
$bocp_title      = $bocp_posts_page ? get_the_title( $bocp_posts_page ) : __( 'Articole', 'bocp' );
$bocp_lead       = $bocp_posts_page && has_excerpt( $bocp_posts_page ) ? get_the_excerpt( $bocp_posts_page ) : '';
?>

<section class="section">
	<div class="wrap">
		<div class="reveal in" style="max-width:720px">
			<h1 class="h-display"><?php echo esc_html( $bocp_title ); ?></h1>
			<?php if ( $bocp_lead ) : ?>
				<p class="lead muted" style="margin-top:12px"><?php echo esc_html( $bocp_lead ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( have_posts() ) : ?>
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
		<?php else : ?>
			<p class="lead muted" style="margin-top:32px"><?php esc_html_e( 'Nimic de afișat.', 'bocp' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
