<?php
/** WP BBTheme child suite 3.8.11.16 - reset-safe layout, media, mega-menu, consent and BBuilder repair. */
defined( 'ABSPATH' ) || exit;

/* 3.8.10.42 removed responsive width attributes from legitimate single-column
 * BBuilder rows. Disable that migration before admin_init/demo-import can run. */
remove_action( 'admin_init', 'wpbb_child_381042_repair_demo_page_widths_once', 40 );
remove_action( 'wp_theme_after_demo_import', 'wpbb_child_381042_repair_demo_page_widths', 140 );

if ( ! function_exists( 'wpbb_child_v116_hero_relatives' ) ) {
    function wpbb_child_v116_hero_relatives() {
        $rels = array(
            'assets/img/demo/hero-v114.jpg',
            'assets/img/demo/hero-v113.jpg',
            'assets/img/demo/hero-v112.jpg',
        );
        $valid = array_values( array_filter( $rels, static function( $rel ) {
            return is_readable( get_stylesheet_directory() . '/' . $rel );
        } ) );
        if ( ! $valid && function_exists( 'wpbb_child_v115_hero_relatives' ) ) $valid = wpbb_child_v115_hero_relatives();
        return $valid;
    }
}

if ( ! function_exists( 'wpbb_child_v116_asset_url' ) ) {
    function wpbb_child_v116_asset_url( $relative ) {
        $relative = ltrim( str_replace( '\\', '/', (string) $relative ), '/' );
        if ( '' === $relative || false !== strpos( $relative, '../' ) ) return '';
        return is_readable( get_stylesheet_directory() . '/' . $relative )
            ? trailingslashit( get_stylesheet_directory_uri() ) . $relative
            : '';
    }
}

if ( ! function_exists( 'wpbb_child_v116_hero_urls' ) ) {
    function wpbb_child_v116_hero_urls() {
        $urls = array();
        foreach ( wpbb_child_v116_hero_relatives() as $rel ) {
            $url = wpbb_child_v116_asset_url( $rel );
            if ( $url ) $urls[] = $url;
        }
        return array_values( array_unique( $urls ) );
    }
}

/* Future demo resets always serialize the three child-owned hero images. Theme
 * URLs are deliberate here: they survive a Media Library/demo reset. */
if ( ! function_exists( 'wpbb_child_v116_demo_profile' ) ) {
    function wpbb_child_v116_demo_profile( $profile ) {
        if ( ! is_array( $profile ) ) $profile = array();
        $urls = wpbb_child_v116_hero_urls();
        if ( ! $urls ) return $profile;
        $profile['hero_image'] = $urls[0];
        if ( ! empty( $profile['hero_slides'] ) && is_array( $profile['hero_slides'] ) ) {
            foreach ( $profile['hero_slides'] as $i => $slide ) {
                if ( is_array( $slide ) ) $profile['hero_slides'][ $i ]['image'] = $urls[ $i % count( $urls ) ];
            }
        }
        return $profile;
    }
    add_filter( 'wp_theme_demo_profile', 'wpbb_child_v116_demo_profile', PHP_INT_MAX );
}

if ( ! function_exists( 'wpbb_child_v116_normalize_partner_markup' ) ) {
    function wpbb_child_v116_normalize_partner_markup( $content ) {
        if ( ! is_string( $content ) || '' === $content ) return $content;
        if ( function_exists( 'wpbb_child_v115_normalize_partner_markup' ) ) $content = wpbb_child_v115_normalize_partner_markup( $content );
        $content = str_replace( '<!-- wp:wpbb/row --></div><!-- /wp:group -->', '<!-- /wp:wpbb/row --></div><!-- /wp:group -->', $content );

        /* Normalize any number of wrappers around the one historical raw partner
         * heading to exactly one valid core/paragraph block. */
        $content = (string) preg_replace(
            '~(?:<!--\s*wp:paragraph(?:\s+\{[^>]*\})?\s*-->\s*)+(<p\s+class=["\']wp-theme-partners-heading["\'][^>]*>.*?</p>)(?:\s*<!--\s*/wp:paragraph\s*-->)+~is',
            '<!-- wp:paragraph {"className":"wp-theme-partners-heading"} -->$1<!-- /wp:paragraph -->',
            $content
        );
        $content = (string) preg_replace_callback(
            '~(<!--\s*wp:wpbb/column\b[^>]*-->\s*)(?!<!--\s*wp:paragraph\b)(<p\s+class=["\']wp-theme-partners-heading["\'][^>]*>.*?</p>)~is',
            static function( $m ) {
                return $m[1] . '<!-- wp:paragraph {"className":"wp-theme-partners-heading"} -->' . $m[2] . '<!-- /wp:paragraph -->';
            },
            $content
        );
        return $content;
    }
}

