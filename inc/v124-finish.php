<?php
/** WP BBTheme child suite 3.8.11.24 - card gap, fun-fact width and hero finish. */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v124_hero_urls' ) ) {
    function wpbb_child_v124_hero_urls() {
        $urls = array();
        for ( $i = 1; $i <= 3; $i++ ) {
            $relative = 'assets/img/hero-v118/slide-' . $i . '.jpg';
            $path = get_stylesheet_directory() . '/' . $relative;
            if ( ! is_readable( $path ) ) return array();
            $size = function_exists( 'getimagesize' ) ? @getimagesize( $path ) : false;
            // Only force the direct source when all bundled hero images are large enough
            // for a wide desktop render. Otherwise leave the theme's existing source alone.
            if ( is_array( $size ) && ( (int) ( $size[0] ?? 0 ) < 2000 || (int) ( $size[1] ?? 0 ) < 1000 ) ) return array();
            $urls[] = add_query_arg( 'v', (string) filemtime( $path ), trailingslashit( get_stylesheet_directory_uri() ) . $relative );
        }
        return $urls;
    }
}

if ( ! function_exists( 'wpbb_child_v124_enqueue' ) ) {
    function wpbb_child_v124_enqueue() {
        // v120/v121/v123 JavaScript each attempted to own hero pagination. Retire only
        // those JS runtimes; their CSS remains in place because later layout fixes depend on it.
        foreach ( array( 'wpbb-suite-v120', 'wpbb-suite-v121', 'wpbb-suite-v123' ) as $handle ) {
            wp_dequeue_script( $handle );
            wp_deregister_script( $handle );
        }

        $css = get_stylesheet_directory() . '/assets/suite-v124.css';
        $js  = get_stylesheet_directory() . '/assets/suite-v124.js';

        if ( is_readable( $css ) ) {
            $deps = ( wp_style_is( 'wpbb-suite-v123', 'registered' ) || wp_style_is( 'wpbb-suite-v123', 'enqueued' ) )
                ? array( 'wpbb-suite-v123' )
                : array();
            wp_enqueue_style( 'wpbb-suite-v124', get_stylesheet_directory_uri() . '/assets/suite-v124.css', $deps, (string) filemtime( $css ) );
        }

        if ( is_readable( $js ) ) {
            $deps = ( wp_script_is( 'wpbb-suite-v118', 'registered' ) || wp_script_is( 'wpbb-suite-v118', 'enqueued' ) )
                ? array( 'wpbb-suite-v118' )
                : array();
            wp_enqueue_script( 'wpbb-suite-v124', get_stylesheet_directory_uri() . '/assets/suite-v124.js', $deps, (string) filemtime( $js ), false );
            wp_add_inline_script(
                'wpbb-suite-v124',
                'window.wpbbSuiteV124=' . wp_json_encode( array( 'heroUrls' => wpbb_child_v124_hero_urls() ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ';',
                'before'
            );
        }
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v124_enqueue', PHP_INT_MAX );
}
