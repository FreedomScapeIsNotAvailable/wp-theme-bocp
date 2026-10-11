<?php
/**
 * Aspect > BOCP Setup — the only place this theme ever creates content.
 *
 * Nothing happens automatically on theme activation. Only when the admin
 * clicks the button here do the 7 BOCP pages get created, as DRAFTS titled
 * "NEW - ...", using the single "BOCP — Pagină" template. Their content is
 * written as real, separate Block Editor (Gutenberg) blocks — Group,
 * Heading, Paragraph, Columns, Image — matching the original design's
 * classes, so every card/step/stat/quote is its own movable, duplicable
 * block, and every title/description is independently editable text, no
 * "Edit as HTML" required. No meta boxes, no custom fields.
 *
 * Running the button again only creates pages that are still missing
 * (tracked via the `_bocp_seed_key` post meta) — unless "Suprascrie
 * paginile existente" is checked, in which case it also resets the
 * content/template of pages already created (useful right after a theme
 * update changes how pages are built, or if you want to start over on one).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---------------------------------------------------------------------
 * Page registry
 * ------------------------------------------------------------------- */

function bocp_seed_page_registry() {
	$template = 'page-templates/template-canvas.php';
	return array(
		'home'    => array( 'title' => __( 'Acasă', 'bocp' ), 'template' => $template ),
		'ai'      => array( 'title' => __( 'AI Ready', 'bocp' ), 'template' => $template ),
		'erp'     => array( 'title' => __( 'CloudSales ERP', 'bocp' ), 'template' => $template ),
		'crm'     => array( 'title' => __( 'CRM-uri', 'bocp' ), 'template' => $template ),
		'market'  => array( 'title' => __( 'Integrări Marketplace', 'bocp' ), 'template' => $template ),
		'work'    => array( 'title' => __( 'WorkManager', 'bocp' ), 'template' => $template ),
		'wms'     => array( 'title' => __( 'WMS', 'bocp' ), 'template' => $template ),
		'contact' => array( 'title' => __( 'Contact', 'bocp' ), 'template' => $template ),
	);
}

/** Finds an already-seeded page for $key, if any. Returns post ID or 0. */
function bocp_find_seeded_page( $key ) {
	$posts = get_posts( array(
		'post_type'      => 'page',
		'post_status'    => array( 'draft', 'publish', 'pending', 'private', 'future' ),
		'meta_key'       => '_bocp_seed_key',
		'meta_value'     => $key,
		'posts_per_page' => 1,
		'fields'         => 'ids',
	) );
	return $posts ? (int) $posts[0] : 0;
}

/* ---------------------------------------------------------------------
 * Block Editor (Gutenberg) primitives.
 *
 * Every piece of text content is now a REAL, separately-selectable block
 * (core/paragraph, core/heading) nested inside core/group / core/columns
 * containers that carry our design-system classes — not one opaque
 * Custom HTML blob. This means: each card/step/stat/quote is its own
 * Group block (movable, duplicable, deletable from the block list), and
 * each title/description inside it is its own Heading/Paragraph block
 * (click straight into the text and edit it, no "Edit as HTML" needed).
 *
 * Decorative icons (bocp_icon() SVGs) are deliberately kept OUTSIDE any
 * Paragraph/Heading's rich-text content: the Block Editor's RichText only
 * preserves formatting it recognises (bold/italic/links/etc.) and silently
 * drops unrecognised inline markup like raw <svg> on load — so icons live
 * in their own tiny core/html block next to the text, never inside it.
 * ------------------------------------------------------------------- */

/**
 * Since WP 6.1, core/group always computes an effective "layout" (default
 * type = "flow") and its save() adds `is-layout-flow` / a matching
 * `wp-block-group-is-layout-flow` selector class even when no layout is
 * customised — if a hand-written block comment omits attributes/classes
 * the running WP version's save() would have produced, the Block Editor
 * flags that instance as "unexpected or invalid content" and swaps its
 * normal toolbar (move/duplicate/drag) for a bare "resolve" prompt. Adding
 * both here defensively is what makes every Group block's real toolbar —
 * and therefore duplicate/move — show up normally instead.
 */
function bocp_block_group( $class_name, $inner ) {
	$attrs      = array( 'layout' => array( 'type' => 'default' ) );
	if ( $class_name ) {
		$attrs['className'] = $class_name;
	}
	$class_attr = 'wp-block-group is-layout-flow wp-block-group-is-layout-flow' . ( $class_name ? ' ' . esc_attr( $class_name ) : '' );
	return '<!-- wp:group ' . wp_json_encode( $attrs ) . ' -->' . "\n"
		. '<div class="' . $class_attr . '">' . $inner . '</div>' . "\n"
		. '<!-- /wp:group -->';
}

function bocp_block_heading( $level, $class_name, $inner_html ) {
	$level      = max( 1, min( 6, (int) $level ) );
	$class_attr = 'wp-block-heading' . ( $class_name ? ' ' . esc_attr( $class_name ) : '' );
	$json       = array( 'level' => $level );
	if ( $class_name ) {
		$json['className'] = $class_name;
	}
	return '<!-- wp:heading ' . wp_json_encode( $json ) . ' -->' . "\n"
		. '<h' . $level . ' class="' . $class_attr . '">' . $inner_html . '</h' . $level . '>' . "\n"
		. '<!-- /wp:heading -->';
}

function bocp_block_paragraph( $class_name, $inner_html ) {
	$json  = $class_name ? ' {"className":"' . esc_attr( $class_name ) . '"}' : '';
	$class = $class_name ? ' class="' . esc_attr( $class_name ) . '"' : '';
	return '<!-- wp:paragraph' . $json . ' -->' . "\n"
		. '<p' . $class . '>' . $inner_html . '</p>' . "\n"
		. '<!-- /wp:paragraph -->';
}

/** Decorative, non-editable-as-text markup (icons, arrows) as its own tiny block. */
function bocp_block_html( $html ) {
	return "<!-- wp:html -->\n" . $html . "\n<!-- /wp:html -->";
}

function bocp_block_icon_chip( $icon, $extra_class = 'lg' ) {
	$class = trim( 'ichip ' . $extra_class );
	return bocp_block_html( '<span class="' . esc_attr( $class ) . '">' . bocp_icon( $icon ) . '</span>' );
}

/** $btn1_label/$btn2_label may be empty to omit that button. Returns one Paragraph block (or ''). */
function bocp_block_buttons( $btn1_label, $btn1_url, $btn2_label = '', $btn2_url = '', $btn2_extra_class = '', $wrap_class = 'hero-actions' ) {
	if ( ! $btn1_label && ! $btn2_label ) {
		return '';
	}
	$html = '';
	if ( $btn1_label ) {
		$html .= '<a class="btn btn-primary btn-lg" href="' . esc_url( $btn1_url ) . '">' . esc_html( $btn1_label ) . '</a> ';
	}
	if ( $btn2_label ) {
		$html .= '<a class="btn btn-outline btn-lg' . ( $btn2_extra_class ? ' ' . esc_attr( $btn2_extra_class ) : '' ) . '" href="' . esc_url( $btn2_url ) . '">' . esc_html( $btn2_label ) . '</a>';
	}
	return bocp_block_paragraph( $wrap_class, $html );
}

/**
 * Builds an inline-SVG "add an image here" placeholder as a data URI —
 * used as the starting `url` of a real core/image block (see
 * bocp_block_image() below), so it's a genuine, natively-replaceable
 * WordPress image from the very first click, not a decorative <div>.
 */
function bocp_svg_placeholder_data_uri( $ratio, $label ) {
	list( $rw, $rh ) = array_map( 'floatval', explode( '/', $ratio ) );
	$vb_w = 400;
	$vb_h = (int) round( $vb_w * ( $rh / $rw ) );

	$svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $vb_w . '" height="' . $vb_h . '" viewBox="0 0 ' . $vb_w . ' ' . $vb_h . '">'
		. '<rect width="100%" height="100%" fill="#f9fafb"/>'
		. '<rect x="2" y="2" width="' . ( $vb_w - 4 ) . '" height="' . ( $vb_h - 4 ) . '" fill="none" stroke="#9ca3af" stroke-width="2" stroke-dasharray="8 6"/>'
		. '<circle cx="' . ( $vb_w / 2 ) . '" cy="' . ( $vb_h / 2 - 14 ) . '" r="16" fill="none" stroke="#9ca3af" stroke-width="2"/>'
		. '<path d="M' . ( $vb_w / 2 - 8 ) . ' ' . ( $vb_h / 2 - 18 ) . 'l16 16m0-16l-16 16" stroke="#9ca3af" stroke-width="2"/>'
		. '<text x="50%" y="' . ( $vb_h / 2 + 26 ) . '" font-family="sans-serif" font-size="13" fill="#6b7280" text-anchor="middle">' . esc_html( $label ) . '</text>'
		. '</svg>';

	return 'data:image/svg+xml,' . rawurlencode( $svg );
}

