<?php
/**
 * Customizer: brand colors, fonts, company/footer info, and footer link
 * columns.
 *
 * Everything here is stored as a theme_mod — never in code — so it
 * survives theme updates and travels with the site (or gets re-entered in
 * ~2 minutes on a fresh install of this theme elsewhere). The header logo
 * uses core's native `custom-logo` support (registered in inc/setup.php)
 * instead of a custom control, so it shows up under the standard Site
 * Identity section. The footer logo (`bocp_footer_logo` below) is a
 * separate, optional image — lets the footer use a different mark (e.g.
 * a light-on-dark variant) than the header; falls back to the header logo
 * when empty (see template-parts/footer-columns.php). Together with
 * bocp_company_footer_note below, it's also the fallback shown in the
 * footer's brand column (widget area "footer-brand") for as long as that
 * area has no widgets — add an Image + Text widget there instead if you'd
 * rather manage it from Aspect > Widgets.
 *
 * IMPORTANT: get_theme_mod( $key ) calls elsewhere in the theme (footer,
 * shortcodes) must pass the SAME default as registered here — the
 * "default" given to add_setting() only feeds the Customizer's own
 * preview/reset behaviour, it does NOT make get_theme_mod() return that
 * value on the rest of the site until you also pass it as get_theme_mod's
 * second argument. bocp_company_field_defaults() below exists so both
 * places share one source of truth instead of the default silently
 * drifting apart.
 *
 * The footer's 4 content columns (Logo/Module/Companie/Contact) are widget
 * areas (Aspect > Widgets), registered in inc/setup.php — not Customizer
 * fields — so they can be rearranged with any widget, no code involved.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** id => [label, type ('text'|'textarea'), default]. */
function bocp_company_field_defaults() {
	return array(
		'bocp_company_name'        => array( __( 'Nume firmă', 'bocp' ), 'text', 'S.C. Real Life S.R.L.' ),
		'bocp_company_address'     => array( __( 'Adresă (poate avea mai multe linii)', 'bocp' ), 'textarea', "Piața Matia Corvin nr. 3, ap. 2\nTârgu Mureș, 540305, jud. Mureș" ),
		'bocp_company_phone'       => array( __( 'Telefon', 'bocp' ), 'text', '0365.424.438' ),
		'bocp_company_email'       => array( __( 'Email', 'bocp' ), 'text', 'office@reallife.solutions' ),
		'bocp_company_legal'       => array( __( 'Date fiscale (CUI, Reg. Com., cont bancar)', 'bocp' ), 'textarea', "CUI: RO23310817\nReg. Com.: J26/306/2008\nCont RON: RO34BACX0000000201776001\nUniCredit Bank, Târgu Mureș" ),
		'bocp_company_footer_note' => array( __( 'Text scurt sub logo, în footer', 'bocp' ), 'textarea', 'Business Online Control Panel — ecosistemul software care reunește ERP, CRM, integrări marketplace, WorkManager și WMS într-un singur loc.' ),
		'bocp_footer_bottom'       => array( __( 'Linie de copyright (footer, jos)', 'bocp' ), 'text', '© ' . gmdate( 'Y' ) . ' S.C. Real Life S.R.L. · CUI RO23310817 · J26/306/2008' ),
	);
}

/** Reads a company-info field with its registered default already applied. */
function bocp_company_field( $id ) {
	$defaults = bocp_company_field_defaults();
	$default  = isset( $defaults[ $id ] ) ? $defaults[ $id ][2] : '';
	return get_theme_mod( $id, $default );
}

