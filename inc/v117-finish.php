<?php
/** WP BBTheme child suite 3.8.11.17 - live-grid, hero, mega-menu and media recovery finish. */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v117_disable_legacy_grid_migrations' ) ) {
    function wpbb_child_v117_disable_legacy_grid_migrations() {
        remove_action( 'admin_init', 'wpbb_child_381042_repair_demo_page_widths_once', 40 );
        remove_action( 'wp_theme_after_demo_import', 'wpbb_child_381042_repair_demo_page_widths', 140 );
    }
}
wpbb_child_v117_disable_legacy_grid_migrations();
add_action( 'after_setup_theme', 'wpbb_child_v117_disable_legacy_grid_migrations', PHP_INT_MAX );
add_action( 'init', 'wpbb_child_v117_disable_legacy_grid_migrations', -9999 );

if ( ! function_exists( 'wpbb_child_v117_hero_relatives' ) ) {
    /**
     * Use the verified sector photo pools instead of the v116 generated hero triplet.
     * Some v116 triplets were visually near-identical and, on cloned demos, could
     * preserve the wrong sector image after a reset. These paths are child-owned.
     */
    function wpbb_child_v117_hero_relatives() {
        $map = array(
            'wp-bbtheme-child-automotive'        => array( 'assets/img/demo/hero-hq.jpg', 'assets/img/demo/item-1.jpg', 'assets/img/demo/item-4.jpg' ),
            'wp-bbtheme-child-building-services' => array( 'assets/img/demo/hero-hq.jpg', 'assets/img/demo/about.jpg', 'assets/img/demo/item-3.jpg' ),
            'wp-bbtheme-child-business'          => array( 'assets/img/demo/office-wide-hq.jpg', 'assets/img/demo/office-studio.jpg', 'assets/img/demo/office-planning.jpg' ),
            'wp-bbtheme-child-elearning'         => array( 'assets/img/demo/hero-hq.jpg', 'assets/img/demo/about.jpg', 'assets/img/demo/hero-photo.jpg' ),
            'wp-bbtheme-child-hotel'             => array( 'assets/img/demo/hero-hq.jpg', 'assets/img/demo/hero-photo.jpg', 'assets/img/demo/item-2.jpg' ),
            'wp-bbtheme-child-insurance'         => array( 'assets/img/demo/hero-hq.jpg', 'assets/img/demo/item-2.jpg', 'assets/img/demo/item-5.jpg' ),
            'wp-bbtheme-child-jobs'              => array( 'assets/img/demo/jobs-hero-hq.jpg', 'assets/img/demo/recruitment-team.jpg', 'assets/img/demo/candidate-opportunity.jpg' ),
            'wp-bbtheme-child-logistics'         => array( 'assets/img/demo/hero-hq.jpg', 'assets/img/demo/item-1.jpg', 'assets/img/demo/item-4.jpg' ),
            'wp-bbtheme-child-medicine'          => array( 'assets/img/medical-photos/hero-health.jpg', 'assets/img/medical-photos/care-1.jpg', 'assets/img/medical-photos/care-4.jpg' ),
            'wp-bbtheme-child-realestate'        => array( 'assets/img/demo/hero-hq.jpg', 'assets/img/site-previews/project-2.jpg', 'assets/img/blog/blog-3.jpg' ),
            'wp-bbtheme-child-restaurant'        => array( 'assets/img/demo/hero-hq.jpg', 'assets/img/demo/hero-photo.jpg', 'assets/img/demo/item-3.jpg' ),
            'wp-bbtheme-child-travel'            => array( 'assets/img/demo/hero-hq.jpg', 'assets/img/demo/hero-photo.jpg', 'assets/img/demo/item-4.jpg' ),
            'wp-bbtheme-child-woo-clouthes'      => array( 'assets/img/demo/hero-hq.jpg', 'assets/img/site-previews/project-2.jpg', 'assets/img/blog/blog-5.jpg' ),
            'wp-bbtheme-child-woo-events'        => array( 'assets/img/events/hero-calendar-hall.jpg', 'assets/img/events/conference-summit.jpg', 'assets/img/events/live-music.jpg' ),
            'wp-bbtheme-child-woo-tech-shop'     => array( 'assets/img/store/tech-hero.jpg', 'assets/img/store/tech-workspace.jpg', 'assets/img/store/studio-monitor.jpg' ),
        );
        $list = $map[ get_stylesheet() ] ?? array();
        $list = array_values( array_filter( $list, static function( $relative ) {
            $relative = ltrim( str_replace( '\\', '/', (string) $relative ), '/' );
            return '' !== $relative && false === strpos( $relative, '../' ) && is_readable( get_stylesheet_directory() . '/' . $relative );
        } ) );
        if ( ! $list && function_exists( 'wpbb_child_v115_hero_relatives' ) ) $list = wpbb_child_v115_hero_relatives();
        if ( ! $list && function_exists( 'wpbb_child_v116_hero_relatives' ) ) $list = wpbb_child_v116_hero_relatives();
        return array_values( array_unique( $list ) );
    }
}

