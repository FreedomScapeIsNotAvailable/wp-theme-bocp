<?php
/**
 * Closes <main> (opened in header.php), prints the footer, wp_footer().
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</main>

<?php get_template_part( 'template-parts/footer-columns' ); ?>

<?php wp_footer(); ?>
</body>
</html>
