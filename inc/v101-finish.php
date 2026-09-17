<?php
/** WP BBTheme sector suite 3.8.11.03 navigation, legal, colour and media correction. */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v101_body_classes' ) ) {
    function wpbb_child_v101_body_classes( $classes ) {
        if ( is_singular( 'page' ) ) {
            $id = get_queried_object_id();
            $slug = (string) get_post_field( 'post_name', $id );
            $title = (string) get_the_title( $id );
            if ( preg_match( '/privacy|terms|condition|cookie|cookies|legal/i', $slug . ' ' . $title ) ) $classes[] = 'wpbb-legal-page';
            if ( false !== stripos( $slug . ' ' . $title, 'contact' ) ) $classes[] = 'wpbb-contact-page';
        }
        if ( is_singular( 'event' ) ) $classes[] = 'wpbb-v101-single-event';
        return array_values( array_unique( $classes ) );
    }
    add_filter( 'body_class', 'wpbb_child_v101_body_classes', 999 );
}

if ( ! function_exists( 'wpbb_child_v101_enqueue' ) ) {
    function wpbb_child_v101_enqueue() {
        $v = wp_get_theme()->get( 'Version' );
        wp_enqueue_style( 'wpbb-suite-v101', get_stylesheet_directory_uri() . '/assets/suite-v101.css', array( 'wpbb-suite-v100' ), $v );
        wp_enqueue_script( 'wpbb-suite-v101', get_stylesheet_directory_uri() . '/assets/suite-v101.js', array(), $v, true );
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
        wp_localize_script( 'wpbb-suite-v101', 'wpbbSuiteV101', array( 'gallery' => $gallery ) );
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v101_enqueue', 2500 );
}

/* Refresh Starter Setup managed demo pages once so stored blocks receive the current image mapping. */
if ( ! function_exists( 'wpbb_child_v101_refresh_managed_demo' ) ) {
    function wpbb_child_v101_refresh_managed_demo() {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) return;
        $key = 'wpbb_child_v101_refresh_' . sanitize_key( get_stylesheet() );
        if ( '3.8.11.03' === (string) get_option( $key ) ) return;
        if ( function_exists( 'wpbb_child_v62_rebuild_demo_pages' ) ) wpbb_child_v62_rebuild_demo_pages( true );
        if ( function_exists( 'wpbb_child_381045_consistency_config' ) ) {
            $cfg = wpbb_child_381045_consistency_config();
            if ( $cfg && function_exists( 'wpbb_child_381045_repair_demo_pages' ) ) wpbb_child_381045_repair_demo_pages( $cfg );
        }
        update_option( $key, '3.8.11.03', false );
    }
    add_action( 'admin_init', 'wpbb_child_v101_refresh_managed_demo', 1300 );
    add_action( 'wp_theme_after_demo_import', 'wpbb_child_v101_refresh_managed_demo', 1300 );
}
