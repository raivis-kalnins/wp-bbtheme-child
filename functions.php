<?php
defined('ABSPATH') || exit;

require_once __DIR__ . '/inc/frontend-password-protection.php';

function wp_theme_child_project_mode($mode){ return 'business'; }
add_filter('wp_theme_project_mode','wp_theme_child_project_mode');

function wpbb_business_assets() {
    $theme = wp_get_theme();
    $manifest = get_stylesheet_directory() . '/dist/.vite/manifest.json';
    if (!is_readable($manifest)) return;
    $data = json_decode((string) file_get_contents($manifest), true);
    if (!is_array($data)) return;
    if (!empty($data['src/scss/public.scss']['file'])) {
        wp_enqueue_style('wpbb_business-app', get_stylesheet_directory_uri() . '/dist/' . ltrim($data['src/scss/public.scss']['file'], '/'), array(), $theme->get('Version'));
        if (function_exists('wp_theme_sector_customizer_css')) wp_add_inline_style('wpbb_business-app', wp_theme_sector_customizer_css('#4b5563', '16px', '--sector-primary', '--sector-radius'));
    }
    if (!empty($data['src/js/main.js']['file'])) wp_enqueue_script('wpbb_business-app', get_stylesheet_directory_uri() . '/dist/' . ltrim($data['src/js/main.js']['file'], '/'), array(), $theme->get('Version'), true);
}
add_action('wp_enqueue_scripts', 'wpbb_business_assets', 30);

function wpbb_business_dark_mode_bootstrap() { echo '<script>(function(){try{var m=localStorage.getItem("wpThemeMode");if(m==="dark"){document.documentElement.classList.add("is-dark-theme");document.documentElement.setAttribute("data-theme","dark");}}catch(e){}})();</script>'; }
add_action('wp_head', 'wpbb_business_dark_mode_bootstrap', 1);


/** v3.8.10.23: JSON-backed demo translations for child-owned strings. */
function wpbb_business_demo_translation_map() {
    static $maps = null;
    if ( null !== $maps ) return $maps;
    $path = get_stylesheet_directory() . '/inc/demo-translations.json';
    if ( ! is_readable( $path ) ) return $maps = array();
    $decoded = json_decode( (string) file_get_contents( $path ), true );
    return $maps = is_array( $decoded ) ? $decoded : array();
}

function wpbb_business_demo_language() {
    if ( function_exists( 'pll_current_language' ) ) {
        $polylang = pll_current_language( 'slug' );
        if ( is_string( $polylang ) && '' !== $polylang ) return strtolower( substr( $polylang, 0, 2 ) );
    }
    $locale = function_exists( 'determine_locale' ) ? determine_locale() : get_locale();
    return strtolower( substr( str_replace( '_', '-', (string) $locale ), 0, 2 ) );
}

function wpbb_business_demo_gettext( $translated, $text, $domain ) {
    if ( 'wp-bbtheme-child' !== $domain || '' === trim( (string) $text ) ) return $translated;
    $language = wpbb_business_demo_language();
    $maps = wpbb_business_demo_translation_map();
    if ( empty( $maps[ $language ] ) || ! is_array( $maps[ $language ] ) ) return $translated;
    return array_key_exists( $text, $maps[ $language ] ) ? (string) $maps[ $language ][ $text ] : $translated;
}
add_filter( 'gettext', 'wpbb_business_demo_gettext', 20, 3 );

function wpbb_business_translate_rendered_demo_content( $content ) {
    if ( is_admin() || 'lv' !== wpbb_business_demo_language() || ! is_string( $content ) || '' === $content ) return $content;
    $maps = wpbb_business_demo_translation_map();
    if ( empty( $maps['lv'] ) || ! is_array( $maps['lv'] ) ) return $content;
    $replace = array();
    foreach ( $maps['lv'] as $source => $target ) {
        if ( is_string( $source ) && '' !== $source && is_string( $target ) && '' !== $target ) $replace[ $source ] = $target;
    }
    return $replace ? strtr( $content, $replace ) : $content;
}
add_filter( 'the_content', 'wpbb_business_translate_rendered_demo_content', 99 );


/** v3.8.10.23: keep the existing three-card client-results block upgrade-safe. */
function wpbb_business_upgrade_testimonial_swiper( $parsed_block ) {
    if ( empty( $parsed_block['blockName'] ) || 'wpbb/swiper' !== $parsed_block['blockName'] ) return $parsed_block;
    $attrs = isset( $parsed_block['attrs'] ) && is_array( $parsed_block['attrs'] ) ? $parsed_block['attrs'] : array();
    if ( 'cards' !== ( $attrs['demoStyle'] ?? '' ) || empty( $attrs['slides'] ) || ! is_array( $attrs['slides'] ) || 3 !== count( $attrs['slides'] ) ) return $parsed_block;

    $titles = array_map(
        static function( $slide ) { return is_array( $slide ) ? (string) ( $slide['title'] ?? '' ) : ''; },
        $attrs['slides']
    );
    if ( ! in_array( 'Clearer decisions and a site the team can actually edit.', $titles, true ) || ! in_array( 'Fast enough to feel simple.', $titles, true ) ) return $parsed_block;

    $attrs['slides'][] = array(
        'type'    => 'card',
        'eyebrow' => __( 'Delivery partner', 'wp-bbtheme-child' ),
        'title'   => __( 'A dependable system for the next release.', 'wp-bbtheme-child' ),
        'text'    => __( 'Clear components, sensible defaults and reusable editorial patterns keep future delivery moving.', 'wp-bbtheme-child' ),
    );
    $attrs['showNavigation'] = true;
    $attrs['rewind'] = true;
    $parsed_block['attrs'] = $attrs;
    return $parsed_block;
}
add_filter( 'render_block_data', 'wpbb_business_upgrade_testimonial_swiper', 20 );


/** v3.8.10.23: repair already-imported Latvian starter content in the database. */
function wpbb_business_repair_existing_lv_demo_content() {
    if ( get_option( 'wpbb_business_lv_demo_translation_v381023_done' ) || ! current_user_can( 'manage_options' ) ) return;

    $maps = wpbb_business_demo_translation_map();
    if ( empty( $maps['lv'] ) || ! is_array( $maps['lv'] ) ) {
        update_option( 'wpbb_business_lv_demo_translation_v381023_done', 1, false );
        return;
    }

    $site_locale = strtolower( (string) get_option( 'WPLANG', '' ) );
    $site_is_lv = 0 === strpos( str_replace( '_', '-', $site_locale ), 'lv' );
    $posts = get_posts( array(
        'post_type'      => array( 'page', 'post' ),
        'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
        'posts_per_page' => -1,
        'orderby'        => 'ID',
        'order'          => 'ASC',
        'fields'         => 'all',
        'no_found_rows'  => true,
    ) );

    foreach ( $posts as $post ) {
        $is_lv = $site_is_lv;
        if ( function_exists( 'pll_get_post_language' ) ) {
            $post_language = pll_get_post_language( $post->ID, 'slug' );
            $is_lv = is_string( $post_language ) && 0 === strpos( strtolower( $post_language ), 'lv' );
        }
        if ( ! $is_lv ) continue;

        $content = strtr( (string) $post->post_content, $maps['lv'] );
        $title   = strtr( (string) $post->post_title, $maps['lv'] );
        $excerpt = strtr( (string) $post->post_excerpt, $maps['lv'] );
        if ( $content === $post->post_content && $title === $post->post_title && $excerpt === $post->post_excerpt ) continue;

        wp_update_post( wp_slash( array(
            'ID'           => $post->ID,
            'post_content' => $content,
            'post_title'   => $title,
            'post_excerpt' => $excerpt,
        ) ) );
    }

    update_option( 'wpbb_business_lv_demo_translation_v381023_done', 1, false );
}
add_action( 'admin_init', 'wpbb_business_repair_existing_lv_demo_content', 55 );

add_action('after_setup_theme', function(){ if(function_exists('wp_theme_enqueue_font_imports')) add_action('wp_enqueue_scripts','wp_theme_enqueue_font_imports',12); if(function_exists('wp_theme_output_style_tokens')) add_action('wp_head','wp_theme_output_style_tokens',8); },20);

