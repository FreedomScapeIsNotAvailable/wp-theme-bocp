<?php
/**
 * Mirrors specific pages from another WordPress site (e.g. cloudsales.ro)
 * into real bocp.ro pages, kept in sync automatically via WP-Cron.
 *
 * How it works: every hour, for each entry in bocp_mirror_sources(), we
 * call the SOURCE site's public REST API (no login needed — this only
 * reads already-published content, the same way a browser would), take
 * its rendered content, and save it into a local page that uses our own
 * "BOCP — Pagină" template (so it gets bocp.ro's header/nav/footer). The
 * source page's own header/footer are NOT included — the REST API's
 * `content.rendered` field is just the post's own content area, already
 * separate from the source theme's chrome.
 *
 * Adding another mirrored page later: just add another entry to
 * bocp_mirror_sources() below — no other code changes needed.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * key => [ 'source_api' => REST API URL (by slug) on the source site,
 *          'local_slug' => desired bocp.ro URL slug,
 *          'local_title_fallback' => title used only if the source
 *             response doesn't include one,
 *          'link_rewrites' => [ path-on-source-site => absolute target URL ],
 *             applied to every matching `href` (relative or with the
 *             source's own domain) before the content is saved locally —
 *             the source's CTA buttons would otherwise point at pages that
 *             don't exist on bocp.ro (relative links resolve against
 *             bocp.ro, not the source site) ]
 */
function bocp_mirror_sources() {
	return array(
		'cloudsales-pachete' => array(
			'source_url'           => 'https://www.cloudsales.ro/alege-pachetul-potrivit-businessului-tau/',
			'source_api'           => 'https://www.cloudsales.ro/wp-json/wp/v2/pages?slug=alege-pachetul-potrivit-businessului-tau&_fields=title,content,modified_gmt',
			'local_slug'           => 'alege-pachetul-potrivit-businessului-tau',
			'local_title_fallback' => __( 'Alege pachetul potrivit businessului tău', 'bocp' ),
			'link_rewrites'        => array(
				'/contact/'                           => 'https://bocp.ro/new-contact/',
				'/testeaza-aplicatia-bocp-cloudsales/' => 'https://bocp.ro/new-contact/',
			),
		),
	);
}

/**
 * Rewrites every `href` in $html that points at $path on the source site —
 * whether written as a relative link ("/contact/") or an absolute one
 * (e.g. "https://www.cloudsales.ro/contact/") — to $target instead.
 */
function bocp_mirror_rewrite_link( $html, $path, $target ) {
	$pattern = '#href="(?:https?://(?:www\.)?cloudsales\.ro)?' . preg_quote( $path, '#' ) . '"#i';
	return preg_replace( $pattern, 'href="' . esc_url( $target ) . '"', $html );
}

/** Finds an already-mirrored local page for $key, if any. Returns post ID or 0. */
function bocp_find_mirrored_page( $key ) {
	$posts = get_posts( array(
		'post_type'      => 'page',
		'post_status'    => array( 'draft', 'publish', 'pending', 'private', 'future' ),
		'meta_key'       => '_bocp_mirror_key',
		'meta_value'     => $key,
		'posts_per_page' => 1,
		'fields'         => 'ids',
	) );
	return $posts ? (int) $posts[0] : 0;
}

/**
 * Pulls one source page's current content via REST API and writes it into
 * its local mirrored page (creating the page on first run, as a DRAFT —
 * same "theme never publishes anything by itself" rule as BOCP Setup).
 * Returns true on success, WP_Error/false on failure (logged, not fatal).
 */
function bocp_sync_one_mirror( $key, $source ) {
	$response = wp_remote_get( $source['source_api'], array( 'timeout' => 15 ) );
	if ( is_wp_error( $response ) ) {
		return $response;
	}
	if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
		return new WP_Error( 'bocp_mirror_http', 'Source API returned ' . wp_remote_retrieve_response_code( $response ) );
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( empty( $body[0]['content']['rendered'] ) ) {
		return new WP_Error( 'bocp_mirror_empty', 'Source API returned no content.' );
	}

	$remote_html  = $body[0]['content']['rendered'];
	$remote_title = ! empty( $body[0]['title']['rendered'] ) ? $body[0]['title']['rendered'] : $source['local_title_fallback'];

	foreach ( $source['link_rewrites'] ?? array() as $path => $target ) {
		$remote_html = bocp_mirror_rewrite_link( $remote_html, $path, $target );
	}

	$post_id = bocp_find_mirrored_page( $key );
	$is_new  = ! $post_id;

	// The source content can legitimately include markup (SVGs, embeds)
	// that default KSES filtering would strip — this only ever writes
	// content we just read from the source's own public API, so it's safe
	// to bypass filtering here the same way WP's core importers do.
	kses_remove_filters();

	if ( $is_new ) {
		$post_id = wp_insert_post( array(
			'post_type'   => 'page',
			'post_status' => 'draft',
			'post_title'  => wp_strip_all_tags( $remote_title ),
			'post_name'   => $source['local_slug'],
			'post_content'=> $remote_html,
		), true );
	} else {
		$post_id = wp_update_post( array(
			'ID'           => $post_id,
			'post_title'   => wp_strip_all_tags( $remote_title ),
			'post_content' => $remote_html,
		), true );
	}

	kses_init_filters();

	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}

	update_post_meta( $post_id, '_bocp_mirror_key', $key );
	update_post_meta( $post_id, '_bocp_mirror_source_url', $source['source_api'] );
	update_post_meta( $post_id, '_bocp_mirror_last_synced', current_time( 'mysql' ) );
	if ( $is_new ) {
		update_post_meta( $post_id, '_wp_page_template', 'page-templates/template-canvas.php' );
	}

	return true;
}

