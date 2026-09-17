<?php
/** WP BBTheme child suite 3.8.11.10 — media, WooCommerce and managed-layout repair. */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v110_config' ) ) {
    function wpbb_child_v110_config() {
        $map = array(
            'wp-bbtheme-child-automotive'        => array( 'accent'=>'#E54B3B', 'hero'=>'assets/img/demo/hero-hq.jpg' ),
            'wp-bbtheme-child-building-services' => array( 'accent'=>'#1682F8', 'hero'=>'assets/img/demo/hero-hq.jpg' ),
            'wp-bbtheme-child-business'          => array( 'accent'=>'#7452E8', 'hero'=>'assets/img/demo/office-wide-hq.jpg' ),
            'wp-bbtheme-child-elearning'         => array( 'accent'=>'#8750ED', 'hero'=>'assets/img/demo/hero-hq.jpg' ),
            'wp-bbtheme-child-hotel'             => array( 'accent'=>'#079A77', 'hero'=>'assets/img/demo/hero-hq.jpg' ),
            'wp-bbtheme-child-insurance'         => array( 'accent'=>'#07966B', 'hero'=>'assets/img/demo/hero-hq.jpg' ),
            'wp-bbtheme-child-jobs'              => array( 'accent'=>'#0A9B78', 'hero'=>'assets/img/demo/jobs-hero-hq.jpg' ),
            'wp-bbtheme-child-logistics'         => array( 'accent'=>'#F47B12', 'hero'=>'assets/img/demo/hero-hq.jpg' ),
            'wp-bbtheme-child-medicine'          => array( 'accent'=>'#078F6A', 'hero'=>'assets/img/demo/hero-hq.jpg' ),
            'wp-bbtheme-child-realestate'        => array( 'accent'=>'#15955B', 'hero'=>'assets/img/demo/hero-hq.jpg' ),
            'wp-bbtheme-child-restaurant'        => array( 'accent'=>'#EF4B32', 'hero'=>'assets/img/demo/hero-hq.jpg' ),
            'wp-bbtheme-child-travel'            => array( 'accent'=>'#079D9A', 'hero'=>'assets/img/demo/hero-hq.jpg' ),
            'wp-bbtheme-child-woo-clouthes'      => array( 'accent'=>'#ED3D75', 'hero'=>'assets/img/demo/hero-hq.jpg' ),
            'wp-bbtheme-child-woo-events'        => array( 'accent'=>'#7A4DF4', 'hero'=>'assets/img/demo/hero-hq.jpg' ),
            'wp-bbtheme-child-woo-tech-shop'     => array( 'accent'=>'#1682F8', 'hero'=>'assets/img/demo/hero-hq.jpg' ),
        );
        $style = get_stylesheet();
        return $map[ $style ] ?? array( 'accent'=>'#6F55E8', 'hero'=>'assets/img/demo/hero-hq.jpg' );
    }
}

