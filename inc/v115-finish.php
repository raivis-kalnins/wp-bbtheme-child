<?php
/** WP BBTheme child suite 3.8.11.15 - final mega menu, hero/media, quote and BBuilder repair. */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v115_hero_relatives' ) ) {
    function wpbb_child_v115_hero_relatives() {
        $map = array(
            'wp-bbtheme-child-automotive'       => array( 'assets/img/demo/hero-hq.jpg', 'assets/img/demo/item-1.jpg', 'assets/img/demo/item-4.jpg' ),
            'wp-bbtheme-child-building-services'=> array( 'assets/img/demo/hero-hq.jpg', 'assets/img/demo/hero-photo.jpg', 'assets/img/demo/about-photo.jpg' ),
            'wp-bbtheme-child-business'         => array( 'assets/img/demo/office-wide-hq.jpg', 'assets/img/demo/office-studio.jpg', 'assets/img/demo/office-planning.jpg' ),
            'wp-bbtheme-child-elearning'        => array( 'assets/img/demo/hero-hq.jpg', 'assets/img/demo/item-1.jpg', 'assets/img/demo/item-3.jpg' ),
            'wp-bbtheme-child-hotel'            => array( 'assets/img/demo/hero-hq.jpg', 'assets/img/demo/hero-photo.jpg', 'assets/img/demo/item-2.jpg' ),
            'wp-bbtheme-child-insurance'        => array( 'assets/img/demo/hero-hq.jpg', 'assets/img/demo/item-2.jpg', 'assets/img/demo/item-5.jpg' ),
            'wp-bbtheme-child-jobs'             => array( 'assets/img/demo/jobs-hero-hq.jpg', 'assets/img/demo/recruitment-team.jpg', 'assets/img/demo/candidate-opportunity.jpg' ),
            'wp-bbtheme-child-logistics'        => array( 'assets/img/demo/hero-hq.jpg', 'assets/img/demo/item-1.jpg', 'assets/img/demo/item-4.jpg' ),
            'wp-bbtheme-child-medicine'         => array( 'assets/img/medical-photos/hero-health.jpg', 'assets/img/medical-photos/care-1.jpg', 'assets/img/medical-photos/care-4.jpg' ),
            'wp-bbtheme-child-realestate'       => array( 'assets/img/demo/hero-hq.jpg', 'assets/img/properties/oak-residence.jpg', 'assets/img/properties/riverside-loft.jpg' ),
            'wp-bbtheme-child-restaurant'       => array( 'assets/img/demo/hero-hq.jpg', 'assets/img/demo/hero-photo.jpg', 'assets/img/demo/item-3.jpg' ),
            'wp-bbtheme-child-travel'           => array( 'assets/img/demo/hero-hq.jpg', 'assets/img/demo/hero-photo.jpg', 'assets/img/demo/item-4.jpg' ),
            'wp-bbtheme-child-woo-clouthes'     => array( 'assets/img/demo/hero-hq.jpg', 'assets/img/products/relaxed-cotton-shirt.jpg', 'assets/img/products/lightweight-parka.jpg' ),
            'wp-bbtheme-child-woo-events'       => array( 'assets/img/events/hero-calendar-hall.jpg', 'assets/img/events/conference-summit.jpg', 'assets/img/events/live-music.jpg' ),
            'wp-bbtheme-child-woo-tech-shop'    => array( 'assets/img/store/tech-hero.jpg', 'assets/img/store/tech-workspace.jpg', 'assets/img/store/studio-monitor.jpg' ),
        );
        $list = $map[ get_stylesheet() ] ?? array( 'assets/img/demo/hero-hq.jpg', 'assets/img/demo/item-1.jpg', 'assets/img/demo/item-2.jpg' );
        return array_values( array_filter( $list, static function( $rel ) { return is_readable( get_stylesheet_directory() . '/' . ltrim( $rel, '/' ) ); } ) );
    }
}

if ( ! function_exists( 'wpbb_child_v115_asset_url' ) ) {
    function wpbb_child_v115_asset_url( $relative, $title = '' ) {
        $relative = ltrim( (string) $relative, '/' );
        if ( '' === $relative || ! is_readable( get_stylesheet_directory() . '/' . $relative ) ) return '';
        if ( ( is_admin() || doing_action( 'wp_theme_after_demo_import' ) ) && function_exists( 'wpbb_child_v110_attachment_url' ) ) {
            $url = (string) wpbb_child_v110_attachment_url( $relative, $title ?: wp_get_theme()->get( 'Name' ) . ' demo image' );
            if ( $url ) return $url;
        }
        return get_stylesheet_directory_uri() . '/' . $relative;
    }
}

