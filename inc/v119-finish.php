<?php
/** WP BBTheme child suite 3.8.11.19 - live regression repair: closer mega menus, canonical gutters/grids and no-flash consent. */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v119_disable_superseded_runtime' ) ) {
    function wpbb_child_v119_disable_superseded_runtime() {
        // v119 owns runtime menu/consent positioning; keep earlier PHP repair helpers available.
        remove_action( 'wp_enqueue_scripts', 'wpbb_child_v118_enqueue', PHP_INT_MAX );
        remove_action( 'admin_init', 'wpbb_child_v118_repair_once', PHP_INT_MAX );
        remove_action( 'wp_theme_after_demo_import', 'wpbb_child_v118_after_demo_import', PHP_INT_MAX );
        remove_action( 'wp_theme_after_demo_reset', 'wpbb_child_v118_after_demo_import', PHP_INT_MAX );
        remove_action( 'wp_theme_demo_reset_complete', 'wpbb_child_v118_after_demo_import', PHP_INT_MAX );
        remove_action( 'wp_theme_starter_setup_complete', 'wpbb_child_v118_after_demo_import', PHP_INT_MAX );
        remove_filter( 'wp_insert_post_data', 'wpbb_child_v118_filter_post_data', PHP_INT_MAX );
    }
}
wpbb_child_v119_disable_superseded_runtime();
add_action( 'after_setup_theme', 'wpbb_child_v119_disable_superseded_runtime', PHP_INT_MAX );
add_action( 'init', 'wpbb_child_v119_disable_superseded_runtime', -9999 );

if ( ! function_exists( 'wpbb_child_v119_cookie_accepted_server' ) ) {
    function wpbb_child_v119_cookie_accepted_server() {
        $keys = array(
            'wpbb_child_cookie_consent', 'wpbb_v116_cookie_consent', 'wpbb_v117_cookie_consent',
            'wpbb_v118_cookie_consent', 'wpbb_v119_cookie_consent', 'wpbb_cookie_consent',
            'wp_theme_cookie_consent', 'cookie_consent', 'cookieConsent', 'cookie_consent_status',
            'cookieConsentStatus', 'wpbb_consent', 'wpbb_consent_status', 'wpbb_cookie_consent_status',
        );
        foreach ( $keys as $key ) {
            if ( ! isset( $_COOKIE[ $key ] ) ) continue;
            $value = strtolower( trim( sanitize_text_field( wp_unslash( $_COOKIE[ $key ] ) ) ) );
            if ( in_array( $value, array( 'accepted', 'accept', 'all', 'allow', 'allowed', 'granted', 'true', '1', 'yes' ), true ) ) return true;
        }
        return false;
    }
}

if ( ! function_exists( 'wpbb_child_v119_html_classes' ) ) {
    function wpbb_child_v119_html_classes( $output ) {
        if ( ! wpbb_child_v119_cookie_accepted_server() ) return $output;
        if ( false !== strpos( $output, 'wpbb-v119-consent-accepted' ) ) return $output;
        return preg_replace( '/class=([' . "\"'" . '])([^' . "\"'" . ']*)\\1/', 'class=$1$2 wpbb-v119-consent-accepted$1', $output, 1 );
    }
}

if ( ! function_exists( 'wpbb_child_v119_body_class' ) ) {
    function wpbb_child_v119_body_class( $classes ) {
        if ( wpbb_child_v119_cookie_accepted_server() ) $classes[] = 'wpbb-v119-consent-accepted';
        return array_values( array_unique( $classes ) );
    }
    add_filter( 'body_class', 'wpbb_child_v119_body_class', PHP_INT_MAX );
}

