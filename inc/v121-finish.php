<?php
/** WP BBTheme child suite 3.8.11.21 - scoped grid recovery. Removes v119/v120 global geometry and keeps stable earlier shell sizing. */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v121_disable_regression_runtime' ) ) {
    function wpbb_child_v121_disable_regression_runtime() {
        // v119 widened its geometry selectors to every row/container; v120 then forced a 1320px shell.
        // Keep their helper functions loaded for backwards compatibility, but stop the destructive/runtime layers.
        foreach ( array(
            array( 'wp_enqueue_scripts', 'wpbb_child_v119_enqueue', PHP_INT_MAX ),
            array( 'admin_init', 'wpbb_child_v119_repair_once', PHP_INT_MAX ),
            array( 'wp_theme_after_demo_import', 'wpbb_child_v119_after_demo_import', PHP_INT_MAX ),
            array( 'wp_theme_after_demo_reset', 'wpbb_child_v119_after_demo_import', PHP_INT_MAX ),
            array( 'wp_theme_demo_reset_complete', 'wpbb_child_v119_after_demo_import', PHP_INT_MAX ),
            array( 'wp_theme_starter_setup_complete', 'wpbb_child_v119_after_demo_import', PHP_INT_MAX ),
            array( 'wp_enqueue_scripts', 'wpbb_child_v120_enqueue', PHP_INT_MAX ),
            array( 'admin_init', 'wpbb_child_v120_repair_once', PHP_INT_MAX ),
            array( 'wp_theme_after_demo_import', 'wpbb_child_v120_after_demo_import', PHP_INT_MAX ),
            array( 'wp_theme_after_demo_reset', 'wpbb_child_v120_after_demo_import', PHP_INT_MAX ),
            array( 'wp_theme_demo_reset_complete', 'wpbb_child_v120_after_demo_import', PHP_INT_MAX ),
            array( 'wp_theme_starter_setup_complete', 'wpbb_child_v120_after_demo_import', PHP_INT_MAX ),
        ) as $hook ) {
            remove_action( $hook[0], $hook[1], $hook[2] );
        }
        remove_filter( 'wp_insert_post_data', 'wpbb_child_v119_filter_post_data', PHP_INT_MAX );
        remove_filter( 'wp_insert_post_data', 'wpbb_child_v120_filter_post_data', PHP_INT_MAX );
    }
}
wpbb_child_v121_disable_regression_runtime();
add_action( 'after_setup_theme', 'wpbb_child_v121_disable_regression_runtime', PHP_INT_MAX );
add_action( 'init', 'wpbb_child_v121_disable_regression_runtime', PHP_INT_MAX );

if ( ! function_exists( 'wpbb_child_v121_enqueue' ) ) {
    function wpbb_child_v121_enqueue() {
        // Explicitly remove only the known regression layers. Earlier sector/jobs CSS owns the intended 1440px shell.
        foreach ( array( 'wpbb-suite-v116', 'wpbb-suite-v117', 'wpbb-suite-v119', 'wpbb-suite-v120' ) as $handle ) {
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

        $css = get_stylesheet_directory() . '/assets/suite-v121.css';
        $js  = get_stylesheet_directory() . '/assets/suite-v121.js';
        if ( is_readable( $css ) ) {
            $deps = is_readable( $base_css ) ? array( 'wpbb-suite-v118' ) : array();
            wp_enqueue_style( 'wpbb-suite-v121', get_stylesheet_directory_uri() . '/assets/suite-v121.css', $deps, (string) filemtime( $css ) );
        }
        if ( is_readable( $js ) ) {
            $deps = is_readable( $base_js ) ? array( 'wpbb-suite-v118' ) : array();
            wp_enqueue_script( 'wpbb-suite-v121', get_stylesheet_directory_uri() . '/assets/suite-v121.js', $deps, (string) filemtime( $js ), false );
        }
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v121_enqueue', PHP_INT_MAX );
}