if ( ! function_exists( 'wpbb_child_v110_enqueue' ) ) {
    function wpbb_child_v110_enqueue() {
        $v = wp_get_theme()->get( 'Version' );
        wp_enqueue_style( 'wpbb-suite-v110', get_stylesheet_directory_uri() . '/assets/suite-v110.css', array(), $v );
        wp_enqueue_script( 'wpbb-suite-v110', get_stylesheet_directory_uri() . '/assets/suite-v110.js', array(), $v, true );
        $cfg = wpbb_child_v110_config();
        $accent = sanitize_hex_color( $cfg['accent'] ?? '' ) ?: '#6F55E8';
        $hero_rel = ltrim( (string) ( $cfg['hero'] ?? '' ), '/' );
        $hero_file = $hero_rel ? get_stylesheet_directory() . '/' . $hero_rel : '';
        $hero_url = $hero_file && is_readable( $hero_file ) ? get_stylesheet_directory_uri() . '/' . $hero_rel : '';
        $inline = ':root{--suite-v110-accent:' . $accent . ';--suite-v109-accent:' . $accent . ';--suite-v108-accent:' . $accent . ';--wp-theme-primary:' . $accent . ';--tfa-brand-color:' . $accent . ';--iws-primary:' . $accent . ';--iws-accent:' . $accent . ';}';
        if ( $hero_url ) $inline .= ':root{--suite-v110-hero:url("' . esc_url( $hero_url ) . '");--suite-v109-hero:url("' . esc_url( $hero_url ) . '");--suite-v99-hero:url("' . esc_url( $hero_url ) . '");}';
        wp_add_inline_style( 'wpbb-suite-v110', $inline );
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v110_enqueue', PHP_INT_MAX );
}

/* Print a tiny late critical layer after plugin inline CSS. */
if ( ! function_exists( 'wpbb_child_v110_critical_css' ) ) {
    function wpbb_child_v110_critical_css() {
        if ( is_admin() ) return;
        $accent = sanitize_hex_color( wpbb_child_v110_config()['accent'] ?? '' ) ?: '#6F55E8';
        echo '<style id="wpbb-suite-v110-critical">:root{--suite-v110-accent:' . esc_html( $accent ) . ';--iws-primary:' . esc_html( $accent ) . ';--iws-accent:' . esc_html( $accent ) . ';--tfa-brand-color:' . esc_html( $accent ) . '}html body #wp-theme-main .iws-filter-submit,html body #wp-theme-main .iws-product-filter button[type=submit],html body #wp-theme-main .iws-filter-wrapper button[type=submit]{background:' . esc_html( $accent ) . '!important;border-color:' . esc_html( $accent ) . '!important;color:#fff!important}html body #wp-theme-main .iws-range-slider input[type=range]{accent-color:' . esc_html( $accent ) . '!important}html body #wp-theme-main .iws-filter-toggle input[type=checkbox]{accent-color:' . esc_html( $accent ) . '!important}</style>';
    }
    add_action( 'wp_head', 'wpbb_child_v110_critical_css', PHP_INT_MAX );
}

if ( ! function_exists( 'wpbb_child_v110_expected_attachment_slug' ) ) {
    function wpbb_child_v110_expected_attachment_slug( $relative ) {
        $config = function_exists( 'wpbb_child_381045_consistency_config' ) ? wpbb_child_381045_consistency_config() : array();
        $relative = function_exists( 'wpbb_child_381045_valid_asset' ) ? wpbb_child_381045_valid_asset( $relative ) : ltrim( (string) $relative, '/' );
        if ( ! $config || ! $relative ) return '';
        $stem = sanitize_title( str_replace( array( 'assets/img/', '/', '\\' ), array( '', '-', '-' ), pathinfo( $relative, PATHINFO_DIRNAME ) . '-' . pathinfo( $relative, PATHINFO_FILENAME ) ) );
        return 'wpbb-381045-' . sanitize_title( $config['key'] ) . '-' . $stem;
    }
}

if ( ! function_exists( 'wpbb_child_v110_attachment_needs_metadata' ) ) {
    function wpbb_child_v110_attachment_needs_metadata( $attachment_id ) {
        $file = get_attached_file( $attachment_id );
        if ( ! $file || ! is_readable( $file ) || filesize( $file ) < 1 ) return true;
        $meta = wp_get_attachment_metadata( $attachment_id );
        if ( ! is_array( $meta ) || empty( $meta['file'] ) ) return true;
        $uploads = wp_upload_dir();
        $attached_relative = ltrim( str_replace( wp_normalize_path( trailingslashit( $uploads['basedir'] ) ), '', wp_normalize_path( $file ) ), '/' );
        if ( $attached_relative && wp_normalize_path( $attached_relative ) !== wp_normalize_path( (string) $meta['file'] ) ) return true;
        foreach ( (array) ( $meta['sizes'] ?? array() ) as $size ) {
            if ( empty( $size['file'] ) ) continue;
            $candidate = trailingslashit( dirname( $file ) ) . basename( (string) $size['file'] );
            if ( ! is_readable( $candidate ) || filesize( $candidate ) < 1 ) return true;
        }
        return false;
    }
}

if ( ! function_exists( 'wpbb_child_v110_regenerate_attachment' ) ) {
    function wpbb_child_v110_regenerate_attachment( $attachment_id, $force = false ) {
        $attachment_id = absint( $attachment_id );
        if ( ! $attachment_id ) return false;
        $file = get_attached_file( $attachment_id );
        if ( ! $file || ! is_readable( $file ) || filesize( $file ) < 1 ) return false;
        if ( ! $force && ! wpbb_child_v110_attachment_needs_metadata( $attachment_id ) ) return true;
        if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) require_once ABSPATH . 'wp-admin/includes/image.php';
        $meta = wp_generate_attachment_metadata( $attachment_id, $file );
        if ( is_array( $meta ) && $meta ) wp_update_attachment_metadata( $attachment_id, $meta );
        $type = wp_check_filetype( $file );
        if ( ! empty( $type['type'] ) ) wp_update_post( array( 'ID'=>$attachment_id, 'post_mime_type'=>$type['type'] ) );
        clean_attachment_cache( $attachment_id );
        return is_array( $meta ) && ! empty( $meta );
    }
}

