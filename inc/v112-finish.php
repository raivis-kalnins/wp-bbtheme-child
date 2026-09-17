<?php
/** WP BBTheme child suite 3.8.11.12 — child-only editorial grid, hero clarity, media recovery and Woo account aliases. */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v112_config' ) ) {
    function wpbb_child_v112_config() {
        $accents = array(
            'wp-bbtheme-child-automotive'=>'#E54B3B','wp-bbtheme-child-building-services'=>'#1682F8','wp-bbtheme-child-business'=>'#7452E8',
            'wp-bbtheme-child-elearning'=>'#8750ED','wp-bbtheme-child-hotel'=>'#079A77','wp-bbtheme-child-insurance'=>'#07966B',
            'wp-bbtheme-child-jobs'=>'#0A9B78','wp-bbtheme-child-logistics'=>'#F47B12','wp-bbtheme-child-medicine'=>'#078F6A',
            'wp-bbtheme-child-realestate'=>'#15955B','wp-bbtheme-child-restaurant'=>'#EF4B32','wp-bbtheme-child-travel'=>'#079D9A',
            'wp-bbtheme-child-woo-clouthes'=>'#ED3D75','wp-bbtheme-child-woo-events'=>'#7A4DF4','wp-bbtheme-child-woo-tech-shop'=>'#1682F8',
        );
        $hero_files = array(
            'wp-bbtheme-child-business' => 'assets/img/demo/office-wide.jpg',
            'wp-bbtheme-child-jobs' => 'assets/img/demo/jobs-hero-premium.jpg',
        );
        $positions = array(
            'wp-bbtheme-child-business'=>'right center','wp-bbtheme-child-building-services'=>'right center','wp-bbtheme-child-elearning'=>'right center',
            'wp-bbtheme-child-hotel'=>'right center','wp-bbtheme-child-automotive'=>'right center',
        );
        $style = get_stylesheet();
        return array(
            'accent' => $accents[ $style ] ?? '#6F55E8',
            'hero' => $hero_files[ $style ] ?? 'assets/img/demo/hero-premium.jpg',
            'position' => $positions[ $style ] ?? 'right center',
        );
    }
}

