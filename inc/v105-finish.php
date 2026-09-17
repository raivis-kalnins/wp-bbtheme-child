<?php
/** WP BBTheme sector suite 3.8.11.05 final legal/contact grid, mobile drawer and WooCommerce template finish. */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v105_body_classes' ) ) {
    function wpbb_child_v105_body_classes( $classes ) {
        if ( is_page() ) {
            $post = get_queried_object();
            if ( $post instanceof WP_Post ) {
                $probe = strtolower( (string) $post->post_name . ' ' . (string) get_the_title( $post ) . ' ' . (string) $post->post_content );
                if ( preg_match( '/privacy|terms|condition|cookie|cookies|legal|wp-theme-legal-section/i', $probe ) ) $classes[] = 'wpbb-legal-page';
                if ( preg_match( '/(^|[\s\/_-])contact([\s\/_-]|$)|wp-theme-contact-section|wpbb-jobs-contact-main/i', $probe ) ) $classes[] = 'wpbb-contact-page';
                if ( false !== stripos( $probe, 'wpbb-jobs-contact-main' ) ) $classes[] = 'wpbb-jobs-contact-page';
            }
        }
        return array_values( array_unique( $classes ) );
    }
    add_filter( 'body_class', 'wpbb_child_v105_body_classes', 10000 );
}

if ( ! function_exists( 'wpbb_child_v105_enqueue' ) ) {
    function wpbb_child_v105_enqueue() {
        $v = wp_get_theme()->get( 'Version' );

        /* v105 owns mobile navigation. Preserve v104 CSS, but remove its runtime
         * so Jobs cannot end up with two drawers/toggles competing for state. */
        wp_dequeue_script( 'wpbb-suite-v104' );

        wp_enqueue_style( 'wpbb-suite-v105', get_stylesheet_directory_uri() . '/assets/suite-v105.css', array( 'wpbb-suite-v104' ), $v );
        wp_enqueue_script( 'wpbb-suite-v105', get_stylesheet_directory_uri() . '/assets/suite-v105.js', array(), $v, true );

        $gallery = array();
        if ( function_exists( 'wpbb_child_381045_consistency_config' ) && function_exists( 'wpbb_child_381045_asset_url' ) ) {
            $cfg = wpbb_child_381045_consistency_config();
            foreach ( (array) ( $cfg['gallery'] ?? array() ) as $item ) {
                $relative = is_array( $item ) ? (string) ( $item[0] ?? '' ) : (string) $item;
                $title = is_array( $item ) ? (string) ( $item[1] ?? '' ) : '';
                $url = wpbb_child_381045_asset_url( $relative );
                if ( $url ) $gallery[] = array( 'url' => $url, 'title' => $title );
            }
        }
        wp_localize_script( 'wpbb-suite-v105', 'wpbbSuiteV101', array( 'gallery' => $gallery ) );
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v105_enqueue', 100500 );
}