if ( ! function_exists( 'wpbb_child_v115_demo_profile' ) ) {
    function wpbb_child_v115_demo_profile( $profile ) {
        if ( ! is_array( $profile ) ) $profile = array();
        $rels = wpbb_child_v115_hero_relatives();
        if ( ! $rels ) return $profile;
        $urls = array();
        foreach ( $rels as $i => $rel ) {
            $url = wpbb_child_v115_asset_url( $rel, (string) ( $profile['name'] ?? wp_get_theme()->get( 'Name' ) ) . ' hero ' . ( $i + 1 ) );
            if ( $url ) $urls[] = $url;
        }
        if ( ! $urls ) return $profile;
        $profile['hero_image'] = $urls[0];
        if ( ! empty( $profile['hero_slides'] ) && is_array( $profile['hero_slides'] ) ) {
            foreach ( $profile['hero_slides'] as $i => $slide ) {
                if ( ! is_array( $slide ) ) continue;
                $profile['hero_slides'][ $i ]['image'] = $urls[ $i % count( $urls ) ];
            }
        }
        return $profile;
    }
    add_filter( 'wp_theme_demo_profile', 'wpbb_child_v115_demo_profile', PHP_INT_MAX );
}

if ( ! function_exists( 'wpbb_child_v115_normalize_partner_markup' ) ) {
    function wpbb_child_v115_normalize_partner_markup( $content ) {
        if ( ! is_string( $content ) || '' === $content ) return $content;
        $content = str_replace( '<!-- wp:wpbb/row --></div><!-- /wp:group -->', '<!-- /wp:wpbb/row --></div><!-- /wp:group -->', $content );
        /* Collapse accidental nested paragraph wrappers created by repeated legacy repair passes. */
        $content = (string) preg_replace(
            '~(?:<!--\s*wp:paragraph\s+\{"className":"wp-theme-partners-heading"\}\s*-->\s*)+(<p class="wp-theme-partners-heading">.*?</p>)(?:\s*<!--\s*/wp:paragraph\s*-->)+~s',
            '<!-- wp:paragraph {"className":"wp-theme-partners-heading"} -->$1<!-- /wp:paragraph -->',
            $content
        );
        /* Only wrap the known raw partner heading when it is a direct child of wpbb/column. */
        $content = (string) preg_replace_callback(
            '~(<!--\s*wp:wpbb/column\b[^>]*-->\s*)(?!<!--\s*wp:paragraph\b)(<p class="wp-theme-partners-heading">.*?</p>)~s',
            static function( $m ) { return $m[1] . '<!-- wp:paragraph {"className":"wp-theme-partners-heading"} -->' . $m[2] . '<!-- /wp:paragraph -->'; },
            $content
        );
        return $content;
    }
}

if ( ! function_exists( 'wpbb_child_v115_filter_post_data' ) ) {
    function wpbb_child_v115_filter_post_data( $data, $postarr ) {
        if ( in_array( (string) ( $data['post_type'] ?? '' ), array( 'page', 'post' ), true ) && ! empty( $data['post_content'] ) ) {
            $data['post_content'] = wpbb_child_v115_normalize_partner_markup( $data['post_content'] );
        }
        return $data;
    }
    add_filter( 'wp_insert_post_data', 'wpbb_child_v115_filter_post_data', 9999, 2 );
}

