<?php
/** WP BBTheme child suite 3.8.11.23 - remaining basic layout + hero finish. */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v123_hero_urls' ) ) {
    function wpbb_child_v123_hero_urls() {
        $urls = array();
        for ( $i = 1; $i <= 3; $i++ ) {
            $relative = 'assets/img/hero-v118/slide-' . $i . '.jpg';
            $path = get_stylesheet_directory() . '/' . $relative;
            if ( ! is_readable( $path ) ) return array();
            $size = function_exists( 'getimagesize' ) ? @getimagesize( $path ) : false;
            // Only force the runtime source when the full hero set is genuinely high-resolution.
            // Themes with an older 1400px source keep their existing rendered source rather than
            // being "upgraded" to a file that would look softer on wide screens.
            if ( is_array( $size ) && ( (int) ( $size[0] ?? 0 ) < 2000 || (int) ( $size[1] ?? 0 ) < 1000 ) ) return array();
            $urls[] = add_query_arg( 'v', (string) filemtime( $path ), trailingslashit( get_stylesheet_directory_uri() ) . $relative );
        }
        return $urls;
    }
}

if ( ! function_exists( 'wpbb_child_v123_enqueue' ) ) {
    function wpbb_child_v123_enqueue() {
        $css = get_stylesheet_directory() . '/assets/suite-v123.css';
        $js  = get_stylesheet_directory() . '/assets/suite-v123.js';

        if ( is_readable( $css ) ) {
            $deps = ( wp_style_is( 'wpbb-suite-v122', 'registered' ) || wp_style_is( 'wpbb-suite-v122', 'enqueued' ) )
                ? array( 'wpbb-suite-v122' )
                : array();
            wp_enqueue_style( 'wpbb-suite-v123', get_stylesheet_directory_uri() . '/assets/suite-v123.css', $deps, (string) filemtime( $css ) );
        }

        if ( is_readable( $js ) ) {
            $deps = ( wp_script_is( 'wpbb-suite-v121', 'registered' ) || wp_script_is( 'wpbb-suite-v121', 'enqueued' ) )
                ? array( 'wpbb-suite-v121' )
                : array();
            wp_enqueue_script( 'wpbb-suite-v123', get_stylesheet_directory_uri() . '/assets/suite-v123.js', $deps, (string) filemtime( $js ), false );
            wp_add_inline_script(
                'wpbb-suite-v123',
                'window.wpbbSuiteV123=' . wp_json_encode( array( 'heroUrls' => wpbb_child_v123_hero_urls() ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ';',
                'before'
            );
        }
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v123_enqueue', PHP_INT_MAX );
}