function bocp_block_image( $ratio, $alt ) {
	$src = bocp_svg_placeholder_data_uri( $ratio, $alt );
	return '<!-- wp:image {"alt":' . wp_json_encode( $alt ) . '} -->'
		. '<figure class="wp-block-image"><img src="' . esc_attr( $src ) . '" alt="' . esc_attr( $alt ) . '"/></figure>'
		. '<!-- /wp:image -->';
}

/**
 * Video placeholder — unlike bocp_block_image(), this is NOT a real
 * core/video block: an empty video block has no src, and WordPress drops
 * blocks with no real content on save, so it wouldn't survive a page save
 * before the real clip exists. Instead this is the same dashed-box visual
 * treatment as the image placeholder, as its own core/html block (see the
 * "decorative, non-editable-as-text markup" note above). Once the real MP4
 * is uploaded to the Media Library, replace this block with a native Video
 * block pointed at it.
 */
function bocp_block_video_placeholder( $id, $ratio, $title, $note = '' ) {
	list( $rw, $rh ) = array_map( 'floatval', explode( '/', $ratio ) );
	$html = '<div class="ph ph-video" style="aspect-ratio:' . esc_attr( $rw ) . '/' . esc_attr( $rh ) . '">'
		. '<span class="ph-badge">' . esc_html( $id ) . '</span>'
		. '<span class="ph-play">' . bocp_icon( 'play' ) . '</span>'
		. '<div class="ph-title">' . esc_html( $title ) . '</div>'
		. '<div class="ph-spec">Video · aspect ' . esc_html( $rw ) . ':' . esc_html( $rh ) . '</div>'
		. ( $note ? '<div class="ph-note">' . esc_html( $note ) . '</div>' : '' )
		. '</div>';
	return bocp_block_html( $html );
}

/** Small inline "menu location / URL" chip — e.g. showing where a feature lives in the app. */
function bocp_block_pathchip( $icon, $text ) {
	return bocp_block_html( '<div style="margin-top:12px"><span class="pathchip">' . bocp_icon( $icon ) . ' ' . esc_html( $text ) . '</span></div>' );
}

/** $left/$right are already-built block markup (concatenated blocks), placed directly as each column's children. */
function bocp_block_columns_raw( $left_blocks, $right_blocks, $class_name ) {
	return '<!-- wp:columns {"className":"' . esc_attr( $class_name ) . '"} -->'
		. '<div class="wp-block-columns ' . esc_attr( $class_name ) . '">'
		. '<!-- wp:column --><div class="wp-block-column">' . $left_blocks . '</div><!-- /wp:column -->'
		. '<!-- wp:column --><div class="wp-block-column">' . $right_blocks . '</div><!-- /wp:column -->'
		. '</div>'
		. '<!-- /wp:columns -->';
}

/* ---------------------------------------------------------------------
 * Page hero / home hero
 * ------------------------------------------------------------------- */

function bocp_block_page_hero( $links, $crumb_label, $kicker, $title, $lead, $btn1_label = '', $btn1_url = '', $btn2_label = '', $btn2_url = '' ) {
	$crumbs = bocp_block_html( '<div class="crumbs"><a href="' . esc_url( $links['home'] ) . '">Acasă</a> ' . bocp_icon( 'arrowNext' ) . ' <span>' . esc_html( $crumb_label ) . '</span></div>' );
	$inner  = ( $kicker ? bocp_block_paragraph( 'kicker', esc_html( $kicker ) ) : '' )
		. bocp_block_heading( 1, 'h-display', esc_html( $title ) )
		. bocp_block_paragraph( 'lead', esc_html( $lead ) )
		. bocp_block_buttons( $btn1_label, $btn1_url, $btn2_label, $btn2_url );
	return bocp_block_group( 'phero wrap', $crumbs . bocp_block_group( '', $inner ) );
}

/** Text-only hero content (no wrapper, no image) — becomes the left Columns child. */
function bocp_block_home_hero_text( $kicker, $title, $lead, $btn1_label, $btn1_url, $btn2_label, $btn2_url, $trust ) {
	$trust_items = '';
	foreach ( $trust as $t ) {
		$num          = bocp_block_paragraph( 'hero-trust-num', esc_html( $t[0] ) );
		$lbl          = bocp_block_paragraph( 'hero-trust-lbl', esc_html( $t[1] ) );
		$trust_items .= bocp_block_group( 'hero-trust-item', $num . $lbl );
	}
	return bocp_block_paragraph( 'kicker', esc_html( $kicker ) )
		. bocp_block_heading( 1, 'h-display', esc_html( $title ) )
		. bocp_block_paragraph( 'lead', esc_html( $lead ) )
		. bocp_block_buttons( $btn1_label, $btn1_url, $btn2_label, $btn2_url )
		. bocp_block_group( 'hero-trust', $trust_items );
}

/** Full hero block: core/columns (text + a real, replaceable core/image). */
function bocp_block_home_hero( $kicker, $title, $lead, $btn1_label, $btn1_url, $btn2_label, $btn2_url, $trust ) {
	$text  = bocp_block_home_hero_text( $kicker, $title, $lead, $btn1_label, $btn1_url, $btn2_label, $btn2_url, $trust );
	$image = bocp_block_image( '16 / 11', 'Captură a panoului de control BOCP' );
	return bocp_block_columns_raw( $text, $image, 'hero wrap hero-grid' );
}

/* ---------------------------------------------------------------------
 * Card grid
 * ------------------------------------------------------------------- */

function bocp_block_card( $icon, $title, $desc, $url = '', $link_label = 'Vezi mai mult' ) {
	$icon_b  = bocp_block_icon_chip( $icon, $url ? 'lg' : '' );
	$title_b = bocp_block_heading( 3, 'mod-title', esc_html( $title ) );
	$desc_b  = bocp_block_paragraph( 'mod-desc', esc_html( $desc ) );
	$link_b  = $url ? bocp_block_paragraph( 'mod-more', '<a href="' . esc_url( $url ) . '">' . esc_html( $link_label ) . ' →</a>' ) : '';
	return bocp_block_group( $url ? 'card card-lift mod' : 'card card-pad', $icon_b . $title_b . $desc_b . $link_b );
}

function bocp_block_card_dark( $title, $desc, $link_label, $url ) {
	$icon_b  = bocp_block_icon_chip( 'arrowRight', 'lg dark' );
	$title_b = bocp_block_heading( 3, 'mod-title', esc_html( $title ) );
	$desc_b  = bocp_block_paragraph( 'mod-desc', esc_html( $desc ) );
	$link_b  = bocp_block_paragraph( 'mod-more', '<a href="' . esc_url( $url ) . '">' . esc_html( $link_label ) . ' →</a>' );
	return bocp_block_group( 'card card-lift mod mod-dark', $icon_b . $title_b . $desc_b . $link_b );
}

/** $cards: array of [icon, title, desc, url, linkLabel]. */
function bocp_block_section_grid( $kicker, $title, $lead, $cards, $columns = 3, $sunken = false, $dark_cta = null ) {
	$heading = '';
	if ( $title ) {
		$heading = ( $kicker ? bocp_block_paragraph( 'kicker', esc_html( $kicker ) ) : '' )
			. bocp_block_heading( 2, 'h-2', esc_html( $title ) )
			. ( $lead ? bocp_block_paragraph( 'lead muted', esc_html( $lead ) ) : '' );
		$heading = bocp_block_group( '', $heading );
	}
	$items = '';
	foreach ( $cards as $c ) {
		$items .= bocp_block_card( $c[0], $c[1], $c[2], $c[3] ?? '', $c[4] ?? 'Vezi mai mult' );
	}
	if ( $dark_cta ) {
		$items .= bocp_block_card_dark( $dark_cta[0], $dark_cta[1], $dark_cta[2], $dark_cta[3] );
	}
	$grid = bocp_block_group( 'grid g-' . (int) $columns, $items );
	return bocp_block_group( 'section' . ( $sunken ? ' section-sunken' : '' ) . ' wrap', $heading . $grid );
}

/* ---------------------------------------------------------------------
 * Stats
 * ------------------------------------------------------------------- */

function bocp_block_stat( $num, $label ) {
	return bocp_block_group( 'stat', bocp_block_paragraph( 'stat-num', esc_html( $num ) ) . bocp_block_paragraph( 'stat-lbl', esc_html( $label ) ) );
}

/** $items: array of [num, label]. */
function bocp_block_section_stats( $items, $dark = false ) {
	$inner = '';
	foreach ( $items as $s ) {
		$inner .= bocp_block_stat( $s[0], $s[1] );
	}
	$stats = bocp_block_group( 'stats', $inner );
	return bocp_block_group( 'section-tight' . ( $dark ? ' section-dark' : '' ) . ' wrap', $stats );
}

/* ---------------------------------------------------------------------
 * Flow
 * ------------------------------------------------------------------- */

function bocp_block_flow_step( $n, $icon, $title, $desc ) {
	$icon_b = bocp_block_icon_chip( $icon, '' );
	$text   = bocp_block_paragraph( 'flow-num', 'Pas ' . (int) $n )
		. bocp_block_heading( 3, '', esc_html( $title ) )
		. bocp_block_paragraph( '', esc_html( $desc ) );
	return bocp_block_group( 'flow-step', $icon_b . bocp_block_group( '', $text ) );
}

