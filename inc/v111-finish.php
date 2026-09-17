<?php
/** WP BBTheme child suite 3.8.11.11 — hero finder, editorial grid, mega-menu and image finish. */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v111_config' ) ) {
    function wpbb_child_v111_config() {
        $base = function_exists( 'wpbb_child_v110_config' ) ? wpbb_child_v110_config() : array();
        $accent = sanitize_hex_color( (string) ( $base['accent'] ?? '' ) ) ?: '#6F55E8';
        $hero = (string) ( $base['hero'] ?? 'assets/img/demo/hero-hq.jpg' );
        if ( 'wp-bbtheme-child-business' === get_stylesheet() && is_readable( get_stylesheet_directory() . '/assets/img/demo/business-hero-v111.jpg' ) ) {
            $hero = 'assets/img/demo/business-hero-v111.jpg';
        }
        return array( 'accent' => $accent, 'hero' => ltrim( $hero, '/' ) );
    }
}

if ( ! function_exists( 'wpbb_child_v111_enqueue' ) ) {
    function wpbb_child_v111_enqueue() {
        $version = wp_get_theme()->get( 'Version' );
        wp_enqueue_style( 'wpbb-suite-v111', get_stylesheet_directory_uri() . '/assets/suite-v111.css', array( 'wpbb-suite-v110' ), $version );
        wp_enqueue_script( 'wpbb-suite-v111', get_stylesheet_directory_uri() . '/assets/suite-v111.js', array(), $version, true );
        $cfg = wpbb_child_v111_config();
        $hero_file = get_stylesheet_directory() . '/' . $cfg['hero'];
        $hero_url = is_readable( $hero_file ) ? get_stylesheet_directory_uri() . '/' . $cfg['hero'] : '';
        $inline = ':root{--suite-v111-accent:' . $cfg['accent'] . ';}';
        if ( $hero_url ) $inline .= ':root{--suite-v111-hero:url("' . esc_url( $hero_url ) . '");}';
        wp_add_inline_style( 'wpbb-suite-v111', $inline );
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v111_enqueue', PHP_INT_MAX );
}

/* Keep future starter/demo imports on the same high-resolution hero source. */
if ( ! function_exists( 'wpbb_child_v111_demo_profile' ) ) {
    function wpbb_child_v111_demo_profile( $profile ) {
        if ( ! is_array( $profile ) ) $profile = array();
        $cfg = wpbb_child_v111_config();
        $relative = $cfg['hero'];
        $url = '';
        if ( function_exists( 'wpbb_child_v110_attachment_url' ) && ( is_admin() || doing_action( 'wp_theme_after_demo_import' ) ) ) {
            $url = (string) wpbb_child_v110_attachment_url( $relative, (string) ( $profile['name'] ?? 'Theme' ) . ' hero' );
        }
        if ( ! $url && is_readable( get_stylesheet_directory() . '/' . $relative ) ) {
            $url = get_stylesheet_directory_uri() . '/' . $relative;
        }
        if ( $url ) {
            $profile['hero_image'] = $url;
            if ( ! empty( $profile['hero_slides'] ) && is_array( $profile['hero_slides'] ) ) {
                foreach ( $profile['hero_slides'] as $index => $slide ) {
                    if ( is_array( $slide ) ) $profile['hero_slides'][ $index ]['image'] = $url;
                }
            }
        }
        return $profile;
    }
    add_filter( 'wp_theme_demo_profile', 'wpbb_child_v111_demo_profile', PHP_INT_MAX );
}

/* Repair existing English and translated managed homepages after update. */
if ( ! function_exists( 'wpbb_child_v111_repair_hero_pages' ) ) {
    function wpbb_child_v111_repair_hero_pages() {
        $cfg = wpbb_child_v111_config();
        $relative = $cfg['hero'];
        $hero_url = '';
        if ( function_exists( 'wpbb_child_v110_attachment_url' ) ) {
            $hero_url = (string) wpbb_child_v110_attachment_url( $relative, wp_get_theme()->get( 'Name' ) . ' hero' );
        }
        if ( ! $hero_url && is_readable( get_stylesheet_directory() . '/' . $relative ) ) {
            $hero_url = get_stylesheet_directory_uri() . '/' . $relative;
        }
        if ( ! $hero_url ) return;

        $pages = get_posts( array(
            'post_type'      => 'page',
            'post_status'    => array( 'publish', 'draft', 'private', 'pending' ),
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'no_found_rows'  => true,
        ) );
        foreach ( $pages as $page_id ) {
            $content = (string) get_post_field( 'post_content', $page_id, 'raw' );
            if ( '' === $content || false === strpos( $content, 'wpbb/swiper' ) ) continue;
            $updated = (string) preg_replace_callback(
                '~<!--\\s+wp:wpbb/swiper\\s+(\\{.*?\\})\\s+/-->~s',
                static function( $match ) use ( $hero_url ) {
                    $attrs = json_decode( $match[1], true );
                    if ( ! is_array( $attrs ) || 'hero' !== sanitize_key( (string) ( $attrs['demoStyle'] ?? '' ) ) ) return $match[0];
                    if ( ! empty( $attrs['slides'] ) && is_array( $attrs['slides'] ) ) {
                        foreach ( $attrs['slides'] as $index => $slide ) if ( is_array( $slide ) ) $attrs['slides'][ $index ]['image'] = $hero_url;
                    }
                    if ( ! empty( $attrs['slidesJson'] ) && is_string( $attrs['slidesJson'] ) ) {
                        $slides = json_decode( $attrs['slidesJson'], true );
                        if ( is_array( $slides ) ) {
                            foreach ( $slides as $index => $slide ) if ( is_array( $slide ) ) $slides[ $index ]['image'] = $hero_url;
                            $attrs['slidesJson'] = wp_json_encode( $slides, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
                        }
                    }
                    return '<!-- wp:wpbb/swiper ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ' /-->';
                },
                $content
            );
            if ( $updated !== $content ) {
                wp_update_post( array( 'ID' => $page_id, 'post_content' => $updated ) );
                clean_post_cache( $page_id );
            }
            update_post_meta( $page_id, '_wpbb_child_v111_media', '3.8.11.11' );
        }
    }
}

if ( ! function_exists( 'wpbb_child_v111_repair_once' ) ) {
    function wpbb_child_v111_repair_once() {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) || wp_doing_ajax() ) return;
        $key = 'wpbb_child_v111_repair_' . sanitize_key( get_stylesheet() );
        if ( '3.8.11.11' === (string) get_option( $key ) ) return;
        wpbb_child_v111_repair_hero_pages();
        update_option( $key, '3.8.11.11', false );
    }
    add_action( 'admin_init', 'wpbb_child_v111_repair_once', 100960 );
    add_action( 'wp_theme_after_demo_import', 'wpbb_child_v111_repair_once', 100960 );
}