if ( ! function_exists( 'wpbb_child_v116_repair_swipers' ) ) {
    function wpbb_child_v116_repair_swipers( $content ) {
        $urls = wpbb_child_v116_hero_urls();
        if ( ! $urls || ! is_string( $content ) || false === strpos( $content, 'wpbb/swiper' ) ) return $content;
        return (string) preg_replace_callback(
            '~<!--\s+wp:wpbb/swiper\s+(\{.*?\})\s+/-->~s',
            static function( $m ) use ( $urls ) {
                $attrs = json_decode( $m[1], true );
                if ( ! is_array( $attrs ) || 'hero' !== sanitize_key( (string) ( $attrs['demoStyle'] ?? '' ) ) ) return $m[0];
                $attrs['autoplay'] = true;
                $attrs['autoplayDelay'] = 8500;
                $attrs['pauseOnHover'] = true;
                $attrs['showPagination'] = true;
                $attrs['loop'] = ! empty( $attrs['slides'] ) && is_array( $attrs['slides'] ) ? count( $attrs['slides'] ) > 1 : true;
                $attrs['rewind'] = true;
                $attrs['speed'] = max( 650, min( 1100, absint( $attrs['speed'] ?? 750 ) ) );
                if ( ! empty( $attrs['slides'] ) && is_array( $attrs['slides'] ) ) {
                    foreach ( $attrs['slides'] as $i => $slide ) if ( is_array( $slide ) ) $attrs['slides'][ $i ]['image'] = $urls[ $i % count( $urls ) ];
                }
                if ( ! empty( $attrs['slidesJson'] ) && is_string( $attrs['slidesJson'] ) ) {
                    $slides = json_decode( $attrs['slidesJson'], true );
                    if ( is_array( $slides ) ) {
                        foreach ( $slides as $i => $slide ) if ( is_array( $slide ) ) $slides[ $i ]['image'] = $urls[ $i % count( $urls ) ];
                        $attrs['slidesJson'] = wp_json_encode( $slides, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
                    }
                }
                return '<!-- wp:wpbb/swiper ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ' /-->';
            },
            $content
        );
    }
}

if ( ! function_exists( 'wpbb_child_v116_image_fallbacks' ) ) {
    function wpbb_child_v116_image_fallbacks() {
        $urls = wpbb_child_v116_hero_urls();
        if ( function_exists( 'wpbb_child_381045_consistency_config' ) ) {
            $config = wpbb_child_381045_consistency_config();
            foreach ( array( 'about', 'hero' ) as $key ) {
                $url = wpbb_child_v116_asset_url( (string) ( $config[ $key ] ?? '' ) );
                if ( $url ) $urls[] = $url;
            }
            foreach ( (array) ( $config['gallery'] ?? array() ) as $item ) {
                $relative = is_array( $item ) ? (string) ( $item[0] ?? '' ) : (string) $item;
                $url = wpbb_child_v116_asset_url( $relative );
                if ( $url ) $urls[] = $url;
            }
        }
        return array_values( array_unique( array_filter( $urls ) ) );
    }
}

if ( ! function_exists( 'wpbb_child_v116_local_upload_exists' ) ) {
    function wpbb_child_v116_local_upload_exists( $url ) {
        $uploads = wp_upload_dir();
        if ( empty( $uploads['baseurl'] ) || empty( $uploads['basedir'] ) ) return null;
        $url_path = (string) wp_parse_url( $url, PHP_URL_PATH );
        $base_path = (string) wp_parse_url( $uploads['baseurl'], PHP_URL_PATH );
        if ( ! $url_path || ! $base_path || 0 !== strpos( $url_path, trailingslashit( $base_path ) ) ) return null;
        $relative = ltrim( substr( $url_path, strlen( $base_path ) ), '/' );
        if ( '' === $relative ) return null;
        return is_readable( trailingslashit( $uploads['basedir'] ) . $relative );
    }
}

if ( ! function_exists( 'wpbb_child_v116_image_needs_repair' ) ) {
    function wpbb_child_v116_image_needs_repair( $url ) {
        $url = html_entity_decode( (string) $url, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
        if ( '' === $url || 0 === strpos( $url, 'data:' ) || 0 === strpos( $url, 'blob:' ) ) return false;
        $active = '/wp-content/themes/' . get_stylesheet() . '/';
        if ( false !== strpos( $url, '/wp-content/themes/wp-bbtheme-child-' ) ) {
            if ( false === strpos( $url, $active ) ) return true;
            $marker = $active;
            $url_path = (string) wp_parse_url( $url, PHP_URL_PATH );
            $pos = strpos( $url_path, $marker );
            $relative = false === $pos ? '' : ltrim( substr( $url_path, $pos + strlen( $marker ) ), '/' );
            if ( $relative ) {
                $relative = (string) preg_replace( '~-[0-9]+x[0-9]+(?=\.[a-z0-9]+$)~i', '', $relative );
                return ! is_readable( get_stylesheet_directory() . '/' . $relative );
            }
        }
        $upload_exists = wpbb_child_v116_local_upload_exists( $url );
        if ( false === $upload_exists ) return true;
        if ( null === $upload_exists && false !== strpos( $url, '/wp-content/uploads/' ) && false !== strpos( strtolower( $url ), 'wpbb-' ) ) return true;
        return false;
    }
}

if ( ! function_exists( 'wpbb_child_v116_repair_img_tags' ) ) {
    function wpbb_child_v116_repair_img_tags( $content ) {
        $fallbacks = wpbb_child_v116_image_fallbacks();
        if ( ! $fallbacks || ! is_string( $content ) || false === stripos( $content, '<img' ) ) return $content;
        $index = 0;
        return (string) preg_replace_callback( '~<img\b[^>]*>~is', static function( $m ) use ( $fallbacks, &$index ) {
            $tag = $m[0];
            if ( ! preg_match( '~\bsrc\s*=\s*(["\'])(.*?)\1~is', $tag, $src_match ) ) return $tag;
            $src = html_entity_decode( (string) $src_match[2], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
            if ( ! wpbb_child_v116_image_needs_repair( $src ) ) return $tag;
            $replacement = $fallbacks[ $index % count( $fallbacks ) ];
            $index++;
            $tag = (string) preg_replace( '~\bsrc\s*=\s*(["\']).*?\1~is', 'src="' . esc_url( $replacement ) . '"', $tag, 1 );
            $tag = (string) preg_replace( '~\s+srcset\s*=\s*(["\']).*?\1~is', '', $tag );
            $tag = (string) preg_replace( '~\s+sizes\s*=\s*(["\']).*?\1~is', '', $tag );
            return $tag;
        }, $content );
    }
}

if ( ! function_exists( 'wpbb_child_v116_repair_page_content' ) ) {
    function wpbb_child_v116_repair_page_content( $content, $page_id = 0 ) {
        if ( ! is_string( $content ) || '' === $content ) return $content;
        $config = function_exists( 'wpbb_child_381045_consistency_config' ) ? wpbb_child_381045_consistency_config() : array();
        if ( $config && function_exists( 'wpbb_child_381045_fix_encoded_copy' ) ) $content = wpbb_child_381045_fix_encoded_copy( $content );
        if ( $config && function_exists( 'wpbb_child_381045_repair_swipers' ) ) $content = wpbb_child_381045_repair_swipers( $content, $config );
        if ( $config && $page_id && function_exists( 'wpbb_child_381045_replace_page_images' ) ) $content = wpbb_child_381045_replace_page_images( $content, $config, $page_id );
        $content = wpbb_child_v116_repair_swipers( $content );
        $content = wpbb_child_v116_repair_img_tags( $content );
        return wpbb_child_v116_normalize_partner_markup( $content );
    }
}

if ( ! function_exists( 'wpbb_child_v116_repair_pages' ) ) {
    function wpbb_child_v116_repair_pages() {
        $ids = get_posts( array(
            'post_type' => 'page', 'post_status' => array( 'publish','draft','pending','private','future' ),
            'posts_per_page' => -1, 'fields' => 'ids', 'no_found_rows' => true,
        ) );
        foreach ( $ids as $id ) {
            $old = (string) get_post_field( 'post_content', $id, 'raw' );
            if ( '' === $old ) continue;
            $front_id = absint( get_option( 'page_on_front' ) );
            $managed = '1' === (string) get_post_meta( $id, '_wp_theme_demo_managed', true )
                || ( $front_id && $front_id === (int) $id )
                || false !== strpos( $old, 'wp-theme-' )
                || false !== strpos( $old, '/wp-bbtheme-child-' );
            if ( ! $managed ) continue;
            $new = wpbb_child_v116_repair_page_content( $old, $id );
            if ( $new !== $old ) {
                wp_update_post( wp_slash( array( 'ID' => $id, 'post_content' => $new ) ) );
                clean_post_cache( $id );
            }
            update_post_meta( $id, '_wpbb_child_v116_repaired', '3.8.11.16' );
        }
    }
}

if ( ! function_exists( 'wpbb_child_v116_repair_media' ) ) {
    function wpbb_child_v116_repair_media() {
        if ( function_exists( 'wpbb_child_v112_repair_media' ) ) wpbb_child_v112_repair_media();
        if ( function_exists( 'wpbb_child_381046_schedule' ) ) wpbb_child_381046_schedule( true );
        if ( post_type_exists( 'product' ) && function_exists( 'wpbb_child_v75_refresh_woo_demo_products' ) ) {
            delete_option( 'wpbb_child_v75_products_' . sanitize_key( get_stylesheet() ) );
            wpbb_child_v75_refresh_woo_demo_products();
        }
    }
}

if ( ! function_exists( 'wpbb_child_v116_repair_all' ) ) {
    function wpbb_child_v116_repair_all( $rebuild = true ) {
        if ( $rebuild && function_exists( 'wpbb_child_v62_rebuild_demo_pages' ) ) wpbb_child_v62_rebuild_demo_pages( true );
        wpbb_child_v116_repair_media();
        wpbb_child_v116_repair_pages();
        if ( function_exists( 'wpbb_child_v114_translations_disabled' ) && wpbb_child_v114_translations_disabled() && function_exists( 'wpbb_child_v114_trash_non_english_content' ) ) {
            wpbb_child_v114_trash_non_english_content();
        }
    }
}

/* Unlike the old one-time marker, demo reset/import always runs this repair. */
if ( ! function_exists( 'wpbb_child_v116_after_demo_import' ) ) {
    function wpbb_child_v116_after_demo_import( $page_id = 0, $profile = array() ) {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) || wp_doing_ajax() ) return;
        wpbb_child_v116_repair_all( true );
        update_option( 'wpbb_child_v116_repair_' . sanitize_key( get_stylesheet() ), '3.8.11.16', false );
    }
    add_action( 'wp_theme_after_demo_import', 'wpbb_child_v116_after_demo_import', PHP_INT_MAX, 2 );
}

if ( ! function_exists( 'wpbb_child_v116_repair_once' ) ) {
    function wpbb_child_v116_repair_once() {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) || wp_doing_ajax() ) return;
        $key = 'wpbb_child_v116_repair_' . sanitize_key( get_stylesheet() );
        if ( '3.8.11.16' === (string) get_option( $key ) ) return;
        wpbb_child_v116_repair_all( true );
        update_option( $key, '3.8.11.16', false );
    }
    add_action( 'admin_init', 'wpbb_child_v116_repair_once', PHP_INT_MAX );
}