if ( ! function_exists( 'wpbb_child_v112_enqueue' ) ) {
    function wpbb_child_v112_enqueue() {
        $version = wp_get_theme()->get( 'Version' );
        wp_enqueue_style( 'wpbb-suite-v112', get_stylesheet_directory_uri() . '/assets/suite-v112.css', array( 'wpbb-suite-v111' ), $version );
        $cfg = wpbb_child_v112_config();
        $file = get_stylesheet_directory() . '/' . ltrim( $cfg['hero'], '/' );
        $url = is_readable( $file ) ? get_stylesheet_directory_uri() . '/' . ltrim( $cfg['hero'], '/' ) : '';
        $inline = ':root{--suite-v112-accent:' . esc_attr( $cfg['accent'] ) . ';--suite-v112-hero-position:' . esc_attr( $cfg['position'] ) . ';}';
        if ( $url ) $inline .= ':root{--suite-v112-hero:url("' . esc_url( $url ) . '");}';
        wp_add_inline_style( 'wpbb-suite-v112', $inline );
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v112_enqueue', PHP_INT_MAX );
}

/* Future imports use the sharper child-owned source without changing parent or plugins. */
if ( ! function_exists( 'wpbb_child_v112_demo_profile' ) ) {
    function wpbb_child_v112_demo_profile( $profile ) {
        if ( ! is_array( $profile ) ) $profile = array();
        $cfg = wpbb_child_v112_config();
        $relative = ltrim( (string) $cfg['hero'], '/' );
        $url = '';
        if ( ( is_admin() || doing_action( 'wp_theme_after_demo_import' ) ) && function_exists( 'wpbb_child_v110_attachment_url' ) ) {
            $url = (string) wpbb_child_v110_attachment_url( $relative, (string) ( $profile['name'] ?? wp_get_theme()->get( 'Name' ) ) . ' hero' );
        }
        if ( ! $url && is_readable( get_stylesheet_directory() . '/' . $relative ) ) $url = get_stylesheet_directory_uri() . '/' . $relative;
        if ( $url ) {
            $profile['hero_image'] = $url;
            if ( ! empty( $profile['hero_slides'] ) && is_array( $profile['hero_slides'] ) ) {
                foreach ( $profile['hero_slides'] as $i => $slide ) if ( is_array( $slide ) ) $profile['hero_slides'][ $i ]['image'] = $url;
            }
        }
        return $profile;
    }
    add_filter( 'wp_theme_demo_profile', 'wpbb_child_v112_demo_profile', PHP_INT_MAX );
}

if ( ! function_exists( 'wpbb_child_v112_repair_hero_pages' ) ) {
    function wpbb_child_v112_repair_hero_pages() {
        $cfg = wpbb_child_v112_config();
        $relative = ltrim( (string) $cfg['hero'], '/' );
        $hero_url = function_exists( 'wpbb_child_v110_attachment_url' ) ? (string) wpbb_child_v110_attachment_url( $relative, wp_get_theme()->get( 'Name' ) . ' hero' ) : '';
        if ( ! $hero_url && is_readable( get_stylesheet_directory() . '/' . $relative ) ) $hero_url = get_stylesheet_directory_uri() . '/' . $relative;
        if ( ! $hero_url ) return;
        $pages = get_posts( array( 'post_type'=>'page','post_status'=>array('publish','draft','private','pending'),'posts_per_page'=>-1,'fields'=>'ids','no_found_rows'=>true ) );
        foreach ( $pages as $page_id ) {
            $content = (string) get_post_field( 'post_content', $page_id, 'raw' );
            if ( '' === $content || false === strpos( $content, 'wpbb/swiper' ) ) continue;
            $updated = (string) preg_replace_callback(
                '~<!--\\s+wp:wpbb/swiper\\s+(\\{.*?\\})\\s+/-->~s',
                static function( $match ) use ( $hero_url ) {
                    $attrs = json_decode( $match[1], true );
                    if ( ! is_array( $attrs ) || 'hero' !== sanitize_key( (string) ( $attrs['demoStyle'] ?? '' ) ) ) return $match[0];
                    if ( ! empty( $attrs['slides'] ) && is_array( $attrs['slides'] ) ) foreach ( $attrs['slides'] as $i => $slide ) if ( is_array( $slide ) ) $attrs['slides'][ $i ]['image'] = $hero_url;
                    if ( ! empty( $attrs['slidesJson'] ) && is_string( $attrs['slidesJson'] ) ) {
                        $slides = json_decode( $attrs['slidesJson'], true );
                        if ( is_array( $slides ) ) {
                            foreach ( $slides as $i => $slide ) if ( is_array( $slide ) ) $slides[ $i ]['image'] = $hero_url;
                            $attrs['slidesJson'] = wp_json_encode( $slides, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
                        }
                    }
                    return '<!-- wp:wpbb/swiper ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ' /-->';
                },
                $content
            );
            if ( $updated !== $content ) { wp_update_post( array( 'ID'=>$page_id, 'post_content'=>$updated ) ); clean_post_cache( $page_id ); }
            update_post_meta( $page_id, '_wpbb_child_v112_media', '3.8.11.12' );
        }
    }
}

/* If a cloned/imported site has the original attachment record but missing generated image sizes,
   regenerate the metadata after the child asset has been restored. */
if ( ! function_exists( 'wpbb_child_v112_regenerate_managed_attachment' ) ) {
    function wpbb_child_v112_regenerate_managed_attachment( $attachment_id ) {
        $attachment_id = absint( $attachment_id );
        if ( ! $attachment_id ) return;
        $file = get_attached_file( $attachment_id );
        if ( ! $file || ! is_file( $file ) ) return;
        $meta = wp_get_attachment_metadata( $attachment_id );
        $needs = ! is_array( $meta ) || empty( $meta['width'] ) || empty( $meta['height'] );
        if ( ! $needs && ! empty( $meta['sizes'] ) && is_array( $meta['sizes'] ) ) {
            $dir = dirname( $file );
            foreach ( $meta['sizes'] as $size ) {
                $name = is_array( $size ) ? (string) ( $size['file'] ?? '' ) : '';
                if ( $name && ! is_file( trailingslashit( $dir ) . $name ) ) { $needs = true; break; }
            }
        }
        if ( ! $needs ) return;
        if ( function_exists( 'wpbb_child_381048_load_media_admin_api' ) ) wpbb_child_381048_load_media_admin_api();
        if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
            $include = ABSPATH . 'wp-admin/includes/image.php';
            if ( is_readable( $include ) ) require_once $include;
        }
        if ( function_exists( 'wp_generate_attachment_metadata' ) ) {
            $fresh = wp_generate_attachment_metadata( $attachment_id, $file );
            if ( $fresh ) wp_update_attachment_metadata( $attachment_id, $fresh );
            clean_attachment_cache( $attachment_id );
        }
    }
}

if ( ! function_exists( 'wpbb_child_v112_repair_media' ) ) {
    function wpbb_child_v112_repair_media() {
        if ( ! function_exists( 'wpbb_child_381045_consistency_config' ) ) return;
        $config = wpbb_child_381045_consistency_config();
        if ( ! $config ) return;
        if ( function_exists( 'wpbb_child_381045_sync_sector_media' ) ) wpbb_child_381045_sync_sector_media( $config );
        if ( function_exists( 'wpbb_child_381045_sync_blog_media' ) ) wpbb_child_381045_sync_blog_media( $config );

        $attachments = get_posts( array(
            'post_type'=>'attachment','post_status'=>'inherit','posts_per_page'=>-1,'fields'=>'ids','no_found_rows'=>true,
            'meta_query'=>array( array( 'key'=>'_wpbb_child_source_relative', 'compare'=>'EXISTS' ) ),
        ) );
        foreach ( $attachments as $attachment_id ) {
            $relative = (string) get_post_meta( $attachment_id, '_wpbb_child_source_relative', true );
            if ( $relative && is_readable( get_stylesheet_directory() . '/' . ltrim( $relative, '/' ) ) ) wpbb_child_v112_regenerate_managed_attachment( $attachment_id );
        }
    }
}

/* WooCommerce endpoints are not standalone Pages. Older demo menus linked to /orders/, /downloads/ etc.
   Keep those child-theme-only legacy URLs working by redirecting them to the canonical My Account endpoint. */
if ( ! function_exists( 'wpbb_child_v112_account_aliases' ) ) {
    function wpbb_child_v112_account_aliases() {
        return array( 'orders', 'downloads', 'edit-address', 'edit-account' );
    }
}
if ( ! function_exists( 'wpbb_child_v112_account_rewrites' ) ) {
    function wpbb_child_v112_account_rewrites() {
        if ( ! class_exists( 'WooCommerce' ) && ! function_exists( 'WC' ) ) return;
        foreach ( wpbb_child_v112_account_aliases() as $endpoint ) {
            add_rewrite_rule( '^' . preg_quote( $endpoint, '~' ) . '/?$', 'index.php?wpbb_wc_account_alias=' . $endpoint, 'top' );
        }
    }
    add_action( 'init', 'wpbb_child_v112_account_rewrites', 99 );
}
if ( ! function_exists( 'wpbb_child_v112_account_query_var' ) ) {
    function wpbb_child_v112_account_query_var( $vars ) { $vars[] = 'wpbb_wc_account_alias'; return $vars; }
    add_filter( 'query_vars', 'wpbb_child_v112_account_query_var' );
}
if ( ! function_exists( 'wpbb_child_v112_myaccount_base' ) ) {
    function wpbb_child_v112_myaccount_base() {
        $id = absint( get_option( 'woocommerce_myaccount_page_id' ) );
        if ( $id && 'publish' === get_post_status( $id ) ) return get_permalink( $id );
        $page = get_page_by_path( 'my-account' );
        return $page ? get_permalink( $page ) : home_url( '/my-account/' );
    }
}
if ( ! function_exists( 'wpbb_child_v112_account_alias_redirect' ) ) {
    function wpbb_child_v112_account_alias_redirect() {
        if ( is_admin() || wp_doing_ajax() ) return;
        $endpoint = sanitize_key( (string) get_query_var( 'wpbb_wc_account_alias' ) );
        if ( ! in_array( $endpoint, wpbb_child_v112_account_aliases(), true ) ) {
            $path = trim( (string) wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ), '/' );
            $tail = sanitize_key( basename( $path ) );
            if ( in_array( $tail, wpbb_child_v112_account_aliases(), true ) ) $endpoint = $tail;
        }
        if ( ! in_array( $endpoint, wpbb_child_v112_account_aliases(), true ) ) return;
        $target = function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( $endpoint ) : '';
        if ( ! $target ) {
            $base = wpbb_child_v112_myaccount_base();
            if ( ! $base ) return;
            $target = trailingslashit( $base ) . $endpoint . '/';
        }
        wp_safe_redirect( $target, 301 );
        exit;
    }
    add_action( 'template_redirect', 'wpbb_child_v112_account_alias_redirect', 1 );
}

if ( ! function_exists( 'wpbb_child_v112_repair_once' ) ) {
    function wpbb_child_v112_repair_once() {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) || wp_doing_ajax() ) return;
        $key = 'wpbb_child_v112_repair_' . sanitize_key( get_stylesheet() );
        if ( '3.8.11.12' === (string) get_option( $key ) ) return;
        wpbb_child_v112_repair_media();
        wpbb_child_v112_repair_hero_pages();
        if ( class_exists( 'WooCommerce' ) || function_exists( 'WC' ) ) flush_rewrite_rules( false );
        update_option( $key, '3.8.11.12', false );
    }
    add_action( 'admin_init', 'wpbb_child_v112_repair_once', 100970 );
    add_action( 'wp_theme_after_demo_import', 'wpbb_child_v112_repair_once', 100970 );
}
