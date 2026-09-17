<?php
defined( 'ABSPATH' ) || exit;

/**
 * Shared child-theme SEO guardrails.
 * - Preserves hand-written Yoast descriptions.
 * - Creates a useful fallback description for every public view.
 * - Backfills missing Yoast page descriptions without overwriting existing SEO copy.
 * - Ensures rendered frontend HTML contains exactly one H1.
 */

if ( ! function_exists( 'wpbb_child_seo_clean_text' ) ) {
    function wpbb_child_seo_clean_text( $value ) {
        $value = strip_shortcodes( (string) $value );
        $value = wp_strip_all_tags( $value, true );
        $value = html_entity_decode( $value, ENT_QUOTES, get_bloginfo( 'charset' ) ?: 'UTF-8' );
        $value = preg_replace( '/\s+/u', ' ', $value );
        return trim( (string) $value );
    }
}

if ( ! function_exists( 'wpbb_child_seo_trim_description' ) ) {
    function wpbb_child_seo_trim_description( $value, $length = 155 ) {
        $value = wpbb_child_seo_clean_text( $value );
        if ( '' === $value ) return '';
        if ( function_exists( 'mb_strlen' ) && mb_strlen( $value ) <= $length ) return $value;
        if ( ! function_exists( 'mb_strlen' ) && strlen( $value ) <= $length ) return $value;
        $cut = function_exists( 'mb_substr' ) ? mb_substr( $value, 0, $length ) : substr( $value, 0, $length );
        $cut = preg_replace( '/\s+\S*$/u', '', $cut );
        $cut = rtrim( (string) $cut, " \t\n\r\0\x0B,.;:-" );
        return $cut . '…';
    }
}

if ( ! function_exists( 'wpbb_child_seo_post_description' ) ) {
    function wpbb_child_seo_post_description( $post_id ) {
        $post = get_post( $post_id );
        if ( ! $post ) return '';

        $candidates = array(
            $post->post_excerpt,
            $post->post_content,
        );
        $text = '';
        foreach ( $candidates as $candidate ) {
            $clean = wpbb_child_seo_clean_text( $candidate );
            if ( '' !== $clean ) {
                $text = $clean;
                break;
            }
        }

        $site_description = wpbb_child_seo_clean_text( get_bloginfo( 'description' ) );
        $title = wpbb_child_seo_clean_text( get_the_title( $post ) );
        if ( '' === $text ) {
            $text = trim( $title . ( $site_description ? ' — ' . $site_description : '' ) );
        } elseif ( ( function_exists( 'mb_strlen' ) ? mb_strlen( $text ) : strlen( $text ) ) < 80 && $site_description ) {
            $text .= ' ' . $site_description;
        }
        return wpbb_child_seo_trim_description( $text );
    }
}

if ( ! function_exists( 'wpbb_child_seo_current_description' ) ) {
    function wpbb_child_seo_current_description() {
        if ( is_singular() ) {
            return wpbb_child_seo_post_description( get_queried_object_id() );
        }
        if ( is_home() ) {
            $posts_page = (int) get_option( 'page_for_posts' );
            if ( $posts_page ) {
                $description = wpbb_child_seo_post_description( $posts_page );
                if ( $description ) return $description;
            }
        }
        if ( is_category() || is_tag() || is_tax() ) {
            $description = wpbb_child_seo_trim_description( term_description() );
            if ( $description ) return $description;
        }
        if ( is_post_type_archive() ) {
            $object = get_queried_object();
            if ( $object && ! empty( $object->description ) ) {
                $description = wpbb_child_seo_trim_description( $object->description );
                if ( $description ) return $description;
            }
        }
        if ( is_search() ) {
            return wpbb_child_seo_trim_description( sprintf( __( 'Search results for “%1$s” on %2$s.', 'wp-bbtheme-child' ), get_search_query(), get_bloginfo( 'name' ) ) );
        }
        if ( is_404() ) {
            return wpbb_child_seo_trim_description( sprintf( __( 'The requested page could not be found on %s.', 'wp-bbtheme-child' ), get_bloginfo( 'name' ) ) );
        }
        $site_description = wpbb_child_seo_trim_description( get_bloginfo( 'description' ) );
        return $site_description ?: wpbb_child_seo_trim_description( get_bloginfo( 'name' ) );
    }
}

if ( ! function_exists( 'wpbb_child_yoast_description_fallback' ) ) {
    function wpbb_child_yoast_description_fallback( $description ) {
        if ( '' !== wpbb_child_seo_clean_text( $description ) ) return $description;
        return wpbb_child_seo_current_description();
    }
}
add_filter( 'wpseo_metadesc', 'wpbb_child_yoast_description_fallback', 30 );
add_filter( 'wpseo_opengraph_desc', 'wpbb_child_yoast_description_fallback', 30 );
add_filter( 'wpseo_twitter_description', 'wpbb_child_yoast_description_fallback', 30 );

