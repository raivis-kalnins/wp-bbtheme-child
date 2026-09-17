<?php
/** WP BBTheme sector suite 3.8.10.99 finishing layer. */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v99_config' ) ) {
    function wpbb_child_v99_config() {
        $map = array(
            'wp-bbtheme-child-automotive'         => array( 'brand'=>'Automotive',        'accent'=>'#e64a3b', 'hero'=>'assets/img/demo/hero-premium.jpg' ),
            'wp-bbtheme-child-building-services' => array( 'brand'=>'Building Services', 'accent'=>'#1682f8', 'hero'=>'assets/img/demo/hero-premium.jpg' ),
            'wp-bbtheme-child-business'           => array( 'brand'=>'Business',          'accent'=>'#6f55e8', 'hero'=>'assets/img/demo/hero-premium.jpg' ),
            'wp-bbtheme-child-elearning'          => array( 'brand'=>'E-Learning',        'accent'=>'#8750ed', 'hero'=>'assets/img/demo/hero-premium.jpg' ),
            'wp-bbtheme-child-hotel'              => array( 'brand'=>'Hotel',             'accent'=>'#079a77', 'hero'=>'assets/img/demo/hero-premium.jpg' ),
            'wp-bbtheme-child-insurance'          => array( 'brand'=>'Insurance',         'accent'=>'#07966b', 'hero'=>'assets/img/demo/hero-premium.jpg' ),
            'wp-bbtheme-child-logistics'          => array( 'brand'=>'Logistics',         'accent'=>'#f47b12', 'hero'=>'assets/img/demo/hero-premium.jpg' ),
            'wp-bbtheme-child-medicine'           => array( 'brand'=>'Medicine',          'accent'=>'#078f6a', 'hero'=>'assets/img/demo/hero-premium.jpg' ),
            'wp-bbtheme-child-realestate'         => array( 'brand'=>'Real Estate',       'accent'=>'#15955b', 'hero'=>'assets/img/demo/hero-premium.jpg' ),
            'wp-bbtheme-child-restaurant'         => array( 'brand'=>'Restaurant',        'accent'=>'#ef4b32', 'hero'=>'assets/img/demo/hero-premium.jpg' ),
            'wp-bbtheme-child-travel'             => array( 'brand'=>'Travel Agency',     'accent'=>'#079d9a', 'hero'=>'assets/img/demo/hero-premium.jpg' ),
            'wp-bbtheme-child-woo-clouthes'       => array( 'brand'=>'Woo Clothes',       'accent'=>'#ed3d75', 'hero'=>'assets/img/demo/hero-premium.jpg' ),
            'wp-bbtheme-child-woo-events'         => array( 'brand'=>'Woo Events',        'accent'=>'#7a4df4', 'hero'=>'assets/img/demo/hero-premium.jpg' ),
            'wp-bbtheme-child-woo-tech-shop'      => array( 'brand'=>'Woo Tech Shop',     'accent'=>'#1682f8', 'hero'=>'assets/img/demo/hero-premium.jpg' ),
        );
        $stylesheet = get_stylesheet();
        return isset( $map[ $stylesheet ] ) ? $map[ $stylesheet ] : array();
    }
}

