<?php
/**
 * WP BBTheme child suite 3.8.11.38 — canonical section grid + hero media refresh.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! function_exists( 'wpbb_child_v138_hero_urls' ) ) {
    function wpbb_child_v138_hero_urls() {
        $dir = get_stylesheet_directory();
        $uri = get_stylesheet_directory_uri();
        $urls = array();
        foreach ( array( 1, 2, 3 ) as $i ) {
            $rel = 'assets/img/hero-v138/slide-' . $i . '.jpg';
            $path = $dir . '/' . $rel;
            if ( is_file( $path ) ) {
                $urls[] = $uri . '/' . $rel . '?v=' . filemtime( $path );
            }
        }
        return $urls;
    }
}

if ( ! function_exists( 'wpbb_child_v138_enqueue' ) ) {
    function wpbb_child_v138_enqueue() {
        $dir = get_stylesheet_directory();
        $uri = get_stylesheet_directory_uri();
        $css = '/assets/suite-v138.css';
        wp_enqueue_style(
            'wpbb-suite-v138',
            $uri . $css,
            array( 'wpbb-suite-v137' ),
            is_file( $dir . $css ) ? filemtime( $dir . $css ) : '3.8.11.38'
        );

        // Keep v136 as the single DOM/slider owner, but feed it the refreshed
        // full-resolution v138 hero sources before the owner script executes.
        $urls = wpbb_child_v138_hero_urls();
        if ( $urls && wp_script_is( 'wpbb-suite-v136', 'enqueued' ) ) {
            wp_add_inline_script(
                'wpbb-suite-v136',
                'if(window.wpbbSuiteV136){window.wpbbSuiteV136.version="3.8.11.38";window.wpbbSuiteV136.heroUrls=' . wp_json_encode( $urls, JSON_UNESCAPED_SLASHES ) . ';}',
                'before'
            );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'wpbb_child_v138_enqueue', PHP_INT_MAX );

if ( ! function_exists( 'wpbb_child_v138_body_class' ) ) {
    function wpbb_child_v138_body_class( $classes ) {
        $classes[] = 'wpbb-v138';
        return array_values( array_unique( $classes ) );
    }
}
add_filter( 'body_class', 'wpbb_child_v138_body_class', PHP_INT_MAX );