function bocp_customize_register( $wp_customize ) {

	/* ---------------- Brand colors ---------------- */
	$wp_customize->add_section( 'bocp_colors', array(
		'title'    => __( 'BOCP — Culori brand', 'bocp' ),
		'priority' => 25,
	) );

	$wp_customize->add_setting( 'bocp_color_primary', array(
		'default'           => '#dc2626',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bocp_color_primary', array(
		'label'   => __( 'Culoare primară (butoane, accente)', 'bocp' ),
		'section' => 'bocp_colors',
	) ) );

	$wp_customize->add_setting( 'bocp_color_dark', array(
		'default'           => '#000000',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bocp_color_dark', array(
		'label'       => __( 'Culoare secundară (footer, secțiuni închise)', 'bocp' ),
		'description' => __( 'Nuanțele de hover/active pentru culoarea primară se calculează automat.', 'bocp' ),
		'section'     => 'bocp_colors',
	) ) );

	/* ---------------- Fonts ---------------- */
	$wp_customize->add_setting( 'bocp_nav_link_color', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bocp_nav_link_color', array(
		'label'       => __( 'Culoare linkuri meniu principal (opțional)', 'bocp' ),
		'description' => __( 'Lasă gol pentru culoarea implicită a temei. Bara secundară de sub header își păstrează culoarea.', 'bocp' ),
		'section'     => 'bocp_colors',
	) ) );

	$wp_customize->add_section( 'bocp_fonts', array(
		'title'    => __( 'BOCP — Fonturi', 'bocp' ),
		'priority' => 26,
	) );

	$wp_customize->add_setting( 'bocp_font_choice', array(
		'default'           => 'plex',
		'sanitize_callback' => 'bocp_sanitize_font_choice',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'bocp_font_choice', array(
		'type'        => 'radio',
		'label'       => __( 'Pereche de fonturi', 'bocp' ),
		'description' => __( 'IBM Plex este servit local din temă (fără dependență externă). Fonturi sistem = zero fișiere descărcate, cel mai rapid.', 'bocp' ),
		'section'     => 'bocp_fonts',
		'choices'     => array(
			'plex'   => __( 'IBM Plex Sans + Mono (implicit, self-hosted)', 'bocp' ),
			'system' => __( 'Fonturi sistem (fără fișiere de font)', 'bocp' ),
		),
	) );

	/* ---------------- Header CTA buttons ---------------- */
	$wp_customize->add_section( 'bocp_nav_buttons', array(
		'title'    => __( 'BOCP — Butoane meniu', 'bocp' ),
		'priority' => 28,
	) );

	$nav_button_fields = array(
		'bocp_nav_btn1_label' => __( 'Buton 1 — text (ex. „Cere demo")', 'bocp' ),
		'bocp_nav_btn1_url'   => __( 'Buton 1 — link', 'bocp' ),
		'bocp_nav_btn2_label' => __( 'Buton 2 — text (ex. „Testează gratuit")', 'bocp' ),
		'bocp_nav_btn2_url'   => __( 'Buton 2 — link', 'bocp' ),
	);
	foreach ( $nav_button_fields as $id => $label ) {
		$wp_customize->add_setting( $id, array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( $id, array(
			'type'    => 'text',
			'label'   => $label,
			'section' => 'bocp_nav_buttons',
		) );
	}

	/* ---------------- Company / footer info ---------------- */
	$wp_customize->add_section( 'bocp_company', array(
		'title'    => __( 'BOCP — Date companie (footer)', 'bocp' ),
		'priority' => 27,
	) );

	$wp_customize->add_setting( 'bocp_footer_logo', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'bocp_footer_logo', array(
		'label'       => __( 'Logo footer', 'bocp' ),
		'description' => __( 'Opțional — dacă vrei alt logo aici decât cel din Site Identity (header). Dacă nu alegi nimic, footer-ul folosește automat logo-ul din header.', 'bocp' ),
		'section'     => 'bocp_company',
		'priority'    => 5,
	) ) );

	foreach ( bocp_company_field_defaults() as $id => $field ) {
		list( $label, $type, $default ) = $field;
		$wp_customize->add_setting( $id, array(
			'default'           => $default,
			'sanitize_callback' => 'textarea' === $type ? 'sanitize_textarea_field' : 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( $id, array(
			'type'    => $type,
			'label'   => $label,
			'section' => 'bocp_company',
		) );
	}

	// Footer columns (Logo/Module/Companie/Contact) are widget areas now —
	// Aspect > Widgets — not Customizer fields; much easier to rearrange.
}
add_action( 'customize_register', 'bocp_customize_register' );

function bocp_sanitize_font_choice( $value ) {
	return in_array( $value, array( 'plex', 'system' ), true ) ? $value : 'plex';
}