function wp_theme_child_demo_profile($profile) {
	$profile['id'] = 'business';
	$profile['name'] = __('Modern Business', 'wp-bbtheme-child');
	$profile['eyebrow'] = __('Business, agency or service website', 'wp-bbtheme-child');
	$profile['hero_title'] = __('A calm, capable website for your next stage of growth.', 'wp-bbtheme-child');
	$profile['hero_text'] = __('Strategy, design and digital delivery for organisations that need a clear, maintainable website.', 'wp-bbtheme-child');
	$profile['commerce'] = false;
	$profile['services_eyebrow'] = __('Expertise', 'wp-bbtheme-child');
	$profile['services_heading'] = __('A compact team for strategy, design and digital delivery.', 'wp-bbtheme-child');
	$profile['about_eyebrow'] = __('How we work', 'wp-bbtheme-child');
	$profile['industries_eyebrow'] = __('Selected capabilities', 'wp-bbtheme-child');
	$profile['industries_heading'] = __('Built for organisations that need clarity, pace and a system they can own.', 'wp-bbtheme-child');
	$profile['process_eyebrow'] = __('Delivery model', 'wp-bbtheme-child');
	$profile['process_heading'] = __('A simple process from first question to measurable release.', 'wp-bbtheme-child');
	$profile['faq_heading'] = __('Practical answers before a project starts.', 'wp-bbtheme-child');
	$profile['palette'] = [
		'theme_brand_color' => '#4b5563',
		'theme_accent_color' => '#6b7280',
		'theme_text_color' => '#20262d',
		'theme_heading_color' => '#171c21',
		'theme_background_color' => '#f5f6f7',
		'theme_surface_color' => '#ffffff',
		'theme_surface_alt_color' => '#f0f2f3',
		'theme_border_color' => '#d9dde1',
		'theme_grey_dark_color' => '#68727c',
		'theme_grey_light_color' => '#f0f2f3',
		'theme_success_color' => '#5f6b66',
		'theme_link_color' => '#4b5563',
		'theme_link_hover_color' => '#343b44',
		'theme_radius' => '16px',
		'theme_font_provider' => 'system',
		'theme_body_font' => "system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
		'theme_heading_font' => "system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
		'theme_ui_font' => "system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
	];
	$profile['services'] = [
		[__('Digital strategy', 'wp-bbtheme-child'), __('Plan the content, journeys and technology needed to move the business forward.', 'wp-bbtheme-child')],
		[__('Web design', 'wp-bbtheme-child'), __('A restrained visual system built from editable WordPress patterns.', 'wp-bbtheme-child')],
		[__('Growth support', 'wp-bbtheme-child'), __('Keep publishing, testing and improving after the first launch.', 'wp-bbtheme-child')],
	];

	return $profile;
}
add_filter('wp_theme_demo_profile', 'wp_theme_child_demo_profile');


/**
 * Premium starter content layered on top of the neutral parent importer.
 */
function wp_theme_child_demo_profile_premium( $profile ) {
	$assets = trailingslashit( get_stylesheet_directory_uri() ) . 'assets/img/demo/';
	$profile['hero_image'] = $assets . 'office-wide.jpg';
	$profile['about_image'] = $assets . 'office-detail.jpg';
	$profile['primary_label'] = __( 'Start a project', 'wp-bbtheme-child' );
	$profile['primary_url'] = wp_theme_demo_page_url( 'contact' );
	$profile['secondary_label'] = __( 'Explore services', 'wp-bbtheme-child' );
	$profile['secondary_url'] = '#services';
	$profile['about_title'] = __( 'Clear strategy. Useful design. A site your team can run.', 'wp-bbtheme-child' );
	$profile['about_text'] = __( 'Use a focused content system, reusable sections and performance-minded WordPress delivery without locking editors into a proprietary page builder.', 'wp-bbtheme-child' );
	$profile['hero_slides'] = array(
		array( 'type'=>'hero', 'eyebrow'=>__( 'Strategy · design · delivery', 'wp-bbtheme-child' ), 'title'=>__( 'Clear digital work for ambitious organisations.', 'wp-bbtheme-child' ), 'text'=>__( 'A senior team for strategy, websites and content systems that are useful on launch day and manageable afterwards.', 'wp-bbtheme-child' ), 'image'=>$assets . 'office-wide.jpg', 'buttonText'=>__( 'Start a project', 'wp-bbtheme-child' ), 'buttonUrl'=>wp_theme_demo_page_url( 'contact' ), 'secondaryText'=>__( 'Explore our work', 'wp-bbtheme-child' ), 'secondaryUrl'=>wp_theme_demo_page_url( 'industries' ) ),
		array( 'type'=>'hero', 'eyebrow'=>__( 'A system your team can own', 'wp-bbtheme-child' ), 'title'=>__( 'Strategy, content and design in one reusable WordPress system.', 'wp-bbtheme-child' ), 'text'=>$profile['about_text'], 'image'=>$assets . 'office-planning.jpg', 'buttonText'=>__( 'Explore services', 'wp-bbtheme-child' ), 'buttonUrl'=>'#services' ),
	);
	$profile['stats'] = array(
		array( '01', __( 'Strategy before decoration', 'wp-bbtheme-child' ) ),
		array( '02', __( 'Reusable editing system', 'wp-bbtheme-child' ) ),
		array( '03', __( 'Accessible by default', 'wp-bbtheme-child' ) ),
		array( '04', __( 'Built for iteration', 'wp-bbtheme-child' ) ),
	);
	$profile['process'] = array(
		array( '01', __( 'Discover', 'wp-bbtheme-child' ), __( 'Agree audiences, goals, content priorities and the few journeys that matter most.', 'wp-bbtheme-child' ) ),
		array( '02', __( 'Design', 'wp-bbtheme-child' ), __( 'Create an editorial system of components, page patterns and useful interactions.', 'wp-bbtheme-child' ) ),
		array( '03', __( 'Deliver', 'wp-bbtheme-child' ), __( 'Launch a fast WordPress site your team can maintain, measure and improve.', 'wp-bbtheme-child' ) ),
	);
	$profile['cta_title'] = __( 'Make the next version easier to understand and easier to manage.', 'wp-bbtheme-child' );
	$profile['cta_text'] = __( 'A senior team to shape the brief, build the system and support it after launch.', 'wp-bbtheme-child' );
	$profile['footer_text'] = __( 'Northstar works with consultancies, agencies and service teams on clear, useful digital platforms.', 'wp-bbtheme-child' );
	$profile['page_labels'] = array( 'about' => __( 'About', 'wp-bbtheme-child' ), 'services' => __( 'Services', 'wp-bbtheme-child' ), 'industries' => __( 'Work', 'wp-bbtheme-child' ), 'contact' => __( 'Contact', 'wp-bbtheme-child' ), 'blog' => __( 'Insights', 'wp-bbtheme-child' ) );
	return $profile;
}
add_filter( 'wp_theme_demo_profile', 'wp_theme_child_demo_profile_premium', 20 );

function wp_theme_child_pattern_markup( $name ) {
	$path = get_stylesheet_directory() . '/patterns/' . sanitize_file_name( $name ) . '.php';
	if ( ! is_readable( $path ) ) {
		return '';
	}
	ob_start();
	include $path;
	return trim( (string) ob_get_clean() );
}

function wp_theme_child_demo_extra_sections( $content, $profile ) {
	if ( empty( $profile['id'] ) || 'business' !== $profile['id'] ) {
		return $content;
	}
	return $content . wp_theme_child_pattern_markup( 'business-testimonials' );
}
add_filter( 'wp_theme_demo_extra_home_sections', 'wp_theme_child_demo_extra_sections', 20, 2 );

