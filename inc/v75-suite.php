<?php
/**
 * v3.8.10.75 suite repair.
 * - forces the active sector media onto managed demo records immediately;
 * - refreshes managed Woo demo product thumbnails from this child theme;
 * - forces classic Woo shells after block-template resolution.
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v75_refresh_sector_media' ) ) {
    function wpbb_child_v75_refresh_sector_media() {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) return;
        $key = 'wpbb_child_v75_sector_media_' . sanitize_key( get_stylesheet() );
        if ( '3.8.10.75' === (string) get_option( $key ) ) return;
        if ( function_exists( 'wpbb_child_381045_consistency_config' ) ) {
            $config = wpbb_child_381045_consistency_config();
            if ( $config ) {
                if ( function_exists( 'wpbb_child_381045_sync_sector_media' ) ) wpbb_child_381045_sync_sector_media( $config );
                if ( function_exists( 'wpbb_child_381045_sync_blog_media' ) ) wpbb_child_381045_sync_blog_media( $config );
                if ( function_exists( 'wpbb_child_381045_repair_demo_pages' ) ) wpbb_child_381045_repair_demo_pages( $config );
            }
        }
        update_option( $key, '3.8.10.75', false );
    }
    add_action( 'admin_init', 'wpbb_child_v75_refresh_sector_media', 155 );
    add_action( 'wp_theme_after_demo_import', 'wpbb_child_v75_refresh_sector_media', 220 );
}

if ( ! function_exists( 'wpbb_child_v75_product_attachment' ) ) {
    function wpbb_child_v75_product_attachment( $source, $product_id ) {
        $source = (string) $source;
        $product_id = absint( $product_id );
        if ( ! $product_id || ! is_readable( $source ) ) return 0;
        $hash = hash_file( 'sha256', $source );
        $key = 'v75|' . sanitize_key( get_stylesheet() ) . '|' . $product_id . '|' . $hash;
        $found = get_posts( array(
            'post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => 1, 'fields' => 'ids',
            'meta_key' => '_wpbb_v75_product_source', 'meta_value' => $key,
        ) );
        if ( $found && is_readable( get_attached_file( $found[0] ) ) ) return absint( $found[0] );
        $uploads = wp_upload_dir();
        if ( ! empty( $uploads['error'] ) ) return 0;
        $dir = trailingslashit( $uploads['basedir'] ) . 'wpbb-sector-products/' . sanitize_file_name( get_stylesheet() );
        if ( ! wp_mkdir_p( $dir ) ) return 0;
        $ext = strtolower( pathinfo( $source, PATHINFO_EXTENSION ) );
        $base = sanitize_title( get_the_title( $product_id ) ) ?: 'product-' . $product_id;
        $target = trailingslashit( $dir ) . $base . '-' . substr( $hash, 0, 10 ) . '.' . $ext;
        if ( ! is_readable( $target ) && ! @copy( $source, $target ) ) return 0;
        $type = wp_check_filetype( $target );
        $attachment_id = wp_insert_attachment( array(
            'post_mime_type' => $type['type'] ?: 'image/jpeg',
            'post_title' => get_the_title( $product_id ),
            'post_status' => 'inherit',
        ), $target, $product_id );
        if ( is_wp_error( $attachment_id ) || ! $attachment_id ) return 0;
        if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) require_once ABSPATH . 'wp-admin/includes/image.php';
        $meta = wp_generate_attachment_metadata( $attachment_id, $target );
        if ( $meta ) wp_update_attachment_metadata( $attachment_id, $meta );
        update_post_meta( $attachment_id, '_wpbb_v75_product_source', $key );
        update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( get_the_title( $product_id ) ) );
        return absint( $attachment_id );
    }
}

if ( ! function_exists( 'wpbb_child_v75_refresh_woo_demo_products' ) ) {
    function wpbb_child_v75_refresh_woo_demo_products() {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) || ! post_type_exists( 'product' ) ) return;
        $key = 'wpbb_child_v75_products_' . sanitize_key( get_stylesheet() );
        if ( '3.8.10.75' === (string) get_option( $key ) ) return;
        $profile = function_exists( 'wp_theme_get_demo_profile' ) ? (array) wp_theme_get_demo_profile() : array();
        $profile_id = sanitize_key( (string) ( $profile['id'] ?? '' ) );

        if ( 'events' === $profile_id && function_exists( 'wpbb_events_sync_demo_ticket_thumbnails' ) ) {
            wpbb_events_sync_demo_ticket_thumbnails( true );
        }

        $products = apply_filters( 'wp_theme_woo_demo_product_data', array() );
        if ( is_array( $products ) ) {
            foreach ( array_values( $products ) as $index => $data ) {
                if ( ! is_array( $data ) || empty( $data[1] ) ) continue;
                $title = (string) $data[1];
                $post = get_page_by_path( sanitize_title( $title ), OBJECT, 'product' );
                if ( ! $post ) continue;
                $managed = 1 === (int) get_post_meta( $post->ID, '_wpbb_child_woo_demo_product', true )
                    || '1' === (string) get_post_meta( $post->ID, '_wp_theme_demo_generated', true )
                    || '' !== (string) get_post_meta( $post->ID, '_wp_theme_demo_product_profile', true )
                    || '' !== (string) get_post_meta( $post->ID, '_wpbb_events_demo_ticket_index', true );
                if ( ! $managed ) continue;
                $source = apply_filters( 'wp_theme_woo_demo_product_image_path', '', $data, $index, $profile );
                if ( $source && is_readable( $source ) ) {
                    $attachment_id = wpbb_child_v75_product_attachment( $source, $post->ID );
                    if ( $attachment_id ) {
                        set_post_thumbnail( $post->ID, $attachment_id );
                        if ( function_exists( 'wc_delete_product_transients' ) ) wc_delete_product_transients( $post->ID );
                    }
                }
                if ( $profile_id ) update_post_meta( $post->ID, '_wp_theme_demo_product_profile', $profile_id );
            }
        }
        update_option( $key, '3.8.10.75', false );
    }
    add_action( 'admin_init', 'wpbb_child_v75_refresh_woo_demo_products', 160 );
    add_action( 'wp_theme_after_demo_import', 'wpbb_child_v75_refresh_woo_demo_products', 225 );
}

if ( ! function_exists( 'wpbb_child_v75_force_woo_legacy_template' ) ) {
    function wpbb_child_v75_force_woo_legacy_template( $template ) {
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
    add_filter( 'template_include', 'wpbb_child_v75_force_woo_legacy_template', PHP_INT_MAX );
}
