<?php
/** WP BBTheme sector suite 3.8.11.09 — media, WooCommerce and account finalisation. */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v109_config' ) ) {
    function wpbb_child_v109_config() {
        $map = array(
            'wp-bbtheme-child-automotive'         => array( 'accent'=>'#C63D2F', 'hero'=>'assets/img/demo/hero-premium.jpg' ),
            'wp-bbtheme-child-building-services' => array( 'accent'=>'#1682F8', 'hero'=>'assets/img/demo/hero-premium.jpg' ),
            'wp-bbtheme-child-business'           => array( 'accent'=>'#6F55E8', 'hero'=>'assets/img/demo/office-wide.jpg' ),
            'wp-bbtheme-child-elearning'          => array( 'accent'=>'#8750ED', 'hero'=>'assets/img/demo/hero-premium.jpg' ),
            'wp-bbtheme-child-hotel'              => array( 'accent'=>'#079A77', 'hero'=>'assets/img/demo/hero-premium.jpg' ),
            'wp-bbtheme-child-insurance'          => array( 'accent'=>'#07966B', 'hero'=>'assets/img/demo/hero-premium.jpg' ),
            'wp-bbtheme-child-jobs'               => array( 'accent'=>'#0A9B78', 'hero'=>'assets/img/demo/jobs-hero-premium.jpg' ),
            'wp-bbtheme-child-logistics'          => array( 'accent'=>'#F47B12', 'hero'=>'assets/img/demo/hero-premium.jpg' ),
            'wp-bbtheme-child-medicine'           => array( 'accent'=>'#078F6A', 'hero'=>'assets/img/demo/hero-premium.jpg' ),
            'wp-bbtheme-child-realestate'         => array( 'accent'=>'#15955B', 'hero'=>'assets/img/demo/hero-premium.jpg' ),
            'wp-bbtheme-child-restaurant'         => array( 'accent'=>'#EF4B32', 'hero'=>'assets/img/demo/hero-premium.jpg' ),
            'wp-bbtheme-child-travel'             => array( 'accent'=>'#079D9A', 'hero'=>'assets/img/demo/hero-premium.jpg' ),
            'wp-bbtheme-child-woo-clouthes'       => array( 'accent'=>'#ED3D75', 'hero'=>'assets/img/demo/hero-premium.jpg' ),
            'wp-bbtheme-child-woo-events'         => array( 'accent'=>'#7A4DF4', 'hero'=>'assets/img/demo/hero-premium.jpg' ),
            'wp-bbtheme-child-woo-tech-shop'      => array( 'accent'=>'#1682F8', 'hero'=>'assets/img/demo/hero-premium.jpg' ),
        );
        $stylesheet = get_stylesheet();
        return isset( $map[ $stylesheet ] ) ? $map[ $stylesheet ] : array( 'accent'=>'#6F55E8', 'hero'=>'assets/img/demo/hero-premium.jpg' );
    }
}

