<?php
/**
 * 404 page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="section" style="text-align:center">
	<div class="wrap">
		<span class="kicker" style="justify-content:center">404</span>
		<h1 class="h-display" style="margin-top:14px"><?php esc_html_e( 'Pagina nu a fost găsită', 'bocp' ); ?></h1>
		<p class="lead muted" style="margin:14px auto 0;max-width:520px"><?php esc_html_e( 'Ne pare rău, pagina căutată nu există sau a fost mutată.', 'bocp' ); ?></p>
		<div class="cta-actions">
			<a class="btn btn-primary btn-lg" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Înapoi la Acasă', 'bocp' ); ?></a>
		</div>
	</div>
</section>

<?php
get_footer();
