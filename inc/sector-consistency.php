<?php
/**
 * Cross-theme presentation and sector-media repair layer.
 *
 * Version 3.8.10.74 keeps demo content aligned after child-theme switching,
 * restores the active sector's bundled media, and provides the richer hotel
 * room gallery used on cards and single-room pages.
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_381048_load_media_admin_api' ) ) {
    /** Load WordPress media helpers safely for cron, admin-post and frontend-triggered repair requests. */
    function wpbb_child_381048_load_media_admin_api() {
        if ( function_exists( 'wp_generate_attachment_metadata' ) ) return true;
        foreach ( array( 'file.php', 'media.php', 'image.php' ) as $include ) {
            $path = ABSPATH . 'wp-admin/includes/' . $include;
            if ( is_readable( $path ) ) require_once $path;
        }
        return function_exists( 'wp_generate_attachment_metadata' );
    }
}

if ( ! function_exists( 'wpbb_child_381048_generate_attachment_metadata' ) ) {
    /** Never let a missing admin include terminate a sector media-repair batch. */
    function wpbb_child_381048_generate_attachment_metadata( $attachment_id, $file ) {
        if ( ! wpbb_child_381048_load_media_admin_api() ) return array();
        return wp_generate_attachment_metadata( absint( $attachment_id ), (string) $file );
    }
}

/**
 * Disable the older synchronous media refreshes registered by 3.8.10.41-45.
 * Those routines can import and regenerate many images during one admin page
 * request, which is unsafe on modest PHP memory/time limits. The resumable
 * worker below replaces them and keeps the dashboard responsive.
 */
if ( ! function_exists( 'wpbb_child_381046_disable_legacy_media_migrations' ) ) {
    function wpbb_child_381046_disable_legacy_media_migrations() {
        remove_action( 'admin_init', 'wpbb_child_381043_refresh_media_once', 130 );
        remove_action( 'wp_theme_after_demo_import', 'wpbb_child_381043_refresh_media_once', 180 );
        $defined = get_defined_functions();
        foreach ( (array) ( $defined['user'] ?? array() ) as $function_name ) {
            if ( preg_match( '/^wpbb_[a-z0-9_]+_realistic_media_upgrade_v381041$/', (string) $function_name ) ) {
                remove_action( 'admin_init', $function_name, 120 );
            }
        }
    }
}
wpbb_child_381046_disable_legacy_media_migrations();

