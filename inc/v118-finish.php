<?php
/** WP BBTheme child suite 3.8.11.18 - reset-safe grid gutters, direct hero assets, trigger-anchored mega-menu and cache finish. */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v118_disable_superseded_runtime' ) ) {
    function wpbb_child_v118_disable_superseded_runtime() {
        // The old 3.8.10.42 migration damaged legitimate responsive widths after reset.
        remove_action( 'admin_init', 'wpbb_child_381042_repair_demo_page_widths_once', 40 );
        remove_action( 'wp_theme_after_demo_import', 'wpbb_child_381042_repair_demo_page_widths', 140 );

        // v118 replaces the broad v117 fallback and the v116/v117 menu scripts.
        remove_action( 'wp_theme_after_demo_import', 'wpbb_child_v117_after_demo_import', PHP_INT_MAX );
        remove_action( 'wp_theme_after_demo_reset', 'wpbb_child_v117_after_demo_import', PHP_INT_MAX );
        remove_action( 'wp_theme_demo_reset_complete', 'wpbb_child_v117_after_demo_import', PHP_INT_MAX );
        remove_action( 'wp_theme_starter_setup_complete', 'wpbb_child_v117_after_demo_import', PHP_INT_MAX );
        remove_filter( 'wp_insert_post_data', 'wpbb_child_v117_filter_post_data', PHP_INT_MAX );
        remove_filter( 'wp_theme_demo_profile', 'wpbb_child_v117_demo_profile', PHP_INT_MAX );
    }
}
wpbb_child_v118_disable_superseded_runtime();
add_action( 'after_setup_theme', 'wpbb_child_v118_disable_superseded_runtime', PHP_INT_MAX );
add_action( 'init', 'wpbb_child_v118_disable_superseded_runtime', -9999 );

if ( ! function_exists( 'wpbb_child_v118_hero_relatives' ) ) {
    function wpbb_child_v118_hero_relatives() {
        $files = array(
            'assets/img/hero-v118/slide-1.jpg',
            'assets/img/hero-v118/slide-2.jpg',
            'assets/img/hero-v118/slide-3.jpg',
        );
        return array_values( array_filter( $files, static function( $relative ) {
            return is_readable( get_stylesheet_directory() . '/' . $relative );
        } ) );
    }
}

if ( ! function_exists( 'wpbb_child_v118_asset_url' ) ) {
    function wpbb_child_v118_asset_url( $relative ) {
        $relative = ltrim( str_replace( '\\', '/', (string) $relative ), '/' );
        if ( '' === $relative || false !== strpos( $relative, '../' ) ) return '';
        $path = get_stylesheet_directory() . '/' . $relative;
        if ( ! is_readable( $path ) ) return '';
        // New path + filemtime query defeats stale child/CDN/browser copies.
        return add_query_arg( 'v', (string) filemtime( $path ), trailingslashit( get_stylesheet_directory_uri() ) . $relative );
    }
}

if ( ! function_exists( 'wpbb_child_v118_hero_urls' ) ) {
    function wpbb_child_v118_hero_urls() {
        $urls = array();
        foreach ( wpbb_child_v118_hero_relatives() as $relative ) {
            $url = wpbb_child_v118_asset_url( $relative );
            if ( $url ) $urls[] = $url;
        }
        return array_values( array_unique( $urls ) );
    }
}

if ( ! function_exists( 'wpbb_child_v118_normalize_hero_attrs' ) ) {
    function wpbb_child_v118_normalize_hero_attrs( $attrs ) {
        if ( ! is_array( $attrs ) || 'hero' !== sanitize_key( (string) ( $attrs['demoStyle'] ?? '' ) ) ) return $attrs;
        $urls = wpbb_child_v118_hero_urls();
        if ( ! $urls ) return $attrs;
        $attrs['autoplay'] = true;
        $attrs['autoplayDelay'] = 8500;
        $attrs['pauseOnHover'] = true;
        $attrs['showPagination'] = true;
        $attrs['loop'] = true;
        $attrs['rewind'] = true;
        $attrs['speed'] = max( 750, min( 1100, absint( $attrs['speed'] ?? 850 ) ) );
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
        return $attrs;
    }
}

if ( ! function_exists( 'wpbb_child_v118_demo_profile' ) ) {
    function wpbb_child_v118_demo_profile( $profile ) {
        if ( ! is_array( $profile ) ) $profile = array();
        $urls = wpbb_child_v118_hero_urls();
        if ( ! $urls ) return $profile;
        $profile['hero_image'] = $urls[0];
        if ( ! empty( $profile['hero_slides'] ) && is_array( $profile['hero_slides'] ) ) {
            foreach ( $profile['hero_slides'] as $index => $slide ) {
                if ( is_array( $slide ) ) $profile['hero_slides'][ $index ]['image'] = $urls[ $index % count( $urls ) ];
            }
        }
        return $profile;
    }
    add_filter( 'wp_theme_demo_profile', 'wpbb_child_v118_demo_profile', PHP_INT_MAX );
}