function bocp_run_mirror_sync() {
	foreach ( bocp_mirror_sources() as $key => $source ) {
		bocp_sync_one_mirror( $key, $source );
	}
}
add_action( 'bocp_mirror_sync_event', 'bocp_run_mirror_sync' );

function bocp_schedule_mirror_sync() {
	if ( ! wp_next_scheduled( 'bocp_mirror_sync_event' ) ) {
		wp_schedule_event( time(), 'hourly', 'bocp_mirror_sync_event' );
	}
}
add_action( 'wp', 'bocp_schedule_mirror_sync' );

/** Clean up the scheduled event if the theme is ever switched away from. */
function bocp_unschedule_mirror_sync() {
	$timestamp = wp_next_scheduled( 'bocp_mirror_sync_event' );
	if ( $timestamp ) {
		wp_unschedule_event( $timestamp, 'bocp_mirror_sync_event' );
	}
}
add_action( 'switch_theme', 'bocp_unschedule_mirror_sync' );

/* ---------------------------------------------------------------------
 * Admin page: Aspect > BOCP Setup > shows mirror status + manual sync
 * ------------------------------------------------------------------- */

function bocp_render_mirror_status_section() {
	if ( isset( $_POST['bocp_mirror_sync_now'] ) && check_admin_referer( 'bocp_mirror_sync_now' ) ) {
		bocp_run_mirror_sync();
		echo '<div class="notice notice-success"><p>' . esc_html__( 'Sincronizare manuală rulată.', 'bocp' ) . '</p></div>';
	}
	?>
	<h2 style="margin-top:32px"><?php esc_html_e( 'Pagini oglindite (mirror) de pe alte site-uri', 'bocp' ); ?></h2>
	<p><?php esc_html_e( 'Aceste pagini se sincronizează automat, o dată pe oră, cu conținutul lor original de pe site-ul sursă (prin API-ul public REST al WordPress). Prima dată se creează ca DRAFT — le publici tu manual, o singură dată; actualizările ulterioare rescriu conținutul paginii deja existente, indiferent dacă e draft sau publicată.', 'bocp' ); ?></p>
	<table class="widefat striped" style="max-width:900px;margin-bottom:16px">
		<thead><tr>
			<th><?php esc_html_e( 'Sursă', 'bocp' ); ?></th>
			<th><?php esc_html_e( 'Pagină locală', 'bocp' ); ?></th>
			<th><?php esc_html_e( 'Ultima sincronizare', 'bocp' ); ?></th>
		</tr></thead>
		<tbody>
			<?php foreach ( bocp_mirror_sources() as $key => $source ) : ?>
				<?php
				$post_id = bocp_find_mirrored_page( $key );
				$last    = $post_id ? get_post_meta( $post_id, '_bocp_mirror_last_synced', true ) : '';
				?>
				<tr>
					<td><a href="<?php echo esc_url( $source['source_url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $source['local_slug'] ); ?></a></td>
					<td>
						<?php if ( $post_id ) : ?>
							<a href="<?php echo esc_url( get_edit_post_link( $post_id ) ); ?>"><?php esc_html_e( 'editează', 'bocp' ); ?></a>
							· <a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php esc_html_e( 'vezi', 'bocp' ); ?></a>
						<?php else : ?>
							<span style="color:#8a8a8a">— <?php esc_html_e( 'nu s-a sincronizat încă', 'bocp' ); ?></span>
						<?php endif; ?>
					</td>
					<td><?php echo $last ? esc_html( $last ) : '—'; ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<form method="post">
		<?php wp_nonce_field( 'bocp_mirror_sync_now' ); ?>
		<button type="submit" name="bocp_mirror_sync_now" value="1" class="button">
			<?php esc_html_e( 'Sincronizează acum', 'bocp' ); ?>
		</button>
	</form>
	<?php
}
add_action( 'bocp_setup_page_after', 'bocp_render_mirror_status_section' );
