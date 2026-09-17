<?php
/**
 * WP BBTheme child suite 3.8.11.37 — wide section grid + tighter homepage rhythm.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! function_exists( 'wpbb_child_v137_enqueue' ) ) {
    function wpbb_child_v137_enqueue() {
        $dir = get_stylesheet_directory();
        $uri = get_stylesheet_directory_uri();
        $css = '/assets/suite-v137.css';
        wp_enqueue_style(
            'wpbb-suite-v137',
            $uri . $css,
            array( 'wpbb-suite-v136' ),
            is_file( $dir . $css ) ? filemtime( $dir . $css ) : '3.8.11.37'
        );
    }
}
add_action( 'wp_enqueue_scripts', 'wpbb_child_v137_enqueue', PHP_INT_MAX );

if ( ! function_exists( 'wpbb_child_v137_body_class' ) ) {
    function wpbb_child_v137_body_class( $classes ) {
        $classes[] = 'wpbb-v137';
        return array_values( array_unique( $classes ) );
    }
}
add_filter( 'body_class', 'wpbb_child_v137_body_class', PHP_INT_MAX );
