<?php
/** WP BBTheme child suite 3.8.11.27 - final live-regression hardening. */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v127_hero_urls' ) ) {
    function wpbb_child_v127_hero_urls() {
        $urls = array();
        for ( $i = 1; $i <= 3; $i++ ) {
            $relative = 'assets/img/hero-v118/slide-' . $i . '.jpg';
            $path = get_stylesheet_directory() . '/' . $relative;
            if ( ! is_readable( $path ) ) {
                continue;
            }
            $urls[] = add_query_arg(
                'v',
                (string) filemtime( $path ),
                trailingslashit( get_stylesheet_directory_uri() ) . $relative
            );
        }
        return $urls;
    }
}

if ( ! function_exists( 'wpbb_child_v127_enqueue' ) ) {
    function wpbb_child_v127_enqueue() {
        // v127 is the only late hero/pager runtime. Keep v126 CSS fixes, retire
        // the v126 JS owner plus older pager owners before scripts are printed.
        foreach ( array( 'wpbb-suite-v119', 'wpbb-suite-v120', 'wpbb-suite-v121', 'wpbb-suite-v123', 'wpbb-suite-v124', 'wpbb-suite-v126' ) as $handle ) {
            wp_dequeue_script( $handle );
            wp_deregister_script( $handle );
        }

        $css = get_stylesheet_directory() . '/assets/suite-v127.css';
        $js  = get_stylesheet_directory() . '/assets/suite-v127.js';

        if ( is_readable( $css ) ) {
            $deps = ( wp_style_is( 'wpbb-suite-v126', 'registered' ) || wp_style_is( 'wpbb-suite-v126', 'enqueued' ) )
                ? array( 'wpbb-suite-v126' )
                : array();
            wp_enqueue_style(
                'wpbb-suite-v127',
                get_stylesheet_directory_uri() . '/assets/suite-v127.css',
                $deps,
                (string) filemtime( $css )
            );
        }

        if ( is_readable( $js ) ) {
            $deps = ( wp_script_is( 'wpbb-suite-v118', 'registered' ) || wp_script_is( 'wpbb-suite-v118', 'enqueued' ) )
                ? array( 'wpbb-suite-v118' )
                : array();
            wp_enqueue_script(
                'wpbb-suite-v127',
                get_stylesheet_directory_uri() . '/assets/suite-v127.js',
                $deps,
                (string) filemtime( $js ),
                false
            );
            wp_add_inline_script(
                'wpbb-suite-v127',
                'window.wpbbSuiteV127=' . wp_json_encode( array( 'heroUrls' => wpbb_child_v127_hero_urls() ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ';',
                'before'
            );
        }
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v127_enqueue', PHP_INT_MAX );
}
