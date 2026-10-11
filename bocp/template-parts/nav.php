<?php
/**
 * Sticky header nav + mobile drawer. Menu items come from the "Meniu
 * principal (header)" WP nav menu (Aspect > Meniuri) — nothing here is
 * hardcoded, so it works the same on any site that assigns its own menu.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$front_page_id = (int) get_option( 'page_on_front' );
$home_url      = $front_page_id ? get_permalink( $front_page_id ) : home_url( '/' );
$has_menu      = has_nav_menu( 'primary' );
?>
<header class="nav">
	<div class="wrap nav-inner">
		<div class="nav-logo">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a href="<?php echo esc_url( $home_url ); ?>" aria-label="<?php esc_attr_e( 'Acasă', 'bocp' ); ?>">
					<strong><?php bloginfo( 'name' ); ?></strong>
				</a>
			<?php endif; ?>
		</div>

		<div class="nav-right">
			<?php if ( $has_menu ) : ?>
				<nav class="nav-links">
					<?php
					wp_nav_menu( array(
						'theme_location' => 'primary',
						'container'      => false,
						'items_wrap'     => '%3$s',
						'link_before'    => '',
						'link_after'     => '',
						'walker'         => new BOCP_Nav_Walker(),
					) );
					?>
				</nav>
			<?php endif; ?>

			<div class="nav-cta">
				<?php echo bocp_get_nav_cta_buttons(); ?>
			</div>

			<button class="nav-burger" aria-label="<?php esc_attr_e( 'Deschide meniul', 'bocp' ); ?>">
				<?php echo bocp_icon( 'menu' ); ?>
			</button>
		</div>
	</div>
</header>

<div class="drawer-scrim"></div>
<aside class="drawer" aria-hidden="true">
	<div class="drawer-head">
		<?php if ( has_custom_logo() ) : ?>
			<?php the_custom_logo(); ?>
		<?php else : ?>
			<a href="<?php echo esc_url( $home_url ); ?>">
				<strong><?php bloginfo( 'name' ); ?></strong>
			</a>
		<?php endif; ?>
		<button class="nav-burger" aria-label="<?php esc_attr_e( 'Închide meniul', 'bocp' ); ?>">
			<?php echo bocp_icon( 'close' ); ?>
		</button>
	</div>

	<?php if ( $has_menu ) : ?>
		<nav class="drawer-links">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'items_wrap'     => '%3$s',
				'walker'         => new BOCP_Drawer_Walker(),
			) );
			?>
		</nav>
	<?php endif; ?>

	<div class="drawer-cta">
		<?php echo bocp_get_nav_cta_buttons( true ); ?>
	</div>
</aside>