/** $steps: array of [icon, title, desc]. */
function bocp_block_section_flow( $kicker, $title, $lead, $steps, $sunken = false ) {
	$heading = bocp_block_paragraph( 'kicker', esc_html( $kicker ) )
		. bocp_block_heading( 2, 'h-2', esc_html( $title ) )
		. ( $lead ? bocp_block_paragraph( 'lead muted', esc_html( $lead ) ) : '' );
	$count   = count( $steps );
	$items   = '';
	foreach ( $steps as $i => $s ) {
		$items .= bocp_block_flow_step( $i + 1, $s[0], $s[1], $s[2] );
		if ( $i < $count - 1 ) {
			$items .= bocp_block_html( '<div class="flow-arrow" aria-hidden="true">' . bocp_icon( 'arrowRight' ) . '</div>' );
		}
	}
	$flow = bocp_block_group( 'flow', $items );
	return bocp_block_group( 'section' . ( $sunken ? ' section-sunken' : '' ) . ' wrap', bocp_block_group( '', $heading ) . $flow );
}

/* ---------------------------------------------------------------------
 * Features split
 * ------------------------------------------------------------------- */

function bocp_block_feature_item( $text ) {
	$icon = bocp_block_html( '<span class="fitem-ic">' . bocp_icon( 'check' ) . '</span>' );
	return bocp_block_group( 'fitem', $icon . bocp_block_paragraph( 'fitem-txt', esc_html( $text ) ) );
}

/**
 * Same fitem/fitem-txt markup as bocp_block_feature_item(), but with a
 * caller-chosen icon instead of the fixed checkmark, and $text taken as
 * already-safe HTML (not escaped) so callers can bold a word inline with
 * <strong> — matches how bocp_block_buttons() builds raw anchor HTML above.
 */
function bocp_block_feature_item_icon( $icon, $html_text ) {
	$icon_b = bocp_block_html( '<span class="fitem-ic">' . bocp_icon( $icon ) . '</span>' );
	return bocp_block_group( 'fitem', $icon_b . bocp_block_paragraph( 'fitem-txt', $html_text ) );
}

/** $features: array of strings. Text-only content (no wrapper, no image). */
function bocp_block_features_text( $kicker, $title, $lead, $features, $cta_label = '', $cta_url = '' ) {
	$heading = ( $kicker ? bocp_block_paragraph( 'kicker', esc_html( $kicker ) ) : '' )
		. ( $title ? bocp_block_heading( 2, 'h-2', esc_html( $title ) ) : '' )
		. ( $lead ? bocp_block_paragraph( 'lead muted', esc_html( $lead ) ) : '' );
	$items   = '';
	foreach ( $features as $f ) {
		$items .= bocp_block_feature_item( $f );
	}
	$flist = $items ? bocp_block_group( 'flist', $items ) : '';
	$cta   = $cta_label ? bocp_block_paragraph( '', '<a class="btn btn-secondary" href="' . esc_url( $cta_url ) . '">' . esc_html( $cta_label ) . '</a>' ) : '';
	return $heading . $flist . $cta;
}

/**
 * Full features-split block: core/columns (text + a real, replaceable
 * core/image). $side: 'text-first' (default, image on the right) or
 * 'image-first' (image on the left) — just swaps which column comes first.
 */
function bocp_block_features_section( $kicker, $title, $lead, $features, $ratio = '4 / 3', $side = 'text-first', $sunken = false, $cta_label = '', $cta_url = '', $image_alt = 'Captură de ecran' ) {
	$text_blocks = bocp_block_features_text( $kicker, $title, $lead, $features, $cta_label, $cta_url );
	$image_block = bocp_block_image( $ratio, $image_alt );
	$class       = 'section' . ( $sunken ? ' section-sunken' : '' ) . ' wrap split';

	return 'image-first' === $side
		? bocp_block_columns_raw( $image_block, $text_blocks, $class )
		: bocp_block_columns_raw( $text_blocks, $image_block, $class );
}

/* ---------------------------------------------------------------------
 * Prompt bubbles / connector cards (AI Ready page)
 * ------------------------------------------------------------------- */

/** One "example question" bubble — a chat icon + a line of text. */
function bocp_block_qbubble( $text ) {
	return bocp_block_html( '<div class="qbubble">' . bocp_icon( 'chat' ) . '<span>' . esc_html( $text ) . '</span></div>' );
}

/**
 * A connector/integration card: icon chip + status badge, title, description,
 * then caller-supplied body blocks (a feature flist, a pathchip, etc. — this
 * varies per connector, so it's built by the caller and passed in), then a
 * CTA button. $badge_tone matches the .badge modifier classes (success,
 * neutral, info…). $external adds target="_blank" + the external-link icon.
 */
function bocp_block_connector_card( $icon, $icon_variant, $badge_label, $badge_tone, $title, $desc, $body_html, $cta_label, $cta_url, $cta_variant = 'primary', $external = false ) {
	$icon_class = trim( 'ichip lg ' . $icon_variant );
	$icon_b     = bocp_block_html( '<span class="' . esc_attr( $icon_class ) . '">' . bocp_icon( $icon ) . '</span>' );
	$badge_b    = bocp_block_html( '<span class="badge ' . esc_attr( $badge_tone ) . '">' . esc_html( $badge_label ) . '</span>' );
	$head       = bocp_block_group( 'conn-head', $icon_b . $badge_b );
	$title_b    = bocp_block_heading( 3, 'mod-title', esc_html( $title ) );
	$desc_b     = bocp_block_paragraph( 'mod-desc', esc_html( $desc ) );
	$ext_attr   = $external ? ' target="_blank" rel="noopener"' : '';
	$ext_ic     = $external ? ' ' . bocp_icon( 'external' ) : '';
	$cta_b      = bocp_block_paragraph( '', '<a class="btn btn-' . esc_attr( $cta_variant ) . '" href="' . esc_url( $cta_url ) . '"' . $ext_attr . '>' . esc_html( $cta_label ) . $ext_ic . '</a>' );
	return bocp_block_group( 'card card-pad conn', $head . $title_b . $desc_b . $body_html . $cta_b );
}

/* ---------------------------------------------------------------------
 * Tags
 * ------------------------------------------------------------------- */

function bocp_block_tag( $label, $icon = 'tag' ) {
	$icon_b = bocp_block_html( bocp_icon( $icon ) );
	return bocp_block_group( 'tag', $icon_b . bocp_block_paragraph( '', esc_html( $label ) ) );
}

/** $groups: array of [label, icon, items[]]. */
function bocp_block_section_tags_split( $kicker, $title, $lead, $groups, $sunken = false ) {
	$left = bocp_block_paragraph( 'kicker', esc_html( $kicker ) )
		. bocp_block_heading( 2, 'h-2', esc_html( $title ) )
		. ( $lead ? bocp_block_paragraph( 'lead muted', esc_html( $lead ) ) : '' );

	$right = '';
	foreach ( $groups as $g ) {
		list( $label, $icon, $items ) = $g;
		if ( ! $items ) {
			continue;
		}
		if ( $label ) {
			$right .= bocp_block_paragraph( 'contact-row-lbl', esc_html( $label ) );
		}
		$tags = '';
		foreach ( $items as $item ) {
			$tags .= bocp_block_tag( $item, $icon );
		}
		$right .= bocp_block_group( 'tag-row', $tags );
	}

	$cols = bocp_block_columns_raw( $left, $right, 'split wide-left' );
	return bocp_block_group( 'section' . ( $sunken ? ' section-sunken' : '' ) . ' wrap', $cols );
}

function bocp_block_section_tags_simple( $kicker, $title, $items, $icon = 'tag' ) {
	$heading = bocp_block_paragraph( 'kicker', esc_html( $kicker ) ) . bocp_block_heading( 2, 'h-2', esc_html( $title ) );
	$tags    = '';
	foreach ( $items as $item ) {
		$tags .= bocp_block_tag( $item, $icon );
	}
	$row = bocp_block_group( 'tag-row', $tags );
	return bocp_block_group( 'section wrap', bocp_block_group( '', $heading ) . $row );
}

/* ---------------------------------------------------------------------
 * Testimonials
 * ------------------------------------------------------------------- */

function bocp_block_quote( $text, $name, $role, $initials ) {
	$mark   = bocp_block_paragraph( 'quote-mark', '&ldquo;' );
	$txt    = bocp_block_paragraph( 'quote-txt', esc_html( $text ) );
	$avatar = bocp_block_paragraph( 'quote-avatar', esc_html( $initials ) );
	$namer  = bocp_block_paragraph( 'quote-name', esc_html( $name ) ) . bocp_block_paragraph( 'quote-role', esc_html( $role ) );
	$by     = bocp_block_group( 'quote-by', $avatar . bocp_block_group( '', $namer ) );
	return bocp_block_group( 'card quote', $mark . $txt . $by );
}