if ( ! function_exists( 'wpbb_child_v109_enqueue' ) ) {
    function wpbb_child_v109_enqueue() {
        $v = wp_get_theme()->get( 'Version' );
        wp_enqueue_style( 'wpbb-suite-v109', get_stylesheet_directory_uri() . '/assets/suite-v109.css', array( 'wpbb-suite-v108' ), $v );
        $cfg = wpbb_child_v109_config();
        $accent = sanitize_hex_color( $cfg['accent'] ?? '' ) ?: '#6F55E8';
        $hero_file = get_stylesheet_directory() . '/' . ltrim( (string) ( $cfg['hero'] ?? '' ), '/' );
        $hero_url = is_readable( $hero_file ) ? get_stylesheet_directory_uri() . '/' . ltrim( (string) $cfg['hero'], '/' ) : '';
        $inline = ':root{--suite-v109-accent:' . $accent . ';--wp-theme-primary:' . $accent . ';--tfa-brand-color:' . $accent . ';}';
        if ( $hero_url ) $inline .= ':root{--suite-v109-hero:url("' . esc_url( $hero_url ) . '");--suite-v99-hero:url("' . esc_url( $hero_url ) . '");}';
        wp_add_inline_style( 'wpbb-suite-v109', $inline );
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v109_enqueue', 100900 );
}

/* Always provide the sharpest bundled hero to Starter Setup and managed page rebuilds. */
if ( ! function_exists( 'wpbb_child_v109_demo_profile' ) ) {
    function wpbb_child_v109_demo_profile( $profile ) {
        $cfg = wpbb_child_v109_config();
        $rel = ltrim( (string) ( $cfg['hero'] ?? '' ), '/' );
        $file = $rel ? get_stylesheet_directory() . '/' . $rel : '';
        if ( $file && is_readable( $file ) ) {
            $url = get_stylesheet_directory_uri() . '/' . $rel;
            $profile['hero_image'] = $url;
            if ( ! empty( $profile['hero_slides'] ) && is_array( $profile['hero_slides'] ) ) {
                foreach ( $profile['hero_slides'] as $i => $slide ) {
                    if ( is_array( $slide ) ) $profile['hero_slides'][ $i ]['image'] = $url;
                }
            }
        }
        if ( empty( $profile['palette'] ) || ! is_array( $profile['palette'] ) ) $profile['palette'] = array();
        $accent = sanitize_hex_color( $cfg['accent'] ?? '' );
        if ( $accent ) {
            $profile['palette']['theme_brand_color'] = $accent;
            $profile['palette']['theme_accent_color'] = $accent;
            $profile['palette']['theme_link_color'] = $accent;
        }
        return $profile;
    }
    add_filter( 'wp_theme_demo_profile', 'wpbb_child_v109_demo_profile', PHP_INT_MAX );
}

/* Repair a local demo attachment when the DB entry exists but its uploads file is missing. */
if ( ! function_exists( 'wpbb_child_v109_local_attachment' ) ) {
    function wpbb_child_v109_local_attachment( $source, $title ) {
        if ( ! is_string( $source ) || ! is_readable( $source ) ) return 0;
        $slug = sanitize_title( 'demo-' . $title );
        $existing = get_posts( array( 'post_type'=>'attachment', 'name'=>$slug, 'post_status'=>'inherit', 'posts_per_page'=>1, 'fields'=>'ids' ) );
        $id = $existing ? (int) $existing[0] : 0;
        if ( $id ) {
            $attached = get_attached_file( $id );
            if ( $attached && is_readable( $attached ) && filesize( $attached ) > 0 ) return $id;
        }
        $upload = wp_upload_dir();
        if ( ! empty( $upload['error'] ) ) return 0;
        $ext = strtolower( (string) pathinfo( $source, PATHINFO_EXTENSION ) );
        $ext = in_array( $ext, array( 'jpg','jpeg','png','webp' ), true ) ? $ext : 'jpg';
        $filename = wp_unique_filename( $upload['path'], $slug . '.' . $ext );
        $target = trailingslashit( $upload['path'] ) . $filename;
        if ( ! copy( $source, $target ) ) return 0;
        $filetype = wp_check_filetype( $target );
        if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) require_once ABSPATH . 'wp-admin/includes/image.php';
        if ( $id ) {
            update_attached_file( $id, $target );
            wp_update_post( array( 'ID'=>$id, 'post_mime_type'=>$filetype['type'] ?: 'image/jpeg', 'guid'=>trailingslashit( $upload['url'] ) . $filename ) );
        } else {
            $id = wp_insert_attachment( array( 'post_mime_type'=>$filetype['type'] ?: 'image/jpeg', 'post_title'=>$title, 'post_name'=>$slug, 'post_status'=>'inherit', 'guid'=>trailingslashit( $upload['url'] ) . $filename ), $target );
            if ( is_wp_error( $id ) ) return 0;
        }
        $meta = wp_generate_attachment_metadata( $id, $target );
        if ( $meta ) wp_update_attachment_metadata( $id, $meta );
        update_post_meta( $id, '_wp_attachment_image_alt', $title );
        return (int) $id;
    }
}

