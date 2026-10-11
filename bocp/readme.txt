=== BOCP ===

Temă WordPress standalone, construită din machetă Claude Design ("Website
de prezentare BOCP"). Fără page builder (Elementor/Pagelayer) — conținutul
folosește Editorul de blocuri nativ WordPress (Gutenberg), ca să poți muta,
duplica și rearanja secțiuni, și să înlocuiești imaginile hero/feature ca
imagini native, direct din Media Library.

== Ce face tema ==

Tema oferă layout + design: header/footer, UN singur page template
("BOCP — Pagină"), plus Customizer pentru culori/fonturi/logo/date companie
și 2 meniuri WP (header + footer).

Conținutul fiecărei pagini e format din blocuri normale WordPress:
- secțiuni întregi (grid de module, statistici, flow, testimoniale, CTA,
  tag-uri) — blocuri „HTML personalizat", mutabile/duplicabile ca blocuri;
- hero-ul și secțiunile cu imagine — blocuri „Coloane" cu textul într-o
  coloană și o imagine reală (bloc Imagine nativ) în cealaltă, deci
  înlocuibilă direct din Media Library.

Tema NU include nicio pagină. Conținutul (cele 7 pagini BOCP) se creează
separat, din Aspect > BOCP Setup, apăsând "Creează paginile de start" — vezi
mai jos.

== Aspect > BOCP Setup ==

Creează cele 7 pagini originale ca DRAFT, titlu "NEW - ...", cu tot
conținutul din machetă deja scris ca blocuri. Rulează din nou oricând —
creează doar paginile care încă lipsesc. Dacă bifezi "Suprascrie paginile
existente", regenerează și conținutul paginilor deja create.

Pașii tăi, după ce apeși butonul:
1. Aspect > Pagini — găsești cele 7 pagini "NEW - ...".
2. Le deschizi normal, în Editorul de blocuri — muți/duplici/ștergi
   secțiuni din lista de blocuri, înlocuiești imaginile din Media Library,
   adaugi orice bloc nou (paragraf, buton, imagine, coloane etc.).
3. Le redenumești (elimini "NEW - ") și le publici când ești gata.
4. Aspect > Meniuri — le adaugi în meniul "Meniu principal (header)".
5. Aspect > Widgets — în "Footer — Coloana 1" pui un widget Meniu de
   navigare (sau Text) cu link-urile spre module, în "Footer — Coloana 2"
   la fel pentru Contact/Testează gratuit/Cere demo, iar în "Footer —
   Contact" un widget HTML personalizat cu shortcode-ul
   [bocp_contact_info] (sau text liber).
6. (Opțional) Setări > Citire — alegi pagina "Acasă" ca pagină statică
   frontală, dacă vrei ca ea să fie homepage-ul site-ului.

== Editare — ce poți face nativ ==

- Mutare: selectezi orice bloc (o secțiune, o imagine) → săgeți sus/jos în
  toolbar-ul blocului, sau tragi de mânerul de mutare.
- Duplicare: selectezi blocul → meniul „⋮" → „Duplică" (sau Ctrl/Cmd+Shift+D).
- Imagini: hero-ul și capturile de ecran sunt deja blocuri Imagine reale —
  le selectezi și apeși „Înlocuiește" pentru poza definitivă.
- Conținut nou: „+” între blocuri, orice bloc nativ (paragraf, imagine,
  buton, coloane), sau un bloc „HTML personalizat” cu clasele CSS ale
  design system-ului (listate în pagina BOCP Setup).
- Dacă un bloc arată „conține conținut neașteptat”: inofensiv, apeși
  „Păstrează ca HTML” — afișarea pe site nu e afectată.

== Personalizare (Aspect > Personalizare) ==

- BOCP — Culori brand: culoare primară + secundară (restul nuanțelor se
  calculează automat).
- BOCP — Fonturi: IBM Plex (self-hosted, implicit) sau fonturi de sistem.
- BOCP — Butoane meniu: cele 2 butoane din header/drawer mobil.
- BOCP — Date companie (footer): nume, adresă, telefon, email, date fiscale
  — folosite de shortcode-ul [bocp_contact_info] și de linia de copyright.
- Identitate site (nativ WP): logo, titlu site.

Cele 3 coloane de conținut ale footer-ului (Module/Companie/Contact) NU
sunt în Personalizare — sunt widget-uri native, în Aspect > Widgets:
"Footer — Coloana 1", "Footer — Coloana 2", "Footer — Contact". Adaugi,
ștergi, reordonezi widget-uri (Meniu de navigare, Text, HTML personalizat)
exact ca la orice altă zonă de widget-uri WordPress.

== Cum adaugi o pagină nouă pe alt site ==

Orice pagină nouă (Aspect > Pagini > Adaugă pagină), cu template-ul
"BOCP — Pagină" ales din Atribute pagină, se comportă la fel: construiești
conținutul din blocuri, folosind clasele CSS din design system pentru
blocurile HTML personalizate (listate în Aspect > BOCP Setup).

== Child theme (bocp-child) ==

wp-content/themes/bocp-child/ e un child theme minimal, gata de activat.
Orice fișier din tema părinte (page-templates/, template-parts/, assets/)
poate fi suprascris punând un fișier cu EXACT același nume/cale în
bocp-child/. La orice update al temei bocp, personalizările din bocp-child,
conținutul paginilor și Customizer/Meniuri rămân neatinse.

== Note tehnice ==

- Fonturile IBM Plex Sans/Mono sunt self-hosted în assets/fonts/ (subset
  latin + latin-ext, pentru diacriticele ă â î ș ț).
- Integrarea cu Contact Form 7 e doar vizuală (inc/cf7-compat.php stilizează
  formularul CF7 ca să semene cu design system-ul) — tema funcționează și
  fără CF7 instalat; pui shortcode-ul CF7 direct în blocul de conținut al
  paginii de Contact.
- Editorul de blocuri e activ pentru pagini (nu se mai forțează Classic
  Editor); pentru articole de blog, alegerea rămâne la plugin-ul Classic
  Editor (deja instalat), din Setări > Scriere.
- Niciun theme.json, niciun block-template complet — tema rămâne un set
  clasic de fișiere PHP (header.php/footer.php/page-templates/), cu un
  singur page template care afișează pur și simplu the_content().