if ( ! function_exists( 'wpbb_child_v118_render_block_data' ) ) {
    /** Runtime guarantee: a stale DB attachment/thumbnail can never replace the v118 hero. */
    function wpbb_child_v118_render_block_data( $parsed_block, $source_block, $parent_block = null ) {
        if ( is_array( $parsed_block ) && 'wpbb/swiper' === (string) ( $parsed_block['blockName'] ?? '' ) ) {
            $parsed_block['attrs'] = wpbb_child_v118_normalize_hero_attrs( (array) ( $parsed_block['attrs'] ?? array() ) );
        }
        return $parsed_block;
    }
    add_filter( 'render_block_data', 'wpbb_child_v118_render_block_data', PHP_INT_MAX, 3 );
}

if ( ! function_exists( 'wpbb_child_v118_repair_swipers' ) ) {
    function wpbb_child_v118_repair_swipers( $content ) {
        if ( ! is_string( $content ) || false === strpos( $content, 'wpbb/swiper' ) ) return $content;
        return (string) preg_replace_callback(
            '~<!--\\s+wp:wpbb/swiper\\s+(\\{.*?\\})\\s+/-->~s',
            static function( $match ) {
                $attrs = json_decode( $match[1], true );
                if ( ! is_array( $attrs ) ) return $match[0];
                $attrs = wpbb_child_v118_normalize_hero_attrs( $attrs );
                return '<!-- wp:wpbb/swiper ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ' /-->';
            },
            $content
        );
    }
}

if ( ! function_exists( 'wpbb_child_v118_repair_page_content' ) ) {
    function wpbb_child_v118_repair_page_content( $content, $page_id = 0 ) {
        if ( ! is_string( $content ) || '' === trim( $content ) ) return $content;
        // Keep all prior structural/media/partner repairs, then make v118 authoritative.
        if ( function_exists( 'wpbb_child_v117_repair_page_content' ) ) $content = wpbb_child_v117_repair_page_content( $content, $page_id );
        $content = wpbb_child_v118_repair_swipers( $content );
        if ( function_exists( 'wpbb_child_v116_normalize_partner_markup' ) ) $content = wpbb_child_v116_normalize_partner_markup( $content );
        if ( function_exists( 'wpbb_child_v72_repair_canonical_markup' ) ) $content = wpbb_child_v72_repair_canonical_markup( $content );
        return $content;
    }
}

if ( ! function_exists( 'wpbb_child_v118_repair_managed_pages' ) ) {
    function wpbb_child_v118_repair_managed_pages() {
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
            $managed = '1' === (string) get_post_meta( $page_id, '_wp_theme_demo_managed', true ) || ( $front_id && $front_id === (int) $page_id );
            if ( ! $managed || '' === trim( $content ) ) continue;
            if ( function_exists( 'wpbb_child_v71_is_source_language' ) && ! wpbb_child_v71_is_source_language( $page_id ) ) continue;
            $new = wpbb_child_v118_repair_page_content( $content, $page_id );
            if ( $new !== $content ) {
                wp_update_post( wp_slash( array( 'ID' => $page_id, 'post_content' => $new ) ) );
                clean_post_cache( $page_id );
            }
            update_post_meta( $page_id, '_wpbb_child_v118_repaired', '3.8.11.18' );
        }
    }
}

if ( ! function_exists( 'wpbb_child_v118_repair_all' ) ) {
    function wpbb_child_v118_repair_all() {
        wpbb_child_v118_disable_superseded_runtime();
        // Rebuild canonical BBuilder markup FIRST, so reset/import cannot keep damaged width attributes.
        if ( function_exists( 'wpbb_child_v62_rebuild_demo_pages' ) ) wpbb_child_v62_rebuild_demo_pages( true );
        wpbb_child_v118_repair_managed_pages();
        if ( function_exists( 'wpbb_child_v117_drain_media_worker' ) ) wpbb_child_v117_drain_media_worker();
        wpbb_child_v118_repair_managed_pages();
        if ( post_type_exists( 'product' ) && function_exists( 'wpbb_child_v75_refresh_woo_demo_products' ) ) {
            delete_option( 'wpbb_child_v75_products_' . sanitize_key( get_stylesheet() ) );
            wpbb_child_v75_refresh_woo_demo_products();
        }
        if ( function_exists( 'wpbb_child_v114_translations_disabled' ) && wpbb_child_v114_translations_disabled() && function_exists( 'wpbb_child_v114_trash_non_english_content' ) ) {
            wpbb_child_v114_trash_non_english_content();
        }
    }
}

