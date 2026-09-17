<?php
/** WP BBTheme child suite 3.8.11.22 - component grid/gap and radius finish. */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v122_enqueue' ) ) {
    function wpbb_child_v122_enqueue() {
        $css = get_stylesheet_directory() . '/assets/suite-v122.css';
        if ( is_readable( $css ) ) {
            $deps = wp_style_is( 'wpbb-suite-v121', 'registered' ) || wp_style_is( 'wpbb-suite-v121', 'enqueued' )
                ? array( 'wpbb-suite-v121' )
                : array();
            wp_enqueue_style(
                'wpbb-suite-v122',
                get_stylesheet_directory_uri() . '/assets/suite-v122.css',
                $deps,
                (string) filemtime( $css )
            );
        }
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v122_enqueue', PHP_INT_MAX );
}
