<?php
/**
 * WP BBTheme child suite 3.8.11.35 — final duplicate/process/hero cleanup.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! function_exists( 'wpbb_child_v135_theme_key' ) ) {
    function wpbb_child_v135_theme_key() {
        if ( function_exists( 'wpbb_child_v134_theme_key' ) ) return wpbb_child_v134_theme_key();
        $slug = basename( get_stylesheet_directory() );
        $slug = preg_replace( '/^wp-bbtheme-child-/', '', $slug );
        return sanitize_key( $slug );
    }
}

if ( ! function_exists( 'wpbb_child_v135_profile' ) ) {
    function wpbb_child_v135_profile() {
        if ( function_exists( 'wpbb_child_v134_profile' ) ) return wpbb_child_v134_profile();
        return array(
            array( '01', 'Explore', 'Find the most useful option for what you need.' ),
            array( '02', 'Compare', 'Review the important details in a clear layout.' ),
            array( '03', 'Continue', 'Take the next action without unnecessary friction.' ),
        );
    }
}

if ( ! function_exists( 'wpbb_child_v135_hero_urls' ) ) {
    function wpbb_child_v135_hero_urls() {
        $dir  = get_stylesheet_directory();
        $uri  = get_stylesheet_directory_uri();
        $urls = array();
        foreach ( array( 1, 2, 3 ) as $i ) {
            $rels = array(
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

if ( ! function_exists( 'wpbb_child_v135_enqueue' ) ) {
    function wpbb_child_v135_enqueue() {
        $dir = get_stylesheet_directory();
        $uri = get_stylesheet_directory_uri();

        // v135 owns the late DOM repair. Keep v134 CSS for WooCommerce fixes,
        // but prevent its JavaScript from creating a second process row.
        wp_dequeue_script( 'wpbb-suite-v134' );
        wp_deregister_script( 'wpbb-suite-v134' );

        $css = '/assets/suite-v135.css';
        $js  = '/assets/suite-v135.js';
        wp_enqueue_style(
            'wpbb-suite-v135',
            $uri . $css,
            array( 'wpbb-suite-v134' ),
            is_file( $dir . $css ) ? filemtime( $dir . $css ) : '3.8.11.35'
        );
        wp_enqueue_script(
            'wpbb-suite-v135',
            $uri . $js,
            array(),
            is_file( $dir . $js ) ? filemtime( $dir . $js ) : '3.8.11.35',
            true
        );
        wp_add_inline_script(
            'wpbb-suite-v135',
            'window.wpbbSuiteV135=' . wp_json_encode( array(
                'version'       => '3.8.11.35',
                'themeKey'      => wpbb_child_v135_theme_key(),
                'heroUrls'      => wpbb_child_v135_hero_urls(),
                'fallbackCards' => wpbb_child_v135_profile(),
            ) ) . ';',
            'before'
        );
    }
}
add_action( 'wp_enqueue_scripts', 'wpbb_child_v135_enqueue', PHP_INT_MAX );

if ( ! function_exists( 'wpbb_child_v135_body_class' ) ) {
    function wpbb_child_v135_body_class( $classes ) {
        $classes[] = 'wpbb-v135';
        $classes[] = 'wpbb-v135-theme-' . wpbb_child_v135_theme_key();
        return array_values( array_unique( $classes ) );
    }
}
add_filter( 'body_class', 'wpbb_child_v135_body_class', PHP_INT_MAX );


/* Remove stale malformed block-comment text left in imported demo content. */
if ( ! function_exists( 'wpbb_child_v135_clean_demo_content' ) ) {
    function wpbb_child_v135_clean_demo_content( $content ) {
        if ( false === strpos( $content, 'wp:wpbb/icon-card' ) ) return $content;
        $content = preg_replace( '~<![\-–—]{1,2}\s*wp:wpbb/icon-card\b.*?wpbb-sector-proof-card.*?/[\-–—]{1,2}>~isu', '', $content );
        $content = preg_replace( '~&lt;![\-–—]{1,2}\s*wp:wpbb/icon-card\b.*?wpbb-sector-proof-card.*?/[\-–—]{1,2}&gt;~isu', '', $content );
        return $content;
    }
}
add_filter( 'the_content', 'wpbb_child_v135_clean_demo_content', 9999 );