/** Sector-specific content for the shared editable Services mega menu. */
function wpbb_business_mega_menu_definitions( $definitions, $profile ) {
	if ( empty( $profile['id'] ) || 'business' !== $profile['id'] || empty( $definitions['services'] ) ) return $definitions;
	$definitions['services']['title']   = __( 'Services navigation', 'wp-bbtheme-child' );
	$definitions['services']['heading'] = __( 'Expertise for useful digital work.', 'wp-bbtheme-child' );
	$definitions['services']['intro']   = __( 'Explore strategy, design, delivery and ongoing growth support.', 'wp-bbtheme-child' );
	$definitions['services']['columns'] = array(
		array( 'title' => __( 'What we do', 'wp-bbtheme-child' ), 'links' => array(
			array( __( 'Digital strategy', 'wp-bbtheme-child' ), __( 'Research, priorities and a practical roadmap.', 'wp-bbtheme-child' ), wp_theme_demo_page_url( 'services' ) ),
			array( __( 'Web design', 'wp-bbtheme-child' ), __( 'Accessible Gutenberg design systems.', 'wp-bbtheme-child' ), wp_theme_demo_page_url( 'services' ) ),
			array( __( 'Growth support', 'wp-bbtheme-child' ), __( 'Measure, improve and publish with confidence.', 'wp-bbtheme-child' ), wp_theme_demo_page_url( 'services' ) ),
		) ),
		array( 'title' => __( 'Who we help', 'wp-bbtheme-child' ), 'links' => array(
			array( __( 'Professional services', 'wp-bbtheme-child' ), __( 'Clear propositions and lead journeys.', 'wp-bbtheme-child' ), wp_theme_demo_page_url( 'industries' ) ),
			array( __( 'Retail & ecommerce', 'wp-bbtheme-child' ), __( 'Content-led commerce and campaigns.', 'wp-bbtheme-child' ), wp_theme_demo_page_url( 'industries' ) ),
			array( __( 'Technology teams', 'wp-bbtheme-child' ), __( 'Product stories and scalable content.', 'wp-bbtheme-child' ), wp_theme_demo_page_url( 'industries' ) ),
		) ),
		array( 'title' => __( 'Start here', 'wp-bbtheme-child' ), 'links' => array(
			array( __( 'Our approach', 'wp-bbtheme-child' ), __( 'See how projects move from brief to launch.', 'wp-bbtheme-child' ), wp_theme_demo_page_url( 'about' ) ),
			array( __( 'Insights', 'wp-bbtheme-child' ), __( 'Ideas, guides and useful updates.', 'wp-bbtheme-child' ), get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/blog/' ) ),
			array( __( 'Start a project', 'wp-bbtheme-child' ), __( 'Tell us what you need to improve.', 'wp-bbtheme-child' ), wp_theme_demo_page_url( 'contact' ) ),
		) ),
	);
	return $definitions;
}
add_filter( 'wp_theme_demo_mega_menu_definitions', 'wpbb_business_mega_menu_definitions', 20, 2 );

/** v3.5 sector editorial labels. */
function wpbb_business_blog_profile_v35( $profile ) {
    if ( ( $profile['id'] ?? '' ) !== 'business' ) return $profile;
    $profile['blog_eyebrow'] = __( 'Ideas & perspective', 'wp-bbtheme-child' );
    $profile['blog_archive_title'] = __( 'Useful thinking for teams building what comes next.', 'wp-bbtheme-child' );
    $profile['blog_archive_intro'] = __( 'Practical articles on strategy, content, design systems and maintainable WordPress delivery.', 'wp-bbtheme-child' );
    return $profile;
}
add_filter( 'wp_theme_demo_profile', 'wpbb_business_blog_profile_v35', 90 );

/**
 * v3.8.2: move installations that still use the previous Business starter
 * palette to the calmer graphite defaults. Custom client colours are preserved.
 */
function wpbb_business_migrate_default_palette_v382() {
    if ( get_option( 'wpbb_business_palette_v382_done' ) ) return;
    if ( ! current_user_can( 'manage_options' ) || ! function_exists( 'wp_theme_acf_ready' ) || ! wp_theme_acf_ready() || ! function_exists( 'update_field' ) ) return;
    $changes = array(
        'theme_brand_color'        => array( '#3157ff', '#4b5563' ),
        'theme_accent_color'       => array( '#22685d', '#6b7280' ),
        'theme_text_color'         => array( '#101426', '#20262d' ),
        'theme_heading_color'      => array( '#101426', '#171c21' ),
        'theme_background_color'   => array( '#f7f8fb', '#f5f6f7' ),
        'theme_surface_alt_color'  => array( '#eef1f6', '#f0f2f3' ),
        'theme_border_color'       => array( '#dde2eb', '#d9dde1' ),
        'theme_grey_dark_color'    => array( '#5c6478', '#68727c' ),
        'theme_grey_light_color'   => array( '#eef1f6', '#f0f2f3' ),
        'theme_success_color'      => array( '#22685d', '#5f6b66' ),
        'theme_link_color'         => array( '#3157ff', '#4b5563' ),
        'theme_link_hover_color'   => array( '#213fbf', '#343b44' ),
        'theme_radius'             => array( '26px', '16px' ),
    );
    foreach ( $changes as $field => $pair ) {
        $current = (string) get_field( $field, 'option' );
        if ( '' === $current || 0 === strcasecmp( trim( $current ), $pair[0] ) ) {
            update_field( $field, $pair[1], 'option' );
        }
    }
    update_option( 'wpbb_business_palette_v382_done', 1, false );
}
add_action( 'admin_init', 'wpbb_business_migrate_default_palette_v382', 40 );

/**
 * v3.8.10.20: keep editable Mega Menu content out of public discovery / SEO.
 * The parent already registers these objects as private; child filters make the
 * intent explicit for Core XML sitemaps and common SEO plugins too.
 */
function wpbb_child_private_megamenu_post_type_args( $args, $post_type ) {
    if ( 'megamenu' !== $post_type ) return $args;
    $args['public'] = false;
    $args['publicly_queryable'] = false;
    $args['exclude_from_search'] = true;
    $args['has_archive'] = false;
    $args['rewrite'] = false;
    $args['query_var'] = false;
    return $args;
}
add_filter( 'register_post_type_args', 'wpbb_child_private_megamenu_post_type_args', 20, 2 );

function wpbb_child_private_megamenu_taxonomy_args( $args, $taxonomy ) {
    if ( 'megamenu-cat' !== $taxonomy ) return $args;
    $args['public'] = false;
    $args['publicly_queryable'] = false;
    $args['rewrite'] = false;
    $args['query_var'] = false;
    return $args;
}
add_filter( 'register_taxonomy_args', 'wpbb_child_private_megamenu_taxonomy_args', 20, 2 );

function wpbb_child_core_sitemap_post_types( $post_types ) {
    unset( $post_types['megamenu'] );
    return $post_types;
}
add_filter( 'wp_sitemaps_post_types', 'wpbb_child_core_sitemap_post_types', 20 );

function wpbb_child_core_sitemap_taxonomies( $taxonomies ) {
    unset( $taxonomies['megamenu-cat'] );
    return $taxonomies;
}
add_filter( 'wp_sitemaps_taxonomies', 'wpbb_child_core_sitemap_taxonomies', 20 );

function wpbb_child_mega_robots( $robots ) {
    if ( is_singular( 'megamenu' ) || is_tax( 'megamenu-cat' ) ) {
        $robots['noindex'] = true;
        $robots['nofollow'] = true;
    }
    return $robots;
}
add_filter( 'wp_robots', 'wpbb_child_mega_robots', 20 );

function wpbb_child_yoast_exclude_megamenu_post_type( $excluded, $post_type ) {
    return 'megamenu' === $post_type ? true : $excluded;
}
add_filter( 'wpseo_sitemap_exclude_post_type', 'wpbb_child_yoast_exclude_megamenu_post_type', 20, 2 );

function wpbb_child_yoast_exclude_megamenu_taxonomy( $excluded, $taxonomy ) {
    return 'megamenu-cat' === $taxonomy ? true : $excluded;
}
add_filter( 'wpseo_sitemap_exclude_taxonomy', 'wpbb_child_yoast_exclude_megamenu_taxonomy', 20, 2 );

function wpbb_child_yoast_mega_robots( $robots ) {
    if ( is_singular( 'megamenu' ) || is_tax( 'megamenu-cat' ) ) return 'noindex, nofollow';
    return $robots;
}
add_filter( 'wpseo_robots', 'wpbb_child_yoast_mega_robots', 20 );


/**
 * v3.8.10.21: global request-a-quote UI is opt-in by child theme.
 * Sector themes with their own quote journeys can keep it; the rest do not
 * expose an unrelated floating "My Quote" control or public route.
 */
if ( ! function_exists( 'wpbb_child_request_quote_enabled' ) ) {
    function wpbb_child_request_quote_enabled() {
        $enabled_themes = array(
            'wp-bbtheme-child-automotive',
            'wp-bbtheme-child-building-services',
            'wp-bbtheme-child-insurance',
            'wp-bbtheme-child-logistics',
            'wp-bbtheme-child-medicine',
            'wp-bbtheme-child-woo-tech-shop',
        );
        $enabled = in_array( get_stylesheet(), $enabled_themes, true );
        return (bool) apply_filters( 'wpbb_child_request_quote_enabled', $enabled, get_stylesheet() );
    }
}

function wpbb_child_request_quote_body_class( $classes ) {
    $classes[] = wpbb_child_request_quote_enabled() ? 'wpbb-request-quote-enabled' : 'wpbb-request-quote-disabled';
    return $classes;
}
add_filter( 'body_class', 'wpbb_child_request_quote_body_class', 30 );

function wpbb_child_request_quote_menu_items( $items ) {
    if ( wpbb_child_request_quote_enabled() ) return $items;
    $target = trim( (string) wp_parse_url( home_url( '/request-a-quote/' ), PHP_URL_PATH ), '/' );
    foreach ( $items as $key => $item ) {
        $path = trim( (string) wp_parse_url( $item->url, PHP_URL_PATH ), '/' );
        if ( $target && $path === $target ) unset( $items[ $key ] );
    }
    return $items;
}
add_filter( 'wp_nav_menu_objects', 'wpbb_child_request_quote_menu_items', 30 );

function wpbb_child_request_quote_disable_route() {
    if ( wpbb_child_request_quote_enabled() ) return;
    $request = isset( $GLOBALS['wp']->request ) ? trim( (string) $GLOBALS['wp']->request, '/' ) : '';
    if ( ! is_page( 'request-a-quote' ) && 'request-a-quote' !== $request ) return;

    global $wp_query;
    if ( $wp_query ) $wp_query->set_404();
    status_header( 404 );
    nocache_headers();
    $template = get_404_template();
    if ( $template ) {
        include $template;
        exit;
    }
    wp_die( esc_html__( 'Page not found.', 'wp-bbtheme-child' ), esc_html__( 'Not found', 'wp-bbtheme-child' ), array( 'response' => 404 ) );
}
add_action( 'template_redirect', 'wpbb_child_request_quote_disable_route', 1 );

function wpbb_child_request_quote_sitemap_args( $args, $post_type ) {
    if ( wpbb_child_request_quote_enabled() || 'page' !== $post_type ) return $args;
    $page = get_page_by_path( 'request-a-quote' );
    if ( $page ) {
        $excluded = isset( $args['post__not_in'] ) ? (array) $args['post__not_in'] : array();
        $excluded[] = (int) $page->ID;
        $args['post__not_in'] = array_values( array_unique( $excluded ) );
    }
    return $args;
}
add_filter( 'wp_sitemaps_posts_query_args', 'wpbb_child_request_quote_sitemap_args', 30, 2 );

require_once get_stylesheet_directory() . '/inc/seo-guardrails.php';

/** v3.8.10.24: identify generated legal pages independently of translated slugs. */
function wpbb_child_legal_page_body_class_v381024( $classes ) {
    if ( ! is_page() ) return $classes;
    $post = get_queried_object();
    if ( ! $post instanceof WP_Post ) return $classes;

    $is_legal = function_exists( 'is_privacy_policy' ) && is_privacy_policy();
    if ( ! $is_legal && false !== strpos( (string) $post->post_content, 'wp-theme-legal-section' ) ) {
        $is_legal = true;
    }
    if ( $is_legal ) $classes[] = 'wpbb-legal-page';
    return array_values( array_unique( $classes ) );
}
add_filter( 'body_class', 'wpbb_child_legal_page_body_class_v381024', 40 );

/** v3.8.10.25: remove generated empty spacing without touching authored copy. */
if ( ! function_exists( 'wpbb_child_remove_empty_paragraphs_v381025' ) ) {
    function wpbb_child_remove_empty_paragraphs_v381025( $content ) {
        if ( is_admin() || ! is_string( $content ) || '' === $content ) return $content;
        return (string) preg_replace(
            '~<p(?:\\s[^>]*)?>(?:\\s|&nbsp;|&#160;|<br\\s*/?>)*</p>~i',
            '',
            $content
        );
    }
}
add_filter( 'the_content', 'wpbb_child_remove_empty_paragraphs_v381025', 120 );

/** v3.8.10.25: do not output a completely empty CTA block above the footer. */
if ( ! function_exists( 'wpbb_child_remove_empty_cta_v381025' ) ) {
    function wpbb_child_remove_empty_cta_v381025( $block_content, $block ) {
        if ( empty( $block['blockName'] ) || 'wpbb/cta-section' !== $block['blockName'] || ! is_string( $block_content ) ) return $block_content;
        if ( preg_match( '~<(?:img|picture|video|iframe|form|button|a)\\b~i', $block_content ) ) return $block_content;
        $plain = trim( html_entity_decode( wp_strip_all_tags( $block_content ), ENT_QUOTES | ENT_HTML5, get_bloginfo( 'charset' ) ) );
        return '' === $plain ? '' : $block_content;
    }
}
add_filter( 'render_block', 'wpbb_child_remove_empty_cta_v381025', 120, 2 );



/** v3.8.10.29: make demo switching/imports self-healing across child themes. */
if ( ! function_exists( 'wpbb_child_demo_refresh_on_activation_v381029' ) ) {
    function wpbb_child_demo_refresh_on_activation_v381029() {
        // The parent importer stores one global version/profile. When a different
        // child theme is activated, invalidate that marker so its own profile is
        // imported instead of reusing the previous child's demo state.
        delete_option( 'wp_theme_demo_import_version' );
        delete_option( 'wp_theme_demo_menu_profile' );
    }
    add_action( 'after_switch_theme', 'wpbb_child_demo_refresh_on_activation_v381029', 5 );
}

if ( ! function_exists( 'wpbb_child_demo_integrity_guard_v381029' ) ) {
    function wpbb_child_demo_integrity_guard_v381029( $page_id = 0, $profile = array() ) {
        $page_id = absint( $page_id ?: get_option( 'page_on_front' ) );
        if ( ! $page_id || 'page' !== get_post_type( $page_id ) ) return;

        $content = (string) get_post_field( 'post_content', $page_id );
        // Never rewrite a real imported or edited homepage. This is only a guard
        // for the genuinely empty/near-empty page seen after switching demos.
        if ( strlen( trim( $content ) ) >= 120 ) return;

        if ( ! is_array( $profile ) ) $profile = array();
        $eyebrow = (string) ( $profile['eyebrow'] ?? __( 'Welcome', 'wp-theme' ) );
        $title = (string) ( $profile['hero_title'] ?? get_bloginfo( 'name' ) );
        $intro = (string) ( $profile['hero_text'] ?? __( 'A practical WordPress starter site ready to edit.', 'wp-theme' ) );
        $primary_label = (string) ( $profile['primary_label'] ?? __( 'Get started', 'wp-theme' ) );
        $primary_url = (string) ( $profile['primary_url'] ?? home_url( '/contact/' ) );
        $secondary_label = (string) ( $profile['secondary_label'] ?? __( 'Explore', 'wp-theme' ) );
        $secondary_url = (string) ( $profile['secondary_url'] ?? home_url( '/services/' ) );
        $services_heading = (string) ( $profile['services_heading'] ?? __( 'Useful services, clearly presented.', 'wp-theme' ) );
        $about_title = (string) ( $profile['about_title'] ?? __( 'A flexible starting point for the real site.', 'wp-theme' ) );
        $about_text = (string) ( $profile['about_text'] ?? $intro );
        $hero_image = esc_url( (string) ( $profile['hero_image'] ?? '' ) );
        $about_image = esc_url( (string) ( $profile['about_image'] ?? $hero_image ) );
        $services = ! empty( $profile['services'] ) && is_array( $profile['services'] ) ? array_slice( $profile['services'], 0, 4 ) : array();
        $stats = ! empty( $profile['stats'] ) && is_array( $profile['stats'] ) ? array_slice( $profile['stats'], 0, 4 ) : array();

        $out = '<!-- wp:wpbb/bootstrap-div {"containerClass":"","utilityClasses":"wp-theme-section-shell wp-theme-sector-hero wp-theme-demo-repair","className":"wpbb-v62-section"} --><!-- wp:wpbb/row {"containerClass":"container","customClasses":"align-items-center"} --><!-- wp:wpbb/column {"xs":12,"lg":6} --><p class="wp-theme-sector-eyebrow">' . esc_html( $eyebrow ) . '</p><h1>' . esc_html( $title ) . '</h1><p class="wp-theme-sector-lead">' . esc_html( $intro ) . '</p><div class="wp-theme-demo-buttons"><a class="btn btn-primary" href="' . esc_url( $primary_url ) . '">' . esc_html( $primary_label ) . '</a><a class="btn btn-outline-primary" href="' . esc_url( $secondary_url ) . '">' . esc_html( $secondary_label ) . '</a></div><!-- /wp:wpbb/column -->';
        if ( $hero_image ) $out .= '<!-- wp:wpbb/column {"xs":12,"lg":6} --><figure class="wp-theme-sector-page-image"><img src="' . $hero_image . '" alt="" loading="eager" decoding="async"></figure><!-- /wp:wpbb/column -->';
        $out .= '<!-- /wp:wpbb/row --><!-- /wp:wpbb/bootstrap-div -->';

        if ( 'automotive' === ( $profile['id'] ?? '' ) ) {
            $out .= '<!-- wp:wpbb/bootstrap-div {"containerClass":"","utilityClasses":"wp-theme-section-shell wpbb-automotive-finder-section","className":"wpbb-v62-section"} --><!-- wp:wpbb/row {"containerClass":"container"} --><!-- wp:wpbb/column {"xs":12} --><!-- wp:wpbb/sector-finder {"context":"automotive","limit":8} /--><!-- /wp:wpbb/column --><!-- /wp:wpbb/row --><!-- /wp:wpbb/bootstrap-div -->';
        }

        $out .= '<!-- wp:wpbb/bootstrap-div {"containerClass":"","utilityClasses":"wp-theme-section-shell wp-theme-services-section","className":"wpbb-v62-section"} --><!-- wp:wpbb/row {"containerClass":"container"} --><!-- wp:wpbb/column {"xs":12} --><p class="wp-theme-sector-eyebrow">' . esc_html( (string) ( $profile['services_eyebrow'] ?? __( 'Services', 'wp-theme' ) ) ) . '</p><h2>' . esc_html( $services_heading ) . '</h2><!-- wp:wpbb/row {"gutterX":"gx-4","gutterY":"gy-4"} -->';
        foreach ( $services as $service ) {
            $service_title = is_array( $service ) ? (string) ( $service[0] ?? '' ) : '';
            $service_text = is_array( $service ) ? (string) ( $service[1] ?? '' ) : '';
            if ( '' === trim( $service_title ) ) continue;
            $out .= '<!-- wp:wpbb/column {"xs":12,"md":6,"lg":3} --><article class="wp-theme-sector-card"><h3>' . esc_html( $service_title ) . '</h3><p>' . esc_html( $service_text ) . '</p></article><!-- /wp:wpbb/column -->';
        }
        $out .= '<!-- /wp:wpbb/row --><!-- /wp:wpbb/column --><!-- /wp:wpbb/row --><!-- /wp:wpbb/bootstrap-div -->';

        $out .= '<!-- wp:wpbb/bootstrap-div {"containerClass":"","utilityClasses":"wp-theme-section-shell wp-theme-about-section","className":"wpbb-v62-section"} --><!-- wp:wpbb/row {"containerClass":"container","customClasses":"align-items-center"} -->';
        if ( $about_image ) $out .= '<!-- wp:wpbb/column {"xs":12,"lg":6} --><figure class="wp-theme-sector-page-image"><img src="' . $about_image . '" alt="" loading="lazy" decoding="async"></figure><!-- /wp:wpbb/column -->';
        $out .= '<!-- wp:wpbb/column {"xs":12,"lg":6} --><p class="wp-theme-sector-eyebrow">' . esc_html( (string) ( $profile['about_eyebrow'] ?? __( 'About', 'wp-theme' ) ) ) . '</p><h2>' . esc_html( $about_title ) . '</h2><p class="wp-theme-sector-lead">' . esc_html( $about_text ) . '</p><!-- /wp:wpbb/column --><!-- /wp:wpbb/row --><!-- /wp:wpbb/bootstrap-div -->';

        if ( $stats ) {
            $out .= '<!-- wp:wpbb/bootstrap-div {"containerClass":"","utilityClasses":"wp-theme-section-shell wp-theme-sector-proof","className":"wpbb-v62-section"} --><!-- wp:wpbb/row {"containerClass":"container","gutterX":"gx-3","gutterY":"gy-3"} -->';
            foreach ( $stats as $stat ) {
                $number = is_array( $stat ) ? (string) ( $stat[0] ?? '' ) : '';
                $label = is_array( $stat ) ? (string) ( $stat[1] ?? '' ) : '';
                $out .= '<!-- wp:wpbb/column {"xs":6,"lg":3} --><div class="wp-theme-sector-proof__item"><h3>' . esc_html( $number ) . '</h3><p>' . esc_html( $label ) . '</p></div><!-- /wp:wpbb/column -->';
            }
            $out .= '<!-- /wp:wpbb/row --><!-- /wp:wpbb/bootstrap-div -->';
        }

        $out .= '<!-- wp:wpbb/cta-section {"title":"' . esc_attr( (string) ( $profile['cta_title'] ?? __( 'Ready to make it yours?', 'wp-theme' ) ) ) . '","titleTag":"h2","text":"' . esc_attr( (string) ( $profile['cta_text'] ?? $intro ) ) . '","buttonText":"' . esc_attr( $primary_label ) . '","buttonUrl":"' . esc_url( $primary_url ) . '","className":"wp-theme-home-cta wp-theme-home-cta--bbuilder"} /-->';

        wp_update_post( array( 'ID' => $page_id, 'post_content' => $out ) );
        update_post_meta( $page_id, '_wp_theme_demo_repaired_381029', current_time( 'mysql' ) );
    }
    add_action( 'wp_theme_after_demo_import', 'wpbb_child_demo_integrity_guard_v381029', 99, 2 );
}


/* v3.8.10.30 visual icon configuration */
function wpbb_business_visual_icon_config() {
    $config = array( 'base' => get_stylesheet_directory_uri(), 'icons' => array('briefcase', 'users', 'chart-line', 'device-laptop', 'building', 'calendar', 'map-pin', 'shield') );
    echo '<script>window.wpbbChildVisuals=' . wp_json_encode( $config ) . ';</script>';
}
add_action( 'wp_footer', 'wpbb_business_visual_icon_config', 1 );


/* v3.8.10.30: realistic demo blog featured images. Runs only after the theme's explicit demo import. */
function wpbb_business_demo_blog_photo_attachment( $filename, $title ) {
    $slug = sanitize_title( pathinfo( $filename, PATHINFO_FILENAME ) );
    $existing = get_page_by_path( 'business-blog-' . $slug, OBJECT, 'attachment' );
    if ( $existing ) {
        if ( function_exists( 'wpbb_business_refresh_bundled_attachment_v381041' ) ) wpbb_business_refresh_bundled_attachment_v381041( (int) $existing->ID, 'assets/img/blog' );
        return (int) $existing->ID;
    }
    $source = get_stylesheet_directory() . '/assets/img/blog/' . basename( $filename );
    if ( ! is_readable( $source ) ) return 0;
    $uploads = wp_upload_dir();
    $dir = trailingslashit( $uploads['basedir'] ) . 'business-blog';
    wp_mkdir_p( $dir );
    $target = $dir . '/' . basename( $filename );
    if ( ! file_exists( $target ) ) copy( $source, $target );
    $filetype = wp_check_filetype( $target );
    if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) require_once ABSPATH . 'wp-admin/includes/image.php';
    $id = wp_insert_attachment( array(
        'post_mime_type' => $filetype['type'] ?: 'image/jpeg',
        'post_title' => $title,
        'post_name' => 'business-blog-' . $slug,
        'post_status' => 'inherit',
    ), $target );
    if ( $id && ! is_wp_error( $id ) ) {
        if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) require_once ABSPATH . 'wp-admin/includes/image.php';
        $meta = wpbb_child_381048_generate_attachment_metadata( $id, $target );
        if ( $meta ) wp_update_attachment_metadata( $id, $meta );
        update_post_meta( $id, '_wp_attachment_image_alt', $title );
        return (int) $id;
    }
    return 0;
}
function wpbb_business_seed_demo_blog_photos( $page_id = 0, $profile = array() ) {
    $posts = get_posts( array( 'post_type'=>'post', 'post_status'=>'publish', 'posts_per_page'=>12, 'orderby'=>'date', 'order'=>'DESC' ) );
    if ( ! $posts ) return;
    $images = array( 'blog-1.jpg','blog-2.jpg','blog-3.jpg','blog-4.jpg','blog-5.jpg','blog-6.jpg' );
    foreach ( $posts as $index => $post ) {
        $filename = $images[ $index % count( $images ) ];
        $attachment = wpbb_business_demo_blog_photo_attachment( $filename, get_the_title( $post ) );
        if ( $attachment ) set_post_thumbnail( $post->ID, $attachment );
    }
}
add_action( 'wp_theme_after_demo_import', 'wpbb_business_seed_demo_blog_photos', 70, 2 );