if ( ! function_exists( 'wpbb_child_v118_repair_once' ) ) {
    function wpbb_child_v118_repair_once() {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) || wp_doing_ajax() ) return;
        $key = 'wpbb_child_v118_repair_' . sanitize_key( get_stylesheet() );
        if ( '3.8.11.18' === (string) get_option( $key ) ) return;
        wpbb_child_v118_repair_all();
        update_option( $key, '3.8.11.18', false );
    }
    add_action( 'admin_init', 'wpbb_child_v118_repair_once', PHP_INT_MAX );
}

if ( ! function_exists( 'wpbb_child_v118_after_demo_import' ) ) {
    function wpbb_child_v118_after_demo_import( $page_id = 0, $profile = array() ) {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) || wp_doing_ajax() ) return;
        wpbb_child_v118_repair_all();
        update_option( 'wpbb_child_v118_repair_' . sanitize_key( get_stylesheet() ), '3.8.11.18', false );
    }
    add_action( 'wp_theme_after_demo_import', 'wpbb_child_v118_after_demo_import', PHP_INT_MAX, 2 );
    add_action( 'wp_theme_after_demo_reset', 'wpbb_child_v118_after_demo_import', PHP_INT_MAX );
    add_action( 'wp_theme_demo_reset_complete', 'wpbb_child_v118_after_demo_import', PHP_INT_MAX );
    add_action( 'wp_theme_starter_setup_complete', 'wpbb_child_v118_after_demo_import', PHP_INT_MAX );
}

if ( ! function_exists( 'wpbb_child_v118_filter_post_data' ) ) {
    function wpbb_child_v118_filter_post_data( $data, $postarr ) {
        if ( in_array( (string) ( $data['post_type'] ?? '' ), array( 'page', 'post' ), true ) && ! empty( $data['post_content'] ) ) {
            $content = (string) $data['post_content'];
            if ( false !== strpos( $content, 'wpbb/' ) || false !== strpos( $content, 'wp-theme-' ) ) {
                $data['post_content'] = wpbb_child_v118_repair_page_content( $content, absint( $postarr['ID'] ?? 0 ) );
            }
        }
        return $data;
    }
    add_filter( 'wp_insert_post_data', 'wpbb_child_v118_filter_post_data', PHP_INT_MAX, 2 );
}

if ( ! function_exists( 'wpbb_child_v118_reversion_core_assets' ) ) {
    /** Replace the old hard-coded ?ver=3.8.10.75 theme-system-v62.css URL. */
    function wpbb_child_v118_reversion_core_assets() {
        $path = get_stylesheet_directory() . '/assets/theme-system-v62.css';
        if ( is_readable( $path ) ) {
            wp_dequeue_style( 'wpbb-child-system-v62' );
            wp_deregister_style( 'wpbb-child-system-v62' );
            wp_enqueue_style( 'wpbb-child-system-v62', get_stylesheet_directory_uri() . '/assets/theme-system-v62.css', array(), (string) filemtime( $path ) );
        }
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v118_reversion_core_assets', PHP_INT_MAX - 5 );
    add_action( 'enqueue_block_editor_assets', 'wpbb_child_v118_reversion_core_assets', PHP_INT_MAX - 5 );
}

if ( ! function_exists( 'wpbb_child_v118_enqueue' ) ) {
    function wpbb_child_v118_enqueue() {
        // v118 deliberately replaces the broad v116/v117 grid/menu layers.
        foreach ( array( 'wpbb-suite-v116', 'wpbb-suite-v117' ) as $handle ) {
            wp_dequeue_style( $handle );
            wp_deregister_style( $handle );
            wp_dequeue_script( $handle );
            wp_deregister_script( $handle );
        }
        $css = get_stylesheet_directory() . '/assets/suite-v118.css';
        $js  = get_stylesheet_directory() . '/assets/suite-v118.js';
        if ( is_readable( $css ) ) wp_enqueue_style( 'wpbb-suite-v118', get_stylesheet_directory_uri() . '/assets/suite-v118.css', array(), (string) filemtime( $css ) );
        if ( is_readable( $js ) ) wp_enqueue_script( 'wpbb-suite-v118', get_stylesheet_directory_uri() . '/assets/suite-v118.js', array(), (string) filemtime( $js ), false );
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v118_enqueue', PHP_INT_MAX );
}