if ( ! function_exists( 'wpbb_child_v117_asset_url' ) ) {
    function wpbb_child_v117_asset_url( $relative ) {
        $relative = ltrim( str_replace( '\\', '/', (string) $relative ), '/' );
        if ( '' === $relative || false !== strpos( $relative, '../' ) ) return '';
        return is_readable( get_stylesheet_directory() . '/' . $relative )
            ? trailingslashit( get_stylesheet_directory_uri() ) . $relative
            : '';
    }
}

if ( ! function_exists( 'wpbb_child_v117_hero_urls' ) ) {
    function wpbb_child_v117_hero_urls() {
        $urls = array();
        foreach ( wpbb_child_v117_hero_relatives() as $relative ) {
            $url = wpbb_child_v117_asset_url( $relative );
            if ( $url ) $urls[] = $url;
        }
        return array_values( array_unique( $urls ) );
    }
}

if ( ! function_exists( 'wpbb_child_v117_demo_profile' ) ) {
    function wpbb_child_v117_demo_profile( $profile ) {
        if ( ! is_array( $profile ) ) $profile = array();
        $urls = wpbb_child_v117_hero_urls();
        if ( ! $urls ) return $profile;
        $profile['hero_image'] = $urls[0];
        if ( ! empty( $profile['hero_slides'] ) && is_array( $profile['hero_slides'] ) ) {
            foreach ( $profile['hero_slides'] as $index => $slide ) {
                if ( is_array( $slide ) ) $profile['hero_slides'][ $index ]['image'] = $urls[ $index % count( $urls ) ];
            }
        }
        return $profile;
    }
    add_filter( 'wp_theme_demo_profile', 'wpbb_child_v117_demo_profile', PHP_INT_MAX );
}