/** $items: array of [text, name, role, initials]. */
function bocp_block_section_testimonials( $kicker, $title, $items ) {
	$heading = bocp_block_paragraph( 'kicker', esc_html( $kicker ) ) . bocp_block_heading( 2, 'h-2', esc_html( $title ) );
	$quotes  = '';
	foreach ( $items as $t ) {
		$quotes .= bocp_block_quote( $t[0], $t[1], $t[2], $t[3] );
	}
	$grid = bocp_block_group( 'grid g-3', $quotes );
	return bocp_block_group( 'section wrap', bocp_block_group( '', $heading ) . $grid );
}

/* ---------------------------------------------------------------------
 * CTA band / infobox
 * ------------------------------------------------------------------- */

/** $heading_class lets a "closing statement" band use the bigger 'h-display' size instead of the usual 'h-2'. $text may be empty to omit the lead paragraph entirely. */
function bocp_block_section_cta( $title, $text, $btn1_label, $btn1_url, $btn2_label, $btn2_url, $dark = true, $kicker = 'Începe astăzi', $heading_class = 'h-2' ) {
	$kicker_class = $dark ? 'kicker on-dark' : 'kicker';
	$text_class   = 'lead' . ( $dark ? ' on-dark-muted' : '' );
	$inner        = bocp_block_paragraph( $kicker_class, esc_html( $kicker ) )
		. bocp_block_heading( 2, $heading_class, esc_html( $title ) )
		. ( $text ? bocp_block_paragraph( $text_class, esc_html( $text ) ) : '' )
		. bocp_block_buttons( $btn1_label, $btn1_url, $btn2_label, $btn2_url, $dark ? 'on-dark' : '', 'cta-actions' );
	return bocp_block_group( 'section ' . ( $dark ? 'section-dark' : 'section-sunken' ) . ' wrap cta-band', $inner );
}

/** $dark puts the card on a black background (reuses .mod-dark's color overrides) with a light icon chip; $tight uses .section-tight instead of the normal .section padding. */
function bocp_block_section_infobox( $icon, $title, $desc, $link, $url, $dark = false, $tight = false ) {
	$icon_b = bocp_block_icon_chip( $icon, $dark ? 'lg dark' : '' );
	$text   = bocp_block_heading( 3, 'mod-title', esc_html( $title ) )
		. bocp_block_paragraph( 'mod-desc', esc_html( $desc ) )
		. bocp_block_paragraph( 'mod-more', '<a href="' . esc_url( $url ) . '">' . esc_html( $link ) . ' →</a>' );
	$card   = bocp_block_group( 'card card-pad infobox' . ( $dark ? ' mod-dark' : '' ), $icon_b . bocp_block_group( 'infobox-body', $text ) );
	return bocp_block_group( ( $tight ? 'section-tight' : 'section' ) . ' wrap', $card );
}

/* ---------------------------------------------------------------------
 * FAQ accordion
 * ------------------------------------------------------------------- */

/**
 * Native <details>/<summary> accordion (no JS), as ONE core/html block:
 * RichText would strip <details>/<summary>, so the whole list stays raw
 * HTML. Plain text only (escaped), so the same strings can be reused
 * verbatim in a FAQPage JSON-LD block. $items: array of [question, answer].
 */
function bocp_block_faq( $items ) {
	$html = '';
	foreach ( $items as $q ) {
		$html .= '<details class="faq-item"><summary>' . esc_html( $q[0] ) . '</summary><div class="faq-a"><p>' . esc_html( $q[1] ) . '</p></div></details>';
	}
	return bocp_block_html( '<div class="faq">' . $html . '</div>' );
}

function bocp_block_section_faq( $kicker, $title, $items, $sunken = false, $dark = false ) {
	$heading = ( $kicker ? bocp_block_paragraph( $dark ? 'kicker on-dark' : 'kicker', esc_html( $kicker ) ) : '' )
		. bocp_block_heading( 2, 'h-2', esc_html( $title ) );
	$class   = 'section' . ( $dark ? ' section-dark' : ( $sunken ? ' section-sunken' : '' ) ) . ' wrap';
	return bocp_block_group( $class, bocp_block_group( '', $heading ) . bocp_block_faq( $items ) );
}

/* ---------------------------------------------------------------------
 * Per-page content — full post_content HTML, given $links = [key => url]
 * ------------------------------------------------------------------- */

function bocp_seed_content_home( $links ) {
	$blocks   = array();
	$blocks[] = bocp_block_home_hero(
		'Business Online Control Panel',
		'Tot business-ul tău, într-un singur ecosistem.',
		'De la ERP și CRM până la integrări marketplace, WorkManager și WMS, BOCP reunește instrumentele de care ai nevoie pentru o firmă mai organizată, mai eficientă și mai ușor de scalat.',
		'Testează gratuit', $links['contact'], 'Cere demo gratuit', $links['contact'],
		array(
			array( '17 ani', 'experiență cu comercianți RO' ),
			array( '12+', 'module CRM specializate' ),
			array( '1 loc', 'pentru online și offline' ),
		)
	);

	$blocks[] = bocp_block_section_grid(
		'Ecosistem modular', 'Cele mai importante module ale ecosistemului BOCP',
		'Alegi modulele de care ai nevoie acum și le conectezi într-un flux mai clar. Fără aplicații separate, fără procese complicate inutil.',
		array(
			array( 'erp', 'CloudSales ERP', 'Stocuri, comenzi, facturi și procese comerciale — totul conectat într-un singur sistem cloud.', $links['erp'], 'Vezi modulul' ),
			array( 'crm', 'CRM-uri modulare', 'Peste 12 fluxuri CRM: vânzări, producție, service, SSM, închirieri și operațiuni.', $links['crm'], 'Vezi modulul' ),
			array( 'market', 'Integrări Marketplace', 'eMAG, Trendyol, Shopify și altele — comenzi, stocuri și prețuri sincronizate automat.', $links['market'], 'Vezi modulul' ),
			array( 'work', 'WorkManager', 'Fișe service, tehnicieni, devize și istoric reparații, de la preluare până la predare.', $links['work'], 'Vezi modulul' ),
			array( 'wms', 'WMS — depozit', 'Recepție, locații, picking și expediere — un depozit organizat, fără marfă rătăcită.', $links['wms'], 'Vezi modulul' ),
		),
		3, true,
		array( 'Nu ești sigur ce ți se potrivește?', 'Facem un meet online și alegem împreună, prin testare, configurația potrivită activității tale.', 'Cere demo gratuit', $links['contact'] )
	);

	$blocks[] = bocp_block_section_stats( array(
		array( '17 ani', 'pe piața din România' ),
		array( '12+', 'module CRM specializate' ),
		array( '8+', 'marketplace-uri & platforme conectate' ),
		array( '1 click', 'de la comandă la factură și AWB' ),
	), true );

	$blocks[] = bocp_block_section_grid(
		'De ce BOCP', 'Mai puține sisteme separate. Mai multă claritate.',
		'BOCP rezolvă problema informației împrăștiate între aplicații diferite: comenzi, stocuri, clienți, documente și procese interne, conectate într-un flux ușor de urmărit și de controlat.',
		array(
			array( 'cloud', '100% în cloud', 'Fără instalări. Accesezi tot din browser, de oriunde, de pe orice dispozitiv.' ),
			array( 'layers', 'Modular', 'Activezi doar modulele de care ai nevoie acum și le extinzi când crești.' ),
			array( 'sync', 'Centralizat', 'Comenzi, stocuri, clienți și documente, într-un singur flux, nu în aplicații separate.' ),
			array( 'zap', 'Automatizat', 'Facturi, AWB-uri și statusuri generate automat sau cu un singur click.' ),
			array( 'shield', 'Conform legislației RO', 'e-Factura, e-Transport, bonuri fiscale și export către contabilitate.' ),
			array( 'handshake', '17 ani alături de comercianți', 'Construit din scenarii reale de business și suport aproape de tine.' ),
		),
		3, false
	);

	$blocks[] = bocp_block_section_tags_split(
		'Un ecosistem, nu o singură aplicație', 'Și tot ce ține un business în mișcare, la un loc',
		'Pe lângă cele cinci module principale, BOCP acoperă și capabilitățile conexe de care ai nevoie zilnic — facturare, plăți, curieri, POS și raportare.',
		array( array( '', 'tag', array( 'CloudBill — facturier online', 'e-Factura & e-Transport', 'POS magazin & restaurant', 'Conectori curieri & AWB', 'Dropshipping & fulfillment', 'Plată cu cardul & încasări', 'Vouchere & fidelizare', 'Rapoarte & export contabilitate' ) ) ),
		true
	);

	$blocks[] = bocp_block_section_testimonials( 'Ce spun clienții', 'Firme reale, procese mai simple', array(
		array( 'Chiar dacă programul la prima vedere pare complicat, am reușit să economisesc zilnic 2 ore.', 'SC Gims IT SRL', 'Client BOCP', 'GI' ),
		array( 'Am automatizat fluxul de lucru astfel încât reușesc acum să fac cu 2 oameni ceea ce făceam înainte cu 5.', 'Imavision Media & Development', 'Client BOCP', 'IM' ),
		array( 'Partenerii văd stocul în timp real și plasează comenzile online — am redus considerabil timpul pierdut la telefon.', 'SC Ceramica Stil SRL', 'Client BOCP', 'CS' ),
	) );

	$blocks[] = bocp_block_section_cta(
		'Vezi BOCP pe datele tale, nu pe un demo generic',
		'Testezi gratuit și stabilim un meet online în care îți prezentăm exact modulele potrivite pentru activitatea ta.',
		'Testează gratuit', $links['contact'], 'Cere demo gratuit', $links['contact'], true
	);

	return implode( "\n\n", $blocks );
}

