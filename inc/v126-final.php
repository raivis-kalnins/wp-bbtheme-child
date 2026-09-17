<?php
/** WP BBTheme child suite 3.8.11.26 - final cross-theme component and hero recovery. */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v126_hero_urls' ) ) {
    function wpbb_child_v126_hero_urls() {
        $urls = array();
        for ( $i = 1; $i <= 3; $i++ ) {
            $relative = 'assets/img/hero-v118/slide-' . $i . '.jpg';
            $path = get_stylesheet_directory() . '/' . $relative;
            if ( ! is_readable( $path ) ) continue;
            $urls[] = add_query_arg(
                'v',
                (string) filemtime( $path ),
                trailingslashit( get_stylesheet_directory_uri() ) . $relative
            );
        }
        return $urls;
    }
}

if ( ! function_exists( 'wpbb_child_v126_enqueue' ) ) {
    function wpbb_child_v126_enqueue() {
        // One JS owner for the hero. Keep v118 core behaviour, retire later competing pagers.
        foreach ( array( 'wpbb-suite-v119', 'wpbb-suite-v120', 'wpbb-suite-v121', 'wpbb-suite-v123', 'wpbb-suite-v124' ) as $handle ) {
            wp_dequeue_script( $handle );
            wp_deregister_script( $handle );
        }

        $css = get_stylesheet_directory() . '/assets/suite-v126.css';
        $js  = get_stylesheet_directory() . '/assets/suite-v126.js';

        if ( is_readable( $css ) ) {
            $deps = ( wp_style_is( 'wpbb-suite-v125', 'registered' ) || wp_style_is( 'wpbb-suite-v125', 'enqueued' ) )
                ? array( 'wpbb-suite-v125' )
                : array();
            wp_enqueue_style(
                'wpbb-suite-v126',
                get_stylesheet_directory_uri() . '/assets/suite-v126.css',
                $deps,
                (string) filemtime( $css )
            );
        }

        if ( is_readable( $js ) ) {
            $deps = ( wp_script_is( 'wpbb-suite-v118', 'registered' ) || wp_script_is( 'wpbb-suite-v118', 'enqueued' ) )
                ? array( 'wpbb-suite-v118' )
                : array();
            wp_enqueue_script(
                'wpbb-suite-v126',
                get_stylesheet_directory_uri() . '/assets/suite-v126.js',
                $deps,
                (string) filemtime( $js ),
                false
            );
            wp_add_inline_script(
                'wpbb-suite-v126',
                'window.wpbbSuiteV126=' . wp_json_encode( array( 'heroUrls' => wpbb_child_v126_hero_urls() ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ';',
                'before'
            );
        }
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v126_enqueue', PHP_INT_MAX );
}
