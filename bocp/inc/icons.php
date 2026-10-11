<?php
/**
 * Inline SVG icon set — outline, 24px viewBox, currentColor, ~1.8 stroke.
 * Ported 1:1 from the Claude Design mockup's assets/icons.jsx so every icon
 * used in the design is available as bocp_icon( $key ).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the inner <path>/<circle>/<rect> markup for an icon key.
 * Kept as a function (not a static array) so it's only built when needed.
 */
function bocp_icon_paths() {
	return array(
		'erp'        => '<rect x="4" y="4" width="16" height="16" rx="2"/><path d="M9 4v16M4 9h5M4 14h5"/><path d="M13 9h4M13 13h4"/>',
		'crm'        => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
		'market'     => '<path d="M3 9h18l-1.5 11.5a1 1 0 0 1-1 .5H5.5a1 1 0 0 1-1-.5z"/><path d="M3 9l2-5h14l2 5"/><path d="M9 13a3 3 0 0 0 6 0"/>',
		'work'       => '<path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18v3h3l6.3-6.3a4 4 0 0 0 5.4-5.4l-2.6 2.6-2.1-.4-.4-2.1z"/>',
		'wms'        => '<path d="M3 21V9l9-5 9 5v12"/><path d="M3 21h18"/><rect x="7" y="13" width="4" height="8"/><rect x="13" y="13" width="4" height="4"/>',
		'arrowRight' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'arrowNext'  => '<path d="M9 6l6 6-6 6"/>',
		'check'      => '<path d="M20 6 9 17l-5-5"/>',
		'checkCircle'=> '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4 12 14.01l-3-3"/>',
		'cloud'      => '<path d="M17.5 19a4.5 4.5 0 0 0 .5-9 6 6 0 0 0-11.6-1.5A4 4 0 0 0 6.5 19z"/>',
		'layers'     => '<path d="M12 2 2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5M2 12l10 5 10-5"/>',
		'zap'        => '<path d="M13 2 3 14h9l-1 8 10-12h-9z"/>',
		'refresh'    => '<path d="M21 2v6h-6M3 12a9 9 0 0 1 15-6.7L21 8M3 22v-6h6M21 12a9 9 0 0 1-15 6.7L3 16"/>',
		'truck'      => '<path d="M1 3h15v13H1z"/><path d="M16 8h4l3 3v5h-7z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
		'file'       => '<path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><path d="M14 3v6h6M9 13h6M9 17h4"/>',
		'box'        => '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><path d="M3.27 6.96 12 12.01l8.73-5.05M12 22.08V12"/>',
		'cart'       => '<circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>',
		'barcode'    => '<path d="M3 5v14M7 5v14M11 5v14M14 5v10M14 17v2M18 5v14M21 5v14"/>',
		'pin'        => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/>',
		'tag'        => '<path d="M20.6 13.4 12 22l-9-9V4h9l8.6 8.6a2 2 0 0 1 0 2.8z"/><circle cx="7.5" cy="7.5" r="1.5"/>',
		'chart'      => '<path d="M3 3v18h18"/><path d="M7 15l3-4 3 2 4-6"/>',
		'sync'       => '<path d="M17 2l4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14M7 22l-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/>',
		'plug'       => '<path d="M9 2v6M15 2v6M6 8h12v3a6 6 0 0 1-12 0z"/><path d="M12 17v5"/>',
		'scan'       => '<path d="M3 7V5a2 2 0 0 1 2-2h2M17 3h2a2 2 0 0 1 2 2v2M21 17v2a2 2 0 0 1-2 2h-2M7 21H5a2 2 0 0 1-2-2v-2"/><path d="M7 12h10"/>',
		'clipboard'  => '<rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M9 12h6M9 16h4"/>',
		'wrench'     => '<path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18v3h3l6.3-6.3a4 4 0 0 0 5.4-5.4l-2.6 2.6-2.1-.4-.4-2.1z"/>',
		'route'      => '<circle cx="6" cy="19" r="3"/><circle cx="18" cy="5" r="3"/><path d="M9 19h6a4 4 0 0 0 0-8H9a4 4 0 0 1 0-8"/>',
		'shield'     => '<path d="M12 2 4 5v6c0 5 3.5 8.5 8 11 4.5-2.5 8-6 8-11V5z"/><path d="m9 12 2 2 4-4"/>',
		'users'      => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
		'phone'      => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3-8.6A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.6a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.5-1.2a2 2 0 0 1 2.1-.5c.8.3 1.7.6 2.6.7a2 2 0 0 1 1.7 2z"/>',
		'mail'       => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 6-10 7L2 6"/>',
		'building'   => '<rect x="4" y="2" width="16" height="20" rx="1"/><path d="M9 22v-4h6v4M8 6h.01M12 6h.01M16 6h.01M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M16 14h.01"/>',
		'menu'       => '<path d="M3 6h18M3 12h18M3 18h18"/>',
		'close'      => '<path d="M18 6 6 18M6 6l12 12"/>',
		'image'      => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/>',
		'clock'      => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'calendar'   => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
		'coins'      => '<circle cx="8" cy="8" r="6"/><path d="M18.09 10.37A6 6 0 1 1 10.34 18M7 6h1v4M16.71 13.88l.7.71-2.82 2.82"/>',
		'percent'    => '<path d="M19 5 5 19"/><circle cx="6.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/>',
		'bell'       => '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/>',
		'globe'      => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20 15 15 0 0 1 0-20z"/>',
		'settings'   => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.9.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.9l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.9.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.9-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.9V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/>',
		'gift'       => '<rect x="3" y="8" width="18" height="4" rx="1"/><path d="M12 8v13M5 12v9h14v-9"/><path d="M12 8S10.5 3 8 3a2.5 2.5 0 0 0 0 5zM12 8s1.5-5 4-5a2.5 2.5 0 0 1 0 5z"/>',
		'receipt'    => '<path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1V2l-2 1-2-1-2 1-2-1-2 1-2-1z"/><path d="M8 7h8M8 11h8M8 15h5"/>',
		'handshake'  => '<path d="m11 17 2 2a1 1 0 0 0 3-3"/><path d="m14 14 2.5 2.5a1 1 0 0 0 3-3l-3.9-3.9a2 2 0 0 1 0-2.8L17 5"/><path d="M7 14 3 10a2 2 0 0 1 0-2.8L7 3l4 4"/><path d="m7 14 3 3"/>',
		'map'        => '<path d="M9 3 3 6v15l6-3 6 3 6-3V3l-6 3-6-3z"/><path d="M9 3v15M15 6v15"/>',
		'car'        => '<path d="M5 13 6.5 8h11L19 13M5 13h14v5H5zM5 13a2 2 0 0 0-2 2v3h2M19 13a2 2 0 0 1 2 2v3h-2"/><circle cx="7.5" cy="18" r="1"/><circle cx="16.5" cy="18" r="1"/>',
		'factory'    => '<path d="M2 20h20V9l-6 4V9l-6 4V4H2z"/><path d="M6 20v-4M10 20v-4M14 20v-4M18 20v-4"/>',
		'star'       => '<path d="M12 2l2.9 6.3 6.9.7-5.1 4.6 1.4 6.8L12 17.8 5.9 20.4l1.4-6.8L2.2 9l6.9-.7z"/>',
		'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
		'ai'         => '<path d="M12 3l1.6 4.4L18 9l-4.4 1.6L12 15l-1.6-4.4L6 9l4.4-1.6z"/><path d="M18.5 15.5l.7 1.8 1.8.7-1.8.7-.7 1.8-.7-1.8-1.8-.7 1.8-.7zM5 15l.5 1.3 1.3.5-1.3.5L5 18.6l-.5-1.3-1.3-.5 1.3-.5z"/>',
		'chat'       => '<path d="M21 11.5a8.4 8.4 0 0 1-9 8.4 9.9 9.9 0 0 1-3.4-.6L3 21l1.7-4.6A8.4 8.4 0 0 1 12 3a8.4 8.4 0 0 1 9 8.5z"/>',
		'toggle'     => '<rect x="2" y="7" width="20" height="10" rx="5"/><circle cx="16" cy="12" r="2.6"/>',
		'key'        => '<path d="M15 7a4 4 0 1 1-3.9 5L9 14H7v2H5v2H2v-3l7.1-7.1A4 4 0 0 1 15 7z"/><path d="M16.5 7.5h.01"/>',
		'download'   => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5M12 15V3"/>',
		'lock'       => '<rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>',
		'external'   => '<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><path d="M15 3h6v6M10 14 21 3"/>',
		'play'       => '<path d="M6 4l14 8-14 8z"/>',
	);
}