if ( ! function_exists( 'wpbb_child_381045_consistency_config' ) ) {
    function wpbb_child_381045_consistency_config() {
        $stylesheet = sanitize_key( get_stylesheet() );
        $blog = array(
            'assets/img/blog/blog-1.jpg',
            'assets/img/blog/blog-2.jpg',
            'assets/img/blog/blog-3.jpg',
            'assets/img/blog/blog-4.jpg',
            'assets/img/blog/blog-5.jpg',
            'assets/img/blog/blog-6.jpg',
        );

        $configs = array(
            'wp-bbtheme-child-automotive' => array(
                'key' => 'automotive',
                'label' => 'Automotive',
                'hero' => 'assets/img/demo/hero-hq.jpg',
                'about' => 'assets/img/demo/about.jpg',
                'gallery' => array(
                    array( 'assets/img/demo/item-1.jpg', 'New vehicle exterior' ),
                    array( 'assets/img/demo/item-2.jpg', 'Used vehicle selection' ),
                    array( 'assets/img/demo/item-3.jpg', 'Rental vehicle' ),
                    array( 'assets/img/demo/item-4.jpg', 'Vehicle showroom' ),
                ),
                'blog' => $blog,
                'sector' => array(
                    array(
                        'post_type' => 'vehicle',
                        'demo_meta' => '_wp_theme_demo_vehicle',
                        'pool' => array(
                            'assets/img/demo/item-1.jpg', 'assets/img/demo/item-2.jpg', 'assets/img/demo/item-3.jpg',
                            'assets/img/demo/item-4.jpg', 'assets/img/demo/item-5.jpg', 'assets/img/demo/item-6.jpg',
                        ),
                        'rich_gallery' => true,
                        'gallery_count' => 5,
                    ),
                ),
            ),
            'wp-bbtheme-child-building-services' => array(
                'key' => 'building-services',
                'label' => 'Building services',
                'hero' => 'assets/img/demo/hero-hq.jpg',
                'about' => 'assets/img/demo/about-photo.jpg',
                'gallery' => array(
                    array( 'assets/img/demo/item-1.jpg', 'Electrical service' ),
                    array( 'assets/img/demo/item-2.jpg', 'Plumbing service' ),
                    array( 'assets/img/demo/item-3.jpg', 'Building development' ),
                    array( 'assets/img/demo/item-4.jpg', 'Property maintenance' ),
                ),
                'blog' => $blog,
                'sector' => array(
                    array(
                        'post_type' => 'trade_service',
                        'demo_meta' => '_wp_theme_demo_trade_service',
                        'pool' => array(
                            'assets/img/demo/item-1.jpg', 'assets/img/demo/item-2.jpg', 'assets/img/demo/item-3.jpg',
                            'assets/img/demo/item-4.jpg', 'assets/img/demo/item-5.jpg', 'assets/img/demo/item-6.jpg',
                        ),
                        'rich_gallery' => true,
                        'gallery_count' => 5,
                    ),
                ),
            ),
            'wp-bbtheme-child-business' => array(
                'key' => 'business',
                'label' => 'Business',
                'hero' => 'assets/img/demo/office-wide-hq.jpg',
                'about' => 'assets/img/demo/office-detail.jpg',
                'gallery' => array(
                    array( 'assets/img/demo/office-wide.jpg', 'Studio workspace' ),
                    array( 'assets/img/demo/office-detail.jpg', 'Team workspace' ),
                    array( 'assets/img/demo/office-planning.jpg', 'Project planning' ),
                    array( 'assets/img/demo/office-studio.jpg', 'Creative studio' ),
                ),
                'blog' => $blog,
                'sector' => array(),
            ),
            'wp-bbtheme-child-elearning' => array(
                'key' => 'elearning',
                'label' => 'E-Learning',
                'hero' => 'assets/img/demo/hero-hq.jpg',
                'about' => 'assets/img/demo/about-photo.jpg',
                'gallery' => array(
                    array( 'assets/img/demo/item-1.jpg', 'Online course lesson' ),
                    array( 'assets/img/demo/item-2.jpg', 'Learning materials' ),
                    array( 'assets/img/demo/item-3.jpg', 'Student workspace' ),
                    array( 'assets/img/demo/item-4.jpg', 'Course study session' ),
                ),
                'blog' => $blog,
                'sector' => array(
                    array(
                        'post_type' => 'course',
                        'demo_meta' => '_wp_theme_demo_course',
                        'pool' => array(
                            'assets/img/demo/item-1.jpg', 'assets/img/demo/item-2.jpg', 'assets/img/demo/item-3.jpg',
                            'assets/img/demo/item-4.jpg', 'assets/img/demo/item-5.jpg', 'assets/img/demo/item-6.jpg',
                        ),
                        'rich_gallery' => true,
                        'gallery_count' => 5,
                    ),
                ),
            ),
            'wp-bbtheme-child-hotel' => array(
                'key' => 'hotel',
                'label' => 'Hotel',
                'hero' => 'assets/img/demo/hero-hq.jpg',
                'about' => 'assets/img/demo/about-photo.jpg',
                'gallery' => array(
                    array( 'assets/img/demo/hero-photo.jpg', 'Hotel arrival' ),
                    array( 'assets/img/demo/item-1.jpg', 'Garden King room' ),
                    array( 'assets/img/demo/item-2.jpg', 'City Suite living area' ),
                    array( 'assets/img/demo/item-3.jpg', 'Family room' ),
                    array( 'assets/img/demo/item-4.jpg', 'Courtyard Twin room' ),
                    array( 'assets/img/demo/item-5.jpg', 'Terrace Studio' ),
                    array( 'assets/img/demo/item-6.jpg', 'Accessible Queen room' ),
                    array( 'assets/img/demo/about.jpg', 'Hotel breakfast terrace' ),
                    array( 'assets/img/demo/hero.jpg', 'Hotel pool and gardens' ),
                ),
                'blog' => $blog,
                'sector' => array(
                    array(
                        'post_type' => 'hotel_room',
                        'demo_meta' => '_wp_theme_demo_hotel_room',
                        'pool' => array(
                            'assets/img/demo/item-1.jpg', 'assets/img/demo/item-2.jpg', 'assets/img/demo/item-3.jpg',
                            'assets/img/demo/item-4.jpg', 'assets/img/demo/item-5.jpg', 'assets/img/demo/item-6.jpg',
                        ),
                        'rich_gallery' => true,
                        'gallery_count' => 5,
                    ),
                ),
            ),
            'wp-bbtheme-child-insurance' => array(
                'key' => 'insurance',
                'label' => 'Insurance',
                'hero' => 'assets/img/demo/hero-hq.jpg',
                'about' => 'assets/img/demo/about-photo.jpg',
                'gallery' => array(
                    array( 'assets/img/demo/item-1.jpg', 'Personal insurance guidance' ),
                    array( 'assets/img/demo/item-2.jpg', 'Home insurance' ),
                    array( 'assets/img/demo/item-3.jpg', 'Vehicle cover' ),
                    array( 'assets/img/demo/item-4.jpg', 'Family protection' ),
                ),
                'blog' => $blog,
                'sector' => array(),
            ),
            'wp-bbtheme-child-logistics' => array(
                'key' => 'logistics',
                'label' => 'Logistics',
                'hero' => 'assets/img/demo/hero-hq.jpg',
                'about' => 'assets/img/demo/about-photo.jpg',
                'gallery' => array(
                    array( 'assets/img/demo/item-1.jpg', 'Road freight service' ),
                    array( 'assets/img/demo/item-2.jpg', 'Van delivery service' ),
                    array( 'assets/img/demo/item-3.jpg', 'Warehouse logistics' ),
                    array( 'assets/img/demo/item-4.jpg', 'Freight planning' ),
                ),
                'blog' => $blog,
                'sector' => array(
                    array(
                        'post_type' => 'logistics_service',
                        'demo_meta' => '_wp_theme_demo_logistics_service',
                        'pool' => array(
                            'assets/img/demo/item-1.jpg', 'assets/img/demo/item-2.jpg', 'assets/img/demo/item-3.jpg',
                            'assets/img/demo/item-4.jpg', 'assets/img/demo/item-5.jpg', 'assets/img/demo/item-6.jpg',
                        ),
                        'rich_gallery' => true,
                        'gallery_count' => 5,
                    ),
                ),
            ),
            'wp-bbtheme-child-medicine' => array(
                'key' => 'medicine',
                'label' => 'Medicine',
                'hero' => 'assets/img/demo/hero-hq.jpg',
                'about' => 'assets/img/medical-photos/about-health.jpg',
                'gallery' => array(
                    array( 'assets/img/medical-photos/care-1.jpg', 'Clinical consultation' ),
                    array( 'assets/img/medical-photos/care-2.jpg', 'Patient care' ),
                    array( 'assets/img/medical-photos/care-3.jpg', 'Medical team' ),
                    array( 'assets/img/medical-photos/care-4.jpg', 'Healthcare support' ),
                ),
                'blog' => $blog,
                'sector' => array(
                    array(
                        'post_type' => 'doctor',
                        'demo_meta' => '_wp_theme_demo_doctor',
                        'pool' => array(
                            'assets/img/medical-photos/care-1.jpg', 'assets/img/medical-photos/care-2.jpg',
                            'assets/img/medical-photos/care-3.jpg', 'assets/img/medical-photos/care-4.jpg',
                            'assets/img/medical-photos/care-5.jpg', 'assets/img/medical-photos/care-6.jpg',
                        ),
                        'preserve_featured' => true,
                        'rich_gallery' => true,
                        'gallery_count' => 5,
                    ),
                ),
            ),
            'wp-bbtheme-child-realestate' => array(
                'key' => 'realestate',
                'label' => 'Real estate',
                'hero' => 'assets/img/demo/hero-hq.jpg',
                'about' => 'assets/img/properties/cedar-cottage.jpg',
                'gallery' => array(
                    array( 'assets/img/properties/harbour-house.jpg', 'Harbour House' ),
                    array( 'assets/img/properties/riverside-loft.jpg', 'Riverside Loft' ),
                    array( 'assets/img/properties/the-glassworks.jpg', 'The Glassworks' ),
                    array( 'assets/img/properties/garden-mews.jpg', 'Garden Mews' ),
                ),
                'blog' => $blog,
                'sector' => array(
                    array(
                        'post_type' => 'property',
                        'demo_meta' => '_wp_theme_demo_property',
                        'pool' => array(
                            'assets/img/properties/cedar-cottage.jpg', 'assets/img/properties/elm-gardens.jpg',
                            'assets/img/properties/garden-mews.jpg', 'assets/img/properties/harbour-house.jpg',
                            'assets/img/properties/kensington-court.jpg', 'assets/img/properties/oak-residence.jpg',
                            'assets/img/properties/park-view.jpg', 'assets/img/properties/riverside-loft.jpg',
                            'assets/img/properties/the-glassworks.jpg', 'assets/img/properties/willow-house.jpg',
                        ),
                        'rich_gallery' => true,
                        'gallery_count' => 5,
                    ),
                ),
            ),
            'wp-bbtheme-child-restaurant' => array(
                'key' => 'restaurant',
                'label' => 'Restaurant',
                'hero' => 'assets/img/demo/hero-hq.jpg',
                'about' => 'assets/img/demo/about-photo.jpg',
                'gallery' => array(
                    array( 'assets/img/demo/item-1.jpg', 'Restaurant dining room' ),
                    array( 'assets/img/demo/item-2.jpg', 'Chef-prepared dish' ),
                    array( 'assets/img/demo/item-3.jpg', 'Seasonal menu' ),
                    array( 'assets/img/demo/item-4.jpg', 'Table setting' ),
                ),
                'blog' => $blog,
                'sector' => array(
                    array(
                        'post_type' => 'menu_item',
                        'demo_meta' => '_wp_theme_demo_menu_item',
                        'pool' => array(
                            'assets/img/demo/item-1.jpg', 'assets/img/demo/item-2.jpg', 'assets/img/demo/item-3.jpg',
                            'assets/img/demo/item-4.jpg', 'assets/img/demo/item-5.jpg', 'assets/img/demo/item-6.jpg',
                        ),
                        'rich_gallery' => true,
                        'gallery_count' => 5,
                    ),
                ),
            ),
            'wp-bbtheme-child-travel' => array(
                'key' => 'travel',
                'label' => 'Travel',
                'hero' => 'assets/img/demo/hero-hq.jpg',
                'about' => 'assets/img/demo/about-photo.jpg',
                'gallery' => array(
                    array( 'assets/img/demo/item-1.jpg', 'Coastal destination' ),
                    array( 'assets/img/demo/item-2.jpg', 'City break' ),
                    array( 'assets/img/demo/item-3.jpg', 'Resort stay' ),
                    array( 'assets/img/demo/item-4.jpg', 'Guided journey' ),
                ),
                'blog' => $blog,
                'sector' => array(
                    array(
                        'post_type' => 'trip',
                        'demo_meta' => '_wp_theme_demo_trip',
                        'pool' => array(
                            'assets/img/demo/item-1.jpg', 'assets/img/demo/item-2.jpg', 'assets/img/demo/item-3.jpg',
                            'assets/img/demo/item-4.jpg', 'assets/img/demo/item-5.jpg', 'assets/img/demo/item-6.jpg',
                        ),
                        'rich_gallery' => true,
                        'gallery_count' => 5,
                    ),
                ),
            ),
            'wp-bbtheme-child-woo-events' => array(
                'key' => 'events',
                'label' => 'Events',
                'hero' => 'assets/img/demo/hero-hq.jpg',
                'about' => 'assets/img/demo/about-photo.jpg',
                'gallery' => array(
                    array( 'assets/img/demo/item-1.jpg', 'Conference stage and audience' ),
                    array( 'assets/img/demo/item-2.jpg', 'Live performance event' ),
                    array( 'assets/img/demo/item-3.jpg', 'Creative conference session' ),
                    array( 'assets/img/demo/item-4.jpg', 'Food and culture festival' ),
                    array( 'assets/img/demo/item-5.jpg', 'Product launch workshop' ),
                    array( 'assets/img/demo/item-6.jpg', 'Community skills workshop' ),
                ),
                'blog' => $blog,
                'sector' => array(
                    array(
                        'post_type' => 'event',
                        'demo_meta' => '_wp_theme_demo_event',
                        'pool' => array(
                            'assets/img/demo/item-1.jpg', 'assets/img/demo/item-2.jpg', 'assets/img/demo/item-3.jpg',
                            'assets/img/demo/item-4.jpg', 'assets/img/demo/item-5.jpg', 'assets/img/demo/item-6.jpg',
                        ),
                        'rich_gallery' => true,
                        'gallery_count' => 5,
                    ),
                ),
            ),
            'wp-bbtheme-child-woo-clouthes' => array(
                'key' => 'fashion-shop',
                'label' => 'Fashion shop',
                'hero' => 'assets/img/demo/hero-hq.jpg',
                'about' => 'assets/img/products/utility-overshirt.jpg',
                'gallery' => array(
                    array( 'assets/img/products/essential-t-shirt.jpg', 'Essential T-shirt' ),
                    array( 'assets/img/products/everyday-denim.jpg', 'Everyday denim' ),
                    array( 'assets/img/products/canvas-weekend-bag.jpg', 'Canvas weekend bag' ),
                    array( 'assets/img/products/lightweight-parka.jpg', 'Lightweight parka' ),
                ),
                'blog' => $blog,
                'sector' => array(),
            ),
            'wp-bbtheme-child-woo-tech-shop' => array(
                'key' => 'tech-shop',
                'label' => 'Tech shop',
                'hero' => 'assets/img/demo/hero-hq.jpg',
                'about' => 'assets/img/store/studio-monitor.jpg',
                'gallery' => array(
                    array( 'assets/img/store/ultralight-laptop.jpg', 'Ultralight laptop' ),
                    array( 'assets/img/store/noise-cancelling-headphones.jpg', 'Noise-cancelling headphones' ),
                    array( 'assets/img/store/mechanical-keyboard.jpg', 'Mechanical keyboard' ),
                    array( 'assets/img/store/usb-c-travel-dock.jpg', 'USB-C travel dock' ),
                ),
                'blog' => $blog,
                'sector' => array(),
            ),
        );

        $config = isset( $configs[ $stylesheet ] ) ? $configs[ $stylesheet ] : array();
        if ( $config ) $config['stylesheet'] = $stylesheet;
        return $config;
    }
}