function bocp_seed_content_ai_ready( $links ) {
	$blocks = array();

	// HERO — a custom phero+hero-grid composition (not bocp_block_page_hero()):
	// this page needs a second lead paragraph + a pathchip between the lead
	// and the buttons, plus a video (not image) in the right column.
	$crumbs = bocp_block_html( '<div class="crumbs"><a href="' . esc_url( $links['home'] ) . '">Acasă</a> ' . bocp_icon( 'arrowNext' ) . ' <span>AI Ready</span></div>' );
	$hero_text = bocp_block_paragraph( 'kicker', 'Conectori AI · disponibil acum' )
		. bocp_block_heading( 1, 'h-display', 'BOCP se conectează acum cu AI' )
		. bocp_block_paragraph( 'lead', 'Poți conecta datele din BOCP cu AI și poți pune întrebări despre businessul tău direct într-o conversație.' )
		. bocp_block_paragraph( 'lead muted', 'Noul modul <strong>Conectori AI</strong> este disponibil pentru toți utilizatorii BOCP în:' )
		. bocp_block_pathchip( 'plug', 'Integrări → Conectori AI' )
		. bocp_block_paragraph( 'hero-actions', '<a class="btn btn-primary btn-lg" href="https://claude.ai/directory/bocp" target="_blank" rel="noopener">Conectează în Claude ' . bocp_icon( 'external' ) . '</a> <a class="btn btn-outline btn-lg" href="' . esc_url( $links['contact'] ) . '">Cere demo gratuit</a>' );
	$hero_video = bocp_block_video_placeholder( 'AI-VIDEO-01', '16 / 9', 'Clipul 1 — conectarea BOCP la AI',
		'Clip scurt (15–40s) în care se vede parcursul: Integrări → Conectori AI → instalare → autentificare → Acces KPI. Trimite fișierul MP4 (H.264) la 1920×1080 și, dacă ai, un poster PNG.'
	);
	$blocks[] = bocp_block_group( 'phero wrap', $crumbs . bocp_block_columns_raw( $hero_text, $hero_video, 'hero-grid' ) );

	// CE POȚI FACE — grid of example questions.
	$questions = array(
		'Cât am vândut astăzi?',
		'Câte comenzi eMAG am avut ieri?',
		'Cum au evoluat vânzările?',
		'Care sunt cei mai importanți KPI?',
		'Ce ar trebui să urmăresc mai atent?',
		'Cum performează businessul meu?',
	);
	$qbubbles = '';
	foreach ( $questions as $q ) {
		$qbubbles .= bocp_block_qbubble( $q );
	}
	$q_heading = bocp_block_paragraph( 'kicker', 'Ce poți face' )
		. bocp_block_heading( 2, 'h-2', 'Întrebi în limbaj natural, primești răspunsul din datele tale' )
		. bocp_block_paragraph( 'lead muted', 'După conectare, poți întreba AI-ul, de exemplu:' );
	$q_after = bocp_block_paragraph( 'lead muted', 'AI-ul folosește informațiile disponibile din BOCP pentru a-ți răspunde.' );
	$blocks[] = bocp_block_group( 'section section-sunken wrap', bocp_block_group( '', $q_heading ) . bocp_block_group( 'qlist', $qbubbles ) . $q_after );

	// TU CONTROLEZI ACCESUL LA DATE — text split, video #2 on the right.
	$control_text = bocp_block_paragraph( 'kicker', 'Tu alegi ce date sunt disponibile' )
		. bocp_block_heading( 2, 'h-2', 'Tu controlezi accesul la date' )
		. bocp_block_paragraph( 'lead muted', 'La conectare trebuie activată opțiunea <strong>Acces KPI</strong>. Din BOCP poți apoi selecta ce KPI-uri dorești să fie accesibile și le poți dezactiva pe cele pe care nu vrei să le folosești prin AI.' )
		. bocp_block_group( 'flist',
			bocp_block_feature_item_icon( 'toggle', 'Activezi explicit <strong>Acces KPI</strong> la conectare' )
			. bocp_block_feature_item_icon( 'settings', 'Selectezi exact ce KPI-uri sunt accesibile' )
			. bocp_block_feature_item_icon( 'lock', 'Dezactivezi oricând KPI-urile pe care nu vrei să le expui' )
		);
	$control_video = bocp_block_video_placeholder( 'AI-VIDEO-02', '16 / 9', 'Clipul 2 — întrebări în conversație / control KPI',
		'Clip scurt (15–40s): o conversație reală cu AI-ul despre vânzări/KPI, sau ecranul de selectare a KPI-urilor accesibile. MP4 (H.264), 1920×1080, + poster PNG dacă ai.'
	);
	$blocks[] = bocp_block_columns_raw( $control_text, $control_video, 'section wrap split' );

	// CUM SE CONECTEAZĂ — reuses the existing numbered-flow section as-is.
	$blocks[] = bocp_block_section_flow(
		'Cum se conectează', 'Patru pași și ești gata', '',
		array(
			array( 'download', 'Instalare', 'Adaugi conectorul BOCP în aplicația AI pe care o folosești.' ),
			array( 'key', 'Autentificare', 'Te autentifici cu contul tău BOCP.' ),
			array( 'toggle', 'Acordare acces KPI', 'Activezi opțiunea Acces KPI și alegi ce date sunt disponibile.' ),
			array( 'checkCircle', 'Gata', 'Poți începe să pui întrebări despre businessul tău.' ),
		),
		true
	);

	// CONECTORI DISPONIBILI — Claude (live) + ChatGPT (pending) cards.
	$claude_body = bocp_block_group( 'flist',
		bocp_block_feature_item_icon( 'globe', 'Disponibil prin <strong>browser</strong>' )
		. bocp_block_feature_item_icon( 'settings', 'Disponibil în <strong>aplicația desktop Claude</strong>' )
	);
	$card_claude = bocp_block_connector_card(
		'ai', '', 'Disponibil', 'success', 'Conectare cu Claude',
		'Conectorul BOCP este disponibil în Claude. Conectarea se face simplu: instalare → autentificare cu contul BOCP → acordare acces KPI → gata.',
		$claude_body, 'Deschide conectorul', 'https://claude.ai/directory/bocp', 'primary', true
	);
	$chatgpt_body = bocp_block_pathchip( 'plug', 'https://secure.bocp.eu/app/rest/mcp' );
	$card_chatgpt = bocp_block_connector_card(
		'chat', 'neutral', 'În curs de aprobare', 'neutral', 'Conectare cu ChatGPT',
		'Listarea oficială BOCP în directorul ChatGPT este în curs de aprobare. Pentru utilizatorii avansați, conectorul poate fi adăugat și manual prin MCP:',
		$chatgpt_body, 'Ai nevoie de ajutor la configurare?', $links['contact'], 'outline', false
	);
	$conn_heading = bocp_block_paragraph( 'kicker', 'Unde funcționează' ) . bocp_block_heading( 2, 'h-2', 'Conectori disponibili' );
	$blocks[] = bocp_block_group( 'section wrap', bocp_block_group( '', $conn_heading ) . bocp_block_group( 'grid g-2', $card_claude . $card_chatgpt ) );

	// AI NEVOIE DE UN ALT KPI — dark callout box.
	$blocks[] = bocp_block_section_infobox(
		'chart', 'Ai nevoie de un alt KPI?',
		'Spune-ne ce informație din BOCP ai vrea să poți cere AI-ului. Vom continua să extindem conectorul cu noi KPI-uri în funcție de nevoile utilizatorilor.',
		'Propune un KPI', $links['contact'], true, true
	);

	// CLOSING STATEMENT — big dark band, no lead paragraph, "BOCP + AI" kicker.
	$blocks[] = bocp_block_section_cta(
		'Nu doar vezi datele businessului tău. Acum poți discuta despre ele.',
		'', 'Testează gratuit', $links['contact'], 'Cere demo gratuit', $links['contact'], true, 'BOCP + AI', 'h-display'
	);

	return implode( "\n\n", $blocks );
}