if ( ! function_exists( 'wpbb_child_v110_import_asset' ) ) {
    function wpbb_child_v110_import_asset( $relative, $title = '' ) {
        if ( ! function_exists( 'wpbb_child_381045_import_attachment' ) ) return 0;
        $id = absint( wpbb_child_381045_import_attachment( $relative, $title ) );
        if ( $id ) wpbb_child_v110_regenerate_attachment( $id, false );
        return $id;
    }
}

if ( ! function_exists( 'wpbb_child_v110_attachment_url' ) ) {
    function wpbb_child_v110_attachment_url( $relative, $title = '' ) {
        $id = wpbb_child_v110_import_asset( $relative, $title );
        return $id ? ( wp_get_attachment_image_url( $id, 'full' ) ?: '' ) : '';
    }
}

/* Future managed rebuilds use stable Media Library originals, not theme-file URLs. */
if ( ! function_exists( 'wpbb_child_v110_demo_profile' ) ) {
    function wpbb_child_v110_demo_profile( $profile ) {
        if ( ! is_array( $profile ) ) $profile = array();
        $cfg = wpbb_child_v110_config();
        $hero_rel = (string) ( $cfg['hero'] ?? '' );
        $hero = '';
        if ( is_admin() || doing_action( 'wp_theme_after_demo_import' ) ) $hero = wpbb_child_v110_attachment_url( $hero_rel, (string) ( $profile['name'] ?? 'Theme' ) . ' hero' );
        if ( ! $hero && $hero_rel && is_readable( get_stylesheet_directory() . '/' . $hero_rel ) ) $hero = get_stylesheet_directory_uri() . '/' . ltrim( $hero_rel, '/' );
        if ( $hero ) {
            $profile['hero_image'] = $hero;
            if ( ! empty( $profile['hero_slides'] ) && is_array( $profile['hero_slides'] ) ) {
                foreach ( $profile['hero_slides'] as $i => $slide ) if ( is_array( $slide ) ) $profile['hero_slides'][ $i ]['image'] = $hero;
            }
        }
        if ( function_exists( 'wpbb_child_381045_consistency_config' ) ) {
            $consistency = wpbb_child_381045_consistency_config();
            $about_rel = (string) ( $consistency['about'] ?? '' );
            if ( $about_rel && ( is_admin() || doing_action( 'wp_theme_after_demo_import' ) ) ) {
                $about = wpbb_child_v110_attachment_url( $about_rel, (string) ( $profile['name'] ?? 'Theme' ) . ' about image' );
                if ( $about ) $profile['about_image'] = $about;
            }
        }
        return $profile;
    }
    add_filter( 'wp_theme_demo_profile', 'wpbb_child_v110_demo_profile', PHP_INT_MAX );
}

if ( ! function_exists( 'wpbb_child_v110_asset_map' ) ) {
    function wpbb_child_v110_asset_map() {
        $config = function_exists( 'wpbb_child_381045_consistency_config' ) ? wpbb_child_381045_consistency_config() : array();
        if ( ! $config ) return array();
        $map = array();
        $add = static function( $relative, $title = '' ) use ( &$map ) {
            $relative = function_exists( 'wpbb_child_381045_valid_asset' ) ? wpbb_child_381045_valid_asset( $relative ) : '';
            if ( ! $relative || isset( $map[ $relative ] ) ) return;
            $url = wpbb_child_v110_attachment_url( $relative, $title );
            if ( $url ) $map[ $relative ] = $url;
        };
        $add( (string) ( $config['about'] ?? '' ), (string) ( $config['label'] ?? '' ) . ' about image' );
        foreach ( (array) ( $config['gallery'] ?? array() ) as $item ) $add( is_array( $item ) ? (string) ( $item[0] ?? '' ) : (string) $item, is_array( $item ) ? (string) ( $item[1] ?? '' ) : '' );
        foreach ( (array) ( $config['sector'] ?? array() ) as $sector ) foreach ( (array) ( $sector['pool'] ?? array() ) as $i => $relative ) $add( (string) $relative, (string) ( $config['label'] ?? '' ) . ' item ' . ( $i + 1 ) );
        $v110 = wpbb_child_v110_config();
        $add( (string) ( $v110['hero'] ?? '' ), (string) ( $config['label'] ?? '' ) . ' hero' );
        return $map;
    }
}

