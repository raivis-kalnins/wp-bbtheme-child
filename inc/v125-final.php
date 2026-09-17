<?php
/** WP BBTheme child suite 3.8.11.25 - final grid, hero and WooCommerce finish. */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v125_enqueue' ) ) {
    function wpbb_child_v125_enqueue() {
        $css = get_stylesheet_directory() . '/assets/suite-v125.css';
        if ( ! is_readable( $css ) ) return;

        $deps = ( wp_style_is( 'wpbb-suite-v124', 'registered' ) || wp_style_is( 'wpbb-suite-v124', 'enqueued' ) )
            ? array( 'wpbb-suite-v124' )
            : array();

        wp_enqueue_style(
            'wpbb-suite-v125',
            get_stylesheet_directory_uri() . '/assets/suite-v125.css',
            $deps,
            (string) filemtime( $css )
        );
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v125_enqueue', PHP_INT_MAX );
}