function bocp_seed_content_erp( $links ) {
	$blocks   = array();
	$blocks[] = bocp_block_page_hero( $links, 'CloudSales ERP', 'Modulul ERP din BOCP',
		'CloudSales ERP — gestiune, comenzi și documente mai bine organizate',
		'Ajută firmele să gestioneze mai clar stocurile, comenzile, vânzările și documentele comerciale. Totul conectat online, ca echipa să lucreze mai rapid, cu mai puține verificări manuale.',
		'Testează gratuit', $links['contact'], 'Cere demo gratuit', $links['contact']
	);

	$blocks[] = bocp_block_section_grid( '', '', '', array(
		array( 'box', 'Stocuri actualizate', 'Urmărești produsele, cantitățile, recepțiile și mișcările de marfă într-un singur sistem.' ),
		array( 'receipt', 'Facturi și avize', 'Generezi documentele necesare mai ușor și păstrezi evidența lor completă în sistem.' ),
		array( 'cart', 'Procesare mai rapidă', 'Preiei și organizezi comenzile într-un flux clar, de la intrare până la livrare.' ),
		array( 'chart', 'Control comercial', 'Ai o imagine clară asupra vânzărilor, prețurilor, clienților și activității zilnice.' ),
	), 4, false );

	$feat_full = array(
		'Gestiune stocuri și catalog de produse',
		'Emitere facturi fiscale și bonuri fiscale',
		'Încasări, plăți și registru de casă',
		'Export către programe de contabilitate (WinMentor, Saga)',
		'Generare automată AWB și etichete de livrare',
		'Preluare comenzi din eCommerce și marketplace',
		'Sincronizarea stocului în timp real, pe toate canalele',
		'Liste de preț personalizabile pe client sau grupă',
		'Multi-depozit, multi-punct de lucru, multi-valută',
	);

	$blocks[] = bocp_block_features_section(
		'Un singur ERP, online și offline', 'De la recepție la raport, fără să sari între programe',
		'Unifici vânzările din POS, magazin online și marketplace într-o singură platformă, cu stocuri, prețuri și produse sincronizate în timp real.',
		array_slice( $feat_full, 0, 5 ), '4 / 3', 'text-first', true, '', '', 'Captură ecran: gestiune stocuri CloudSales'
	);

	$blocks[] = bocp_block_section_flow( 'Cum funcționează', 'Fluxul comercial, de la intrare la livrare',
		'Un traseu clar pentru fiecare comandă, indiferent de canalul pe care a intrat.',
		array(
			array( 'truck', 'Recepție marfă', 'Intrări, NIR-uri și transferuri între depozite, cu stoc actualizat automat.' ),
			array( 'box', 'Gestiune stoc', 'Stoc real pe fiecare punct de lucru, cu alerte de stoc minim și rezervări.' ),
			array( 'cart', 'Preluare comenzi', 'De pe site, marketplace, telefon sau POS — toate într-un singur flux.' ),
			array( 'receipt', 'Facturare', 'Facturi și bonuri fiscale emise automat sau cu un click, conform legislației.' ),
			array( 'truck', 'AWB & livrare', 'Generezi AWB-ul, imprimi eticheta și alertezi clientul prin email sau SMS.' ),
		)
	);

	$blocks[] = bocp_block_features_section(
		'Tot ce include modulul', 'Funcționalități esențiale pentru comerț modern', '',
		$feat_full, '4 / 3', 'image-first', true, '', '', 'Captură ecran: emitere factură / listă facturi'
	);

	$blocks[] = bocp_block_section_cta(
		'Vezi CloudSales ERP pe fluxul tău real',
		'Îți facem un cont demo și îți arătăm cum se conectează gestiunea, comenzile și facturarea pentru activitatea ta.',
		'Testează gratuit', $links['contact'], 'Cere demo gratuit', $links['contact'], false
	);

	return implode( "\n\n", $blocks );
}

function bocp_seed_content_crm( $links ) {
	$blocks   = array();
	$blocks[] = bocp_block_page_hero( $links, 'CRM-uri', 'Diversitatea CRM-urilor BOCP',
		'CRM-uri modulare pentru fluxurile reale din compania ta',
		'BOCP oferă module CRM specializate pentru companii cu fluxuri diferite — nu doar liste de contacte. Lead-uri, agenți, producție, service, SSM, închirieri și operațiuni de depozit, toate gândite modular.',
		'Testează gratuit', $links['contact'], 'Cere demo gratuit', $links['contact']
	);

	$blocks[] = bocp_block_section_grid(
		'Peste 12 module', 'Un CRM pentru fiecare tip de activitate',
		'Activezi doar componentele care se potrivesc firmei tale — și le extinzi când apar nevoi noi.',
		array(
			array( 'handshake', 'Sales Leads', 'Colectezi clienți potențiali din formulare web și îi urmărești până la conversie.' ),
			array( 'route', 'Agenți vânzări', 'Urmărești activitatea agenților pe teren, comenzile și țintele lor.' ),
			array( 'factory', 'Producție', 'Urmărești linia de producție, materiile prime și produsele finite.' ),
			array( 'clipboard', 'Prestări servicii & lucrări', 'Devize, lucrări și execuție pentru firmele care oferă servicii.' ),
			array( 'work', 'WorkManager Service', 'Fișe service, reparații, piese și istoric pentru service-uri electronice.', $links['work'], 'Pagină dedicată' ),
			array( 'map', 'Dispecerat Service', 'Contracte, intervenții pe teren și alocarea sesizărilor la tehnicieni.' ),
			array( 'shield', 'SSM Control', 'Documente și controale de securitate a muncii, standardizate și la zi.' ),
			array( 'box', 'Închirieri echipamente', 'Contracte, disponibilitate și retur pentru echipamentele închiriate.' ),
			array( 'car', 'Închirieri autovehicule', 'Booking, rezervări și gestiunea flotei pentru firme de închirieri auto.' ),
			array( 'truck', 'Achiziții & aprovizionare', 'Comenzi către furnizori, feed-uri și reaprovizionare la timp.' ),
			array( 'file', 'Document Management', 'Documente organizate, scadențar și generare automată.' ),
			array( 'wms', 'Warehouse Management', 'Recepție, locații, picking și expediere pentru depozit.', $links['wms'], 'Pagină dedicată' ),
		),
		3, false
	);

	$blocks[] = bocp_block_section_flow( 'Un flux comun, adaptat pe rol', 'De la primul contact la client fidel', '', array(
		array( 'handshake', 'Lead captat', 'Din formular web, telefon sau import — fiecare contact ajunge în sistem.' ),
		array( 'receipt', 'Ofertă & comandă', 'Trimiți ofertă, o transformi în comandă și o corelezi cu stocul.' ),
		array( 'settings', 'Execuție / livrare', 'Producție, service sau livrare — fluxul potrivit pentru activitatea ta.' ),
		array( 'coins', 'Facturare & fidelizare', 'Facturezi, încasezi și fidelizezi clientul cu vouchere și oferte.' ),
	), true );

	$blocks[] = bocp_block_features_section(
		'Totul modular', 'Activezi doar ce folosești, restul rămâne curat',
		'Fiecare modul CRM se conectează la aceleași date de clienți, comenzi și documente, astfel încât echipa ta lucrează într-un singur sistem, nu în aplicații separate.',
		array(), '4 / 3', 'text-first', false, 'Alege modulele potrivite', $links['contact'], 'Captură ecran: modul CRM'
	);

	$blocks[] = bocp_block_section_cta(
		'Nu ești sigur ce CRM ți se potrivește?',
		'Facem un meet online și, prin testare, alegem împreună modulele CRM care se potrivesc fluxului tău de lucru.',
		'Testează gratuit', $links['contact'], 'Cere demo gratuit', $links['contact'], true
	);

	return implode( "\n\n", $blocks );
}

function bocp_seed_content_wms( $links ) {
	$blocks   = array();
	$blocks[] = bocp_block_page_hero( $links, 'WMS', 'Warehouse Management System',
		'Un depozit organizat, fără marfă rătăcită',
		'WMS din BOCP aduce ordine în depozit: recepție, locații și rafturi, picking ghidat, packing și expediere. Fiecare produs are locul lui, iar comenzile se pregătesc mai rapid și cu mai puține erori.',
		'Testează gratuit', $links['contact'], 'Cere demo gratuit', $links['contact']
	);

	$blocks[] = bocp_block_section_flow( 'Cum funcționează', 'De la recepție la expediere, într-un flux clar', '', array(
		array( 'truck', 'Recepție', 'Intrarea mărfii, verificarea cantităților și alocarea pe locații.' ),
		array( 'wms', 'Locații & rafturi', 'Fiecare produs are locul lui, ușor de găsit de oricine din echipă.' ),
		array( 'scan', 'Picking', 'Culegi produsele pentru comenzi ghidat, cu scanare barcode.' ),
		array( 'box', 'Packing', 'Verifici, împachetezi și pregătești coletul pentru curier.' ),
		array( 'route', 'Expediere', 'Generezi AWB, imprimi eticheta și predai coletul curierului.' ),
	) );

	$blocks[] = bocp_block_features_section( 'Ce include WMS', 'Control complet asupra depozitului', '', array(
		'Organizare pe locații și rafturi de depozit',
		'Picking ghidat, cu scanare barcode',
		'Verificare la packing, mai puține erori de livrare',
		'Inventar rapid, direct din depozit',
		'Mișcări de marfă și transferuri între depozite',
		'Legătură directă cu comenzile din ERP și marketplace',
	), '4 / 3', 'image-first', true, '', '', 'Captură ecran: hartă locații / listă de picking' );

	$blocks[] = bocp_block_section_grid(
		'Conectat la restul ecosistemului', 'Depozitul nu lucrează izolat',
		'WMS folosește aceleași stocuri și comenzi ca și CloudSales ERP și integrările marketplace, astfel încât ce se întâmplă în depozit se reflectă instant în tot sistemul.',
		array(
			array( 'erp', 'CloudSales ERP', 'Stocul din depozit rămâne sincronizat cu gestiunea și facturarea.', $links['erp'], 'Vezi modulul' ),
			array( 'market', 'Integrări Marketplace', 'Comenzile din marketplace ajung direct la picking și expediere.', $links['market'], 'Vezi modulul' ),
			array( 'crm', 'CRM-uri', 'Achiziții, producție și operațiuni conectate la mișcările de stoc.', $links['crm'], 'Vezi modulul' ),
		),
		3, false
	);

	$blocks[] = bocp_block_section_cta(
		'Pune ordine în depozit cu WMS',
		'Îți arătăm cum arată picking-ul ghidat și cum scad erorile de livrare când fiecare produs are locul lui.',
		'Testează gratuit', $links['contact'], 'Cere demo gratuit', $links['contact'], true
	);

	return implode( "\n\n", $blocks );
}