if ( ! function_exists( 'wpbb_child_v117_repair_swipers' ) ) {
    function wpbb_child_v117_repair_swipers( $content ) {
        $urls = wpbb_child_v117_hero_urls();
        if ( ! $urls || ! is_string( $content ) || false === strpos( $content, 'wpbb/swiper' ) ) return $content;
        return (string) preg_replace_callback(
            '~<!--\s+wp:wpbb/swiper\s+(\{.*?\})\s+/-->~s',
            static function( $match ) use ( $urls ) {
                $attrs = json_decode( $match[1], true );
                if ( ! is_array( $attrs ) || 'hero' !== sanitize_key( (string) ( $attrs['demoStyle'] ?? '' ) ) ) return $match[0];
                $attrs['autoplay'] = true;
                $attrs['autoplayDelay'] = 8500;
                $attrs['pauseOnHover'] = true;
                $attrs['showPagination'] = true;
                $attrs['loop'] = true;
                $attrs['rewind'] = true;
                $attrs['speed'] = max( 700, min( 1100, absint( $attrs['speed'] ?? 800 ) ) );
                if ( ! empty( $attrs['slides'] ) && is_array( $attrs['slides'] ) ) {
                    foreach ( $attrs['slides'] as $index => $slide ) {
                        if ( is_array( $slide ) ) $attrs['slides'][ $index ]['image'] = $urls[ $index % count( $urls ) ];
                    }
                }
                if ( ! empty( $attrs['slidesJson'] ) && is_string( $attrs['slidesJson'] ) ) {
                    $slides = json_decode( $attrs['slidesJson'], true );
                    if ( is_array( $slides ) ) {
                        foreach ( $slides as $index => $slide ) {
                            if ( is_array( $slide ) ) $slides[ $index ]['image'] = $urls[ $index % count( $urls ) ];
                        }
                        $attrs['slidesJson'] = wp_json_encode( $slides, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
                    }
                }
                return '<!-- wp:wpbb/swiper ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ' /-->';
            },
            $content
        );
    }
}

if ( ! function_exists( 'wpbb_child_v117_repair_page_content' ) ) {
    function wpbb_child_v117_repair_page_content( $content, $page_id = 0 ) {
        if ( ! is_string( $content ) || '' === trim( $content ) ) return $content;
        if ( function_exists( 'wpbb_child_v116_repair_page_content' ) ) $content = wpbb_child_v116_repair_page_content( $content, $page_id );
        $content = wpbb_child_v117_repair_swipers( $content );
        if ( function_exists( 'wpbb_child_v116_normalize_partner_markup' ) ) $content = wpbb_child_v116_normalize_partner_markup( $content );
        if ( function_exists( 'wpbb_child_v72_repair_canonical_markup' ) ) $content = wpbb_child_v72_repair_canonical_markup( $content );
        return $content;
    }
}

if ( ! function_exists( 'wpbb_child_v117_repair_managed_pages' ) ) {
    function wpbb_child_v117_repair_managed_pages() {
        $ids = get_posts( array(
            'post_type' => 'page',
            'post_status' => array( 'publish', 'draft', 'pending', 'private', 'future' ),
            'posts_per_page' => -1,
            'fields' => 'ids',
            'no_found_rows' => true,
        ) );
        $front_id = absint( get_option( 'page_on_front' ) );
        foreach ( $ids as $page_id ) {
            $content = (string) get_post_field( 'post_content', $page_id, 'raw' );
            $managed = '1' === (string) get_post_meta( $page_id, '_wp_theme_demo_managed', true )
                || ( $front_id && $front_id === (int) $page_id );
            if ( ! $managed || '' === trim( $content ) ) continue;
            if ( function_exists( 'wpbb_child_v71_is_source_language' ) && ! wpbb_child_v71_is_source_language( $page_id ) ) continue;
            $new = wpbb_child_v117_repair_page_content( $content, $page_id );
            if ( $new !== $content ) {
                wp_update_post( wp_slash( array( 'ID' => $page_id, 'post_content' => $new ) ) );
                clean_post_cache( $page_id );
            }
            update_post_meta( $page_id, '_wpbb_child_v117_repaired', '3.8.11.17' );
        }
    }
}

if ( ! function_exists( 'wpbb_child_v117_drain_media_worker' ) ) {
    /** Run the resumable image repair immediately for an upgrade/reset, with a time guard. */
    function wpbb_child_v117_drain_media_worker() {
        if ( ! function_exists( 'wpbb_child_381045_consistency_config' ) || ! function_exists( 'wpbb_child_381046_schedule' ) ) return;
        wpbb_child_381046_schedule( true );
        if ( ! function_exists( 'wpbb_child_381046_consistency_batch' ) || ! function_exists( 'wpbb_child_381046_get_state' ) ) return;
        $config = wpbb_child_381045_consistency_config();
        if ( ! $config ) return;
        $start = microtime( true );
        for ( $i = 0; $i < 36; $i++ ) {
            $state = wpbb_child_381046_get_state( $config );
            if ( 'done' === (string) ( $state['stage'] ?? '' ) ) break;
            wpbb_child_381046_consistency_batch();
            if ( microtime( true ) - $start > 18 ) break;
        }
    }
}

if ( ! function_exists( 'wpbb_child_v117_repair_all' ) ) {
    function wpbb_child_v117_repair_all() {
        wpbb_child_v117_disable_legacy_grid_migrations();
        if ( function_exists( 'wpbb_child_v62_rebuild_demo_pages' ) ) wpbb_child_v62_rebuild_demo_pages( true );
        wpbb_child_v117_repair_managed_pages();
        wpbb_child_v117_drain_media_worker();
        // Re-apply page URLs after the media worker because the older worker can touch slider markup.
        wpbb_child_v117_repair_managed_pages();
        if ( post_type_exists( 'product' ) && function_exists( 'wpbb_child_v75_refresh_woo_demo_products' ) ) {
            delete_option( 'wpbb_child_v75_products_' . sanitize_key( get_stylesheet() ) );
            wpbb_child_v75_refresh_woo_demo_products();
        }
        if ( function_exists( 'wpbb_child_v114_translations_disabled' ) && wpbb_child_v114_translations_disabled() && function_exists( 'wpbb_child_v114_trash_non_english_content' ) ) {
            wpbb_child_v114_trash_non_english_content();
        }
    }
}

if ( ! function_exists( 'wpbb_child_v117_repair_once' ) ) {
    function wpbb_child_v117_repair_once() {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) || wp_doing_ajax() ) return;
        $key = 'wpbb_child_v117_repair_' . sanitize_key( get_stylesheet() );
        if ( '3.8.11.17' === (string) get_option( $key ) ) return;
        wpbb_child_v117_repair_all();
        update_option( $key, '3.8.11.17', false );
    }
    add_action( 'admin_init', 'wpbb_child_v117_repair_once', PHP_INT_MAX );
}