/** v3.8.10.31: apply bundled realistic media to already-imported demos after theme upgrade. */

/**
 * Refresh an already-imported demo attachment from the current child-theme asset.
 *
 * Image optimisation may have changed `_wp_attached_file` from e.g. item-1.jpg to
 * item-1.avif/webp. Resolve the bundled source by filename stem instead of requiring
 * the child theme to ship every generated format, then regenerate all WP sub-sizes.
 */
function wpbb_business_refresh_bundled_attachment_v381041( $attachment_id, $asset_dir ) {
    $attachment_id = absint( $attachment_id );
    if ( ! $attachment_id || 'attachment' !== get_post_type( $attachment_id ) ) return false;

    $attached = (string) get_post_meta( $attachment_id, '_wp_attached_file', true );
    $stem = pathinfo( basename( $attached ), PATHINFO_FILENAME );
    if ( '' === $stem ) return false;

    $base = trailingslashit( get_stylesheet_directory() ) . trailingslashit( $asset_dir ) . $stem;
    $source = '';
    foreach ( array( '.jpg', '.jpeg', '.png', '.webp', '.avif' ) as $extension ) {
        if ( is_readable( $base . $extension ) ) {
            $source = $base . $extension;
            break;
        }
    }
    if ( ! $source ) return false;

    $target = get_attached_file( $attachment_id );
    if ( ! $target ) return false;

    if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) require_once ABSPATH . 'wp-admin/includes/image.php';

    $source_ext = strtolower( (string) pathinfo( $source, PATHINFO_EXTENSION ) );
    $target_ext = strtolower( (string) pathinfo( $target, PATHINFO_EXTENSION ) );
    $written = false;

    if ( $source_ext === $target_ext ) {
        $written = (bool) @copy( $source, $target );
    } else {
        $target_type = wp_check_filetype( $target );
        $target_mime = ! empty( $target_type['type'] ) ? (string) $target_type['type'] : '';
        $editor = wp_get_image_editor( $source );
        if ( ! is_wp_error( $editor ) && 0 === strpos( $target_mime, 'image/' ) ) {
            $saved = $editor->save( $target, $target_mime );
            $written = ! is_wp_error( $saved ) && is_readable( $target );
        }
    }

    // Some hosts can read AVIF/WebP but cannot encode it. Fall back to the bundled
    // source extension and update WordPress to the new original file explicitly.
    if ( ! $written ) {
        $fallback = trailingslashit( dirname( $target ) ) . $stem . '.' . $source_ext;
        if ( ! @copy( $source, $fallback ) ) return false;
        update_attached_file( $attachment_id, $fallback );
        $filetype = wp_check_filetype( $fallback );
        if ( ! empty( $filetype['type'] ) ) {
            wp_update_post( array( 'ID' => $attachment_id, 'post_mime_type' => $filetype['type'] ) );
        }
        $target = $fallback;
    }

    // Remove old generated sizes first. Otherwise stale JPG thumbnails can remain
    // referenced after the original was converted to AVIF/WebP by an optimiser.
    $old_meta = wp_get_attachment_metadata( $attachment_id );
    if ( is_array( $old_meta ) && ! empty( $old_meta['sizes'] ) && is_array( $old_meta['sizes'] ) ) {
        foreach ( $old_meta['sizes'] as $old_size ) {
            if ( empty( $old_size['file'] ) ) continue;
            $old_file = trailingslashit( dirname( $target ) ) . basename( (string) $old_size['file'] );
            if ( is_file( $old_file ) && wp_normalize_path( $old_file ) !== wp_normalize_path( $target ) ) @unlink( $old_file );
        }
    }

    $meta = wpbb_child_381048_generate_attachment_metadata( $attachment_id, $target );
    if ( $meta ) wp_update_attachment_metadata( $attachment_id, $meta );
    clean_attachment_cache( $attachment_id );
    return true;
}

