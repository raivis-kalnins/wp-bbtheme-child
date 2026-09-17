<?php
/** WP BBTheme sector suite 3.8.11.08 — WooCommerce layout/polish and packaging finish. */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v108_body_classes' ) ) {
    function wpbb_child_v108_body_classes( $classes ) {
        $classes[] = 'wpbb-suite-v108';
        if ( function_exists( 'is_woocommerce' ) && ( is_woocommerce() || ( function_exists( 'is_cart' ) && is_cart() ) || ( function_exists( 'is_checkout' ) && is_checkout() ) || ( function_exists( 'is_account_page' ) && is_account_page() ) ) ) {
            $classes[] = 'wpbb-v108-woo-page';
        }
        return array_values( array_unique( $classes ) );
    }
    add_filter( 'body_class', 'wpbb_child_v108_body_classes', 10080 );
}

if ( ! function_exists( 'wpbb_child_v108_enqueue' ) ) {
    function wpbb_child_v108_enqueue() {
        $v = wp_get_theme()->get( 'Version' );
        wp_enqueue_style( 'wpbb-suite-v108', get_stylesheet_directory_uri() . '/assets/suite-v108.css', array( 'wpbb-suite-v107' ), $v );
        wp_enqueue_script( 'wpbb-suite-v108', get_stylesheet_directory_uri() . '/assets/suite-v108.js', array(), $v, true );
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v108_enqueue', 100800 );
}

/* Final WooCommerce route owner: use the child-owned shells whenever WooCommerce is active. */
if ( ! function_exists( 'wpbb_child_v108_force_woo_template' ) ) {
    function wpbb_child_v108_force_woo_template( $template ) {
        if ( is_admin() || wp_doing_ajax() || is_feed() || ! post_type_exists( 'product' ) ) return $template;
        $base = trailingslashit( get_stylesheet_directory() ) . 'woocommerce-legacy/';
        $candidate = '';
        if ( ( function_exists( 'is_product' ) && is_product() ) || is_singular( 'product' ) ) $candidate = 'product.php';
        elseif ( function_exists( 'is_cart' ) && is_cart() ) $candidate = 'cart.php';
        elseif ( function_exists( 'is_checkout' ) && is_checkout() ) $candidate = 'checkout.php';
        elseif ( function_exists( 'is_account_page' ) && is_account_page() ) $candidate = 'account.php';
        elseif ( ( function_exists( 'is_shop' ) && is_shop() ) || ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() ) ) $candidate = 'catalog.php';
        return $candidate && is_readable( $base . $candidate ) ? $base . $candidate : $template;
    }
    add_filter( 'template_include', 'wpbb_child_v108_force_woo_template', PHP_INT_MAX );
}
