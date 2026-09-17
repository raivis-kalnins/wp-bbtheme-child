<?php
/** WP BBTheme child suite 3.8.11.13 — final hero edge/clarity and Latest Thinking grid alignment. */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v113_config' ) ) {
    function wpbb_child_v113_config() {
        $hero_files = array(
            'wp-bbtheme-child-automotive'        => 'assets/img/demo/hero-v113.jpg',
            'wp-bbtheme-child-building-services' => 'assets/img/demo/hero-v113.jpg',
            'wp-bbtheme-child-business'          => 'assets/img/demo/hero-v113.jpg',
            'wp-bbtheme-child-elearning'         => 'assets/img/demo/hero-v113.jpg',
            'wp-bbtheme-child-hotel'             => 'assets/img/demo/hero-v113.jpg',
            'wp-bbtheme-child-insurance'         => 'assets/img/demo/hero-v113.jpg',
            'wp-bbtheme-child-jobs'              => 'assets/img/demo/hero-v113.jpg',
            'wp-bbtheme-child-logistics'         => 'assets/img/demo/hero-v113.jpg',
            'wp-bbtheme-child-medicine'          => 'assets/img/demo/hero-v113.jpg',
            'wp-bbtheme-child-realestate'        => 'assets/img/demo/hero-v113.jpg',
            'wp-bbtheme-child-restaurant'        => 'assets/img/demo/hero-v113.jpg',
            'wp-bbtheme-child-travel'            => 'assets/img/demo/hero-v113.jpg',
            'wp-bbtheme-child-woo-clouthes'      => 'assets/img/demo/hero-v113.jpg',
            'wp-bbtheme-child-woo-events'        => 'assets/img/demo/hero-v113.jpg',
            'wp-bbtheme-child-woo-tech-shop'     => 'assets/img/demo/hero-v113.jpg',
        );
        $style = get_stylesheet();
        return array( 'hero' => $hero_files[ $style ] ?? 'assets/img/demo/hero-v113.jpg' );
    }
}

if ( ! function_exists( 'wpbb_child_v113_enqueue' ) ) {
    function wpbb_child_v113_enqueue() {
        $version = wp_get_theme()->get( 'Version' );
        wp_enqueue_style( 'wpbb-suite-v113', get_stylesheet_directory_uri() . '/assets/suite-v113.css', array( 'wpbb-suite-v112' ), $version );
        $cfg = wpbb_child_v113_config();
        $relative = ltrim( (string) $cfg['hero'], '/' );
        $file = get_stylesheet_directory() . '/' . $relative;
        if ( is_readable( $file ) ) {
            wp_add_inline_style( 'wpbb-suite-v113', ':root{--suite-v113-hero:url("' . esc_url( get_stylesheet_directory_uri() . '/' . $relative ) . '");}' );
        }
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v113_enqueue', PHP_INT_MAX );
}

/* Future starter imports and existing translated homepages use the same child-owned hero original. */
if ( ! function_exists( 'wpbb_child_v113_demo_profile' ) ) {
    function wpbb_child_v113_demo_profile( $profile ) {
        if ( ! is_array( $profile ) ) $profile = array();
        $cfg = wpbb_child_v113_config();
        $relative = ltrim( (string) $cfg['hero'], '/' );
        $url = '';
        if ( ( is_admin() || doing_action( 'wp_theme_after_demo_import' ) ) && function_exists( 'wpbb_child_v110_attachment_url' ) ) {
            $url = (string) wpbb_child_v110_attachment_url( $relative, (string) ( $profile['name'] ?? wp_get_theme()->get( 'Name' ) ) . ' hero' );
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
    add_filter( 'wp_theme_demo_profile', 'wpbb_child_v113_demo_profile', PHP_INT_MAX );
}

if ( ! function_exists( 'wpbb_child_v113_repair_hero_pages' ) ) {
    function wpbb_child_v113_repair_hero_pages() {
        $cfg = wpbb_child_v113_config();
        $relative = ltrim( (string) $cfg['hero'], '/' );
        $hero_url = function_exists( 'wpbb_child_v110_attachment_url' ) ? (string) wpbb_child_v110_attachment_url( $relative, wp_get_theme()->get( 'Name' ) . ' hero' ) : '';
        if ( ! $hero_url && is_readable( get_stylesheet_directory() . '/' . $relative ) ) $hero_url = get_stylesheet_directory_uri() . '/' . $relative;
        if ( ! $hero_url ) return;

        $pages = get_posts( array(
            'post_type' => 'page',
            'post_status' => array( 'publish', 'draft', 'private', 'pending' ),
            'posts_per_page' => -1,
            'fields' => 'ids',
            'no_found_rows' => true,
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
            update_post_meta( $page_id, '_wpbb_child_v113_media', '3.8.11.13' );
        }
    }
}

if ( ! function_exists( 'wpbb_child_v113_repair_once' ) ) {
    function wpbb_child_v113_repair_once() {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) || wp_doing_ajax() ) return;
        $key = 'wpbb_child_v113_repair_' . sanitize_key( get_stylesheet() );
        if ( '3.8.11.13' === (string) get_option( $key ) ) return;
        wpbb_child_v113_repair_hero_pages();
        update_option( $key, '3.8.11.13', false );
    }
    add_action( 'admin_init', 'wpbb_child_v113_repair_once', 100980 );
    add_action( 'wp_theme_after_demo_import', 'wpbb_child_v113_repair_once', 100980 );
}