function wpbb_business_realistic_media_upgrade_v381041() {
    if ( ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) || ( function_exists( 'wp_doing_cron' ) && wp_doing_cron() ) ) return;
    $done_key = 'wpbb_business_realistic_media_upgrade_v381041';
    if ( get_option( $done_key ) ) return;
    if ( ! current_user_can( 'manage_options' ) ) return;

    $pairs = array(array('business-blog','assets/img/blog'));
    foreach ( $pairs as $pair ) {
        $upload_prefix = $pair[0];
        $asset_dir = $pair[1];
        $ids = get_posts( array(
            'post_type' => 'attachment',
            'post_status' => 'inherit',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'meta_query' => array( array( 'key'=>'_wp_attached_file', 'value'=>$upload_prefix . '/', 'compare'=>'LIKE' ) ),
        ) );
        foreach ( $ids as $attachment_id ) {
            wpbb_business_refresh_bundled_attachment_v381041( $attachment_id, $asset_dir );
        }
    }
    if ( function_exists( 'wpbb_business_seed_demo_blog_photos' ) ) wpbb_business_seed_demo_blog_photos( 0, array() );
    update_option( $done_key, current_time( 'mysql' ), false );
}
add_action( 'admin_init', 'wpbb_business_realistic_media_upgrade_v381041', 120 );


