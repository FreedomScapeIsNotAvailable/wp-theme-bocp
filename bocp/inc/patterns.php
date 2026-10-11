<?php
/**
 * BOCP block patterns — ready-made section designs, one click away from
 * the block inserter's "Patterns" tab (own "BOCP" category). This is how
 * you "retrofit" a design onto content that doesn't have it yet (e.g. this
 * theme installed over an existing site's pages): insert the pattern that
 * matches the shape you want, then move your existing text into it and
 * delete the old plain content.
 *
 * Every pattern reuses the exact same bocp_block_*() functions that build
 * the 7 seeded BOCP pages (inc/admin-setup.php) — same markup, same CSS
 * classes, so a pattern inserted here looks and behaves identically to the
 * equivalent section on the original pages (movable/duplicable blocks,
 * real Image blocks, etc.), just with generic placeholder copy instead of
 * BOCP-specific text.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bocp_register_pattern_category() {
	register_block_pattern_category( 'bocp', array(
		'label' => __( 'BOCP', 'bocp' ),
	) );
}
add_action( 'init', 'bocp_register_pattern_category' );

function bocp_register_patterns() {

	register_block_pattern( 'bocp/card-grid', array(
		'title'       => __( 'BOCP: Grid de carduri (3 coloane)', 'bocp' ),
		'description' => __( 'Titlu de secțiune + 3 carduri cu iconiță, titlu, descriere și link.', 'bocp' ),
		'categories'  => array( 'bocp' ),
		'content'     => bocp_block_section_grid(
			__( 'Kicker secțiune', 'bocp' ),
			__( 'Titlul secțiunii', 'bocp' ),
			__( 'O propoziție scurtă care explică despre ce sunt cardurile de mai jos.', 'bocp' ),
			array(
				array( 'zap', __( 'Primul avantaj', 'bocp' ), __( 'Descriere scurtă a primului avantaj sau modul.', 'bocp' ), '#', __( 'Vezi mai mult', 'bocp' ) ),
				array( 'shield', __( 'Al doilea avantaj', 'bocp' ), __( 'Descriere scurtă a celui de-al doilea avantaj sau modul.', 'bocp' ), '#', __( 'Vezi mai mult', 'bocp' ) ),
				array( 'layers', __( 'Al treilea avantaj', 'bocp' ), __( 'Descriere scurtă a celui de-al treilea avantaj sau modul.', 'bocp' ), '#', __( 'Vezi mai mult', 'bocp' ) ),
			),
			3,
			false
		),
	) );

	register_block_pattern( 'bocp/feature-list', array(
		'title'       => __( 'BOCP: Listă de features (secvențial)', 'bocp' ),
		'description' => __( 'Titlu de secțiune + o listă verticală de puncte cu bifă, unul după altul.', 'bocp' ),
		'categories'  => array( 'bocp' ),
		'content'     => bocp_block_group( 'section wrap', bocp_block_group( '',
			bocp_block_paragraph( 'kicker', __( 'Kicker secțiune', 'bocp' ) )
			. bocp_block_heading( 2, 'h-2', __( 'Ce include', 'bocp' ) )
			. bocp_block_paragraph( 'lead muted', __( 'O propoziție scurtă introductivă pentru lista de mai jos.', 'bocp' ) )
			. bocp_block_features_text( '', '', '', array(
				__( 'Primul punct din listă', 'bocp' ),
				__( 'Al doilea punct din listă', 'bocp' ),
				__( 'Al treilea punct din listă', 'bocp' ),
				__( 'Al patrulea punct din listă', 'bocp' ),
			) )
		) ),
	) );

	register_block_pattern( 'bocp/flow-steps', array(
		'title'       => __( 'BOCP: Pași numerotați (flow)', 'bocp' ),
		'description' => __( 'Titlu de secțiune + 4 pași numerotați, cu săgeți între ei (orizontal pe desktop).', 'bocp' ),
		'categories'  => array( 'bocp' ),
		'content'     => bocp_block_section_flow(
			__( 'Kicker secțiune', 'bocp' ),
			__( 'Cum funcționează', 'bocp' ),
			__( 'O propoziție scurtă introductivă pentru fluxul de mai jos.', 'bocp' ),
			array(
				array( 'clipboard', __( 'Primul pas', 'bocp' ), __( 'Descrierea primului pas din flux.', 'bocp' ) ),
				array( 'settings', __( 'Al doilea pas', 'bocp' ), __( 'Descrierea celui de-al doilea pas din flux.', 'bocp' ) ),
				array( 'sync', __( 'Al treilea pas', 'bocp' ), __( 'Descrierea celui de-al treilea pas din flux.', 'bocp' ) ),
				array( 'checkCircle', __( 'Al patrulea pas', 'bocp' ), __( 'Descrierea celui de-al patrulea pas din flux.', 'bocp' ) ),
			),
			false
		),
	) );

	register_block_pattern( 'bocp/stats', array(
		'title'       => __( 'BOCP: Bandă de statistici', 'bocp' ),
		'description' => __( '4 cifre-cheie, alăturate, pe fundal negru.', 'bocp' ),
		'categories'  => array( 'bocp' ),
		'content'     => bocp_block_section_stats(
			array(
				array( 'X ani', __( 'pe piață', 'bocp' ) ),
				array( 'X+', __( 'clienți mulțumiți', 'bocp' ) ),
				array( 'X+', __( 'proiecte finalizate', 'bocp' ) ),
				array( 'X%', __( 'satisfacție', 'bocp' ) ),
			),
			true
		),
	) );

	register_block_pattern( 'bocp/testimonials', array(
		'title'       => __( 'BOCP: Testimoniale', 'bocp' ),
		'description' => __( 'Titlu de secțiune + 3 carduri cu citate de la clienți.', 'bocp' ),
		'categories'  => array( 'bocp' ),
		'content'     => bocp_block_section_testimonials(
			__( 'Ce spun clienții', 'bocp' ),
			__( 'Firme reale, rezultate reale', 'bocp' ),
			array(
				array( __( 'Textul testimonialului — ce a spus clientul despre experiența lui.', 'bocp' ), __( 'Nume Prenume', 'bocp' ), __( 'Rol, Firmă', 'bocp' ), 'NP' ),
				array( __( 'Textul testimonialului — ce a spus clientul despre experiența lui.', 'bocp' ), __( 'Nume Prenume', 'bocp' ), __( 'Rol, Firmă', 'bocp' ), 'NP' ),
				array( __( 'Textul testimonialului — ce a spus clientul despre experiența lui.', 'bocp' ), __( 'Nume Prenume', 'bocp' ), __( 'Rol, Firmă', 'bocp' ), 'NP' ),
			)
		),
	) );

	register_block_pattern( 'bocp/cta-band', array(
		'title'       => __( 'BOCP: CTA final', 'bocp' ),
		'description' => __( 'Bandă de închidere, cu titlu, text și 2 butoane, pe fundal negru.', 'bocp' ),
		'categories'  => array( 'bocp' ),
		'content'     => bocp_block_section_cta(
			__( 'Titlul apelului la acțiune', 'bocp' ),
			__( 'O propoziție scurtă care explică ce se întâmplă dacă apeși butonul.', 'bocp' ),
			__( 'Buton principal', 'bocp' ), '#',
			__( 'Buton secundar', 'bocp' ), '#',
			true
		),
	) );

	register_block_pattern( 'bocp/split-text-image', array(
		'title'       => __( 'BOCP: Split text + imagine', 'bocp' ),
		'description' => __( 'Text (titlu, listă de puncte, buton) pe o parte, imagine reală (înlocuibilă din Media Library) pe cealaltă.', 'bocp' ),
		'categories'  => array( 'bocp' ),
		'content'     => bocp_block_features_section(
			__( 'Kicker secțiune', 'bocp' ),
			__( 'Titlul secțiunii', 'bocp' ),
			__( 'O propoziție scurtă introductivă pentru punctele de mai jos.', 'bocp' ),
			array(
				__( 'Primul punct din listă', 'bocp' ),
				__( 'Al doilea punct din listă', 'bocp' ),
				__( 'Al treilea punct din listă', 'bocp' ),
			),
			'4 / 3',
			'text-first',
			false,
			__( 'Text buton', 'bocp' ),
			'#',
			__( 'Imagine reprezentativă', 'bocp' )
		),
	) );

	register_block_pattern( 'bocp/page-hero', array(
		'title'       => __( 'BOCP: Hero pagină interioară', 'bocp' ),
		'description' => __( 'Breadcrumb, kicker, titlu H1, text scurt și 2 butoane. Un singur H1 pe pagină.', 'bocp' ),
		'categories'  => array( 'bocp' ),
		'content'     => bocp_block_page_hero(
			array( 'home' => home_url( '/' ) ),
			__( 'Numele paginii', 'bocp' ),
			__( 'Kicker pagină', 'bocp' ),
			__( 'Titlul principal al paginii', 'bocp' ),
			__( 'Două rânduri care spun pe scurt ce găsește vizitatorul pe această pagină.', 'bocp' ),
			__( 'Buton principal', 'bocp' ), '#',
			__( 'Buton secundar', 'bocp' ), '#'
		),
	) );

	register_block_pattern( 'bocp/infobox', array(
		'title'       => __( 'BOCP: Infobox', 'bocp' ),
		'description' => __( 'Card lat cu iconiță, titlu, descriere și link — pentru o trimitere sau o notă importantă.', 'bocp' ),
		'categories'  => array( 'bocp' ),
		'content'     => bocp_block_section_infobox(
			'zap',
			__( 'Titlul infobox-ului', 'bocp' ),
			__( 'O propoziție sau două despre subiectul spre care trimite linkul.', 'bocp' ),
			__( 'Vezi detalii', 'bocp' ),
			'#',
			false,
			true
		),
	) );

	register_block_pattern( 'bocp/faq', array(
		'title'       => __( 'BOCP: Întrebări frecvente (acordeon)', 'bocp' ),
		'description' => __( 'Titlu de secțiune + listă de întrebări care se deschid la click, fără JS. Editezi textul cu „Editează ca HTML”.', 'bocp' ),
		'categories'  => array( 'bocp' ),
		'content'     => bocp_block_section_faq(
			__( 'Întrebări frecvente', 'bocp' ),
			__( 'Ce ne întreabă clienții', 'bocp' ),
			array(
				array( __( 'Prima întrebare?', 'bocp' ), __( 'Răspunsul scurt la prima întrebare.', 'bocp' ) ),
				array( __( 'A doua întrebare?', 'bocp' ), __( 'Răspunsul scurt la a doua întrebare.', 'bocp' ) ),
				array( __( 'A treia întrebare?', 'bocp' ), __( 'Răspunsul scurt la a treia întrebare.', 'bocp' ) ),
			),
			true
		),
	) );
}
add_action( 'init', 'bocp_register_patterns' );
