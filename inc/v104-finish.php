<?php
/** WP BBTheme sector suite 3.8.11.04 deterministic navigation and WooCommerce/alignment finish. */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v104_enqueue' ) ) {
    function wpbb_child_v104_enqueue() {
        $v = wp_get_theme()->get( 'Version' );

        /* These legacy child scripts each owned the same mobile trigger.  Keep
         * their CSS, but remove the competing JavaScript and let v104 be the
         * single navigation owner. v104 also preserves their cookie/gallery jobs. */
        foreach ( array( 'wpbb-child-sector-v97', 'wpbb-suite-v98', 'wpbb-suite-v99', 'wpbb-suite-v100', 'wpbb-suite-v101' ) as $handle ) {
            wp_dequeue_script( $handle );
        }

        wp_enqueue_style( 'wpbb-suite-v104', get_stylesheet_directory_uri() . '/assets/suite-v104.css', array(), $v );
        wp_enqueue_script( 'wpbb-suite-v104', get_stylesheet_directory_uri() . '/assets/suite-v104.js', array(), $v, true );

        /* Reuse v101's local gallery payload shape so the replacement runtime
         * can hydrate home/single galleries without loading the old nav script. */
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
        wp_localize_script( 'wpbb-suite-v104', 'wpbbSuiteV101', array( 'gallery' => $gallery ) );
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v104_enqueue', 100000 );
}
