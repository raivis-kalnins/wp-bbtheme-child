<?php
/**
 * WP BBTheme child suite 3.8.11.36 — full-width hero + stable process/grid owner.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! function_exists( 'wpbb_child_v136_theme_key' ) ) {
    function wpbb_child_v136_theme_key() {
        if ( function_exists( 'wpbb_child_v135_theme_key' ) ) return wpbb_child_v135_theme_key();
        $slug = basename( get_stylesheet_directory() );
        $slug = preg_replace( '/^wp-bbtheme-child-/', '', $slug );
        return sanitize_key( $slug );
    }
}

if ( ! function_exists( 'wpbb_child_v136_profile' ) ) {
    function wpbb_child_v136_profile() {
        if ( function_exists( 'wpbb_child_v135_profile' ) ) return wpbb_child_v135_profile();
        return array(
            array( '01', 'Explore', 'Find the most useful option for what you need.' ),
            array( '02', 'Compare', 'Review the important details in a clear layout.' ),
            array( '03', 'Continue', 'Take the next action without unnecessary friction.' ),
        );
    }
}

if ( ! function_exists( 'wpbb_child_v136_hero_urls' ) ) {
    function wpbb_child_v136_hero_urls() {
        $dir  = get_stylesheet_directory();
        $uri  = get_stylesheet_directory_uri();
        $urls = array();
        foreach ( array( 1, 2, 3 ) as $i ) {
            $rels = array(
                'assets/img/hero-v136/slide-' . $i . '.jpg',
                'assets/img/hero-v135/slide-' . $i . '.jpg',
                'assets/img/hero-v134/slide-' . $i . '.jpg',
                'assets/img/hero-v118/slide-' . $i . '.jpg',
            );
            foreach ( $rels as $rel ) {
                $path = $dir . '/' . $rel;
                if ( is_file( $path ) ) {
                    $urls[] = $uri . '/' . $rel . '?v=' . filemtime( $path );
                    break;
                }
            }
        }
        return $urls;
    }
}

if ( ! function_exists( 'wpbb_child_v136_enqueue' ) ) {
    function wpbb_child_v136_enqueue() {
        $dir = get_stylesheet_directory();
        $uri = get_stylesheet_directory_uri();

        // One DOM owner only. Earlier late repair scripts were the cause of
        // duplicated hero/process blocks and racing layout mutations.
        foreach ( range( 119, 135 ) as $n ) {
            $handle = 'wpbb-suite-v' . $n;
            wp_dequeue_script( $handle );
            wp_deregister_script( $handle );
        }

        $css = '/assets/suite-v136.css';
        $js  = '/assets/suite-v136.js';
        wp_enqueue_style(
            'wpbb-suite-v136',
            $uri . $css,
            array(),
            is_file( $dir . $css ) ? filemtime( $dir . $css ) : '3.8.11.36'
        );
        wp_enqueue_script(
            'wpbb-suite-v136',
            $uri . $js,
            array(),
            is_file( $dir . $js ) ? filemtime( $dir . $js ) : '3.8.11.36',
            true
        );
        wp_add_inline_script(
            'wpbb-suite-v136',
            'window.wpbbSuiteV136=' . wp_json_encode( array(
                'version'       => '3.8.11.36',
                'themeKey'      => wpbb_child_v136_theme_key(),
                'heroUrls'      => wpbb_child_v136_hero_urls(),
                'fallbackCards' => wpbb_child_v136_profile(),
            ) ) . ';',
            'before'
        );
    }
}
add_action( 'wp_enqueue_scripts', 'wpbb_child_v136_enqueue', PHP_INT_MAX );

if ( ! function_exists( 'wpbb_child_v136_body_class' ) ) {
    function wpbb_child_v136_body_class( $classes ) {
        $classes[] = 'wpbb-v136';
        $classes[] = 'wpbb-v136-theme-' . wpbb_child_v136_theme_key();
        return array_values( array_unique( $classes ) );
    }
}
add_filter( 'body_class', 'wpbb_child_v136_body_class', PHP_INT_MAX );