if ( ! function_exists( 'wpbb_child_381045_valid_asset' ) ) {
    function wpbb_child_381045_valid_asset( $relative ) {
        $relative = ltrim( str_replace( '\\', '/', (string) $relative ), '/' );
        if ( '' === $relative || false !== strpos( $relative, '../' ) ) return '';
        $path = trailingslashit( get_stylesheet_directory() ) . $relative;
        return is_readable( $path ) ? $relative : '';
    }
}

if ( ! function_exists( 'wpbb_child_381045_asset_url' ) ) {
    function wpbb_child_381045_asset_url( $relative ) {
        $relative = wpbb_child_381045_valid_asset( $relative );
        return $relative ? trailingslashit( get_stylesheet_directory_uri() ) . $relative : '';
    }
}

if ( ! function_exists( 'wpbb_child_381045_import_attachment' ) ) {
    function wpbb_child_381045_import_attachment( $relative, $title = '' ) {
        static $cache = array();
        $config = wpbb_child_381045_consistency_config();
        $relative = wpbb_child_381045_valid_asset( $relative );
        if ( ! $config || ! $relative ) return 0;

        $cache_key = $config['key'] . '|' . $relative;
        if ( isset( $cache[ $cache_key ] ) ) return (int) $cache[ $cache_key ];

        $source = trailingslashit( get_stylesheet_directory() ) . $relative;
        $stem = sanitize_title( str_replace( array( 'assets/img/', '/', '\\' ), array( '', '-', '-' ), pathinfo( $relative, PATHINFO_DIRNAME ) . '-' . pathinfo( $relative, PATHINFO_FILENAME ) ) );
        $slug = 'wpbb-381045-' . sanitize_title( $config['key'] ) . '-' . $stem;
        $existing = get_page_by_path( $slug, OBJECT, 'attachment' );

        $uploads = wp_upload_dir();
        if ( ! empty( $uploads['error'] ) ) return 0;
        $directory = trailingslashit( $uploads['basedir'] ) . 'wpbb-sector-media/' . sanitize_file_name( $config['key'] );
        if ( ! wp_mkdir_p( $directory ) ) return 0;
        $filename = sanitize_file_name( $stem . '.' . strtolower( pathinfo( $relative, PATHINFO_EXTENSION ) ) );
        $target = trailingslashit( $directory ) . $filename;
        $source_hash = hash_file( 'sha256', $source );

        if ( ! is_file( $target ) || $source_hash !== hash_file( 'sha256', $target ) ) {
            if ( ! @copy( $source, $target ) ) return 0;
        }

        if ( $existing ) {
            $attachment_id = (int) $existing->ID;
            $attached = get_attached_file( $attachment_id );
            if ( ! $attached || wp_normalize_path( $attached ) !== wp_normalize_path( $target ) ) {
                update_attached_file( $attachment_id, $target );
            }
        } else {
            $filetype = wp_check_filetype( $target );
            $attachment_id = wp_insert_attachment(
                array(
                    'post_mime_type' => $filetype['type'] ? $filetype['type'] : 'image/jpeg',
                    'post_title' => $title ? $title : ucwords( str_replace( '-', ' ', $stem ) ),
                    'post_name' => $slug,
                    'post_status' => 'inherit',
                ),
                $target
            );
            if ( ! $attachment_id || is_wp_error( $attachment_id ) ) return 0;
            $attachment_id = (int) $attachment_id;
        }

        $current_hash = (string) get_post_meta( $attachment_id, '_wpbb_child_source_hash', true );
        if ( $source_hash !== $current_hash ) {
            if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) require_once ABSPATH . 'wp-admin/includes/image.php';
            $meta = wpbb_child_381048_generate_attachment_metadata( $attachment_id, $target );
            if ( $meta ) wp_update_attachment_metadata( $attachment_id, $meta );
            update_post_meta( $attachment_id, '_wpbb_child_source_hash', $source_hash );
            update_post_meta( $attachment_id, '_wpbb_child_source_relative', $relative );
            clean_attachment_cache( $attachment_id );
        }

        $alt = $title ? $title : $config['label'] . ' image';
        update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( $alt ) );
        $cache[ $cache_key ] = $attachment_id;
        return $attachment_id;
    }
}

if ( ! function_exists( 'wpbb_child_381045_run_existing_media_upgrade' ) ) {
    function wpbb_child_381045_run_existing_media_upgrade() {
        $defined = get_defined_functions();
        foreach ( (array) ( $defined['user'] ?? array() ) as $function_name ) {
            if ( ! preg_match( '/^wpbb_[a-z0-9_]+_realistic_media_upgrade_v381041$/', $function_name ) ) continue;
            delete_option( $function_name );
            try {
                call_user_func( $function_name );
            } catch ( Throwable $error ) {
                // Keep the upgrade self-healing on hosts with partial image support.
            }
        }
    }
}

if ( ! function_exists( 'wpbb_child_381045_demo_post_ids' ) ) {
    function wpbb_child_381045_demo_post_ids( $post_type, $demo_meta = '' ) {
        if ( ! post_type_exists( $post_type ) ) return array();
        $args = array(
            'post_type' => $post_type,
            'post_status' => array( 'publish', 'draft', 'private', 'pending' ),
            'posts_per_page' => -1,
            'orderby' => array( 'menu_order' => 'ASC', 'ID' => 'ASC' ),
            'fields' => 'ids',
            'no_found_rows' => true,
        );
        if ( $demo_meta ) {
            $args['meta_query'] = array(
                'relation' => 'OR',
                array( 'key' => $demo_meta, 'value' => '1' ),
                array( 'key' => '_wp_theme_demo_generated', 'value' => '1' ),
            );
        }
        $ids = get_posts( $args );
        return array_values( array_map( 'absint', $ids ) );
    }
}