/* v3.8.10.42: full-width single-column demo rows + optional frontend demo protection. */
function wpbb_child_381042_repair_single_columns( $blocks ) {
    foreach ( $blocks as &$block ) {
        if ( 'wpbb/row' === ( $block['blockName'] ?? '' ) && ! empty( $block['innerBlocks'] ) ) {
            $column_indexes = array();
            foreach ( $block['innerBlocks'] as $index => $inner ) {
                if ( 'wpbb/column' === ( $inner['blockName'] ?? '' ) ) $column_indexes[] = $index;
            }
            if ( 1 === count( $column_indexes ) ) {
                $idx = $column_indexes[0];
                $attrs = $block['innerBlocks'][ $idx ]['attrs'] ?? array();
                if ( 12 === (int) ( $attrs['xs'] ?? 12 ) ) {
                    $attrs['xs'] = 12;
                    foreach ( array( 'sm', 'md', 'lg', 'xl', 'xxl' ) as $breakpoint ) unset( $attrs[ $breakpoint ] );
                    $block['innerBlocks'][ $idx ]['attrs'] = $attrs;
                }
            }
        }
        if ( ! empty( $block['innerBlocks'] ) ) $block['innerBlocks'] = wpbb_child_381042_repair_single_columns( $block['innerBlocks'] );
    }
    unset( $block );
    return $blocks;
}

function wpbb_child_381042_repair_demo_page_widths() {
    $pages = get_posts( array(
        'post_type' => 'page', 'post_status' => 'any', 'posts_per_page' => -1,
        'meta_key' => '_wp_theme_demo_managed', 'meta_value' => '1', 'fields' => 'ids',
    ) );
    foreach ( $pages as $page_id ) {
        $content = (string) get_post_field( 'post_content', $page_id );
        if ( false === strpos( $content, 'wpbb/column' ) ) continue;
        $blocks = parse_blocks( $content );
        $repaired = serialize_blocks( wpbb_child_381042_repair_single_columns( $blocks ) );
        if ( $repaired !== $content ) wp_update_post( array( 'ID' => $page_id, 'post_content' => $repaired ) );
    }
}
add_action( 'wp_theme_after_demo_import', 'wpbb_child_381042_repair_demo_page_widths', 140 );
function wpbb_child_381042_repair_demo_page_widths_once() {
    if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) return;
    $key = 'wpbb_381042_single_col_' . sanitize_key( get_stylesheet() );
    if ( get_option( $key ) ) return;
    wpbb_child_381042_repair_demo_page_widths();
    update_option( $key, 1, false );
}
add_action( 'admin_init', 'wpbb_child_381042_repair_demo_page_widths_once', 40 );