if ( ! function_exists( 'wpbb_child_native_meta_description' ) ) {
    function wpbb_child_native_meta_description() {
        $has_seo_plugin = defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || class_exists( 'WPSEO_Frontend' );
        if ( $has_seo_plugin ) return;
        $description = wpbb_child_seo_current_description();
        if ( $description ) echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'wpbb_child_native_meta_description', 4 );

if ( ! function_exists( 'wpbb_child_fill_missing_yoast_page_description' ) ) {
    function wpbb_child_fill_missing_yoast_page_description( $post_id, $post = null ) {
        if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) return;
        $post = $post ?: get_post( $post_id );
        if ( ! $post || 'page' !== $post->post_type ) return;
        $existing = (string) get_post_meta( $post_id, '_yoast_wpseo_metadesc', true );
        if ( '' !== wpbb_child_seo_clean_text( $existing ) ) return;
        $description = wpbb_child_seo_post_description( $post_id );
        if ( $description ) update_post_meta( $post_id, '_yoast_wpseo_metadesc', $description );
    }
}

if ( ! function_exists( 'wpbb_child_fill_missing_yoast_page_description_on_save' ) ) {
    function wpbb_child_fill_missing_yoast_page_description_on_save( $post_id, $post, $update ) {
        wpbb_child_fill_missing_yoast_page_description( $post_id, $post );
    }
}
add_action( 'save_post_page', 'wpbb_child_fill_missing_yoast_page_description_on_save', 30, 3 );

if ( ! function_exists( 'wpbb_child_backfill_yoast_page_descriptions' ) ) {
    function wpbb_child_backfill_yoast_page_descriptions() {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) return;
        if ( ! defined( 'WPSEO_VERSION' ) && ! class_exists( 'WPSEO_Frontend' ) ) return;
        $key = 'wpbb_child_yoast_desc_381022_' . md5( get_stylesheet() );
        if ( get_option( $key ) ) return;
        $ids = get_posts( array(
            'post_type'      => 'page',
            'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'orderby'        => 'ID',
            'order'          => 'ASC',
            'no_found_rows'  => true,
        ) );
        foreach ( $ids as $post_id ) wpbb_child_fill_missing_yoast_page_description( (int) $post_id );
        update_option( $key, gmdate( 'c' ), false );
    }
}
add_action( 'admin_init', 'wpbb_child_backfill_yoast_page_descriptions', 80 );

if ( ! function_exists( 'wpbb_child_frontend_heading_title' ) ) {
    function wpbb_child_frontend_heading_title() {
        if ( is_singular() ) return get_the_title( get_queried_object_id() );
        if ( is_home() ) {
            $posts_page = (int) get_option( 'page_for_posts' );
            return $posts_page ? get_the_title( $posts_page ) : __( 'Blog', 'wp-bbtheme-child' );
        }
        if ( is_category() || is_tag() || is_tax() ) return single_term_title( '', false );
        if ( is_post_type_archive() ) return post_type_archive_title( '', false );
        if ( is_search() ) return sprintf( __( 'Search results for %s', 'wp-bbtheme-child' ), get_search_query() );
        if ( is_404() ) return __( 'Page not found', 'wp-bbtheme-child' );
        return get_bloginfo( 'name' );
    }
}

if ( ! function_exists( 'wpbb_child_normalize_frontend_h1' ) ) {
    function wpbb_child_normalize_frontend_h1( $html ) {
        if ( ! is_string( $html ) || false === stripos( $html, '<html' ) ) return $html;

        $seen = 0;
        $html = preg_replace_callback(
            '~<h1(\b[^>]*)>(.*?)</h1\s*>~is',
            static function( $match ) use ( &$seen ) {
                $seen++;
                if ( 1 === $seen ) return $match[0];
                return '<h2' . $match[1] . '>' . $match[2] . '</h2>';
            },
            $html
        );
        if ( $seen > 0 ) return $html;

        $main_pattern = '~(<main\b[^>]*\bid=["\']wp-theme-main["\'][^>]*>)(.*?)(</main\s*>)~is';
        if ( preg_match( $main_pattern, $html ) ) {
            $html = preg_replace_callback(
                $main_pattern,
                static function( $match ) {
                    $inner = preg_replace( '~<h2(\b[^>]*)>(.*?)</h2\s*>~is', '<h1$1>$2</h1>', $match[2], 1, $promoted );
                    if ( $promoted ) return $match[1] . $inner . $match[3];
                    $title = wpbb_child_frontend_heading_title();
                    return $match[1] . '<h1 class="wpbb-seo-only-heading">' . esc_html( $title ) . '</h1>' . $match[2] . $match[3];
                },
                $html,
                1
            );
            return $html;
        }

        $title = wpbb_child_frontend_heading_title();
        return preg_replace( '~(<body\b[^>]*>)~i', '$1<h1 class="wpbb-seo-only-heading">' . esc_html( $title ) . '</h1>', $html, 1 );
    }
}

if ( ! function_exists( 'wpbb_child_start_frontend_h1_guard' ) ) {
    function wpbb_child_start_frontend_h1_guard() {
        if ( is_admin() || wp_doing_ajax() || is_feed() || is_embed() || is_robots() || is_trackback() ) return;
        if ( function_exists( 'wp_is_json_request' ) && wp_is_json_request() ) return;
        ob_start( 'wpbb_child_normalize_frontend_h1' );
    }
}
add_action( 'template_redirect', 'wpbb_child_start_frontend_h1_guard', 999 );