if ( ! function_exists( 'wpbb_child_v115_repair_hero_content' ) ) {
    function wpbb_child_v115_repair_hero_content( $content, $urls ) {
        if ( ! is_string( $content ) || '' === $content || false === strpos( $content, 'wpbb/swiper' ) || ! $urls ) return $content;
        return (string) preg_replace_callback(
            '~<!--\s+wp:wpbb/swiper\s+(\{.*?\})\s+/-->~s',
            static function( $m ) use ( $urls ) {
                $a = json_decode( $m[1], true );
                if ( ! is_array( $a ) || 'hero' !== sanitize_key( (string) ( $a['demoStyle'] ?? '' ) ) ) return $m[0];
                $a['autoplay'] = true;
                $a['autoplayDelay'] = 8500;
                $a['pauseOnHover'] = true;
                $a['showPagination'] = true;
                $a['rewind'] = true;
                $a['speed'] = max( 650, min( 1100, absint( $a['speed'] ?? 700 ) ) );
                if ( ! empty( $a['slides'] ) && is_array( $a['slides'] ) ) {
                    foreach ( $a['slides'] as $i => $slide ) if ( is_array( $slide ) ) $a['slides'][ $i ]['image'] = $urls[ $i % count( $urls ) ];
                }
                if ( ! empty( $a['slidesJson'] ) && is_string( $a['slidesJson'] ) ) {
                    $slides = json_decode( $a['slidesJson'], true );
                    if ( is_array( $slides ) ) {
                        foreach ( $slides as $i => $slide ) if ( is_array( $slide ) ) $slides[ $i ]['image'] = $urls[ $i % count( $urls ) ];
                        $a['slidesJson'] = wp_json_encode( $slides, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
                    }
                }
                return '<!-- wp:wpbb/swiper ' . wp_json_encode( $a, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ' /-->';
            },
            $content
        );
    }
}

if ( ! function_exists( 'wpbb_child_v115_repair_managed_content' ) ) {
    function wpbb_child_v115_repair_managed_content() {
        $rels = wpbb_child_v115_hero_relatives();
        $urls = array();
        foreach ( $rels as $i => $rel ) {
            $url = wpbb_child_v115_asset_url( $rel, wp_get_theme()->get( 'Name' ) . ' hero ' . ( $i + 1 ) );
            if ( $url ) $urls[] = $url;
        }
        $ids = get_posts( array( 'post_type'=>array('page','post'), 'post_status'=>array('publish','draft','pending','private','future'), 'posts_per_page'=>-1, 'fields'=>'ids', 'no_found_rows'=>true ) );
        foreach ( $ids as $id ) {
            $old = (string) get_post_field( 'post_content', $id, 'raw' );
            if ( '' === $old ) continue;
            $new = wpbb_child_v115_normalize_partner_markup( $old );
            if ( 'page' === get_post_type( $id ) ) $new = wpbb_child_v115_repair_hero_content( $new, $urls );
            if ( $new !== $old ) {
                wp_update_post( wp_slash( array( 'ID'=>$id, 'post_content'=>$new ) ) );
                clean_post_cache( $id );
            }
        }
    }
}

if ( ! function_exists( 'wpbb_child_v115_enqueue' ) ) {
    function wpbb_child_v115_enqueue() {
        $v = wp_get_theme()->get( 'Version' );
        wp_enqueue_style( 'wpbb-suite-v115', get_stylesheet_directory_uri() . '/assets/suite-v115.css', array( 'wpbb-suite-v114' ), $v );
        wp_enqueue_script( 'wpbb-suite-v115', get_stylesheet_directory_uri() . '/assets/suite-v115.js', array(), $v, true );
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v115_enqueue', PHP_INT_MAX );
}

if ( ! function_exists( 'wpbb_child_v115_repair_once' ) ) {
    function wpbb_child_v115_repair_once() {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) || wp_doing_ajax() ) return;
        $key = 'wpbb_child_v115_repair_' . sanitize_key( get_stylesheet() );
        if ( '3.8.11.15' === (string) get_option( $key ) ) return;
        wpbb_child_v115_repair_managed_content();
        if ( function_exists( 'wpbb_child_v112_repair_media' ) ) wpbb_child_v112_repair_media();
        /* Force the Woo demo-product thumbnail pass once for this release so cloned sites with broken upload files recover from bundled sources. */
        if ( function_exists( 'wpbb_child_v75_refresh_woo_demo_products' ) && post_type_exists( 'product' ) ) {
            delete_option( 'wpbb_child_v75_products_' . sanitize_key( get_stylesheet() ) );
            wpbb_child_v75_refresh_woo_demo_products();
        }
        if ( function_exists( 'wpbb_child_v114_translations_disabled' ) && wpbb_child_v114_translations_disabled() && function_exists( 'wpbb_child_v114_trash_non_english_content' ) ) {
            wpbb_child_v114_trash_non_english_content();
        }
        update_option( $key, '3.8.11.15', false );
    }
    add_action( 'admin_init', 'wpbb_child_v115_repair_once', 101100 );
    add_action( 'wp_theme_after_demo_import', 'wpbb_child_v115_repair_once', 101100 );
}
