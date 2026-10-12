<?php
/**
 * Default page template, for any page that isn't using one of the BOCP page templates.
 *
 * - Plain text pages (Privacy Policy, Terms) keep the narrow 820px reading column.
 * - Pages built from block layouts (groups, columns, tables, raw HTML, shortcodes) or with Elementor get the site's normal
 *   width (.wrap), so no child theme is needed for them. Override with the `bocp_page_wide` filter.
 * - A static front page prints its content only: its headings live inside the content, so no extra title or breadcrumbs.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<?php
while ( have_posts() ) :
	the_post();

	if ( is_front_page() ) {
		the_content();
		continue;
	}

	$post_content = (string) get_post_field( 'post_content', get_the_ID() );
	// Content placeholders ({INCLUDE:...} from the AI WP Site Manager plugin, {bocp_...} from the content loader) output
	// whole HTML sections such as the price tables, so pages that use them are wide too.
	$is_wide      = (bool) preg_match( '/<!--\s*wp:(group|columns|cover|table|html|shortcode)\b|\{(INCLUDE:|bocp_)/', $post_content )
		|| 'builder' === get_post_meta( get_the_ID(), '_elementor_edit_mode', true );
	$is_wide      = (bool) apply_filters( 'bocp_page_wide', $is_wide, get_the_ID() );
	?>
	<section class="section">
		<div class="wrap"<?php echo $is_wide ? '' : ' style="max-width:820px"'; ?>>
			<?php bocp_the_breadcrumbs(); ?>
			<h1 class="h-display"><?php the_title(); ?></h1>
			<div class="entry-content" style="margin-top:24px">
				<?php the_content(); ?>
			</div>
		</div>
	</section>
	<?php
endwhile;

get_footer();
