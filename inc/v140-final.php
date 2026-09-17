<?php
/**
 * WP BBTheme child suite 3.8.11.40 — exact header grid + sector proof content.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! function_exists( 'wpbb_child_v140_sector_cards' ) ) {
    function wpbb_child_v140_sector_cards() {
        $stylesheet = (string) get_stylesheet();
        if ( false !== strpos( $stylesheet, 'building-services' ) ) {
            return array(
                array(
                    'title' => 'Multi-trade coordination',
                    'text'  => 'Plumbing, electrical and maintenance work are routed through one clear service workflow.',
                ),
                array(
                    'title' => 'Transparent call-outs',
                    'text'  => 'Response targets, coverage and call-out expectations are visible before a customer enquires.',
                ),
                array(
                    'title' => 'Property portfolio support',
                    'text'  => 'Repeat maintenance, snagging and planned works stay organised across homes and managed sites.',
                ),
            );
        }
        return array(
            array(
                'title' => 'Strategy & discovery',
                'text'  => 'Audiences, journeys and content priorities are agreed before design work begins.',
            ),
            array(
                'title' => 'Reusable content system',
                'text'  => 'Editable Gutenberg patterns keep pages consistent without locking the team into a page builder.',
            ),
            array(
                'title' => 'Growth & optimisation',
                'text'  => 'Measure performance, improve journeys and publish new campaigns from the same design system.',
            ),
        );
    }
}

if ( ! function_exists( 'wpbb_child_v140_enqueue' ) ) {
    function wpbb_child_v140_enqueue() {
        $dir = get_stylesheet_directory();
        $uri = get_stylesheet_directory_uri();
        $css = '/assets/suite-v140.css';
        $js  = '/assets/suite-v140.js';

        wp_enqueue_style(
            'wpbb-suite-v140',
            $uri . $css,
            array( 'wpbb-suite-v139' ),
            is_file( $dir . $css ) ? filemtime( $dir . $css ) : '3.8.11.40'
        );
        wp_enqueue_script(
            'wpbb-suite-v140',
            $uri . $js,
            array(),
            is_file( $dir . $js ) ? filemtime( $dir . $js ) : '3.8.11.40',
            true
        );
        wp_localize_script(
            'wpbb-suite-v140',
            'wpbbSuiteV140',
            array(
                'version' => '3.8.11.40',
                'cards'   => wpbb_child_v140_sector_cards(),
            )
        );
    }
}
add_action( 'wp_enqueue_scripts', 'wpbb_child_v140_enqueue', PHP_INT_MAX );

if ( ! function_exists( 'wpbb_child_v140_body_class' ) ) {
    function wpbb_child_v140_body_class( $classes ) {
        $classes[] = 'wpbb-v140';
        return array_values( array_unique( $classes ) );
    }
}
add_filter( 'body_class', 'wpbb_child_v140_body_class', PHP_INT_MAX );

if ( ! function_exists( 'wpbb_child_v140_replace_occurrences' ) ) {
    function wpbb_child_v140_replace_occurrences( $content, $needle, $replacements ) {
        $content = (string) $content;
        foreach ( (array) $replacements as $replacement ) {
            $pos = strpos( $content, $needle );
            if ( false === $pos ) break;
            $content = substr_replace( $content, (string) $replacement, $pos, strlen( $needle ) );
        }
        return $content;
    }
}

if ( ! function_exists( 'wpbb_child_v140_repair_placeholder_string' ) ) {
    function wpbb_child_v140_repair_placeholder_string( $content ) {
        if ( ! is_string( $content ) || '' === $content ) return $content;
        if ( false === strpos( $content, 'Card title' ) && false === strpos( $content, 'Add a short description.' ) ) return $content;

        $cards  = wpbb_child_v140_sector_cards();
        $titles = array();
        $texts  = array();
        foreach ( $cards as $card ) {
            $titles[] = (string) $card['title'];
            $texts[]  = (string) $card['text'];
        }
        $content = wpbb_child_v140_replace_occurrences( $content, 'Card title', $titles );
        $content = wpbb_child_v140_replace_occurrences( $content, 'Add a short description.', $texts );
        return $content;
    }
}

/* Immediate frontend fallback for already-imported demo pages. */
if ( ! function_exists( 'wpbb_child_v140_rendered_content' ) ) {
    function wpbb_child_v140_rendered_content( $content ) {
        if ( is_admin() || ! is_front_page() ) return $content;
        return wpbb_child_v140_repair_placeholder_string( $content );
    }
}
add_filter( 'the_content', 'wpbb_child_v140_rendered_content', 9999 );

/* Repair placeholder wpbb/icon-card attributes before the block renders. */
if ( ! function_exists( 'wpbb_child_v140_render_block_data' ) ) {
    function wpbb_child_v140_render_block_data( $parsed_block ) {
        if ( is_admin() || ! is_front_page() || empty( $parsed_block['attrs'] ) || ! is_array( $parsed_block['attrs'] ) ) return $parsed_block;
        $title = isset( $parsed_block['attrs']['title'] ) ? trim( (string) $parsed_block['attrs']['title'] ) : '';
        $text  = isset( $parsed_block['attrs']['text'] ) ? trim( (string) $parsed_block['attrs']['text'] ) : '';
        if ( 'Card title' !== $title && 'Add a short description.' !== $text ) return $parsed_block;

        static $index = 0;
        $cards = wpbb_child_v140_sector_cards();
        $card  = $cards[ min( $index, count( $cards ) - 1 ) ];
        $parsed_block['attrs']['title'] = $card['title'];
        $parsed_block['attrs']['text']  = $card['text'];
        $index++;
        return $parsed_block;
    }
}
add_filter( 'render_block_data', 'wpbb_child_v140_render_block_data', 9999 );

/* Persist the sector copy into managed demo content on the next admin visit. */
if ( ! function_exists( 'wpbb_child_v140_repair_managed_front_page' ) ) {
    function wpbb_child_v140_repair_managed_front_page() {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) return;
        $front = absint( get_option( 'page_on_front' ) );
        if ( ! $front ) return;
        $raw = (string) get_post_field( 'post_content', $front, 'raw' );
        if ( false === strpos( $raw, 'Card title' ) && false === strpos( $raw, 'Add a short description.' ) ) return;
        $fixed = wpbb_child_v140_repair_placeholder_string( $raw );
        if ( $fixed === $raw ) return;
        wp_update_post( wp_slash( array( 'ID' => $front, 'post_content' => $fixed ) ) );
        update_post_meta( $front, '_wpbb_child_v140_sector_copy', '3.8.11.40' );
        clean_post_cache( $front );
    }
}
add_action( 'admin_init', 'wpbb_child_v140_repair_managed_front_page', 999 );
add_action( 'wp_theme_after_demo_import', 'wpbb_child_v140_repair_managed_front_page', 1200 );