if ( ! function_exists( 'wpbb_child_v110_repair_page_media' ) ) {
    function wpbb_child_v110_repair_page_media( $map ) {
        if ( ! $map ) return;
        $config = function_exists( 'wpbb_child_381045_consistency_config' ) ? wpbb_child_381045_consistency_config() : array();
        $about_rel = (string) ( $config['about'] ?? '' );
        $about_url = $map[ $about_rel ] ?? '';
        $hero_rel = (string) ( wpbb_child_v110_config()['hero'] ?? '' );
        $hero_url = $map[ $hero_rel ] ?? '';
        $pages = get_posts( array( 'post_type'=>'page','post_status'=>array('publish','draft','private','pending'),'posts_per_page'=>-1,'fields'=>'ids','no_found_rows'=>true ) );
        foreach ( $pages as $page_id ) {
            $content = (string) get_post_field( 'post_content', $page_id, 'raw' );
            if ( '' === $content ) continue;
            $new = $content;
            foreach ( $map as $relative => $url ) {
                $base = preg_quote( basename( $relative ), '~' );
                $stem = preg_quote( pathinfo( basename( $relative ), PATHINFO_FILENAME ), '~' );
                $new = (string) preg_replace( '~https?://[^\\s\"\'<>)]*/wp-content/themes/wp-bbtheme-child-[^/\\s\"\'<>)]*/' . preg_quote( $relative, '~' ) . '~i', esc_url( $url ), $new );
                $new = (string) preg_replace( '~https?://[^\\s\"\'<>)]*/wp-content/uploads/(?:[^/]+/)*wpbb-(?:sector-media|[a-z0-9-]+)/(?:[^\\s\"\'<>)]*/)?' . $stem . '(?:-[0-9]+x[0-9]+)?\\.[a-z0-9]+~i', esc_url( $url ), $new );
            }
            if ( $about_url && false !== strpos( $new, 'wp-theme-about-page-intro' ) ) {
                $new = (string) preg_replace( '~(<figure\\b[^>]*class="[^"]*(?:wp-theme-sector-media-text__media|wp-theme-about-page-intro__media)[^"]*"[^>]*>.*?<img\\b[^>]*\\bsrc=")[^"]+("[^>]*>)~is', '$1' . esc_url( $about_url ) . '$2', $new, 1 );
            }
            if ( $hero_url && false !== strpos( $new, 'wp-theme-sector-hero' ) ) {
                $new = (string) preg_replace_callback( '~<!--\\s+wp:wpbb/swiper\\s+(\\{.*?\\})\\s+/-->~s', static function( $m ) use ( $hero_url ) {
                    $attrs = json_decode( $m[1], true );
                    if ( ! is_array( $attrs ) || 'hero' !== sanitize_key( (string) ( $attrs['demoStyle'] ?? '' ) ) ) return $m[0];
                    if ( ! empty( $attrs['slides'] ) && is_array( $attrs['slides'] ) ) foreach ( $attrs['slides'] as &$slide ) if ( is_array( $slide ) ) $slide['image'] = $hero_url;
                    if ( ! empty( $attrs['slidesJson'] ) && is_string( $attrs['slidesJson'] ) ) {
                        $slides = json_decode( $attrs['slidesJson'], true );
                        if ( is_array( $slides ) ) { foreach ( $slides as &$slide ) if ( is_array( $slide ) ) $slide['image'] = $hero_url; $attrs['slidesJson'] = wp_json_encode( $slides, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); }
                    }
                    return '<!-- wp:wpbb/swiper ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ' /-->';
                }, $new );
            }
            if ( $new !== $content ) { wp_update_post( array( 'ID'=>$page_id, 'post_content'=>$new ) ); clean_post_cache( $page_id ); }
            update_post_meta( $page_id, '_wpbb_child_v110_media', '3.8.11.10' );
        }
    }
}

