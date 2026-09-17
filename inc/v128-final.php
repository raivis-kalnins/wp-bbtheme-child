<?php
/**
 * v3.8.11.28 final visual recovery layer.
 *
 * Keeps the recovered global Bootstrap/BBuilder geometry intact and owns only
 * the late component fixes that were still regressing on imported demo pages:
 * hero media/pagination, process cards, Automotive commerce grids, cart and
 * checkout layout, and stale Automotive proof-card content.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'wpbb_child_v128_hero_urls' ) ) {
    function wpbb_child_v128_hero_urls() {
        $dir  = get_stylesheet_directory();
        $uri  = get_stylesheet_directory_uri();
        $slug = basename( $dir );

        // Business originals are materially cleaner than the repeatedly
        // upscaled v118 derivatives. Keep their native source detail and let
        // the browser downscale them to the rendered hero size.
        if ( false !== strpos( $slug, 'business' ) && false === strpos( $slug, 'building' ) ) {
            $business = array(
                'assets/img/demo/office-studio.jpg',
                'assets/img/demo/office-detail.jpg',
                'assets/img/demo/office-planning.jpg',
            );
            $urls = array();
            foreach ( $business as $rel ) {
                $path = $dir . '/' . $rel;
                if ( is_file( $path ) ) {
                    $urls[] = $uri . '/' . $rel . '?v=' . filemtime( $path );
                }
            }
            if ( 3 === count( $urls ) ) {
                return $urls;
            }
        }

        // Prefer a v128 hero set when a theme ships one, otherwise retain the
        // known three-slide v118 source set used by the stable hero runtime.
        $base = is_dir( $dir . '/assets/img/hero-v128' ) ? 'assets/img/hero-v128' : 'assets/img/hero-v118';
        $urls = array();
        for ( $i = 1; $i <= 3; $i++ ) {
            $rel  = $base . '/slide-' . $i . '.jpg';
            $path = $dir . '/' . $rel;
            if ( is_file( $path ) ) {
                $urls[] = $uri . '/' . $rel . '?v=' . filemtime( $path );
            }
        }
        return $urls;
    }
}

if ( ! function_exists( 'wpbb_child_v128_enqueue' ) ) {
    function wpbb_child_v128_enqueue() {
        $dir  = get_stylesheet_directory();
        $uri  = get_stylesheet_directory_uri();
        $slug = basename( $dir );

        // v128 is the only late DOM owner. Older CSS remains available for
        // unaffected components, but the older late runtimes must not race it.
        foreach ( array( 'wpbb-suite-v127', 'wpbb-suite-v126', 'wpbb-suite-v124', 'wpbb-suite-v123', 'wpbb-suite-v121', 'wpbb-suite-v120', 'wpbb-suite-v119' ) as $handle ) {
            wp_dequeue_script( $handle );
            wp_deregister_script( $handle );
        }

        $css = '/assets/suite-v128.css';
        $js  = '/assets/suite-v128.js';

        wp_enqueue_style(
            'wpbb-suite-v128',
            $uri . $css,
            array( 'wpbb-suite-v127' ),
            is_file( $dir . $css ) ? filemtime( $dir . $css ) : '3.8.11.28'
        );

        wp_enqueue_script(
            'wpbb-suite-v128',
            $uri . $js,
            array(),
            is_file( $dir . $js ) ? filemtime( $dir . $js ) : '3.8.11.28',
            true
        );

        $config = array(
            'themeSlug' => $slug,
            'heroUrls'  => wpbb_child_v128_hero_urls(),
            'version'   => '3.8.11.28',
        );
        wp_add_inline_script( 'wpbb-suite-v128', 'window.wpbbSuiteV128=' . wp_json_encode( $config ) . ';', 'before' );
    }
}
add_action( 'wp_enqueue_scripts', 'wpbb_child_v128_enqueue', PHP_INT_MAX );
