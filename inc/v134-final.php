<?php
/**
 * v3.8.11.34 final cross-theme visual ownership layer.
 *
 * Applies only to the maintained demo child themes (Jobs intentionally not
 * packaged in this release). It retires the late competing DOM runtimes and
 * owns hero media/pagination, component grids/process cards and WooCommerce
 * catalogue/product/cart/checkout/account layouts.
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v134_theme_key' ) ) {
    function wpbb_child_v134_theme_key() {
        $slug = strtolower( basename( get_stylesheet_directory() ) );
        $slug = preg_replace( '/^wp-bbtheme-child-/', '', $slug );
        return sanitize_key( $slug );
    }
}

if ( ! function_exists( 'wpbb_child_v134_profile' ) ) {
    function wpbb_child_v134_profile() {
        $key = wpbb_child_v134_theme_key();
        $profiles = array(
            'automotive' => array(
                array( '01', 'Search', 'Filter vehicles, parts and services by the details that matter.' ),
                array( '02', 'Compare', 'Review specifications, availability and pricing side by side.' ),
                array( '03', 'Act', 'Enquire, arrange a visit, book service or add the right part to your basket.' ),
            ),
            'building-services' => array(
                array( '01', 'Find', 'Choose the trade, location and type of property work you need.' ),
                array( '02', 'Describe', 'Share the job details, urgency and access information clearly.' ),
                array( '03', 'Schedule', 'Route the request to the right team for pricing or attendance.' ),
            ),
            'business' => array(
                array( '01', 'Discover', 'Agree audiences, goals, content priorities and the journeys that matter most.' ),
                array( '02', 'Design', 'Create a reusable system of components, page patterns and useful interactions.' ),
                array( '03', 'Deliver', 'Launch a fast WordPress site your team can maintain, measure and improve.' ),
            ),
            'elearning' => array(
                array( '01', 'Choose', 'Pick a course that matches the skill or subject you want to build.' ),
                array( '02', 'Learn', 'Follow lessons, resources and examples in a clear sequence.' ),
                array( '03', 'Check', 'Use knowledge checks and progress cues to confirm understanding.' ),
            ),
            'hotel' => array(
                array( '01', 'Explore', 'Compare rooms, amenities and stay details without unnecessary steps.' ),
                array( '02', 'Request', 'Choose dates, guests and the room option that fits your stay.' ),
                array( '03', 'Confirm', 'Review the essentials and complete a clear booking journey.' ),
            ),
            'insurance' => array(
                array( '01', 'Compare', 'Review cover options and the details that affect the right choice.' ),
                array( '02', 'Protect', 'Select suitable protection with clear pricing and policy information.' ),
                array( '03', 'Manage', 'Keep documents, renewals and support routes easy to find.' ),
            ),
            'logistics' => array(
                array( '01', 'Plan', 'Define the route, service level and shipment requirements.' ),
                array( '02', 'Track', 'Keep milestones, status and useful updates visible through the journey.' ),
                array( '03', 'Deliver', 'Complete the movement with clear handover and outcome information.' ),
            ),
            'medicine' => array(
                array( '01', 'Search', 'Find the right service, clinician or information for the next step.' ),
                array( '02', 'Book', 'Choose an appropriate time and provide the details needed for the visit.' ),
                array( '03', 'Follow up', 'Keep guidance, results and useful next actions easy to access.' ),
            ),
            'realestate' => array(
                array( '01', 'Search', 'Narrow properties by location, type, budget and useful features.' ),
                array( '02', 'View', 'Compare the facts, imagery and practical details before enquiring.' ),
                array( '03', 'Enquire', 'Send the right information to arrange a viewing or discuss the next step.' ),
            ),
            'restaurant' => array(
                array( '01', 'Browse', 'Explore menus, opening information and the dining options available.' ),
                array( '02', 'Book', 'Choose a date, time and party size through a simple reservation flow.' ),
                array( '03', 'Dine', 'Arrive with the essential booking and venue details already clear.' ),
            ),
            'travel' => array(
                array( '01', 'Discover', 'Explore destinations, stays and experiences that match the trip.' ),
                array( '02', 'Plan', 'Compare dates, options and practical details in one clear journey.' ),
                array( '03', 'Book', 'Choose the right option and complete the next step with confidence.' ),
            ),
            'woo-clouthes' => array(
                array( '01', 'Browse', 'Explore collections, sizes and useful product details.' ),
                array( '02', 'Choose', 'Compare options and select the right variation for your order.' ),
                array( '03', 'Order', 'Review the basket, checkout clearly and manage the order from your account.' ),
            ),
            'woo-events' => array(
                array( '01', 'Discover', 'Browse upcoming events by date, category and the experience you want.' ),
                array( '02', 'Book', 'Choose tickets or event options and review the details before checkout.' ),
                array( '03', 'Attend', 'Keep order and event information available from your account.' ),
            ),
            'woo-tech-shop' => array(
                array( '01', 'Compare', 'Review specifications, availability and the features that matter.' ),
                array( '02', 'Configure', 'Choose the right product or option for the way you will use it.' ),
                array( '03', 'Order', 'Complete checkout and keep orders, addresses and account details together.' ),
            ),
        );
        return isset( $profiles[ $key ] ) ? $profiles[ $key ] : array(
            array( '01', 'Explore', 'Find the most useful option for what you need.' ),
            array( '02', 'Compare', 'Review the important details in a clear, consistent layout.' ),
            array( '03', 'Continue', 'Take the next action without unnecessary friction.' ),
        );
    }
}

if ( ! function_exists( 'wpbb_child_v134_hero_urls' ) ) {
    function wpbb_child_v134_hero_urls() {
        $dir  = get_stylesheet_directory();
        $uri  = get_stylesheet_directory_uri();
        $urls = array();
        foreach ( array( 1, 2, 3 ) as $i ) {
            $rel  = 'assets/img/hero-v134/slide-' . $i . '.jpg';
            $path = $dir . '/' . $rel;
            if ( is_file( $path ) ) {
                $urls[] = $uri . '/' . $rel . '?v=' . filemtime( $path );
            }
        }
        return $urls;
    }
}

if ( ! function_exists( 'wpbb_child_v134_enqueue' ) ) {
    function wpbb_child_v134_enqueue() {
        $dir = get_stylesheet_directory();
        $uri = get_stylesheet_directory_uri();

        // Retire the late DOM owners that were racing each other on imported
        // demo pages. Their CSS may remain for unaffected components; v134 is
        // enqueued last and owns only the repaired component classes.
        foreach ( range( 119, 133 ) as $n ) {
            $handle = 'wpbb-suite-v' . $n;
            wp_dequeue_script( $handle );
            wp_deregister_script( $handle );
        }

        $css = '/assets/suite-v134.css';
        $js  = '/assets/suite-v134.js';
        wp_enqueue_style(
            'wpbb-suite-v134',
            $uri . $css,
            array(),
            is_file( $dir . $css ) ? filemtime( $dir . $css ) : '3.8.11.34'
        );
        wp_enqueue_script(
            'wpbb-suite-v134',
            $uri . $js,
            array(),
            is_file( $dir . $js ) ? filemtime( $dir . $js ) : '3.8.11.34',
            true
        );
        wp_add_inline_script(
            'wpbb-suite-v134',
            'window.wpbbSuiteV134=' . wp_json_encode( array(
                'version'       => '3.8.11.34',
                'themeKey'      => wpbb_child_v134_theme_key(),
                'heroUrls'      => wpbb_child_v134_hero_urls(),
                'fallbackCards' => wpbb_child_v134_profile(),
            ) ) . ';',
            'before'
        );
    }
}
add_action( 'wp_enqueue_scripts', 'wpbb_child_v134_enqueue', PHP_INT_MAX );

if ( ! function_exists( 'wpbb_child_v134_body_class' ) ) {
    function wpbb_child_v134_body_class( $classes ) {
        $classes[] = 'wpbb-v134';
        $classes[] = 'wpbb-v134-theme-' . wpbb_child_v134_theme_key();
        return array_values( array_unique( $classes ) );
    }
}
add_filter( 'body_class', 'wpbb_child_v134_body_class', PHP_INT_MAX );

/* Checkout: remove only newsletter opt-in fields. Never remove a generic
 * checkout container, order review or payment node. */