if ( ! function_exists( 'wpbb_child_v99_enqueue' ) ) {
    function wpbb_child_v99_enqueue() {
        $version = wp_get_theme()->get( 'Version' );
        $css = get_stylesheet_directory() . '/assets/suite-v99.css';
        $js  = get_stylesheet_directory() . '/assets/suite-v99.js';
        if ( is_readable( $css ) ) wp_enqueue_style( 'wpbb-suite-v99', get_stylesheet_directory_uri() . '/assets/suite-v99.css', array( 'wpbb-suite-v98' ), $version );
        if ( is_readable( $js ) ) wp_enqueue_script( 'wpbb-suite-v99', get_stylesheet_directory_uri() . '/assets/suite-v99.js', array(), $version, true );
        $cfg = wpbb_child_v99_config();
        if ( $cfg && wp_style_is( 'wpbb-suite-v99', 'enqueued' ) ) {
            $hero = trailingslashit( get_stylesheet_directory_uri() ) . ltrim( $cfg['hero'], '/' );
            $mark = trailingslashit( get_stylesheet_directory_uri() ) . 'assets/brand/mark.svg';
            $brand = str_replace( array( '"', "\n", "\r" ), '', (string) $cfg['brand'] );
            $css_vars = ':root{--suite-v99-accent:' . sanitize_hex_color( $cfg['accent'] ) . ';--suite-v99-hero:url("' . esc_url( $hero ) . '");--suite-v99-brand-mark:url("' . esc_url( $mark ) . '");--suite-v99-brand-label:"' . esc_attr( $brand ) . '";}';
            wp_add_inline_style( 'wpbb-suite-v99', $css_vars );
        }
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v99_enqueue', 1200 );
}

if ( ! function_exists( 'wpbb_child_v99_theme_color' ) ) {
    function wpbb_child_v99_theme_color( $color, $stylesheet = '' ) {
        $cfg = wpbb_child_v99_config();
        return $cfg ? $cfg['accent'] : $color;
    }
    add_filter( 'wp_theme_project_theme_color', 'wpbb_child_v99_theme_color', 99, 2 );
}

if ( ! function_exists( 'wpbb_child_v99_favicons' ) ) {
    function wpbb_child_v99_favicons() {
        $cfg = wpbb_child_v99_config(); if ( ! $cfg ) return;
        $base = trailingslashit( get_stylesheet_directory_uri() ) . 'assets/brand/';
        echo '<link rel="icon" type="image/png" sizes="32x32" href="' . esc_url( $base . 'favicon-32.png' ) . '">' . "\n";
        echo '<link rel="icon" type="image/png" sizes="192x192" href="' . esc_url( $base . 'favicon-192.png' ) . '">' . "\n";
        echo '<link rel="apple-touch-icon" sizes="180x180" href="' . esc_url( $base . 'apple-touch-icon.png' ) . '">' . "\n";
        echo '<meta name="theme-color" content="' . esc_attr( $cfg['accent'] ) . '">' . "\n";
    }
    add_action( 'wp_head', 'wpbb_child_v99_favicons', 999 );
}

if ( ! function_exists( 'wpbb_child_v99_profile' ) ) {
    function wpbb_child_v99_profile( $profile ) {
        $cfg = wpbb_child_v99_config(); if ( ! $cfg ) return $profile;
        $hero = trailingslashit( get_stylesheet_directory_uri() ) . ltrim( $cfg['hero'], '/' );
        $profile['hero_image'] = $hero;
        if ( ! empty( $profile['hero_slides'] ) && is_array( $profile['hero_slides'] ) ) {
            foreach ( $profile['hero_slides'] as $i => $slide ) if ( is_array( $slide ) ) $profile['hero_slides'][ $i ]['image'] = $hero;
        }
        if ( empty( $profile['palette'] ) || ! is_array( $profile['palette'] ) ) $profile['palette'] = array();
        $profile['palette']['theme_brand_color'] = $cfg['accent'];
        $profile['palette']['theme_accent_color'] = $cfg['accent'];
        $profile['palette']['theme_link_color'] = $cfg['accent'];
        return $profile;
    }
    add_filter( 'wp_theme_demo_profile', 'wpbb_child_v99_profile', 2000 );
}

/** Refresh only Starter Setup managed demo pages once after the 3.8.10.99 update. */
if ( ! function_exists( 'wpbb_child_v99_refresh_managed_demo' ) ) {
    function wpbb_child_v99_refresh_managed_demo() {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) return;
        $key = 'wpbb_child_v99_refresh_' . sanitize_key( get_stylesheet() );
        if ( '3.8.10.99' === (string) get_option( $key ) ) return;
        if ( function_exists( 'wpbb_child_v62_rebuild_demo_pages' ) ) wpbb_child_v62_rebuild_demo_pages( true );
        if ( function_exists( 'wpbb_child_381045_consistency_config' ) ) {
            $cfg = wpbb_child_381045_consistency_config();
            if ( $cfg ) {
                if ( function_exists( 'wpbb_child_381045_sync_sector_media' ) ) wpbb_child_381045_sync_sector_media( $cfg );
                if ( function_exists( 'wpbb_child_381045_sync_blog_media' ) ) wpbb_child_381045_sync_blog_media( $cfg );
                if ( function_exists( 'wpbb_child_381045_repair_demo_pages' ) ) wpbb_child_381045_repair_demo_pages( $cfg );
            }
        }
        update_option( $key, '3.8.10.99', false );
    }
    add_action( 'admin_init', 'wpbb_child_v99_refresh_managed_demo', 500 );
    add_action( 'wp_theme_after_demo_import', 'wpbb_child_v99_refresh_managed_demo', 500 );
}