if ( ! function_exists( 'wpbb_child_v110_repair_automotive_directory' ) ) {
    function wpbb_child_v110_repair_automotive_directory() {
        if ( 'wp-bbtheme-child-automotive' !== get_stylesheet() || ! post_type_exists( 'vehicle' ) ) return;
        if ( function_exists( 'wpbb_automotive_seed_directory' ) ) wpbb_automotive_seed_directory( array( 'id'=>'automotive' ) );
        $asset_ids = array();
        for ( $i=1; $i<=6; $i++ ) {
            $source = get_stylesheet_directory() . '/assets/img/demo/item-' . $i . '.jpg';
            if ( ! is_readable( $source ) ) continue;
            $id = function_exists( 'wpbb_automotive_demo_attachment' ) ? absint( wpbb_automotive_demo_attachment( 'item-' . $i . '.jpg', 'Automotive item ' . $i ) ) : 0;
            if ( $id ) { wpbb_child_v110_regenerate_attachment( $id, false ); $asset_ids[ $i - 1 ] = $id; }
        }
        if ( ! $asset_ids ) return;
        $posts = get_posts( array( 'post_type'=>'vehicle','post_status'=>array('publish','draft','private','pending'),'posts_per_page'=>-1,'orderby'=>array('menu_order'=>'ASC','ID'=>'ASC'),'fields'=>'ids','no_found_rows'=>true ) );
        foreach ( $posts as $post_id ) {
            $source_id = absint( get_post_meta( $post_id, '_wp_theme_demo_translation_source', true ) );
            $order = $source_id ? (int) get_post_field( 'menu_order', $source_id ) : (int) get_post_field( 'menu_order', $post_id );
            $index = ( ( $order % 6 ) + 6 ) % 6;
            if ( empty( $asset_ids[ $index ] ) ) continue;
            $primary = $asset_ids[ $index ];
            set_post_thumbnail( $post_id, $primary );
            $gallery = array( $primary );
            for ( $step=1; $step<6 && count( $gallery )<5; $step++ ) {
                $candidate = $asset_ids[ ( $index + $step ) % 6 ] ?? 0;
                if ( $candidate && ! in_array( $candidate, $gallery, true ) ) $gallery[] = $candidate;
            }
            update_post_meta( $post_id, '_wpbb_child_gallery_ids', $gallery );
            update_post_meta( $post_id, '_wp_theme_item_gallery_ids', $gallery );
            update_post_meta( $post_id, '_wp_theme_gallery_ids', implode( ',', $gallery ) );
            update_post_meta( $post_id, '_wp_theme_item_gallery', implode( ',', $gallery ) );
            update_post_meta( $post_id, '_wpbb_child_gallery_version', '3.8.11.10' );
            clean_post_cache( $post_id );
        }
    }
}

if ( ! function_exists( 'wpbb_child_v110_repair_once' ) ) {
    function wpbb_child_v110_repair_once() {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) || wp_doing_ajax() ) return;
        $key = 'wpbb_child_v110_repair_' . sanitize_key( get_stylesheet() );
        if ( '3.8.11.10' === (string) get_option( $key ) ) return;

        $map = wpbb_child_v110_asset_map();
        wpbb_child_v110_repair_automotive_directory();
        if ( function_exists( 'wpbb_child_381045_sync_sector_media' ) && function_exists( 'wpbb_child_381045_consistency_config' ) ) wpbb_child_381045_sync_sector_media( wpbb_child_381045_consistency_config() );
        if ( function_exists( 'wpbb_child_v62_rebuild_demo_pages' ) ) wpbb_child_v62_rebuild_demo_pages( true );
        wpbb_child_v110_repair_page_media( $map );
        update_option( $key, '3.8.11.10', false );
    }
    add_action( 'admin_init', 'wpbb_child_v110_repair_once', 100950 );
    add_action( 'wp_theme_after_demo_import', 'wpbb_child_v110_repair_once', 100950 );
}
