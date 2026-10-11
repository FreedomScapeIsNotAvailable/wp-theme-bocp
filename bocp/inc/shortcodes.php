<?php
/**
 * Small, optional shortcodes an editor can type/paste directly into the
 * Classic Editor content — the native WordPress way to reuse a bit of
 * dynamic output inside otherwise-static page content.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * [bocp_icon key="erp"] — inline SVG icon, for hand-authoring new content
 * blocks (e.g. a new card) without leaving the editor to look up markup.
 */
function bocp_icon_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'key' => '' ), $atts, 'bocp_icon' );
	return bocp_icon( sanitize_key( $atts['key'] ) );
}
add_shortcode( 'bocp_icon', 'bocp_icon_shortcode' );

/**
 * [bocp_contact_info] — the phone/email/address/legal block driven by
 * Aspect > Personalizare > BOCP — Date companie, so it can be dropped into
 * the Contact page (or anywhere else) without retyping the company details.
 */
function bocp_contact_info_shortcode() {
	$phone   = bocp_company_field( 'bocp_company_phone' );
	$email   = bocp_company_field( 'bocp_company_email' );
	$address = bocp_company_field( 'bocp_company_address' );
	$legal   = bocp_company_field( 'bocp_company_legal' );
	$name    = bocp_company_field( 'bocp_company_name' );

	ob_start();
	?>
	<aside class="contact-side">
		<?php if ( $phone ) : ?>
			<div class="contact-row">
				<span class="ichip"><?php echo bocp_icon( 'phone' ); ?></span>
				<div>
					<div class="contact-row-lbl"><?php esc_html_e( 'Telefon', 'bocp' ); ?></div>
					<div class="contact-row-val"><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></div>
				</div>
			</div>
		<?php endif; ?>
		<?php if ( $email ) : ?>
			<div class="contact-row">
				<span class="ichip"><?php echo bocp_icon( 'mail' ); ?></span>
				<div>
					<div class="contact-row-lbl"><?php esc_html_e( 'Email', 'bocp' ); ?></div>
					<div class="contact-row-val"><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></div>
				</div>
			</div>
		<?php endif; ?>
		<?php if ( $address ) : ?>
			<div class="contact-row">
				<span class="ichip"><?php echo bocp_icon( 'pin' ); ?></span>
				<div>
					<div class="contact-row-lbl"><?php esc_html_e( 'Adresă', 'bocp' ); ?></div>
					<div class="contact-row-val"><?php echo nl2br( esc_html( $address ) ); ?></div>
				</div>
			</div>
		<?php endif; ?>
		<?php if ( $legal ) : ?>
			<div class="card card-pad" style="background:var(--surface-sunken);box-shadow:none">
				<div class="contact-row-lbl" style="margin-bottom:8px"><?php echo esc_html( $name ? $name : __( 'Date firmă', 'bocp' ) ); ?></div>
				<div class="mod-desc" style="line-height:1.7"><?php echo nl2br( esc_html( $legal ) ); ?></div>
			</div>
		<?php endif; ?>
	</aside>
	<?php
	return ob_get_clean();
}
add_shortcode( 'bocp_contact_info', 'bocp_contact_info_shortcode' );