/* Keep future editor saves from reintroducing the known invalid raw paragraph. */
if ( ! function_exists( 'wpbb_child_v116_filter_post_data' ) ) {
    function wpbb_child_v116_filter_post_data( $data, $postarr ) {
        if ( in_array( (string) ( $data['post_type'] ?? '' ), array( 'page', 'post' ), true ) && ! empty( $data['post_content'] ) ) {
            $data['post_content'] = wpbb_child_v116_normalize_partner_markup( $data['post_content'] );
        }
        return $data;
    }
    add_filter( 'wp_insert_post_data', 'wpbb_child_v116_filter_post_data', PHP_INT_MAX, 2 );
}

if ( ! function_exists( 'wpbb_child_v116_enqueue' ) ) {
    function wpbb_child_v116_enqueue() {
        $version = wp_get_theme()->get( 'Version' );
        wp_enqueue_style( 'wpbb-suite-v116', get_stylesheet_directory_uri() . '/assets/suite-v116.css', array( 'wpbb-suite-v115' ), $version );
        /* Head loading lets an accepted cookie preference suppress the banner before paint. */
        wp_enqueue_script( 'wpbb-suite-v116', get_stylesheet_directory_uri() . '/assets/suite-v116.js', array(), $version, false );
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v116_enqueue', PHP_INT_MAX );
}