/**
 * Echo/return an inline <svg> for the given icon key. Falls back silently
 * (empty string) for unknown keys so a bad meta value never breaks layout.
 *
 * @param string $key   One of the keys in bocp_icon_paths().
 * @param array  $attrs Extra HTML attributes for the <svg> tag (e.g. class).
 */
function bocp_icon( $key, $attrs = array() ) {
	$paths = bocp_icon_paths();
	if ( empty( $key ) || ! isset( $paths[ $key ] ) ) {
		return '';
	}

	$attr_str = '';
	foreach ( $attrs as $k => $v ) {
		$attr_str .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
	}

	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"' . $attr_str . '>' . $paths[ $key ] . '</svg>';
}

/**
 * key => label list for <select> icon pickers in meta boxes.
 */
function bocp_icon_choices() {
	return array(
		''            => __( '— fără iconiță —', 'bocp' ),
		'erp'         => 'ERP',
		'crm'         => 'CRM',
		'market'      => 'Marketplace',
		'work'        => 'WorkManager / cheie',
		'wms'         => 'WMS / depozit',
		'cloud'       => 'Cloud',
		'layers'      => 'Layers / modular',
		'zap'         => 'Zap / automatizare',
		'sync'        => 'Sync',
		'shield'      => 'Shield / conformitate',
		'handshake'   => 'Handshake',
		'receipt'     => 'Chitanță / factură',
		'file'        => 'Document',
		'tag'         => 'Etichetă',
		'truck'       => 'Curier / livrare',
		'box'         => 'Cutie / stoc',
		'coins'       => 'Bani / încasări',
		'gift'        => 'Cadou / voucher',
		'chart'       => 'Grafic / raport',
		'cart'        => 'Coș / comenzi',
		'barcode'     => 'Cod de bare',
		'pin'         => 'Locație',
		'clipboard'   => 'Clipboard',
		'wrench'      => 'Cheie fixă / reparație',
		'route'       => 'Traseu / dispecerat',
		'users'       => 'Utilizatori / clienți',
		'phone'       => 'Telefon',
		'mail'        => 'Email',
		'building'    => 'Clădire / firmă',
		'clock'       => 'Ceas / timp',
		'calendar'    => 'Calendar',
		'percent'     => 'Procent / reducere',
		'bell'        => 'Notificare',
		'globe'       => 'Global / online',
		'settings'    => 'Setări',
		'map'         => 'Hartă',
		'car'         => 'Autovehicul',
		'factory'     => 'Producție / fabrică',
		'star'        => 'Stea / recomandare',
		'plug'        => 'Conector / integrare',
		'scan'        => 'Scanare',
		'refresh'     => 'Reîmprospătare',
		'checkCircle' => 'Bifă în cerc',
		'check'       => 'Bifă',
		'arrowRight'  => 'Săgeată dreapta',
		'ai'          => 'AI / conector AI',
		'chat'        => 'Chat / conversație',
		'toggle'      => 'Comutator (toggle)',
		'key'         => 'Cheie / autentificare',
		'download'    => 'Descărcare / instalare',
		'lock'        => 'Lacăt / securitate',
		'external'    => 'Link extern',
	);
}
