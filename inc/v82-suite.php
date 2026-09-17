<?php
/**
 * Sector suite 3.8.10.82 consistency/cleanup layer.
 *
 * Keeps all non-Jobs sector children on one visual system, makes BBuilder
 * presentation defaults inherit the active sector palette, and cleans only
 * theme-managed demo revisions/auto-drafts after a canonical repair pass.
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v82_enqueue_consistency' ) ) {
    function wpbb_child_v82_enqueue_consistency() {
        $version = wp_get_theme()->get( 'Version' );
        wp_enqueue_style(
            'wpbb-child-sector-v82',
            get_stylesheet_directory_uri() . '/assets/sector-v82.css',
            array(),
            $version
        );
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v82_enqueue_consistency', 220 );
}

if ( ! function_exists( 'wpbb_child_v82_active_accent' ) ) {
    function wpbb_child_v82_active_accent() {
        $profile = function_exists( 'wp_theme_get_demo_profile' ) ? (array) wp_theme_get_demo_profile() : array();
        $candidate = (string) ( $profile['palette']['theme_brand_color'] ?? '' );
        $candidate = sanitize_hex_color( $candidate );
        if ( $candidate ) return $candidate;

        $map = array(
            'wp-bbtheme-child-automotive'       => '#C63D2F',
            'wp-bbtheme-child-building-services'=> '#1F4F62',
            'wp-bbtheme-child-business'         => '#4B5563',
            'wp-bbtheme-child-elearning'        => '#4C45C6',
            'wp-bbtheme-child-hotel'            => '#253E5B',
            'wp-bbtheme-child-insurance'        => '#1D4ED8',
            'wp-bbtheme-child-logistics'        => '#E47B25',
            'wp-bbtheme-child-medicine'         => '#176B87',
            'wp-bbtheme-child-realestate'       => '#315C52',
            'wp-bbtheme-child-restaurant'       => '#6D2E2E',
            'wp-bbtheme-child-travel'           => '#185B57',
            'wp-bbtheme-child-woo-clouthes'     => '#8A5A14',
            'wp-bbtheme-child-woo-events'       => '#4B3FCE',
            'wp-bbtheme-child-woo-tech-shop'    => '#2563EB',
        );
        $stylesheet = get_stylesheet();
        return $map[ $stylesheet ] ?? '#334155';
    }
}

if ( ! function_exists( 'wpbb_child_v82_bbuilder_palette' ) ) {
    function wpbb_child_v82_bbuilder_palette( $value, $key = '' ) {
        $accent = wpbb_child_v82_active_accent();
        $map = array(
            'default_button_bg'          => $accent,
            'default_button_text'        => '#ffffff',
            'default_label_color'        => '#243447',
            'default_input_border_color' => '#cfdcdf',
            'cookie_button_bg'           => $accent,
            'cookie_button_text'         => '#ffffff',
        );
        return array_key_exists( $key, $map ) ? $map[ $key ] : $value;
    }
    add_filter( 'wpbb_option', 'wpbb_child_v82_bbuilder_palette', 40, 2 );
}

if ( ! function_exists( 'wpbb_child_v82_cleanup_managed_demo' ) ) {
    function wpbb_child_v82_cleanup_managed_demo() {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) return;
        $option = 'wpbb_child_v82_cleanup_' . sanitize_key( get_stylesheet() );
        if ( '3.8.10.82' === (string) get_option( $option ) ) return;

        // First use the existing canonical sector repair/media functions. They
        // already limit themselves to demo-managed records.
        if ( function_exists( 'wpbb_child_381045_consistency_config' ) ) {
            $config = wpbb_child_381045_consistency_config();
            if ( $config ) {
                if ( function_exists( 'wpbb_child_381045_sync_sector_media' ) ) wpbb_child_381045_sync_sector_media( $config );
                if ( function_exists( 'wpbb_child_381045_sync_blog_media' ) ) wpbb_child_381045_sync_blog_media( $config );
                if ( function_exists( 'wpbb_child_381045_repair_demo_pages' ) ) wpbb_child_381045_repair_demo_pages( $config );
            }
        }

        // Prune stale revisions only for pages explicitly owned by the demo.
        $managed = get_posts( array(
            'post_type'      => 'page',
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_key'       => '_wp_theme_demo_managed',
            'meta_value'     => '1',
        ) );
        foreach ( $managed as $page_id ) {
            foreach ( wp_get_post_revisions( $page_id, array( 'posts_per_page' => -1 ) ) as $revision ) {
                wp_delete_post_revision( $revision->ID );
            }
        }

        // Remove only generated auto-drafts. Never touch published/editor pages.
        $auto_drafts = get_posts( array(
            'post_type'      => array( 'page', 'post' ),
            'post_status'    => 'auto-draft',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => array(
                'relation' => 'OR',
                array( 'key' => '_wp_theme_demo_managed', 'value' => '1' ),
                array( 'key' => '_wp_theme_demo_generated', 'value' => '1' ),
            ),
        ) );
        foreach ( $auto_drafts as $post_id ) wp_delete_post( $post_id, true );

        update_option( $option, '3.8.10.82', false );
    }
    add_action( 'admin_init', 'wpbb_child_v82_cleanup_managed_demo', 230 );
    add_action( 'wp_theme_after_demo_import', 'wpbb_child_v82_cleanup_managed_demo', 260 );
}