/**
 * v3.8.10.43: repair shared demo alignment and force one fresh media pass.
 *
 * The previous media migration was intentionally one-shot. This release uses a
 * new per-theme marker so sites that already ran v381041 receive the current
 * child-owned room/product/project/blog images as well.
 */
if ( ! function_exists( 'wpbb_child_381043_normalize_text' ) ) {
    function wpbb_child_381043_normalize_text( $value ) {
        $value = html_entity_decode( wp_strip_all_tags( (string) $value ), ENT_QUOTES | ENT_HTML5, get_bloginfo( 'charset' ) );
        return trim( preg_replace( '/\\s+/u', ' ', $value ) );
    }
}

if ( ! function_exists( 'wpbb_child_381043_dedupe_single_body' ) ) {
    function wpbb_child_381043_dedupe_single_body( $content, $excerpt = '' ) {
        $excerpt_text = wpbb_child_381043_normalize_text( $excerpt );
        if ( '' === $excerpt_text ) return $content;

        $content_text = wpbb_child_381043_normalize_text( $content );
        if ( $content_text === $excerpt_text ) return '';

        if ( preg_match( '~^\\s*<p(?:\\s[^>]*)?>(.*?)</p>~is', (string) $content, $match ) ) {
            if ( wpbb_child_381043_normalize_text( $match[1] ) === $excerpt_text ) {
                return ltrim( substr( (string) $content, strlen( $match[0] ) ) );
            }
        }
        return $content;
    }
}

if ( ! function_exists( 'wpbb_child_381043_repair_block_alignment' ) ) {
    function wpbb_child_381043_repair_block_alignment( $blocks ) {
        foreach ( $blocks as &$block ) {
            if ( 'wpbb/row' === ( $block['blockName'] ?? '' ) ) {
                $attrs = $block['attrs'] ?? array();
                $classes = preg_split( '/\\s+/', trim( (string) ( $attrs['customClasses'] ?? '' ) ) );
                $classes = array_values( array_filter( array_map( 'sanitize_html_class', $classes ) ) );
                if ( in_array( 'wp-theme-sector-media-text', $classes, true ) ) {
                    $classes = array_values( array_diff( $classes, array( 'align-items-center', 'align-items-end' ) ) );
                    if ( ! in_array( 'align-items-start', $classes, true ) ) $classes[] = 'align-items-start';
                    $attrs['customClasses'] = implode( ' ', $classes );
                    $block['attrs'] = $attrs;
                }
            }
            if ( ! empty( $block['innerBlocks'] ) ) {
                $block['innerBlocks'] = wpbb_child_381043_repair_block_alignment( $block['innerBlocks'] );
            }
        }
        unset( $block );
        return $blocks;
    }
}

if ( ! function_exists( 'wpbb_child_381043_repair_demo_pages' ) ) {
    function wpbb_child_381043_repair_demo_pages() {
        // Repair every page that actually contains the theme's media/text row.
        // This also covers front pages imported before the managed-page marker existed.
        $page_ids = get_posts( array(
            'post_type' => 'page',
            'post_status' => 'any',
            'posts_per_page' => -1,
            'fields' => 'ids',
        ) );
        foreach ( $page_ids as $page_id ) {
            $content = (string) get_post_field( 'post_content', $page_id );
            if ( false === strpos( $content, 'wp-theme-sector-media-text' ) ) continue;
            $repaired = serialize_blocks( wpbb_child_381043_repair_block_alignment( parse_blocks( $content ) ) );
            if ( $repaired !== $content ) {
                wp_update_post( array( 'ID' => $page_id, 'post_content' => $repaired ) );
                clean_post_cache( $page_id );
            }
        }
    }
}

if ( ! function_exists( 'wpbb_child_381043_refresh_media_once' ) ) {
    function wpbb_child_381043_refresh_media_once( $page_id = 0, $profile = array() ) {
        if ( ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) || ( function_exists( 'wp_doing_cron' ) && wp_doing_cron() ) ) return;
        if ( ! current_user_can( 'manage_options' ) ) return;

        $current_stylesheet = sanitize_key( get_stylesheet() );
        $done_key = 'wpbb_child_381043_media_' . $current_stylesheet;
        $owner_key = 'wpbb_child_381043_media_owner';
        // Demo posts are shared while child themes are switched. Refresh again
        // whenever a different child theme last supplied the active media.
        if ( get_option( $done_key ) && $current_stylesheet === (string) get_option( $owner_key ) ) return;

        $defined = get_defined_functions();
        foreach ( (array) ( $defined['user'] ?? array() ) as $function_name ) {
            if ( ! preg_match( '/^wpbb_[a-z0-9_]+_realistic_media_upgrade_v381041$/', $function_name ) ) continue;
            delete_option( $function_name );
            call_user_func( $function_name );
        }

        // Correct stale titles/alt text left behind when the same demo posts were
        // reused while switching child themes.
        $post_ids = get_posts( array(
            'post_type' => 'any',
            'post_status' => 'any',
            'posts_per_page' => -1,
            'meta_key' => '_thumbnail_id',
            'fields' => 'ids',
        ) );
        foreach ( $post_ids as $post_id ) {
            $thumbnail_id = (int) get_post_thumbnail_id( $post_id );
            if ( ! $thumbnail_id ) continue;
            $attached = (string) get_post_meta( $thumbnail_id, '_wp_attached_file', true );
            $attachment_name = (string) get_post_field( 'post_name', $thumbnail_id );
            if ( false === strpos( $attached, '-blog/' ) && 0 !== strpos( $attachment_name, 'wpbb-' ) ) continue;
            $title = get_the_title( $post_id );
            if ( '' === trim( (string) $title ) ) continue;
            wp_update_post( array( 'ID' => $thumbnail_id, 'post_title' => $title ) );
            update_post_meta( $thumbnail_id, '_wp_attachment_image_alt', $title );
            clean_post_cache( $post_id );
            clean_attachment_cache( $thumbnail_id );
        }

        wpbb_child_381043_repair_demo_pages();
        update_option( $done_key, current_time( 'mysql' ), false );
        update_option( $owner_key, $current_stylesheet, false );
    }
}
add_action( 'wp_theme_after_demo_import', 'wpbb_child_381043_refresh_media_once', 180, 2 );
add_action( 'admin_init', 'wpbb_child_381043_refresh_media_once', 130 );

/**
 * v3.8.10.45: shared rhythm, contrast, sector-media and gallery repair.
 */
require_once __DIR__ . '/inc/sector-consistency.php';

// v3.8.10.64 shared BBuilder/demo consistency layer.
require_once get_stylesheet_directory() . '/inc/bbuilder-system-v62.php';

/**
 * v3.8.10.64 PWA endpoint hardening.
 *
 * The parent theme links to ?wpbb-pwa=manifest and registers
 * ?wpbb-pwa=service-worker. Serve those endpoints before the normal template
 * loader so browsers always receive the expected MIME type and valid payload.
 * The service worker intentionally has no fetch handler: this prevents stale
 * worker-cached ES modules from causing Chromium cross-world preload warnings.
 */
