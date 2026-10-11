<?php
/**
 * Footer: 4 widget-area columns (Aspect > Widgets: "Footer — Coloana 1"
 * through "Coloana 4") + a bottom copyright line. All 4 are plain
 * WordPress widget areas — drag in an Image widget, a Navigation Menu
 * widget, a Text widget, or a Custom HTML widget (e.g. with the
 * [bocp_contact_info] shortcode) and rearrange freely from Aspect >
 * Widgets, no code involved.
 *
 * Coloana 1 (the brand/logo column, sidebar id "footer-brand") falls back
 * to the Customizer's logo + note fields below for as long as it has no
 * widgets, so the footer doesn't go blank before it's set up with widgets.
 * The fallback logo defaults to the header's custom logo, but Aspect >
 * Customize > BOCP — Date companie (footer) > "Logo footer" lets it be
 * swapped for a different image (e.g. a light-on-dark variant) without
 * touching the header.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$footer_note = bocp_company_field( 'bocp_company_footer_note' );
$bottom_line = bocp_company_field( 'bocp_footer_bottom' );
$footer_logo = get_theme_mod( 'bocp_footer_logo', '' );
?>
<footer class="footer">
	<div class="wrap">
		<div class="footer-grid">
			<div class="footer-brand">
				<?php if ( is_active_sidebar( 'footer-brand' ) ) : ?>
					<?php dynamic_sidebar( 'footer-brand' ); ?>
				<?php else : ?>
					<?php if ( $footer_logo ) : ?>
						<img src="<?php echo esc_url( $footer_logo ); ?>" alt="<?php bloginfo( 'name' ); ?>" />
					<?php elseif ( has_custom_logo() ) : ?>
						<?php the_custom_logo(); ?>
					<?php else : ?>
						<strong style="color:#fff"><?php bloginfo( 'name' ); ?></strong>
					<?php endif; ?>
					<?php if ( $footer_note ) : ?>
						<p><?php echo esc_html( $footer_note ); ?></p>
					<?php endif; ?>
				<?php endif; ?>
			</div>

			<?php if ( is_active_sidebar( 'footer-col-1' ) ) : ?>
				<?php dynamic_sidebar( 'footer-col-1' ); ?>
			<?php endif; ?>

			<?php if ( is_active_sidebar( 'footer-col-2' ) ) : ?>
				<?php dynamic_sidebar( 'footer-col-2' ); ?>
			<?php endif; ?>

			<?php if ( is_active_sidebar( 'footer-contact' ) ) : ?>
				<?php dynamic_sidebar( 'footer-contact' ); ?>
			<?php endif; ?>
		</div>

		<?php if ( $bottom_line ) : ?>
			<div class="footer-bottom">
				<div><?php echo esc_html( $bottom_line ); ?></div>
			</div>
		<?php endif; ?>
	</div>
</footer>