/* Re-attach bundled product media to already-imported demo products.
 * Return false when Woo Support is not available yet so a later plugin install
 * can still trigger the repair instead of permanently marking it complete. */
if ( ! function_exists( 'wpbb_child_v109_repair_demo_product_media' ) ) {
    function wpbb_child_v109_repair_demo_product_media() {
        if ( ! function_exists( 'wpbb_child_woo_demo_product_data' ) || ! function_exists( 'wc_get_product' ) ) return false;
        $profile = function_exists( 'wp_theme_get_demo_profile' ) ? wp_theme_get_demo_profile() : array();
        foreach ( (array) wpbb_child_woo_demo_product_data() as $i => $data ) {
            if ( ! is_array( $data ) || empty( $data[1] ) ) continue;
            $name = (string) $data[1];
            $source = apply_filters( 'wp_theme_woo_demo_product_image_path', '', $data, $i, $profile );
            if ( ! $source || ! is_readable( $source ) ) continue;
            $post = get_page_by_path( sanitize_title( $name ), OBJECT, 'product' );
            if ( ! $post ) {
                $matches = get_posts( array( 'post_type'=>'product', 'post_status'=>'any', 'posts_per_page'=>1, 'title'=>$name ) );
                $post = $matches ? $matches[0] : null;
            }
            if ( ! $post ) continue;
            $image_id = wpbb_child_v109_local_attachment( $source, $name );
            if ( ! $image_id ) continue;
            $product = wc_get_product( $post->ID );
            if ( $product ) {
                $product->set_image_id( $image_id );
                $product->save();
            } else {
                set_post_thumbnail( $post->ID, $image_id );
            }
        }
        if ( function_exists( 'wc_delete_product_transients' ) ) wc_delete_product_transients();
        return true;
    }
}

if ( ! function_exists( 'wpbb_child_v109_repair_demo_product_media_once' ) ) {
    function wpbb_child_v109_repair_demo_product_media_once() {
        if ( is_admin() && ! current_user_can( 'manage_options' ) ) return;
        $key = 'wpbb_child_v109_product_media_' . sanitize_key( get_stylesheet() );
        if ( '3.8.11.09' === (string) get_option( $key ) ) return;
        if ( wpbb_child_v109_repair_demo_product_media() ) update_option( $key, '3.8.11.09', false );
    }
    add_action( 'admin_init', 'wpbb_child_v109_repair_demo_product_media_once', 100910 );
    add_action( 'wp_theme_after_demo_import', 'wpbb_child_v109_repair_demo_product_media_once', 100910 );
}

/* One managed refresh after installing 3.8.11.09: rebuild managed hero/content
 * and re-seed Automotive directory media. Product media has its own retryable
 * one-time repair above because Woo Support may be installed after the theme. */
if ( ! function_exists( 'wpbb_child_v109_refresh_once' ) ) {
    function wpbb_child_v109_refresh_once( $page_id = 0, $profile = array() ) {
        if ( is_admin() && ! current_user_can( 'manage_options' ) ) return;
        $key = 'wpbb_child_v109_refresh_' . sanitize_key( get_stylesheet() );
        if ( '3.8.11.09' === (string) get_option( $key ) ) return;
        if ( function_exists( 'wpbb_child_v62_rebuild_demo_pages' ) ) wpbb_child_v62_rebuild_demo_pages( true );
        if ( 'wp-bbtheme-child-automotive' === get_stylesheet() && function_exists( 'wpbb_automotive_seed_directory' ) ) {
            $p = function_exists( 'wp_theme_get_demo_profile' ) ? wp_theme_get_demo_profile() : array( 'id'=>'automotive' );
            wpbb_automotive_seed_directory( $p );
        }
        update_option( $key, '3.8.11.09', false );
    }
    add_action( 'admin_init', 'wpbb_child_v109_refresh_once', 100900 );
    add_action( 'wp_theme_after_demo_import', 'wpbb_child_v109_refresh_once', 100900, 2 );
}