function bocp_seed_content_workmanager( $links ) {
	$blocks   = array();
	$blocks[] = bocp_block_page_hero( $links, 'WorkManager', 'Soluție specializată pentru service',
		'Service mai organizat, de la preluare până la returnare',
		'WorkManager ajută firmele care repară echipamente să gestioneze clar fișele service, clienții, statusurile, tehnicienii, devizele și istoricul reparațiilor — fiecare produs cu ID unic și documente generate automat.',
		'Testează gratuit', $links['contact'], 'Cere demo gratuit', $links['contact']
	);

	$blocks[] = bocp_block_section_flow( 'Fluxul unei reparații', 'Fiecare produs, pas cu pas, fără fișe pierdute',
		'De la telefoane și electrocasnice până la imprimante sau aparate de aer condiționat — urmărești fiecare reparație într-un singur sistem.',
		array(
			array( 'clipboard', 'Preluare echipament', 'Înregistrezi produsul cu ID unic, client și defectul reclamat.' ),
			array( 'scan', 'Evaluare & deviz', 'Tehnicianul evaluează, întocmește devizul și trimite oferta.' ),
			array( 'wrench', 'Reparație', 'Urmărești statusul, piesele consumate și timpul de lucru.' ),
			array( 'receipt', 'Facturare', 'Emiți factura și proces-verbalul, cu toate datele reparației.' ),
			array( 'checkCircle', 'Predare', 'Predai echipamentul, cu istoric complet și garanție înregistrată.' ),
		)
	);

	$blocks[] = bocp_block_features_section( 'Ce include WorkManager', 'Mai puține fișe pierdute, mai mult control', '', array(
		'Fișe service cu ID unic pentru fiecare echipament',
		'Clienți, tehnicieni și statusuri într-un singur loc',
		'Devize, piese consumate și manoperă',
		'Documente generate automat (fișă, proces-verbal, garanție)',
		'Istoric complet al reparațiilor pe client și pe produs',
		'Urmărire plăți și încasări pe fiecare fișă',
	), '4 / 3', 'text-first', true, '', '', 'Captură ecran: fișă service WorkManager' );

	$blocks[] = bocp_block_section_tags_simple( 'Cui i se potrivește', 'Pentru orice service care ține evidența pe hârtie sau în tabele',
		array( 'Service telefoane & IT', 'Electrocasnice', 'Imprimante & birotică', 'Aer condiționat & instalații' ), 'settings'
	);

	$blocks[] = bocp_block_section_infobox( 'map', 'Ai și intervenții pe teren? Vezi Dispecerat Service',
		'Pentru firmele care oferă mentenanță și intervenții pe teren: contracte clienți, alertare revizii, alocarea sesizărilor la tehnicieni și localizarea agenților pe hartă.',
		'Vezi toate CRM-urile', $links['crm']
	);

	$blocks[] = bocp_block_section_cta(
		'Adu-ți service-ul într-un singur sistem',
		'Îți facem un cont demo și îți arătăm cum arată o fișă service completă, de la preluare până la predare.',
		'Testează gratuit', $links['contact'], 'Cere demo gratuit', $links['contact'], true
	);

	return implode( "\n\n", $blocks );
}

function bocp_seed_content_marketplace( $links ) {
	$blocks   = array();
	$blocks[] = bocp_block_page_hero( $links, 'Integrări Marketplace', 'Canale externe, un singur flux',
		'Marketplace-uri multiple. Control dintr-un singur loc.',
		'Preiei comenzile din marketplace-urile conectate, sincronizezi produsele, stocurile și prețurile, emiți facturi și AWB-uri, apoi actualizezi statusurile automat. Vânzările online devin un flux mai clar, mai rapid și mai ușor de controlat.',
		'Testează gratuit', $links['contact'], 'Cere demo gratuit', $links['contact']
	);

	$blocks[] = bocp_block_section_tags_split(
		'Conectori disponibili', 'Marketplace-uri, platforme eCommerce și curieri',
		'Indiferent unde vinzi acum — sau vei vinde în viitor — păstrezi controlul complet asupra datelor, comenzilor și rapoartelor din BOCP.',
		array(
			array( 'Marketplace & eCommerce', 'plug', array( 'eMAG Marketplace', 'Trendyol', 'Shopify', 'MerchantPro', 'Gomag', 'Cel.ro', 'FashionUp', 'ShopMania' ) ),
			array( 'Conectori curieri', 'truck', array( 'FAN Courier', 'Urgent Cargus', 'GLS', 'Sameday', 'DPD', 'Și altele' ) ),
		)
	);

	$blocks[] = bocp_block_section_flow( 'Cum funcționează', 'De la comandă în marketplace la status actualizat', '', array(
		array( 'plug', 'Marketplace conectat', 'Legi conturile de marketplace și magazinele online la un singur sistem.' ),
		array( 'sync', 'Sincronizare', 'Produse, stocuri și prețuri actualizate automat pe toate canalele.' ),
		array( 'cart', 'Comenzi centralizate', 'Toate comenzile ajung într-un singur loc, gata de procesare.' ),
		array( 'receipt', 'Facturare & AWB', 'Emiți facturi și generezi AWB-uri automat sau cu un click.' ),
		array( 'refresh', 'Status înapoi', 'Statusul comenzii se transmite automat înapoi în marketplace.' ),
	), true );

	$blocks[] = bocp_block_section_stats( array(
		array( '1 flux', 'comenzi, stocuri, prețuri și documente pentru toate marketplace-urile' ),
		array( 'Automat', 'statusuri transmise înapoi pe măsură ce procesezi comanda' ),
	), false );

	$blocks[] = bocp_block_features_section( 'Ce include integrarea', 'Vânzările online, într-un flux unitar', '', array(
		'Preiei comenzile din toate marketplace-urile conectate',
		'Sincronizezi produse, stocuri și prețuri în timp real',
		'Emiți facturi și generezi AWB-uri automat',
		'Actualizezi statusurile automat, înapoi în marketplace',
		'RO e-Factura — transmitere și preluare facturi',
		'RO e-Transport direct din ERP',
		'Preluare feed-uri de la furnizori',
		'Decontări: Netopia, Shopify Payments, EuPlătesc',
	), '4 / 3', 'image-first', false, '', '', 'Captură ecran: comenzi marketplace centralizate' );

	$blocks[] = bocp_block_section_cta(
		'Conectează-ți marketplace-urile și scapă de dublă muncă',
		'Îți arătăm cum se sincronizează stocurile și cum ajung comenzile automat la facturare și AWB, pe canalele tale.',
		'Testează gratuit', $links['contact'], 'Cere demo gratuit', $links['contact'], true
	);

	return implode( "\n\n", $blocks );
}

function bocp_seed_content_contact( $links ) {
	$blocks   = array();
	$blocks[] = bocp_block_page_hero( $links, 'Contact', 'Hai să vorbim',
		'Testează gratuit sau cere un demo',
		'Completează formularul și te contactăm pentru a stabili un meet online, în care îți prezentăm exact modulele potrivite pentru activitatea ta.'
	);

	$note      = bocp_block_paragraph( 'mod-desc', 'Adaugă aici shortcode-ul formularului tău din Contact &gt; Formulare de contact (ex. <code>[contact-form-7 id="12" title="Contact"]</code>) — șterge acest text și lipește shortcode-ul în locul lui.' );
	$note_card = bocp_block_group( 'card card-pad', $note );
	$shortcode = '<!-- wp:shortcode -->[bocp_contact_info]<!-- /wp:shortcode -->';
	$cols      = bocp_block_columns_raw( $note_card, $shortcode, 'contact-grid' );
	$blocks[]  = bocp_block_group( 'section wrap', $cols );

	return implode( "\n\n", $blocks );
}

