<?php
/**
 * Admin interaction performance guard.
 *
 * Legacy demo/media migration callbacks are useful after activation/import, but
 * they must not run while an administrator is saving Theme Settings or running
 * a WordPress theme update/upload request. Those callbacks can perform broad
 * post/attachment scans and make an otherwise small settings save appear hung.
 *
 * This guard does not change the parent theme. It only suppresses child-owned
 * one-time maintenance callbacks for the current interactive request. They can
 * still run on a normal wp-admin page load, and their explicit demo-import hooks
 * remain untouched.
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_admin_is_interactive_request' ) ) {
    function wpbb_child_admin_is_interactive_request() {
        if ( ! is_admin() ) return false;
        if ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) return true;

        $method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) ) : 'GET';
        if ( 'POST' === $method || 'PUT' === $method || 'PATCH' === $method || 'DELETE' === $method ) return true;

        global $pagenow;
        $screen = is_string( $pagenow ) ? $pagenow : '';
        $page   = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
        $action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : '';

        if ( 'wp-theme-settings' === $page ) return true;
        if ( in_array( $screen, array( 'update.php', 'update-core.php', 'theme-install.php' ), true ) ) return true;
        if ( 'themes.php' === $screen && in_array( $action, array( 'update', 'upgrade', 'upload-theme', 'delete' ), true ) ) return true;

        return false;
    }
}

if ( ! function_exists( 'wpbb_child_admin_pause_legacy_maintenance' ) ) {
    function wpbb_child_admin_pause_legacy_maintenance() {
        if ( ! wpbb_child_admin_is_interactive_request() ) return;

        $callbacks = array(
            'wpbb_automotive_realistic_media_upgrade_v381041',
            'wpbb_building_services_realistic_media_upgrade_v381041',
            'wpbb_business_migrate_default_palette_v382',
            'wpbb_business_realistic_media_upgrade_v381041',
            'wpbb_business_repair_existing_lv_demo_content',
            'wpbb_child_381042_repair_demo_page_widths_once',
            'wpbb_child_381043_refresh_media_once',
            'wpbb_child_backfill_yoast_page_descriptions',
            'wpbb_child_v62_rebuild_demo_pages',
            'wpbb_child_v74_sync_commerce_demo',
            'wpbb_child_v75_refresh_sector_media',
            'wpbb_child_v75_refresh_woo_demo_products',
            'wpbb_elearning_realistic_media_upgrade_v381041',
            'wpbb_events_schedule_demo_seed',
            'wpbb_hotel_realistic_media_upgrade_v381041',
            'wpbb_insurance_realistic_media_upgrade_v381041',
            'wpbb_jobs_maybe_refresh_rewrites_v71',
            'wpbb_jobs_v80_sync_managed_translations',
            'wpbb_logistics_realistic_media_upgrade_v381041',
            'wpbb_medicine_realistic_media_upgrade_v381041',
            'wpbb_realestate_realistic_media_upgrade_v381041',
            'wpbb_restaurant_realistic_media_upgrade_v381041',
            'wpbb_travel_realistic_media_upgrade_v381041',
            'wpbb_woo_clouthes_realistic_media_upgrade_v381041',
            'wpbb_woo_tech_shop_realistic_media_upgrade_v381041',
        );

        global $wp_filter;
        if ( empty( $wp_filter['admin_init'] ) || ! is_object( $wp_filter['admin_init'] ) || empty( $wp_filter['admin_init']->callbacks ) ) return;

        foreach ( $wp_filter['admin_init']->callbacks as $priority => $items ) {
            foreach ( (array) $items as $item ) {
                $function = isset( $item['function'] ) ? $item['function'] : null;
                if ( ! is_string( $function ) || ! in_array( $function, $callbacks, true ) ) continue;
                remove_action( 'admin_init', $function, (int) $priority );
            }
        }
    }
}
add_action( 'admin_init', 'wpbb_child_admin_pause_legacy_maintenance', -999 );