if ( ! function_exists( 'wpbb_child_381045_sync_sector_media' ) ) {
    function wpbb_child_381045_sync_sector_media( $config ) {
        foreach ( (array) ( $config['sector'] ?? array() ) as $sector ) {
            $post_type = sanitize_key( $sector['post_type'] ?? '' );
            $pool = array_values( array_filter( array_map( 'wpbb_child_381045_valid_asset', (array) ( $sector['pool'] ?? array() ) ) ) );
            if ( ! $post_type || ! $pool ) continue;

            $ids = wpbb_child_381045_demo_post_ids( $post_type, (string) ( $sector['demo_meta'] ?? '' ) );
            if ( ! $ids ) continue;

            $attachment_labels = array();
            foreach ( (array) ( $config['gallery'] ?? array() ) as $gallery_item ) {
                if ( ! is_array( $gallery_item ) ) continue;
                $gallery_relative = wpbb_child_381045_valid_asset( (string) ( $gallery_item[0] ?? '' ) );
                if ( $gallery_relative ) $attachment_labels[ $gallery_relative ] = (string) ( $gallery_item[1] ?? '' );
            }

            $attachments = array();
            foreach ( $pool as $index => $relative ) {
                $label = trim( (string) ( $attachment_labels[ $relative ] ?? '' ) );
                if ( '' === $label ) $label = $config['label'] . ' item ' . ( $index + 1 );
                $attachments[] = wpbb_child_381045_import_attachment( $relative, $label );
            }
            $attachments = array_values( array_filter( array_map( 'absint', $attachments ) ) );
            if ( ! $attachments ) continue;

            $gallery_pool = $attachments;
            foreach ( (array) ( $config['gallery'] ?? array() ) as $gallery_item ) {
                $relative = is_array( $gallery_item ) ? (string) ( $gallery_item[0] ?? '' ) : (string) $gallery_item;
                $label = is_array( $gallery_item ) ? (string) ( $gallery_item[1] ?? $config['label'] . ' gallery' ) : $config['label'] . ' gallery';
                $attachment_id = wpbb_child_381045_import_attachment( $relative, $label );
                if ( $attachment_id ) $gallery_pool[] = $attachment_id;
            }
            $gallery_pool = array_values( array_unique( array_filter( array_map( 'absint', $gallery_pool ) ) ) );

            foreach ( $ids as $index => $post_id ) {
                $existing_primary = absint( get_post_thumbnail_id( $post_id ) );
                $primary = ! empty( $sector['preserve_featured'] ) && $existing_primary
                    ? $existing_primary
                    : $attachments[ $index % count( $attachments ) ];
                if ( $primary && empty( $sector['preserve_featured'] ) ) set_post_thumbnail( $post_id, $primary );
                if ( ! $primary ) $primary = $attachments[ $index % count( $attachments ) ];
                update_post_meta( $post_id, '_wpbb_child_sector_media_theme', $config['key'] );

                if ( empty( $sector['rich_gallery'] ) ) continue;
                $wanted = max( 3, min( 8, absint( $sector['gallery_count'] ?? 5 ) ) );
                $gallery_ids = array( $primary );
                $pool_count = count( $gallery_pool );
                for ( $step = 1; $step < $pool_count && count( $gallery_ids ) < $wanted; $step++ ) {
                    $candidate = $gallery_pool[ ( $index + $step ) % $pool_count ];
                    if ( ! in_array( $candidate, $gallery_ids, true ) ) $gallery_ids[] = $candidate;
                }
                update_post_meta( $post_id, '_wpbb_child_gallery_ids', $gallery_ids );
                update_post_meta( $post_id, '_wp_theme_item_gallery_ids', $gallery_ids );
                update_post_meta( $post_id, '_wp_theme_item_gallery', implode( ',', $gallery_ids ) );
                update_post_meta( $post_id, '_wpbb_child_gallery_version', '3.8.10.74' );
            }
        }
    }
}

if ( ! function_exists( 'wpbb_child_381045_sync_blog_media' ) ) {
    function wpbb_child_381045_sync_blog_media( $config ) {
        $files = array_values( array_filter( array_map( 'wpbb_child_381045_valid_asset', (array) ( $config['blog'] ?? array() ) ) ) );
        if ( ! $files ) return;

        $candidate_ids = get_posts(
            array(
                'post_type' => 'post',
                'post_status' => array( 'publish', 'draft', 'private', 'pending' ),
                'posts_per_page' => -1,
                'orderby' => 'ID',
                'order' => 'ASC',
                'fields' => 'ids',
                'no_found_rows' => true,
            )
        );
        $post_ids = array();
        foreach ( $candidate_ids as $post_id ) {
            $managed = '1' === (string) get_post_meta( $post_id, '_wp_theme_demo_blog', true )
                || '1' === (string) get_post_meta( $post_id, '_wp_theme_demo_generated', true )
                || '' !== (string) get_post_meta( $post_id, '_wpbb_child_sector_media_theme', true );

            if ( ! $managed ) {
                $thumbnail_id = absint( get_post_thumbnail_id( $post_id ) );
                if ( $thumbnail_id ) {
                    $attached = strtolower( (string) get_post_meta( $thumbnail_id, '_wp_attached_file', true ) );
                    $attachment_slug = strtolower( (string) get_post_field( 'post_name', $thumbnail_id ) );
                    $managed = false !== strpos( $attached, 'wpbb-' ) || 0 === strpos( $attachment_slug, 'wpbb-' );
                }
            }
            if ( $managed ) $post_ids[] = absint( $post_id );
        }
        if ( ! $post_ids ) return;

        $attachments = array();
        foreach ( $files as $index => $relative ) {
            $attachments[] = wpbb_child_381045_import_attachment( $relative, $config['label'] . ' editorial image ' . ( $index + 1 ) );
        }
        $attachments = array_values( array_filter( array_map( 'absint', $attachments ) ) );
        if ( ! $attachments ) return;

        foreach ( array_values( $post_ids ) as $index => $post_id ) {
            $attachment_id = $attachments[ $index % count( $attachments ) ];
            set_post_thumbnail( $post_id, $attachment_id );
            update_post_meta( $post_id, '_wpbb_child_sector_media_theme', $config['key'] );
            update_post_meta( $post_id, '_wpbb_child_blog_media_version', '3.8.10.74' );
            clean_post_cache( $post_id );
        }
    }
}

if ( ! function_exists( 'wpbb_child_381045_fix_encoded_copy' ) ) {
    function wpbb_child_381045_fix_encoded_copy( $content ) {
        if ( ! is_string( $content ) || '' === $content ) return $content;
        $content = str_replace( array( '\\u0026', '\u0026' ), '&', $content );
        // Repair the malformed ampersand variants seen in imported demo copy: u26, u026 and u0026.
        $content = (string) preg_replace( '/\bu0{0,2}26\b/i', '&', $content );
        return $content;
    }
}