if ( ! function_exists( 'wpbb_child_v119_consent_bootstrap' ) ) {
    function wpbb_child_v119_consent_bootstrap() {
        ?>
<style id="wpbb-v119-consent-guard">html.wpbb-v119-consent-accepted :is(.wpbb-cookie-consent,.wpbb-cookie-consent-banner,.wpbb-cookie-consent__banner,.wpbb-cookie-banner,[data-wpbb-cookie-consent],[data-wpbb-cookie-banner],[data-cookie-consent-banner],.wp-theme-cookie-banner,[class*="cookie-banner"],[class*="cookie_banner"],[class*="cookie-consent"],[class*="consent-banner"],[id*="cookie-banner"],[id*="cookie_banner"],[id*="cookie-consent"],[id*="consent-banner"],#cookie-law-info-bar,.cookie-notice-container,.cky-consent-container,.cmplz-cookiebanner){display:none!important;visibility:hidden!important;opacity:0!important;pointer-events:none!important}</style>
<script id="wpbb-v119-consent-bootstrap">(function(){try{var h=document.documentElement,k=['wpbb_child_cookie_consent','wpbb_v116_cookie_consent','wpbb_v117_cookie_consent','wpbb_v118_cookie_consent','wpbb_v119_cookie_consent','wpbb_cookie_consent','wp_theme_cookie_consent','cookie_consent','cookieConsent'],ok=false,v,i,n;function yes(x){x=String(x==null?'':x).trim();return /^(accepted?|all|allow(?:ed)?|granted|true|1|yes)$/i.test(x)||(/^[{[]/.test(x)&&/(accepted?|granted|allow(?:ed)?)[\"']?\s*[:=]\s*[\"']?(?:true|1|yes|all)/i.test(x));}for(i=0;i<k.length;i++){v=localStorage.getItem(k[i]);if(yes(v)){ok=true;break}}if(!ok){for(i=0;i<localStorage.length;i++){n=localStorage.key(i)||'';if(/cookie|consent/i.test(n)&&yes(localStorage.getItem(n))){ok=true;break}}}if(!ok){var parts=(document.cookie||'').split(';');for(i=0;i<parts.length;i++){var p=parts[i].split('='),name=(p.shift()||'').trim(),val=decodeURIComponent(p.join('=')||'');if(/cookie|consent/i.test(name)&&yes(val)){ok=true;break}}}if(ok){h.classList.add('wpbb-v119-consent-accepted');try{localStorage.setItem('wpbb_child_cookie_consent','accepted');localStorage.setItem('wpbb_v119_cookie_consent','accepted')}catch(e){}try{document.cookie='wpbb_child_cookie_consent=accepted; Max-Age=31536000; Path=/; SameSite=Lax'+(location.protocol==='https:'?'; Secure':'')}catch(e){}}}catch(e){}})();</script>
        <?php
    }
    add_action( 'wp_head', 'wpbb_child_v119_consent_bootstrap', 0 );
}

if ( ! function_exists( 'wpbb_child_v119_repair_page_content' ) ) {
    function wpbb_child_v119_repair_page_content( $content, $page_id = 0 ) {
        if ( function_exists( 'wpbb_child_v118_repair_page_content' ) ) return wpbb_child_v118_repair_page_content( $content, $page_id );
        return $content;
    }
}

if ( ! function_exists( 'wpbb_child_v119_repair_all' ) ) {
    function wpbb_child_v119_repair_all() {
        wpbb_child_v119_disable_superseded_runtime();
        // Use the complete v118 canonical/media repair once, then let v119 own front-end geometry.
        if ( function_exists( 'wpbb_child_v118_repair_all' ) ) {
            wpbb_child_v118_repair_all();
        } elseif ( function_exists( 'wpbb_child_v62_rebuild_demo_pages' ) ) {
            wpbb_child_v62_rebuild_demo_pages( true );
        }
        if ( function_exists( 'wpbb_child_v117_drain_media_worker' ) ) wpbb_child_v117_drain_media_worker();
        if ( post_type_exists( 'product' ) && function_exists( 'wpbb_child_v75_refresh_woo_demo_products' ) ) {
            delete_option( 'wpbb_child_v75_products_' . sanitize_key( get_stylesheet() ) );
            wpbb_child_v75_refresh_woo_demo_products();
        }
    }
}

if ( ! function_exists( 'wpbb_child_v119_repair_once' ) ) {
    function wpbb_child_v119_repair_once() {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) || wp_doing_ajax() ) return;
        $key = 'wpbb_child_v119_repair_' . sanitize_key( get_stylesheet() );
        if ( '3.8.11.19' === (string) get_option( $key ) ) return;
        wpbb_child_v119_repair_all();
        update_option( $key, '3.8.11.19', false );
    }
    add_action( 'admin_init', 'wpbb_child_v119_repair_once', PHP_INT_MAX );
}

if ( ! function_exists( 'wpbb_child_v119_after_demo_import' ) ) {
    function wpbb_child_v119_after_demo_import( $page_id = 0, $profile = array() ) {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) || wp_doing_ajax() ) return;
        wpbb_child_v119_repair_all();
        update_option( 'wpbb_child_v119_repair_' . sanitize_key( get_stylesheet() ), '3.8.11.19', false );
    }
    add_action( 'wp_theme_after_demo_import', 'wpbb_child_v119_after_demo_import', PHP_INT_MAX, 2 );
    add_action( 'wp_theme_after_demo_reset', 'wpbb_child_v119_after_demo_import', PHP_INT_MAX );
    add_action( 'wp_theme_demo_reset_complete', 'wpbb_child_v119_after_demo_import', PHP_INT_MAX );
    add_action( 'wp_theme_starter_setup_complete', 'wpbb_child_v119_after_demo_import', PHP_INT_MAX );
}

if ( ! function_exists( 'wpbb_child_v119_filter_post_data' ) ) {
    function wpbb_child_v119_filter_post_data( $data, $postarr ) {
        if ( in_array( (string) ( $data['post_type'] ?? '' ), array( 'page', 'post' ), true ) && ! empty( $data['post_content'] ) ) {
            $content = (string) $data['post_content'];
            if ( false !== strpos( $content, 'wpbb/' ) || false !== strpos( $content, 'wp-theme-' ) ) {
                $data['post_content'] = wpbb_child_v119_repair_page_content( $content, absint( $postarr['ID'] ?? 0 ) );
            }
        }
        return $data;
    }
    add_filter( 'wp_insert_post_data', 'wpbb_child_v119_filter_post_data', PHP_INT_MAX, 2 );
}

if ( ! function_exists( 'wpbb_child_v119_enqueue' ) ) {
    function wpbb_child_v119_enqueue() {
        // Stop the v116-v118 JS position/consent loops from competing with v119. Keep their CSS as historical base layers.
        foreach ( array( 'wpbb-suite-v116', 'wpbb-suite-v117', 'wpbb-suite-v118' ) as $handle ) {
            wp_dequeue_script( $handle );
            wp_deregister_script( $handle );
        }
        $base_css = get_stylesheet_directory() . '/assets/suite-v118.css';
        if ( is_readable( $base_css ) && ! wp_style_is( 'wpbb-suite-v118', 'enqueued' ) ) {
            wp_enqueue_style( 'wpbb-suite-v118', get_stylesheet_directory_uri() . '/assets/suite-v118.css', array(), (string) filemtime( $base_css ) );
        }
        $css = get_stylesheet_directory() . '/assets/suite-v119.css';
        $js  = get_stylesheet_directory() . '/assets/suite-v119.js';
        if ( is_readable( $css ) ) wp_enqueue_style( 'wpbb-suite-v119', get_stylesheet_directory_uri() . '/assets/suite-v119.css', array( 'wpbb-suite-v118' ), (string) filemtime( $css ) );
        if ( is_readable( $js ) ) wp_enqueue_script( 'wpbb-suite-v119', get_stylesheet_directory_uri() . '/assets/suite-v119.js', array(), (string) filemtime( $js ), false );
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v119_enqueue', PHP_INT_MAX );
}