if ( ! function_exists( 'wpbb_child_v134_checkout_fields' ) ) {
    function wpbb_child_v134_checkout_fields( $fields ) {
        foreach ( $fields as $group => $group_fields ) {
            if ( ! is_array( $group_fields ) ) continue;
            foreach ( $group_fields as $key => $field ) {
                $haystack = strtolower( $key . ' ' . ( isset( $field['label'] ) ? wp_strip_all_tags( $field['label'] ) : '' ) );
                if ( false !== strpos( $haystack, 'newsletter' ) || false !== strpos( $haystack, 'newslatter' ) || false !== strpos( $haystack, 'email updates' ) ) {
                    unset( $fields[ $group ][ $key ] );
                }
            }
        }
        return $fields;
    }
}
add_filter( 'woocommerce_checkout_fields', 'wpbb_child_v134_checkout_fields', 9999 );

/* Keep WooCommerce account endpoints usable on demo installs even when an old
 * permalink snapshot was imported with the page content. */
if ( ! function_exists( 'wpbb_child_v134_account_endpoints' ) ) {
    function wpbb_child_v134_account_endpoints() {
        if ( ! class_exists( 'WooCommerce' ) ) return;
        foreach ( array( 'orders', 'downloads', 'edit-address', 'edit-account', 'payment-methods', 'lost-password' ) as $endpoint ) {
            add_rewrite_endpoint( $endpoint, EP_ROOT | EP_PAGES );
        }
    }
}
add_action( 'init', 'wpbb_child_v134_account_endpoints', 99 );

if ( ! function_exists( 'wpbb_child_v134_flush_rules_once' ) ) {
    function wpbb_child_v134_flush_rules_once() {
        if ( ! class_exists( 'WooCommerce' ) ) return;
        if ( '3.8.11.34' === get_option( 'wpbb_child_v134_rewrite_version' ) ) return;
        flush_rewrite_rules( false );
        update_option( 'wpbb_child_v134_rewrite_version', '3.8.11.34', false );
    }
}
add_action( 'wp_loaded', 'wpbb_child_v134_flush_rules_once', 999 );

if ( ! function_exists( 'wpbb_child_v134_account_404_redirect' ) ) {
    function wpbb_child_v134_account_404_redirect() {
        if ( ! class_exists( 'WooCommerce' ) || ! is_404() ) return;
        $path = trim( (string) wp_parse_url( home_url( add_query_arg( array() ) ), PHP_URL_PATH ), '/' );
        if ( isset( $_SERVER['REQUEST_URI'] ) ) {
            $path = trim( (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ), '/' );
        }
        $allowed = array( 'orders', 'downloads', 'edit-address', 'edit-account', 'payment-methods', 'lost-password' );
        if ( ! in_array( $path, $allowed, true ) ) return;
        $base = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/my-account/' );
        wp_safe_redirect( trailingslashit( $base ) . trailingslashit( $path ), 301 );
        exit;
    }
}
add_action( 'template_redirect', 'wpbb_child_v134_account_404_redirect', 1 );
