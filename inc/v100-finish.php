<?php
/** WP BBTheme sector suite 3.8.11.01 finishing layer. */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v100_enqueue' ) ) {
    function wpbb_child_v100_enqueue() {
        $v = wp_get_theme()->get( 'Version' );
        wp_enqueue_style( 'wpbb-suite-v100', get_stylesheet_directory_uri() . '/assets/suite-v100.css', array( 'wpbb-suite-v99' ), $v );
        wp_enqueue_script( 'wpbb-suite-v100', get_stylesheet_directory_uri() . '/assets/suite-v100.js', array(), $v, true );
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v100_enqueue', 1900 );
}

/* The only consent UI in the sector themes is the WP BBuilder cookie banner. */
add_filter( 'wp_theme_cookie_consent_enabled', '__return_false', PHP_INT_MAX );
add_filter( 'wp_theme_cookie_banner_enabled', '__return_false', PHP_INT_MAX );
add_filter( 'wp_theme_cookie_settings_enabled', '__return_false', PHP_INT_MAX );

/* Keep the premium demo image mapping consistent with the supplied theme-preview artwork. */
if ( ! function_exists( 'wpbb_child_v100_demo_profile' ) ) {
    function wpbb_child_v100_demo_profile( $profile ) {
        $hero = get_stylesheet_directory() . '/assets/img/demo/hero-premium.jpg';
        if ( is_readable( $hero ) ) {
            $url = get_stylesheet_directory_uri() . '/assets/img/demo/hero-premium.jpg';
            $profile['hero_image'] = $url;
            if ( ! empty( $profile['hero_slides'] ) && is_array( $profile['hero_slides'] ) ) {
                foreach ( $profile['hero_slides'] as $i => $slide ) if ( is_array( $slide ) ) $profile['hero_slides'][ $i ]['image'] = $url;
            }
        }
        return $profile;
    }
    add_filter( 'wp_theme_demo_profile', 'wpbb_child_v100_demo_profile', 3100 );
}