if ( ! function_exists( 'wpbb_child_381063_serve_pwa_endpoint' ) ) {
    function wpbb_child_381063_serve_pwa_endpoint() {
        if ( empty( $_GET['wpbb-pwa'] ) ) return;
        $mode = sanitize_key( wp_unslash( $_GET['wpbb-pwa'] ) );
        if ( ! in_array( $mode, array( 'manifest', 'service-worker' ), true ) ) return;

        while ( ob_get_level() ) {
            @ob_end_clean();
        }
        nocache_headers();
        header( 'X-Content-Type-Options: nosniff' );

        if ( 'manifest' === $mode ) {
            header( 'Content-Type: application/manifest+json; charset=UTF-8' );
            $name = trim( (string) get_bloginfo( 'name' ) );
            if ( '' === $name ) $name = 'WP Base';
            $scope = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
            if ( '' === $scope ) $scope = '/';
            $icons = array();
            foreach ( array( 192, 512 ) as $size ) {
                $file = get_stylesheet_directory() . '/assets/icons/icon-' . $size . '.png';
                if ( is_readable( $file ) ) {
                    $icons[] = array(
                        'src' => get_stylesheet_directory_uri() . '/assets/icons/icon-' . $size . '.png',
                        'sizes' => $size . 'x' . $size,
                        'type' => 'image/png',
                        'purpose' => 'any maskable',
                    );
                }
            }
            echo wp_json_encode( array(
                'name' => $name,
                'short_name' => function_exists( 'mb_substr' ) ? mb_substr( $name, 0, 24 ) : substr( $name, 0, 24 ),
                'start_url' => home_url( '/' ),
                'scope' => $scope,
                'display' => 'standalone',
                'background_color' => '#ffffff',
                'theme_color' => '#3155D9',
                'icons' => $icons,
            ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
            exit;
        }

        header( 'Content-Type: application/javascript; charset=UTF-8' );
        header( 'Service-Worker-Allowed: /' );
        echo "self.addEventListener('install',function(event){self.skipWaiting();});\n";
        echo "self.addEventListener('activate',function(event){event.waitUntil((async function(){try{var keys=await caches.keys();await Promise.all(keys.filter(function(k){return /^(wpbb|wp-theme|wpbase)/i.test(k);}).map(function(k){return caches.delete(k);}));}catch(e){}await self.clients.claim();})());});\n";
        exit;
    }
    add_action( 'template_redirect', 'wpbb_child_381063_serve_pwa_endpoint', -9999 );
}
// v3.8.10.75 structural/media/Woo repair.
require_once get_stylesheet_directory() . '/inc/v75-suite.php';

// v3.8.10.81: keep interactive wp-admin saves/updates fast.
require_once get_stylesheet_directory() . '/inc/v82-suite.php';
require_once get_stylesheet_directory() . '/inc/admin-performance.php';

// v3.8.10.83 premium Jobs-aligned sector presentation and multilingual managed-demo refresh.
require_once get_stylesheet_directory() . '/inc/v83-premium-suite.php';
// v3.8.10.97 final premium Jobs-aligned suite and mobile navigation.
require_once get_stylesheet_directory() . '/inc/v97-premium-suite.php';


/** 3.8.10.98 suite-wide layout/mobile finishing layer. */
function wpbb_suite_v98_enqueue(){
    $v = wp_get_theme()->get('Version');
    wp_enqueue_style('wpbb-suite-v98', get_stylesheet_directory_uri() . '/assets/suite-v98.css', array(), $v);
    wp_enqueue_script('wpbb-suite-v98', get_stylesheet_directory_uri() . '/assets/suite-v98.js', array(), $v, true);
}
add_action('wp_enqueue_scripts','wpbb_suite_v98_enqueue',999);

// v3.8.10.99 final suite-wide grid, branding, hero and mobile finish.
require_once get_stylesheet_directory() . '/inc/v99-finish.php';

// v3.8.11.00 cookie ownership, hero/colour and mobile navigation finish.
require_once get_stylesheet_directory() . '/inc/v100-finish.php';

// v3.8.11.02 navigation, legal, colour and media correction.
require_once get_stylesheet_directory() . '/inc/v101-finish.php';

// v3.8.11.04 deterministic mobile navigation and WooCommerce/alignment finish.
require_once get_stylesheet_directory() . '/inc/v104-finish.php';

// v3.8.11.05 legal/contact grid, mobile drawer and WooCommerce template finish.
require_once get_stylesheet_directory() . '/inc/v105-finish.php';

// v3.8.11.07 final search, WooCommerce, Jobs captcha/grid and responsive repair.
require_once get_stylesheet_directory() . '/inc/v107-finish.php';

// v3.8.11.08 WooCommerce layout/polish and packaging finish.
require_once get_stylesheet_directory() . '/inc/v108-finish.php';

// v3.8.11.09 WooCommerce, media and account finalisation.
require_once get_stylesheet_directory() . '/inc/v109-finish.php';

// v3.8.11.10 media, WooCommerce, managed-page and route-facing finish.
require_once get_stylesheet_directory() . '/inc/v110-finish.php';

// v3.8.11.11 hero finder, editorial grid, mega-menu and image-quality finish.
require_once get_stylesheet_directory() . '/inc/v111-finish.php';

// v3.8.11.12 editorial grid, hero clarity and media recovery.
require_once get_stylesheet_directory() . '/inc/v112-finish.php';

// v3.8.11.13 final hero edge/clarity and editorial-grid alignment.
require_once get_stylesheet_directory() . '/inc/v113-finish.php';

// v3.8.11.14 child-only settings, editor, legal, editorial and hero finish.
require_once get_stylesheet_directory() . '/inc/v114-finish.php';

// v3.8.11.15 final mega-menu, hero/media, quote and BBuilder repair.
require_once get_stylesheet_directory() . '/inc/v115-finish.php';

// v3.8.11.16 reset-safe layout/media, mega-menu, consent and BBuilder finish.
require_once get_stylesheet_directory() . '/inc/v116-finish.php';

// v3.8.11.17 exact mega-menu placement, reset-safe BBuilder grid and immediate media recovery.
require_once get_stylesheet_directory() . '/inc/v117-finish.php';


// v3.8.11.18 reset-safe gutters, direct hero assets, nav-trigger mega positioning and cache finish.
require_once get_stylesheet_directory() . '/inc/v118-finish.php';

// v3.8.11.19 live regression repair: closer mega menus, canonical gutters/grids and no-flash consent.
require_once get_stylesheet_directory() . '/inc/v119-finish.php';

// v3.8.11.20 stable v119 rollback, restored gutters/grids and deterministic hero pagination/quality repair.
require_once get_stylesheet_directory() . '/inc/v120-finish.php';

// v3.8.11.21 scoped BBuilder grid recovery; retire v119/v120 global geometry while preserving hero quality/pagination.
require_once get_stylesheet_directory() . '/inc/v121-finish.php';

// v3.8.11.22 component-only grid-gap finish; keep v121 alignment and restore stable card/media/stat spacing.
require_once get_stylesheet_directory() . '/inc/v122-finish.php';

// v3.8.11.23 remaining basic grids/gaps + authoritative hero source/pagination finish.
require_once get_stylesheet_directory() . '/inc/v123-finish.php';

// v3.8.11.24 final basic visual hardening: deterministic card gaps, full-width fun-facts and one compact hero pager.
require_once get_stylesheet_directory() . '/inc/v124-finish.php';

// v3.8.11.25 final scoped grid, hero clarity and WooCommerce shop/cart/account finish.
require_once get_stylesheet_directory() . '/inc/v125-final.php';

// v3.8.11.26 final cross-theme component grids, hero image/pagination and process-card recovery.
require_once get_stylesheet_directory() . '/inc/v126-final.php';

// v3.8.11.27 final live-regression hardening: robust card grids, process-card shape, hero pagination and Business/Building hero fade.
require_once get_stylesheet_directory() . '/inc/v127-final.php';

// v3.8.11.28 final live component recovery: commerce grids, cart/checkout, process cards and stable hero media/pagination.
require_once get_stylesheet_directory() . '/inc/v128-final.php';

// v3.8.11.34 final cross-theme hero, grid, process and WooCommerce ownership layer.
require_once get_stylesheet_directory() . '/inc/v134-final.php';


// v3.8.11.35 final duplicate/process/hero cleanup.
require_once get_stylesheet_directory() . '/inc/v135-final.php';

// v3.8.11.36 full-width hero, stable process and cross-theme grid ownership.
require_once get_stylesheet_directory() . '/inc/v136-final.php';

// v3.8.11.37 wide section grid and tighter homepage rhythm.
require_once get_stylesheet_directory() . '/inc/v137-final.php';

// v3.8.11.38 canonical 1320px section grid and refreshed high-quality hero assets.
require_once get_stylesheet_directory() . '/inc/v138-final.php';

// v3.8.11.39 one canonical header-to-footer grid + refreshed hero assets.
require_once get_stylesheet_directory() . '/inc/v139-final.php';

// v3.8.11.40 exact header-grid alignment + sector-specific placeholder proof content.
require_once get_stylesheet_directory() . '/inc/v140-final.php';

// v3.8.11.41 targeted grid repair + server-side sector proof card content.
require_once get_stylesheet_directory() . '/inc/v141-final.php';
