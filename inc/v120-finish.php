<?php
/** WP BBTheme child suite 3.8.11.20 - stable v119 rollback, canonical gutters, grid hardening and hero pagination/quality repair. */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v120_disable_v119_runtime' ) ) {
    function wpbb_child_v120_disable_v119_runtime() {
        // v119 introduced suite-wide geometry overrides. Keep its helpers available, but stop its runtime layer.
        remove_action( 'wp_enqueue_scripts', 'wpbb_child_v119_enqueue', PHP_INT_MAX );
        remove_action( 'admin_init', 'wpbb_child_v119_repair_once', PHP_INT_MAX );
        remove_action( 'wp_theme_after_demo_import', 'wpbb_child_v119_after_demo_import', PHP_INT_MAX );
        remove_action( 'wp_theme_after_demo_reset', 'wpbb_child_v119_after_demo_import', PHP_INT_MAX );
        remove_action( 'wp_theme_demo_reset_complete', 'wpbb_child_v119_after_demo_import', PHP_INT_MAX );
        remove_action( 'wp_theme_starter_setup_complete', 'wpbb_child_v119_after_demo_import', PHP_INT_MAX );
        remove_filter( 'wp_insert_post_data', 'wpbb_child_v119_filter_post_data', PHP_INT_MAX );

        // Prevent v119 from repeatedly removing the stable v118 hooks after this file has loaded.
        remove_action( 'after_setup_theme', 'wpbb_child_v119_disable_superseded_runtime', PHP_INT_MAX );
        remove_action( 'init', 'wpbb_child_v119_disable_superseded_runtime', -9999 );
    }
}
wpbb_child_v120_disable_v119_runtime();
add_action( 'after_setup_theme', 'wpbb_child_v120_disable_v119_runtime', PHP_INT_MAX );
add_action( 'init', 'wpbb_child_v120_disable_v119_runtime', PHP_INT_MAX );

if ( ! function_exists( 'wpbb_child_v120_repair_page_content' ) ) {
    function wpbb_child_v120_repair_page_content( $content, $page_id = 0 ) {
        if ( function_exists( 'wpbb_child_v118_repair_page_content' ) ) {
            return wpbb_child_v118_repair_page_content( $content, $page_id );
        }
        return $content;
    }
}

if ( ! function_exists( 'wpbb_child_v120_repair_all' ) ) {
    function wpbb_child_v120_repair_all() {
        wpbb_child_v120_disable_v119_runtime();
        if ( function_exists( 'wpbb_child_v118_repair_all' ) ) {
            wpbb_child_v118_repair_all();
        } elseif ( function_exists( 'wpbb_child_v62_rebuild_demo_pages' ) ) {
            wpbb_child_v62_rebuild_demo_pages( true );
        }
        if ( function_exists( 'wpbb_child_v117_drain_media_worker' ) ) {
            wpbb_child_v117_drain_media_worker();
        }
    }
}

if ( ! function_exists( 'wpbb_child_v120_repair_once' ) ) {
    function wpbb_child_v120_repair_once() {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) || wp_doing_ajax() ) return;
        $key = 'wpbb_child_v120_repair_' . sanitize_key( get_stylesheet() );
        if ( '3.8.11.20' === (string) get_option( $key ) ) return;
        wpbb_child_v120_repair_all();
        update_option( $key, '3.8.11.20', false );
    }
    add_action( 'admin_init', 'wpbb_child_v120_repair_once', PHP_INT_MAX );
}

if ( ! function_exists( 'wpbb_child_v120_after_demo_import' ) ) {
    function wpbb_child_v120_after_demo_import( $page_id = 0, $profile = array() ) {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) || wp_doing_ajax() ) return;
        wpbb_child_v120_repair_all();
        update_option( 'wpbb_child_v120_repair_' . sanitize_key( get_stylesheet() ), '3.8.11.20', false );
    }
    add_action( 'wp_theme_after_demo_import', 'wpbb_child_v120_after_demo_import', PHP_INT_MAX, 2 );
    add_action( 'wp_theme_after_demo_reset', 'wpbb_child_v120_after_demo_import', PHP_INT_MAX );
    add_action( 'wp_theme_demo_reset_complete', 'wpbb_child_v120_after_demo_import', PHP_INT_MAX );
    add_action( 'wp_theme_starter_setup_complete', 'wpbb_child_v120_after_demo_import', PHP_INT_MAX );
}

if ( ! function_exists( 'wpbb_child_v120_filter_post_data' ) ) {
    function wpbb_child_v120_filter_post_data( $data, $postarr ) {
        if ( in_array( (string) ( $data['post_type'] ?? '' ), array( 'page', 'post' ), true ) && ! empty( $data['post_content'] ) ) {
            $content = (string) $data['post_content'];
            if ( false !== strpos( $content, 'wpbb/' ) || false !== strpos( $content, 'wp-theme-' ) ) {
                $data['post_content'] = wpbb_child_v120_repair_page_content( $content, absint( $postarr['ID'] ?? 0 ) );
            }
        }
        return $data;
    }
    add_filter( 'wp_insert_post_data', 'wpbb_child_v120_filter_post_data', PHP_INT_MAX, 2 );
}

if ( ! function_exists( 'wpbb_child_v120_enqueue' ) ) {
    function wpbb_child_v120_enqueue() {
        // v118 is the last known-good geometry baseline. Explicitly retire v116/v117 and the v119 regression layer.
        foreach ( array( 'wpbb-suite-v116', 'wpbb-suite-v117', 'wpbb-suite-v119' ) as $handle ) {
            wp_dequeue_style( $handle );
            wp_deregister_style( $handle );
            wp_dequeue_script( $handle );
            wp_deregister_script( $handle );
        }

        $base_css = get_stylesheet_directory() . '/assets/suite-v118.css';
        $base_js  = get_stylesheet_directory() . '/assets/suite-v118.js';
        if ( is_readable( $base_css ) ) {
            wp_enqueue_style( 'wpbb-suite-v118', get_stylesheet_directory_uri() . '/assets/suite-v118.css', array(), (string) filemtime( $base_css ) );
        }
        if ( is_readable( $base_js ) ) {
            wp_enqueue_script( 'wpbb-suite-v118', get_stylesheet_directory_uri() . '/assets/suite-v118.js', array(), (string) filemtime( $base_js ), false );
        }

        $css = get_stylesheet_directory() . '/assets/suite-v120.css';
        $js  = get_stylesheet_directory() . '/assets/suite-v120.js';
        if ( is_readable( $css ) ) {
            wp_enqueue_style( 'wpbb-suite-v120', get_stylesheet_directory_uri() . '/assets/suite-v120.css', array( 'wpbb-suite-v118' ), (string) filemtime( $css ) );
        }
        if ( is_readable( $js ) ) {
            $deps = is_readable( $base_js ) ? array( 'wpbb-suite-v118' ) : array();
            wp_enqueue_script( 'wpbb-suite-v120', get_stylesheet_directory_uri() . '/assets/suite-v120.js', $deps, (string) filemtime( $js ), false );
        }
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v120_enqueue', PHP_INT_MAX );
}