if ( ! function_exists( 'wpbb_child_v117_after_demo_import' ) ) {
    function wpbb_child_v117_after_demo_import( $page_id = 0, $profile = array() ) {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) || wp_doing_ajax() ) return;
        wpbb_child_v117_repair_all();
        update_option( 'wpbb_child_v117_repair_' . sanitize_key( get_stylesheet() ), '3.8.11.17', false );
    }
    add_action( 'wp_theme_after_demo_import', 'wpbb_child_v117_after_demo_import', PHP_INT_MAX, 2 );
    // Harmless compatibility aliases for reset/import implementations that expose a dedicated hook.
    add_action( 'wp_theme_after_demo_reset', 'wpbb_child_v117_after_demo_import', PHP_INT_MAX );
    add_action( 'wp_theme_demo_reset_complete', 'wpbb_child_v117_after_demo_import', PHP_INT_MAX );
    add_action( 'wp_theme_starter_setup_complete', 'wpbb_child_v117_after_demo_import', PHP_INT_MAX );
}

if ( ! function_exists( 'wpbb_child_v117_filter_post_data' ) ) {
    function wpbb_child_v117_filter_post_data( $data, $postarr ) {
        if ( in_array( (string) ( $data['post_type'] ?? '' ), array( 'page', 'post' ), true ) && ! empty( $data['post_content'] ) ) {
            $content = (string) $data['post_content'];
            // Keep this serializer guard scoped to BBuilder/theme-managed markup.
            // Editorial posts that do not use the suite's blocks/classes are untouched.
            if ( false !== strpos( $content, 'wpbb/' ) || false !== strpos( $content, 'wp-theme-' ) ) {
                $data['post_content'] = wpbb_child_v117_repair_page_content( $content, absint( $postarr['ID'] ?? 0 ) );
            }
        }
        return $data;
    }
    add_filter( 'wp_insert_post_data', 'wpbb_child_v117_filter_post_data', PHP_INT_MAX, 2 );
}

if ( ! function_exists( 'wpbb_child_v117_enqueue' ) ) {
    function wpbb_child_v117_enqueue() {
        $version = wp_get_theme()->get( 'Version' );
        // No dependency chain: this final layer must still load if an older cached handle was dropped.
        wp_enqueue_style( 'wpbb-suite-v117', get_stylesheet_directory_uri() . '/assets/suite-v117.css', array(), $version );
        wp_enqueue_script( 'wpbb-suite-v117', get_stylesheet_directory_uri() . '/assets/suite-v117.js', array(), $version, false );
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v117_enqueue', PHP_INT_MAX );
}
