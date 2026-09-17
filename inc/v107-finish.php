<?php
/** WP BBTheme sector suite 3.8.11.07 — final search, WooCommerce, Jobs captcha/grid and responsive repair. */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v107_is_jobs_theme' ) ) {
    function wpbb_child_v107_is_jobs_theme() {
        return false !== stripos( (string) get_stylesheet(), 'jobs' );
    }
}

if ( ! function_exists( 'wpbb_child_v107_body_classes' ) ) {
    function wpbb_child_v107_body_classes( $classes ) {
        $classes[] = 'wpbb-suite-v107';
        $classes[] = 'wpbb-sector-premium';
        if ( wpbb_child_v107_is_jobs_theme() ) {
            $classes[] = 'wpbb-jobs-theme';
            if ( is_front_page() || is_home() ) $classes[] = 'wpbb-jobs-marketplace-home';
        }
        if ( is_search() ) $classes[] = 'wpbb-search-page';
        if ( function_exists( 'is_woocommerce' ) ) {
            if ( is_woocommerce() || ( function_exists( 'is_cart' ) && is_cart() ) || ( function_exists( 'is_checkout' ) && is_checkout() ) || ( function_exists( 'is_account_page' ) && is_account_page() ) ) {
                $classes[] = 'wpbb-v107-woo-page';
            }
        }
        return array_values( array_unique( $classes ) );
    }
    add_filter( 'body_class', 'wpbb_child_v107_body_classes', 10020 );
}

if ( ! function_exists( 'wpbb_child_v107_enqueue' ) ) {
    function wpbb_child_v107_enqueue() {
        $v = wp_get_theme()->get( 'Version' );
        wp_enqueue_style( 'wpbb-suite-v107', get_stylesheet_directory_uri() . '/assets/suite-v107.css', array( 'wpbb-suite-v105' ), $v );
        wp_enqueue_script( 'wpbb-suite-v107', get_stylesheet_directory_uri() . '/assets/suite-v107.js', array(), $v, true );
        wp_localize_script( 'wpbb-suite-v107', 'wpbbSuiteV107', array(
            'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
            'nonce'     => wp_create_nonce( 'wpbb_v107_search' ),
            'action'    => 'wpbb_v107_search',
            'minChars'  => 2,
            'searching' => __( 'Searching…', 'wp-bbtheme-child' ),
            'noResults' => __( 'No matching results.', 'wp-bbtheme-child' ),
            'viewAll'   => __( 'View all results', 'wp-bbtheme-child' ),
        ) );
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v107_enqueue', 100700 );
}

if ( ! function_exists( 'wpbb_child_v107_search_post_types' ) ) {
    function wpbb_child_v107_search_post_types() {
        $types = get_post_types( array( 'public' => true, 'exclude_from_search' => false ), 'names' );
        unset( $types['attachment'] );
        $types = array_values( $types );
        return $types ? $types : array( 'post', 'page' );
    }
}

if ( ! function_exists( 'wpbb_child_v107_ajax_search' ) ) {
    function wpbb_child_v107_ajax_search() {
        check_ajax_referer( 'wpbb_v107_search', 'nonce' );
        $term     = isset( $_REQUEST['q'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['q'] ) ) : '';
        $location = isset( $_REQUEST['location'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['location'] ) ) : '';
        $term     = function_exists( 'mb_substr' ) ? mb_substr( $term, 0, 80 ) : substr( $term, 0, 80 );
        $location = function_exists( 'mb_substr' ) ? mb_substr( $location, 0, 80 ) : substr( $location, 0, 80 );
        $needle   = trim( $term );
        if ( strlen( $needle ) < 2 && strlen( trim( $location ) ) >= 2 ) $needle = trim( $location );
        if ( strlen( $needle ) < 2 ) wp_send_json_success( array( 'items' => array() ) );

        $query = new WP_Query( array(
            'post_type'              => wpbb_child_v107_search_post_types(),
            'post_status'            => 'publish',
            's'                      => $needle,
            'posts_per_page'         => 8,
            'ignore_sticky_posts'    => true,
            'no_found_rows'          => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ) );
        $items = array();
        foreach ( $query->posts as $post ) {
            $type    = get_post_type_object( $post->post_type );
            $excerpt = has_excerpt( $post ) ? $post->post_excerpt : wp_strip_all_tags( strip_shortcodes( (string) $post->post_content ) );
            $items[] = array(
                'title'   => get_the_title( $post ),
                'url'     => get_permalink( $post ),
                'type'    => $type && ! empty( $type->labels->singular_name ) ? $type->labels->singular_name : ucfirst( (string) $post->post_type ),
                'excerpt' => wp_trim_words( $excerpt, 15, '…' ),
                'thumb'   => (string) get_the_post_thumbnail_url( $post, 'thumbnail' ),
            );
        }
        wp_reset_postdata();
        wp_send_json_success( array( 'items' => $items ) );
    }
    add_action( 'wp_ajax_wpbb_v107_search', 'wpbb_child_v107_ajax_search' );
    add_action( 'wp_ajax_nopriv_wpbb_v107_search', 'wpbb_child_v107_ajax_search' );
}

/* Direct Woo renderers. They deliberately do not depend on the standard single-product
 * callback stack, because older compatibility layers in this suite can remove those
 * callbacks after WooCommerce registers them. */
if ( ! function_exists( 'wpbb_child_v107_render_product_media' ) ) {
    function wpbb_child_v107_render_product_media() {
        if ( function_exists( 'woocommerce_show_product_sale_flash' ) ) woocommerce_show_product_sale_flash();
        if ( function_exists( 'woocommerce_show_product_images' ) ) woocommerce_show_product_images();
    }
}
if ( ! function_exists( 'wpbb_child_v107_render_product_summary' ) ) {
    function wpbb_child_v107_render_product_summary() {
        foreach ( array(
            'woocommerce_template_single_title',
            'woocommerce_template_single_rating',
            'woocommerce_template_single_price',
            'woocommerce_template_single_excerpt',
            'woocommerce_template_single_add_to_cart',
            'woocommerce_template_single_meta',
            'woocommerce_template_single_sharing',
        ) as $callback ) {
            if ( function_exists( $callback ) ) call_user_func( $callback );
        }
    }
}
if ( ! function_exists( 'wpbb_child_v107_render_product_lower' ) ) {
    function wpbb_child_v107_render_product_lower() {
        foreach ( array(
            'woocommerce_output_product_data_tabs',
            'woocommerce_upsell_display',
            'woocommerce_output_related_products',
        ) as $callback ) {
            if ( function_exists( $callback ) ) call_user_func( $callback );
        }
    }
}

if ( ! function_exists( 'wpbb_child_v107_woo_gallery_columns' ) ) {
    function wpbb_child_v107_woo_gallery_columns() { return 4; }
    add_filter( 'woocommerce_product_thumbnails_columns', 'wpbb_child_v107_woo_gallery_columns', 1000 );
}