/* ---------------------------------------------------------------------
 * Admin page + seeding action
 * ------------------------------------------------------------------- */

function bocp_admin_menu() {
	add_theme_page(
		__( 'BOCP Setup', 'bocp' ),
		__( 'BOCP Setup', 'bocp' ),
		'edit_theme_options',
		'bocp-setup',
		'bocp_render_setup_page'
	);
}
add_action( 'admin_menu', 'bocp_admin_menu' );

function bocp_run_seed( $overwrite = false ) {
	$registry = bocp_seed_page_registry();

	// Pass 1: create missing pages (or reuse existing IDs), minimal content.
	$post_ids = array();
	$to_fill  = array();
	foreach ( $registry as $key => $def ) {
		$existing = bocp_find_seeded_page( $key );
		if ( $existing ) {
			$post_ids[ $key ] = $existing;
			if ( $overwrite ) {
				$to_fill[ $key ] = $existing;
			}
			continue;
		}
		$post_id = wp_insert_post( array(
			'post_type'   => 'page',
			'post_status' => 'draft',
			'post_title'  => 'NEW - ' . $def['title'],
		), true );
		if ( is_wp_error( $post_id ) ) {
			continue;
		}
		update_post_meta( $post_id, '_wp_page_template', $def['template'] );
		update_post_meta( $post_id, '_bocp_seed_key', $key );
		$post_ids[ $key ] = $post_id;
		$to_fill[ $key ]  = $post_id;
	}

	// Every page's permalink, whether pre-existing or just created.
	$links = array();
	foreach ( $post_ids as $key => $post_id ) {
		$links[ $key ] = get_permalink( $post_id );
	}

	// Footer link columns are widget areas now (Aspect > Widgets), not
	// Customizer fields — nothing to auto-populate here anymore.

	$builders = array(
		'home'    => 'bocp_seed_content_home',
		'ai'      => 'bocp_seed_content_ai_ready',
		'erp'     => 'bocp_seed_content_erp',
		'crm'     => 'bocp_seed_content_crm',
		'market'  => 'bocp_seed_content_marketplace',
		'work'    => 'bocp_seed_content_workmanager',
		'wms'     => 'bocp_seed_content_wms',
		'contact' => 'bocp_seed_content_contact',
	);

	foreach ( $to_fill as $key => $post_id ) {
		if ( ! isset( $builders[ $key ] ) ) {
			continue;
		}
		update_post_meta( $post_id, '_wp_page_template', $registry[ $key ]['template'] );
		wp_update_post( array(
			'ID'           => $post_id,
			'post_content' => call_user_func( $builders[ $key ], $links ),
		) );
	}

	return count( $to_fill );
}

function bocp_render_setup_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$result = null;
	if ( isset( $_POST['bocp_seed_action'] ) && check_admin_referer( 'bocp_seed_pages' ) ) {
		$overwrite = ! empty( $_POST['bocp_seed_overwrite'] );
		$result    = bocp_run_seed( $overwrite );
	}

	$registry = bocp_seed_page_registry();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'BOCP Setup', 'bocp' ); ?></h1>
		<p>
			<?php esc_html_e( 'Tema BOCP nu creează nicio pagină automat. Apasă butonul de mai jos ca să generezi cele 7 pagini de start, ca ciorne (draft), cu titlul prefixat „NEW - ". Conținutul e scris ca blocuri normale (Editorul de blocuri WordPress) — secțiunile pot fi mutate, duplicate sau șterse din lista de blocuri, iar imaginile hero/feature sunt blocuri Imagine reale, înlocuibile din Media Library. Le poți edita liber, redenumi (elimini „NEW - "), publica și adăuga în meniu în ritmul tău — tema nu atinge niciodată meniurile sau pagina frontală.', 'bocp' ); ?>
		</p>

		<?php if ( null !== $result ) : ?>
			<div class="notice notice-success"><p>
				<?php
				if ( $result > 0 ) {
					printf(
						/* translators: %d: number of pages created/updated */
						esc_html( _n( 'S-a creat/actualizat %d pagină.', 'S-au creat/actualizat %d pagini.', $result, 'bocp' ) ),
						(int) $result
					);
				} else {
					esc_html_e( 'Toate paginile există deja — nu s-a schimbat nimic (bifează „Suprascrie" ca să le regenerezi).', 'bocp' );
				}
				?>
			</p></div>
		<?php endif; ?>

		<table class="widefat striped" style="max-width:760px;margin-bottom:20px">
			<thead><tr>
				<th><?php esc_html_e( 'Pagină', 'bocp' ); ?></th>
				<th><?php esc_html_e( 'Status', 'bocp' ); ?></th>
			</tr></thead>
			<tbody>
				<?php foreach ( $registry as $key => $def ) : ?>
					<?php $existing = bocp_find_seeded_page( $key ); ?>
					<tr>
						<td><?php echo esc_html( $def['title'] ); ?></td>
						<td>
							<?php if ( $existing ) : ?>
								<span style="color:#1a7f37">✓ <?php esc_html_e( 'Creată', 'bocp' ); ?></span>
								— <a href="<?php echo esc_url( get_edit_post_link( $existing ) ); ?>"><?php esc_html_e( 'editează', 'bocp' ); ?></a>
							<?php else : ?>
								<span style="color:#8a8a8a">— <?php esc_html_e( 'Lipsă', 'bocp' ); ?></span>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<form method="post">
			<?php wp_nonce_field( 'bocp_seed_pages' ); ?>
			<label style="display:block;margin-bottom:14px">
				<input type="checkbox" name="bocp_seed_overwrite" value="1" />
				<?php esc_html_e( 'Suprascrie paginile existente (resetează conținutul și template-ul lor la varianta din machetă)', 'bocp' ); ?>
			</label>
			<button type="submit" name="bocp_seed_action" value="1" class="button button-primary button-hero">
				<?php esc_html_e( 'Creează paginile de start', 'bocp' ); ?>
			</button>
		</form>

		<h2 style="margin-top:32px"><?php esc_html_e( 'Cum editezi o pagină BOCP', 'bocp' ); ?></h2>
		<ul style="max-width:760px;list-style:disc;padding-left:22px">
			<li><?php esc_html_e( 'Mutare / reordonare: selectezi orice bloc (o secțiune întreagă, sau o imagine) și folosești săgețile sus/jos din toolbar-ul blocului, sau tragi cu mânerul de mutare (⠿).', 'bocp' ); ?></li>
			<li><?php esc_html_e( 'Duplicare: selectezi blocul → meniul „⋮" din toolbar → „Duplică" (sau Ctrl/Cmd+Shift+D).', 'bocp' ); ?></li>
			<li><?php esc_html_e( 'Imagini (hero, capturi de ecran): sunt deja blocuri Imagine reale — le selectezi și apeși „Înlocuiește" ca să încarci poza definitivă din Media Library.', 'bocp' ); ?></li>
			<li><?php esc_html_e( 'Conținut nou: apeși „+" între blocuri ca să adaugi orice bloc nativ (paragraf, imagine, buton, coloane etc.), sau un bloc „HTML personalizat" dacă vrei să scrii cod cu clasele design system-ului de mai jos.', 'bocp' ); ?></li>
			<li><?php esc_html_e( 'Dacă un bloc arată o notificare „conține conținut neașteptat": e inofensiv, apeși „Păstrează ca HTML" — afișarea pe site nu e afectată în niciun caz.', 'bocp' ); ?></li>
		</ul>
		<h3 style="margin-top:24px"><?php esc_html_e( 'Clase disponibile pentru blocuri HTML personalizate', 'bocp' ); ?></h3>
		<p><?php esc_html_e( '.hero, .phero, .wrap, .section / .section-sunken / .section-dark, .kicker, .h-display / .h-2, .lead, .btn.btn-primary/.btn-outline/.btn-secondary, .card.card-pad sau .card.card-lift.mod, .grid.g-2/.g-3/.g-4, .stats/.stat, .flow/.flow-step, .flist/.fitem, .tag, .quote, .cta-band, .infobox, .faq > details.faq-item > summary + .faq-a (acordeon, fără JS), nav.anchor-nav cu linkuri #ancoră (rând de chip-uri).', 'bocp' ); ?></p>

		<h2 style="margin-top:32px"><?php esc_html_e( 'Coloanele din footer', 'bocp' ); ?></h2>
		<p>
			<?php esc_html_e( 'Cele 3 coloane din footer (lângă logo) sunt zone de widget-uri native WordPress — nu blocuri, nu Customizer. Le găsești în Aspect > Widgets: „Footer — Coloana 1", „Footer — Coloana 2", „Footer — Contact". Tragi acolo un widget „Meniu de navigare" (alegi/creezi un meniu cu link-urile tale), „Text" sau „HTML personalizat" — le rearanjezi liber, fără cod. Coloana de Contact rămâne cu telefon/email/adresă din Aspect > Personalizare > Date companie dacă nu pui niciun widget acolo.', 'bocp' ); ?>
		</p>

		<?php do_action( 'bocp_setup_page_after' ); ?>
	</div>
	<?php
}