if ( ! function_exists( 'wpbb_child_381045_repair_swipers' ) ) {
    function wpbb_child_381045_repair_swipers( $content, $config ) {
        $hero = wpbb_child_381045_asset_url( (string) ( $config['hero'] ?? '' ) );
        $about = wpbb_child_381045_asset_url( (string) ( $config['about'] ?? '' ) );
        $gallery = array();
        foreach ( (array) ( $config['gallery'] ?? array() ) as $item ) {
            $relative = is_array( $item ) ? (string) ( $item[0] ?? '' ) : (string) $item;
            $title = is_array( $item ) ? (string) ( $item[1] ?? '' ) : '';
            $url = wpbb_child_381045_asset_url( $relative );
            if ( $url ) $gallery[] = array( 'url' => $url, 'title' => $title );
        }

        $hero_images = array_values( array_unique( array_filter( array_merge( array( $hero, $about ), wp_list_pluck( $gallery, 'url' ) ) ) ) );
        /* v3.8.11.16: keep the dedicated three-image hero set even when the
         * resumable media worker runs after the reset-safe finish pass. */
        if ( function_exists( 'wpbb_child_v116_hero_urls' ) ) {
            $v116_hero_images = wpbb_child_v116_hero_urls();
            if ( $v116_hero_images ) $hero_images = $v116_hero_images;
        }

        return (string) preg_replace_callback(
            '~<!--\s+wp:wpbb/swiper\s+(\{.*?\})\s+/-->~s',
            static function ( $match ) use ( $hero_images, $gallery ) {
                $attrs = json_decode( $match[1], true );
                if ( ! is_array( $attrs ) ) return $match[0];

                $slides = array();
                $slides_json = false;
                if ( ! empty( $attrs['slides'] ) && is_array( $attrs['slides'] ) ) {
                    $slides = $attrs['slides'];
                } elseif ( ! empty( $attrs['slidesJson'] ) && is_string( $attrs['slidesJson'] ) ) {
                    $decoded = json_decode( $attrs['slidesJson'], true );
                    if ( is_array( $decoded ) ) {
                        $slides = $decoded;
                        $slides_json = true;
                    }
                }
                if ( ! $slides ) return $match[0];

                $style = sanitize_key( (string) ( $attrs['demoStyle'] ?? '' ) );
                if ( 'hero' === $style && $hero_images ) {
                    foreach ( $slides as $index => &$slide ) {
                        if ( is_array( $slide ) ) $slide['image'] = $hero_images[ $index % count( $hero_images ) ];
                    }
                    unset( $slide );
                } elseif ( 'gallery' === $style && $gallery ) {
                    foreach ( $slides as $index => &$slide ) {
                        if ( ! is_array( $slide ) ) continue;
                        $replacement = $gallery[ $index % count( $gallery ) ];
                        $slide['image'] = $replacement['url'];
                        if ( $replacement['title'] ) $slide['title'] = $replacement['title'];
                    }
                    unset( $slide );
                } else {
                    return $match[0];
                }

                if ( $slides_json ) {
                    $attrs['slidesJson'] = wp_json_encode( $slides, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
                } else {
                    $attrs['slides'] = $slides;
                }
                return '<!-- wp:wpbb/swiper ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ' /-->';
            },
            $content
        );
    }
}

if ( ! function_exists( 'wpbb_child_381045_replace_about_image' ) ) {
    function wpbb_child_381045_replace_about_image( $content, $about_url ) {
        if ( ! $about_url ) return $content;
        return (string) preg_replace(
            '~(<figure\b[^>]*class="[^"]*(?:wp-theme-sector-media-text__media|wp-theme-about-page-intro__media)[^"]*"[^>]*>.*?<img\b[^>]*\bsrc=")[^"]+("[^>]*>)~is',
            '$1' . esc_url( $about_url ) . '$2',
            $content
        );
    }
}

if ( ! function_exists( 'wpbb_child_381045_replace_page_images' ) ) {
    function wpbb_child_381045_replace_page_images( $content, $config, $page_id ) {
        $hero = wpbb_child_381045_asset_url( (string) ( $config['hero'] ?? '' ) );
        $about = wpbb_child_381045_asset_url( (string) ( $config['about'] ?? '' ) );
        $gallery = array();
        foreach ( (array) ( $config['gallery'] ?? array() ) as $item ) {
            $relative = is_array( $item ) ? (string) ( $item[0] ?? '' ) : (string) $item;
            $url = wpbb_child_381045_asset_url( $relative );
            if ( $url ) $gallery[] = $url;
        }

        $content = wpbb_child_381045_replace_about_image( $content, $about );
        $slug = sanitize_key( (string) get_post_field( 'post_name', $page_id ) );
        $is_about = false !== strpos( $slug, 'about' ) || false !== strpos( $content, 'wp-theme-about-page-intro' );
        $page_image_index = 0;
        $content = (string) preg_replace_callback(
            '~(<figure\b[^>]*class="[^"]*wp-theme-sector-page-image[^"]*"[^>]*>.*?<img\b[^>]*\bsrc=")[^"]+("[^>]*>)~is',
            static function ( $match ) use ( $hero, $about, $is_about, &$page_image_index ) {
                $replacement = $is_about ? $about : ( 0 === $page_image_index ? $hero : $about );
                $page_image_index++;
                return $replacement ? $match[1] . esc_url( $replacement ) . $match[2] : $match[0];
            },
            $content
        );

        $gallery_index = 0;
        $theme_uri = trailingslashit( get_stylesheet_directory_uri() );
        $theme_path = trailingslashit( get_stylesheet_directory() );
        $content = (string) preg_replace_callback(
            '~https?://[^\s"\'<>)]*/wp-content/themes/wp-bbtheme-child-[^/\s"\'<>)]*/assets/img/[^\s"\'<>)]*~i',
            static function ( $match ) use ( $hero, $about, $gallery, $theme_uri, $theme_path, &$gallery_index ) {
                $url = html_entity_decode( $match[0], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
                $marker = '/assets/img/';
                $position = strpos( $url, $marker );
                if ( false === $position ) return $match[0];
                $tail = substr( $url, $position + strlen( $marker ) );
                $tail = preg_replace( '~-[0-9]+x[0-9]+(?=\.[a-z0-9]+$)~i', '', $tail );
                $same_relative = 'assets/img/' . ltrim( $tail, '/' );
                if ( is_readable( $theme_path . $same_relative ) ) return esc_url( $theme_uri . $same_relative );

                $basename = strtolower( pathinfo( $tail, PATHINFO_FILENAME ) );
                $hero_names = array( 'hero', 'hero-photo', 'hero-health', 'office-wide', 'willow-house', 'relaxed-cotton-shirt', 'tech-workspace', 'tech-hero' );
                $about_names = array( 'about', 'about-photo', 'about-health', 'office-detail', 'cedar-cottage', 'utility-overshirt', 'studio-monitor' );
                if ( in_array( $basename, $hero_names, true ) && $hero ) return esc_url( $hero );
                if ( in_array( $basename, $about_names, true ) && $about ) return esc_url( $about );
                if ( $gallery ) {
                    $replacement = $gallery[ $gallery_index % count( $gallery ) ];
                    $gallery_index++;
                    return esc_url( $replacement );
                }
                return $about ? esc_url( $about ) : ( $hero ? esc_url( $hero ) : $match[0] );
            },
            $content
        );

        // Replace previously imported media from another sector. This covers
        // content that stores wp-content/uploads URLs instead of child-theme URLs.
        $upload_index = 0;
        $content = (string) preg_replace_callback(
            '~https?://[^\s"\'<>)]*/wp-content/uploads/(?:[^/]+/)*wpbb-(?:sector-media|[a-z0-9-]+)/(?:[^\s"\'<>)]*)~i',
            static function ( $match ) use ( $hero, $about, $gallery, $is_about, &$upload_index ) {
                $url = html_entity_decode( $match[0], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
                $basename = strtolower( pathinfo( (string) wp_parse_url( $url, PHP_URL_PATH ), PATHINFO_FILENAME ) );
                if ( $is_about && $about ) return esc_url( $about );
                if ( false !== strpos( $basename, 'hero' ) && $hero ) return esc_url( $hero );
                if ( false !== strpos( $basename, 'about' ) && $about ) return esc_url( $about );
                if ( $gallery ) {
                    $replacement = $gallery[ $upload_index % count( $gallery ) ];
                    $upload_index++;
                    return esc_url( $replacement );
                }
                return $about ? esc_url( $about ) : ( $hero ? esc_url( $hero ) : $match[0] );
            },
            $content
        );

        if ( false !== strpos( $content, 'wp-theme-about-page-intro' ) ) {
            $content = (string) preg_replace(
                '~(wp-theme-about-page-intro[\s\S]{0,1800}?"customClasses":")align-items-center(")~',
                '$1align-items-start wp-theme-sector-media-text$2',
                $content,
                1
            );
        }
        return $content;
    }
}

if ( ! function_exists( 'wpbb_child_381045_repair_demo_pages' ) ) {
    function wpbb_child_381045_repair_demo_pages( $config ) {
        $page_ids = get_posts(
            array(
                'post_type' => 'page',
                'post_status' => array( 'publish', 'draft', 'private', 'pending' ),
                'posts_per_page' => -1,
                'orderby' => 'ID',
                'order' => 'ASC',
                'fields' => 'ids',
                'no_found_rows' => true,
            )
        );
        foreach ( $page_ids as $page_id ) {
            $content = (string) get_post_field( 'post_content', $page_id );
            if ( '' === $content ) continue;
            if ( false === strpos( $content, 'wp-theme-' ) && false === strpos( $content, '/wp-bbtheme-child-' ) ) continue;
            $repaired = wpbb_child_381045_fix_encoded_copy( $content );
            $repaired = wpbb_child_381045_repair_swipers( $repaired, $config );
            $repaired = wpbb_child_381045_replace_page_images( $repaired, $config, $page_id );
            if ( $repaired !== $content ) {
                wp_update_post( array( 'ID' => $page_id, 'post_content' => $repaired ) );
                clean_post_cache( $page_id );
            }
            update_post_meta( $page_id, '_wpbb_child_consistency_version', '3.8.10.74' );
        }
    }
}

if ( ! function_exists( 'wpbb_child_381045_gallery_ids' ) ) {
    function wpbb_child_381045_gallery_ids( $post_id ) {
        $value = array();
        foreach ( array( '_wp_theme_item_gallery_ids', '_wp_theme_item_gallery', '_wpbb_child_gallery_ids' ) as $meta_key ) {
            $candidate = get_post_meta( $post_id, $meta_key, true );
            if ( is_string( $candidate ) ) $candidate = preg_split( '/\s*,\s*/', $candidate );
            if ( is_array( $candidate ) && $candidate ) {
                $value = $candidate;
                break;
            }
        }
        $ids = array_values( array_unique( array_filter( array_map( 'absint', (array) $value ) ) ) );
        $featured = absint( get_post_thumbnail_id( $post_id ) );
        if ( $featured ) {
            $ids = array_values( array_unique( array_merge( array( $featured ), $ids ) ) );
        }
        return $ids;
    }
}

if ( ! function_exists( 'wpbb_child_381045_gallery_images' ) ) {
    function wpbb_child_381045_gallery_images( $post_id ) {
        $images = array();
        foreach ( wpbb_child_381045_gallery_ids( $post_id ) as $attachment_id ) {
            $full = wp_get_attachment_image_url( $attachment_id, 'full' );
            if ( ! $full ) continue;
            $images[] = array(
                'id' => $attachment_id,
                'full' => $full,
                'display' => wp_get_attachment_image_url( $attachment_id, 'large' ) ?: $full,
                'thumb' => wp_get_attachment_image_url( $attachment_id, 'thumbnail' ) ?: $full,
                'alt' => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ?: get_the_title( $post_id ),
            );
        }
        return $images;
    }
}

if ( ! function_exists( 'wpbb_child_381045_gallery_single_markup' ) ) {
    function wpbb_child_381045_gallery_single_markup( $post_id ) {
        $images = wpbb_child_381045_gallery_images( $post_id );
        if ( count( $images ) < 2 ) return '';
        $key = wp_parse_url( get_permalink( $post_id ), PHP_URL_PATH );
        if ( ! $key ) $key = (string) $post_id;
        $first = $images[0];
        ob_start();
        ?>
        <div class="wp-theme-item-gallery--single wpbb-child-sector-gallery" data-wpbb-child-gallery-key="<?php echo esc_attr( $key ); ?>">
            <div class="wp-theme-item-gallery__stage">
                <button class="wp-theme-item-gallery__stage-button" type="button" data-wpbb-gallery-open="0" aria-label="<?php echo esc_attr__( 'Open image gallery', 'wp-theme' ); ?>">
                    <img src="<?php echo esc_url( $first['display'] ); ?>" alt="<?php echo esc_attr( $first['alt'] ); ?>" loading="eager" decoding="async">
                    <span class="wp-theme-item-gallery__stage-action" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M8 3H5a2 2 0 0 0-2 2v3M16 3h3a2 2 0 0 1 2 2v3M8 21H5a2 2 0 0 1-2-2v-3M16 21h3a2 2 0 0 0 2-2v-3"/></svg>
                        <?php echo esc_html__( 'View gallery', 'wp-theme' ); ?>
                        <span><?php echo esc_html( count( $images ) ); ?></span>
                    </span>
                </button>
                <div class="wp-theme-item-gallery__thumbs wp-theme-item-gallery__thumbs--overlay" aria-label="<?php echo esc_attr__( 'Gallery thumbnails', 'wp-theme' ); ?>">
                    <?php foreach ( $images as $index => $image ) : ?>
                        <button class="wp-theme-item-gallery__thumb<?php echo 0 === $index ? ' is-active' : ''; ?>" type="button" data-wpbb-gallery-index="<?php echo esc_attr( $index ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Show image %d', 'wp-theme' ), $index + 1 ) ); ?>">
                            <img src="<?php echo esc_url( $image['thumb'] ); ?>" alt="" loading="lazy" decoding="async">
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
        return trim( (string) ob_get_clean() );
    }
}

if ( ! function_exists( 'wpbb_child_381045_gallery_frontend_config' ) ) {
    function wpbb_child_381045_gallery_frontend_config() {
        if ( is_admin() ) return;
        $config = wpbb_child_381045_consistency_config();
        if ( empty( $config['sector'] ) ) return;
        $payload = array();
        foreach ( (array) $config['sector'] as $sector ) {
            if ( empty( $sector['rich_gallery'] ) ) continue;
            $post_type = sanitize_key( (string) ( $sector['post_type'] ?? '' ) );
            foreach ( wpbb_child_381045_demo_post_ids( $post_type, (string) ( $sector['demo_meta'] ?? '' ) ) as $post_id ) {
                $images = wpbb_child_381045_gallery_images( $post_id );
                if ( count( $images ) < 2 ) continue;
                $path = wp_parse_url( get_permalink( $post_id ), PHP_URL_PATH );
                if ( ! $path ) continue;
                $payload[ untrailingslashit( $path ) ] = array(
                    'title' => get_the_title( $post_id ),
                    'images' => $images,
                );
            }
        }
        if ( ! $payload ) return;
        echo '<script>window.wpbbChildSectorGalleries=' . wp_json_encode( $payload, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ';</script>';
    }
}
add_action( 'wp_footer', 'wpbb_child_381045_gallery_frontend_config', 1 );

/**
 * v3.8.10.74 uses a resumable worker. The former admin_init migration could
 * import and regenerate dozens of images in one request, which was capable of
 * exhausting PHP memory or the request timeout on the WordPress dashboard.
 */
if ( ! function_exists( 'wpbb_child_381046_state_key' ) ) {
    function wpbb_child_381046_state_key() {
        return 'wpbb_child_381046_consistency_state_' . sanitize_key( get_stylesheet() );
    }
}

if ( ! function_exists( 'wpbb_child_381046_done_key' ) ) {
    function wpbb_child_381046_done_key() {
        return 'wpbb_child_381046_consistency_' . sanitize_key( get_stylesheet() );
    }
}

if ( ! function_exists( 'wpbb_child_381046_asset_jobs' ) ) {
    function wpbb_child_381046_asset_jobs( $config ) {
        $jobs = array();
        $add = static function ( $relative, $title ) use ( &$jobs ) {
            $relative = wpbb_child_381045_valid_asset( $relative );
            if ( ! $relative || isset( $jobs[ $relative ] ) ) return;
            $jobs[ $relative ] = array( 'relative' => $relative, 'title' => sanitize_text_field( $title ) );
        };
        $add( (string) ( $config['hero'] ?? '' ), (string) ( $config['label'] ?? '' ) . ' hero' );
        $add( (string) ( $config['about'] ?? '' ), (string) ( $config['label'] ?? '' ) . ' about image' );
        foreach ( (array) ( $config['gallery'] ?? array() ) as $index => $item ) {
            $add( is_array( $item ) ? (string) ( $item[0] ?? '' ) : (string) $item, is_array( $item ) ? (string) ( $item[1] ?? '' ) : (string) ( $config['label'] ?? '' ) . ' gallery ' . ( $index + 1 ) );
        }
        foreach ( (array) ( $config['blog'] ?? array() ) as $index => $relative ) {
            $add( (string) $relative, (string) ( $config['label'] ?? '' ) . ' editorial image ' . ( $index + 1 ) );
        }
        foreach ( (array) ( $config['sector'] ?? array() ) as $sector ) {
            foreach ( (array) ( $sector['pool'] ?? array() ) as $index => $relative ) {
                $add( (string) $relative, (string) ( $config['label'] ?? '' ) . ' item ' . ( $index + 1 ) );
            }
        }
        return array_values( $jobs );
    }
}

if ( ! function_exists( 'wpbb_child_381046_default_state' ) ) {
    function wpbb_child_381046_default_state( $config ) {
        return array(
            'version' => '3.8.10.74',
            'signature' => md5( wp_json_encode( $config ) ),
            'stage' => 'assets',
            'asset_offset' => 0,
            'sector_index' => 0,
            'sector_offset' => 0,
            'blog_offset' => 0,
            'page_offset' => 0,
            'processed' => 0,
            'updated' => time(),
        );
    }
}

if ( ! function_exists( 'wpbb_child_381046_get_state' ) ) {
    function wpbb_child_381046_get_state( $config ) {
        $default = wpbb_child_381046_default_state( $config );
        $state = get_option( wpbb_child_381046_state_key(), array() );
        if ( ! is_array( $state ) || ( $state['signature'] ?? '' ) !== $default['signature'] || ( $state['version'] ?? '' ) !== '3.8.10.74' ) return $default;
        return array_merge( $default, $state );
    }
}

if ( ! function_exists( 'wpbb_child_381046_save_state' ) ) {
    function wpbb_child_381046_save_state( $state ) {
        $state['updated'] = time();
        update_option( wpbb_child_381046_state_key(), $state, false );
    }
}

if ( ! function_exists( 'wpbb_child_381046_schedule' ) ) {
    function wpbb_child_381046_schedule( $reset = false ) {
        $config = wpbb_child_381045_consistency_config();
        if ( ! $config ) return;
        if ( $reset ) {
            delete_option( wpbb_child_381046_state_key() );
            delete_option( wpbb_child_381046_done_key() );
            delete_option( 'wpbb_child_381046_consistency_last_error_' . sanitize_key( get_stylesheet() ) );
        }
        $done = get_option( wpbb_child_381046_done_key(), array() );
        $signature = md5( wp_json_encode( $config ) );
        if ( is_array( $done ) && ( $done['version'] ?? '' ) === '3.8.10.74' && ( $done['signature'] ?? '' ) === $signature ) return;
        if ( ! wp_next_scheduled( 'wpbb_child_381046_consistency_batch' ) ) {
            wp_schedule_single_event( time() + 5, 'wpbb_child_381046_consistency_batch' );
        }
    }
}
add_action( 'init', 'wpbb_child_381046_schedule', 40 );
add_action( 'wp_theme_after_demo_import', 'wpbb_child_381046_schedule', 220 );

if ( ! function_exists( 'wpbb_child_381046_sector_attachments' ) ) {
    function wpbb_child_381046_sector_attachments( $config, $sector ) {
        $labels = array();
        foreach ( (array) ( $config['gallery'] ?? array() ) as $item ) {
            if ( ! is_array( $item ) ) continue;
            $relative = wpbb_child_381045_valid_asset( (string) ( $item[0] ?? '' ) );
            if ( $relative ) $labels[ $relative ] = (string) ( $item[1] ?? '' );
        }
        $primary = array();
        foreach ( (array) ( $sector['pool'] ?? array() ) as $index => $relative ) {
            $relative = wpbb_child_381045_valid_asset( (string) $relative );
            if ( ! $relative ) continue;
            $title = trim( (string) ( $labels[ $relative ] ?? '' ) );
            if ( '' === $title ) $title = $config['label'] . ' item ' . ( $index + 1 );
            $id = wpbb_child_381045_import_attachment( $relative, $title );
            if ( $id ) $primary[] = absint( $id );
        }
        $gallery = $primary;
        foreach ( (array) ( $config['gallery'] ?? array() ) as $item ) {
            $relative = is_array( $item ) ? (string) ( $item[0] ?? '' ) : (string) $item;
            $title = is_array( $item ) ? (string) ( $item[1] ?? $config['label'] . ' gallery' ) : $config['label'] . ' gallery';
            $id = wpbb_child_381045_import_attachment( $relative, $title );
            if ( $id ) $gallery[] = absint( $id );
        }
        return array( array_values( array_unique( array_filter( $primary ) ) ), array_values( array_unique( array_filter( $gallery ) ) ) );
    }
}

if ( ! function_exists( 'wpbb_child_381046_run_sector_batch' ) ) {
    function wpbb_child_381046_run_sector_batch( $config, &$state ) {
        $sectors = array_values( (array) ( $config['sector'] ?? array() ) );
        if ( (int) $state['sector_index'] >= count( $sectors ) ) {
            $state['stage'] = 'blog';
            $state['blog_offset'] = 0;
            return;
        }
        $sector = $sectors[ (int) $state['sector_index'] ];
        $post_type = sanitize_key( (string) ( $sector['post_type'] ?? '' ) );
        if ( ! $post_type || ! post_type_exists( $post_type ) ) {
            $state['sector_index']++;
            $state['sector_offset'] = 0;
            return;
        }
        $args = array(
            'post_type' => $post_type,
            'post_status' => array( 'publish', 'draft', 'private', 'pending' ),
            'posts_per_page' => 5,
            'offset' => absint( $state['sector_offset'] ),
            'orderby' => array( 'menu_order' => 'ASC', 'ID' => 'ASC' ),
            'fields' => 'ids',
            'no_found_rows' => true,
        );
        $demo_meta = (string) ( $sector['demo_meta'] ?? '' );
        if ( $demo_meta ) {
            $args['meta_query'] = array( 'relation' => 'OR', array( 'key' => $demo_meta, 'value' => '1' ), array( 'key' => '_wp_theme_demo_generated', 'value' => '1' ) );
        }
        $ids = get_posts( $args );
        if ( ! $ids ) {
            $state['sector_index']++;
            $state['sector_offset'] = 0;
            return;
        }
        list( $primary_pool, $gallery_pool ) = wpbb_child_381046_sector_attachments( $config, $sector );
        if ( ! $primary_pool ) {
            $state['sector_index']++;
            $state['sector_offset'] = 0;
            return;
        }
        $base_index = absint( $state['sector_offset'] );
        foreach ( array_values( $ids ) as $batch_index => $post_id ) {
            $index = $base_index + $batch_index;
            $existing = absint( get_post_thumbnail_id( $post_id ) );
            $primary = ! empty( $sector['preserve_featured'] ) && $existing ? $existing : $primary_pool[ $index % count( $primary_pool ) ];
            if ( empty( $sector['preserve_featured'] ) && $primary ) set_post_thumbnail( $post_id, $primary );
            update_post_meta( $post_id, '_wpbb_child_sector_media_theme', $config['key'] );
            if ( ! empty( $sector['rich_gallery'] ) ) {
                $wanted = max( 3, min( 8, absint( $sector['gallery_count'] ?? 5 ) ) );
                $gallery_ids = array( $primary );
                $pool_count = count( $gallery_pool );
                for ( $step = 1; $step <= $pool_count && count( $gallery_ids ) < $wanted; $step++ ) {
                    $candidate = $gallery_pool[ ( $index + $step ) % $pool_count ];
                    if ( $candidate && ! in_array( $candidate, $gallery_ids, true ) ) $gallery_ids[] = $candidate;
                }
                $gallery_ids = array_values( array_filter( array_unique( array_map( 'absint', $gallery_ids ) ) ) );
                update_post_meta( $post_id, '_wpbb_child_gallery_ids', $gallery_ids );
                update_post_meta( $post_id, '_wp_theme_item_gallery_ids', $gallery_ids );
                update_post_meta( $post_id, '_wp_theme_gallery_ids', implode( ',', $gallery_ids ) );
                update_post_meta( $post_id, '_wp_theme_item_gallery', implode( ',', $gallery_ids ) );
                update_post_meta( $post_id, '_wpbb_child_gallery_version', '3.8.10.74' );
            }
            clean_post_cache( $post_id );
            $state['processed']++;
        }
        $state['sector_offset'] += count( $ids );
        if ( count( $ids ) < 5 ) {
            $state['sector_index']++;
            $state['sector_offset'] = 0;
        }
    }
}

if ( ! function_exists( 'wpbb_child_381046_run_blog_batch' ) ) {
    function wpbb_child_381046_run_blog_batch( $config, &$state ) {
        $files = array_values( array_filter( array_map( 'wpbb_child_381045_valid_asset', (array) ( $config['blog'] ?? array() ) ) ) );
        if ( ! $files ) { $state['stage'] = 'pages'; return; }
        $attachments = array();
        foreach ( $files as $index => $relative ) {
            $id = wpbb_child_381045_import_attachment( $relative, $config['label'] . ' editorial image ' . ( $index + 1 ) );
            if ( $id ) $attachments[] = absint( $id );
        }
        if ( count( $attachments ) < count( $files ) ) {
            $state['stage'] = 'assets';
            $state['asset_offset'] = 0;
            return;
        }
        $ids = get_posts( array(
            'post_type' => 'post',
            'post_status' => array( 'publish', 'draft', 'private', 'pending' ),
            'posts_per_page' => 20,
            'offset' => absint( $state['blog_offset'] ),
            'orderby' => 'ID',
            'order' => 'ASC',
            'fields' => 'ids',
            'no_found_rows' => true,
            'meta_query' => array(
                'relation' => 'OR',
                array( 'key' => '_wp_theme_demo_blog', 'value' => '1' ),
                array( 'key' => '_wp_theme_demo_generated', 'value' => '1' ),
                array( 'key' => '_wpbb_child_sector_media_theme', 'compare' => 'EXISTS' ),
            ),
        ) );
        if ( ! $ids ) {
            $state['stage'] = 'pages';
            $state['page_offset'] = 0;
            return;
        }
        $base_index = absint( $state['blog_offset'] );
        foreach ( array_values( $ids ) as $batch_index => $post_id ) {
            $id = $attachments[ ( $base_index + $batch_index ) % count( $attachments ) ];
            set_post_thumbnail( $post_id, $id );
            update_post_meta( $post_id, '_wpbb_child_sector_media_theme', $config['key'] );
            update_post_meta( $post_id, '_wpbb_child_blog_media_version', '3.8.10.74' );
            clean_post_cache( $post_id );
            $state['processed']++;
        }
        $state['blog_offset'] += count( $ids );
        if ( count( $ids ) < 20 ) {
            $state['stage'] = 'pages';
            $state['page_offset'] = 0;
        }
    }
}

if ( ! function_exists( 'wpbb_child_381046_run_page_batch' ) ) {
    function wpbb_child_381046_run_page_batch( $config, &$state ) {
        $ids = get_posts( array(
            'post_type' => 'page',
            'post_status' => array( 'publish', 'draft', 'private', 'pending' ),
            'posts_per_page' => 10,
            'offset' => absint( $state['page_offset'] ),
            'orderby' => 'ID',
            'order' => 'ASC',
            'fields' => 'ids',
            'no_found_rows' => true,
        ) );
        if ( ! $ids ) { $state['stage'] = 'verify'; return; }
        foreach ( $ids as $page_id ) {
            $content = (string) get_post_field( 'post_content', $page_id );
            if ( '' !== $content && ( false !== strpos( $content, 'wp-theme-' ) || false !== strpos( $content, '/wp-bbtheme-child-' ) || false !== strpos( $content, 'wpbb-sector-media/' ) ) ) {
                $repaired = wpbb_child_381045_fix_encoded_copy( $content );
                $repaired = wpbb_child_381045_repair_swipers( $repaired, $config );
                $repaired = wpbb_child_381045_replace_page_images( $repaired, $config, $page_id );
                if ( $repaired !== $content ) wp_update_post( array( 'ID' => $page_id, 'post_content' => $repaired ) );
            }
            update_post_meta( $page_id, '_wpbb_child_consistency_version', '3.8.10.74' );
            clean_post_cache( $page_id );
            $state['processed']++;
        }
        $state['page_offset'] += count( $ids );
        if ( count( $ids ) < 10 ) $state['stage'] = 'verify';
    }
}

if ( ! function_exists( 'wpbb_child_381046_verify' ) ) {
    function wpbb_child_381046_verify( $config ) {
        $files = array_values( array_filter( array_map( 'wpbb_child_381045_valid_asset', (array) ( $config['blog'] ?? array() ) ) ) );
        $allowed = array();
        foreach ( $files as $index => $relative ) {
            $id = wpbb_child_381045_import_attachment( $relative, $config['label'] . ' editorial image ' . ( $index + 1 ) );
            if ( ! $id ) return false;
            $allowed[] = absint( $id );
        }
        $posts = get_posts( array(
            'post_type' => 'post', 'post_status' => array( 'publish', 'draft', 'private', 'pending' ), 'posts_per_page' => -1,
            'fields' => 'ids', 'no_found_rows' => true,
            'meta_query' => array( 'relation' => 'OR', array( 'key' => '_wp_theme_demo_blog', 'value' => '1' ), array( 'key' => '_wp_theme_demo_generated', 'value' => '1' ) ),
        ) );
        foreach ( $posts as $post_id ) {
            if ( ! in_array( absint( get_post_thumbnail_id( $post_id ) ), $allowed, true ) ) return false;
            if ( (string) get_post_meta( $post_id, '_wpbb_child_sector_media_theme', true ) !== (string) $config['key'] ) return false;
        }
        return true;
    }
}

if ( ! function_exists( 'wpbb_child_381046_consistency_batch' ) ) {
    function wpbb_child_381046_consistency_batch() {
        $config = wpbb_child_381045_consistency_config();
        if ( ! $config ) return;
        $lock = 'wpbb_child_381046_consistency_lock_' . sanitize_key( get_stylesheet() );
        if ( get_transient( $lock ) ) return;
        set_transient( $lock, '1', 5 * MINUTE_IN_SECONDS );
        try {
            $state = wpbb_child_381046_get_state( $config );
            if ( 'assets' === $state['stage'] ) {
                $jobs = wpbb_child_381046_asset_jobs( $config );
                $limit = min( count( $jobs ), absint( $state['asset_offset'] ) + 2 );
                while ( absint( $state['asset_offset'] ) < $limit ) {
                    $job = $jobs[ absint( $state['asset_offset'] ) ];
                    wpbb_child_381045_import_attachment( $job['relative'], $job['title'] );
                    $state['asset_offset']++;
                    $state['processed']++;
                }
                if ( absint( $state['asset_offset'] ) >= count( $jobs ) ) { $state['stage'] = 'sector'; $state['sector_index'] = 0; $state['sector_offset'] = 0; }
            } elseif ( 'sector' === $state['stage'] ) {
                wpbb_child_381046_run_sector_batch( $config, $state );
            } elseif ( 'blog' === $state['stage'] ) {
                wpbb_child_381046_run_blog_batch( $config, $state );
            } elseif ( 'pages' === $state['stage'] ) {
                wpbb_child_381046_run_page_batch( $config, $state );
            } elseif ( 'verify' === $state['stage'] ) {
                if ( wpbb_child_381046_verify( $config ) ) {
                    $state['stage'] = 'done';
                    update_option( wpbb_child_381046_done_key(), array( 'version' => '3.8.10.74', 'signature' => $state['signature'], 'completed' => time() ), false );
                } else {
                    $state['stage'] = 'assets';
                    $state['asset_offset'] = 0;
                    $state['blog_offset'] = 0;
                }
            }
            wpbb_child_381046_save_state( $state );
            delete_option( 'wpbb_child_381046_consistency_last_error_' . sanitize_key( get_stylesheet() ) );
        } catch ( Throwable $error ) {
            update_option( 'wpbb_child_381046_consistency_last_error_' . sanitize_key( get_stylesheet() ), sanitize_text_field( $error->getMessage() ), false );
        }
        delete_transient( $lock );
        $state = wpbb_child_381046_get_state( $config );
        if ( 'done' !== ( $state['stage'] ?? '' ) && ! wp_next_scheduled( 'wpbb_child_381046_consistency_batch' ) ) {
            wp_schedule_single_event( time() + 10, 'wpbb_child_381046_consistency_batch' );
        }
    }
}
add_action( 'wpbb_child_381046_consistency_batch', 'wpbb_child_381046_consistency_batch' );

if ( ! function_exists( 'wpbb_child_381046_manual_run' ) ) {
    function wpbb_child_381046_manual_run() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( esc_html__( 'You are not allowed to run this repair.', 'wp-theme' ) );
        check_admin_referer( 'wpbb_child_381046_manual_run' );
        $reset = isset( $_POST['reset'] ) && '1' === sanitize_text_field( wp_unslash( $_POST['reset'] ) );
        wpbb_child_381046_schedule( $reset );
        wpbb_child_381046_consistency_batch();
        wp_safe_redirect( add_query_arg( array( 'page' => 'wp-theme-settings', 'wpbb_media_repair' => '1' ), admin_url( 'options-general.php' ) ) );
        exit;
    }
}
add_action( 'admin_post_wpbb_child_381046_manual_run', 'wpbb_child_381046_manual_run' );

if ( ! function_exists( 'wpbb_child_381046_settings_status' ) ) {
    function wpbb_child_381046_settings_status( $markup ) {
        if ( ! current_user_can( 'manage_options' ) ) return $markup;
        $config = wpbb_child_381045_consistency_config();
        if ( ! $config ) return $markup;
        $state = wpbb_child_381046_get_state( $config );
        $stage = sanitize_key( (string) ( $state['stage'] ?? 'pending' ) );
        $labels = array( 'assets' => __( 'Importing sector media', 'wp-theme' ), 'sector' => __( 'Updating directory items', 'wp-theme' ), 'blog' => __( 'Replacing demo blog images', 'wp-theme' ), 'pages' => __( 'Repairing page imagery and alignment', 'wp-theme' ), 'verify' => __( 'Verifying results', 'wp-theme' ), 'done' => __( 'Complete', 'wp-theme' ) );
        $last_error = get_option( 'wpbb_child_381046_consistency_last_error_' . sanitize_key( get_stylesheet() ), '' );
        $next_run = wp_next_scheduled( 'wpbb_child_381046_consistency_batch' );
        ob_start(); ?>
        <section class="wpbb-demo-protection-settings" aria-labelledby="wpbb-sector-repair-title">
            <h2 id="wpbb-sector-repair-title"><?php esc_html_e( 'Sector media and layout repair', 'wp-theme' ); ?></h2>
            <p><?php echo esc_html( $labels[ $stage ] ?? ucfirst( $stage ) ); ?> · <?php echo esc_html( sprintf( __( '%d items processed', 'wp-theme' ), absint( $state['processed'] ?? 0 ) ) ); ?></p>
            <p class="description"><?php esc_html_e( 'The repair runs in small background batches to avoid dashboard timeouts. It replaces only managed demonstration media and leaves unrelated editorial uploads untouched.', 'wp-theme' ); ?></p>
            <?php if ( 'done' !== $stage ) : ?>
                <p class="description">
                    <?php
                    echo $next_run
                        ? esc_html( sprintf( __( 'A background batch is scheduled for %s.', 'wp-theme' ), wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $next_run ) ) )
                        : esc_html__( 'No background batch is currently scheduled. Use the button below to process the next small batch.', 'wp-theme' );
                    ?>
                </p>
            <?php endif; ?>
            <?php if ( is_string( $last_error ) && '' !== trim( $last_error ) ) : ?>
                <div class="notice notice-error inline"><p><strong><?php esc_html_e( 'Last repair error:', 'wp-theme' ); ?></strong> <?php echo esc_html( $last_error ); ?></p></div>
            <?php endif; ?>
            <div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center">
                <?php if ( 'done' !== $stage ) : ?>
                    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                        <input type="hidden" name="action" value="wpbb_child_381046_manual_run">
                        <?php wp_nonce_field( 'wpbb_child_381046_manual_run' ); ?>
                        <button type="submit" class="button button-primary"><?php esc_html_e( 'Run next repair batch now', 'wp-theme' ); ?></button>
                    </form>
                <?php endif; ?>
                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                    <input type="hidden" name="action" value="wpbb_child_381046_manual_run">
                    <input type="hidden" name="reset" value="1">
                    <?php wp_nonce_field( 'wpbb_child_381046_manual_run' ); ?>
                    <button type="submit" class="button button-secondary"><?php esc_html_e( 'Restart repair from beginning', 'wp-theme' ); ?></button>
                </form>
            </div>
        </section>
        <?php return (string) $markup . trim( (string) ob_get_clean() );
    }
}
add_filter( 'wp_theme_general_settings_extension_markup', 'wpbb_child_381046_settings_status', 30 );

if ( ! function_exists( 'wpbb_child_381046_reset_on_switch' ) ) {
    function wpbb_child_381046_reset_on_switch() {
        wpbb_child_381046_schedule( true );
    }
}
add_action( 'after_switch_theme', 'wpbb_child_381046_reset_on_switch', 25 );
