<?php
/**
 * Sticky header nav + optional secondary bar + mobile drawer.
 *
 * Menu items come from the WP nav menus assigned in Aspect > Meniuri:
 *  - "Meniu principal (header)"           -> the row next to the logo
 *  - "Meniu secundar (bară sub header)"   -> a second row under it; the bar only
 *    renders when a menu is assigned, so sites that don't need it see no change.
 * Both appear in the mobile drawer, one after the other. Nothing here is
 * hardcoded, so it works the same on any site that assigns its own menus.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$front_page_id = (int) get_option( 'page_on_front' );
$home_url      = $front_page_id ? get_permalink( $front_page_id ) : home_url( '/' );
$has_menu      = has_nav_menu( 'primary' );
$has_secondary = has_nav_menu( 'secondary' );
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
				<nav class="nav-links" aria-label="<?php esc_attr_e( 'Meniu principal', 'bocp' ); ?>">
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

	<?php if ( $has_secondary ) : ?>
		<div class="nav-secondary">
			<div class="wrap">
				<nav class="nav-links nav-links-secondary" aria-label="<?php esc_attr_e( 'Meniu secundar', 'bocp' ); ?>">
					<?php
					wp_nav_menu( array(
						'theme_location' => 'secondary',
						'container'      => false,
						'items_wrap'     => '%3$s',
						'link_before'    => '',
						'link_after'     => '',
						'walker'         => new BOCP_Nav_Walker(),
					) );
					?>
				</nav>
			</div>
		</div>
	<?php endif; ?>
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

	<div class="drawer-scroll">
		<?php if ( $has_menu ) : ?>
			<nav class="drawer-links" aria-label="<?php esc_attr_e( 'Meniu principal', 'bocp' ); ?>">
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

		<?php if ( $has_secondary ) : ?>
			<div class="drawer-sep"></div>
			<nav class="drawer-links" aria-label="<?php esc_attr_e( 'Meniu secundar', 'bocp' ); ?>">
				<?php
				wp_nav_menu( array(
					'theme_location' => 'secondary',
					'container'      => false,
					'items_wrap'     => '%3$s',
					'walker'         => new BOCP_Drawer_Walker(),
				) );
				?>
			</nav>
		<?php endif; ?>
	</div>

	<div class="drawer-cta">
		<?php echo bocp_get_nav_cta_buttons( true ); ?>
	</div>
</aside>
