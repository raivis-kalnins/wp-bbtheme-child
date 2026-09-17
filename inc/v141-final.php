<?php
/**
 * WP BBTheme child suite 3.8.11.41 — targeted grid repair + sector card content.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* Fully retire v140 so its broad grid JS and earlier placeholder copy cannot run first. */
remove_action( 'wp_enqueue_scripts', 'wpbb_child_v140_enqueue', PHP_INT_MAX );
remove_filter( 'body_class', 'wpbb_child_v140_body_class', PHP_INT_MAX );
remove_filter( 'the_content', 'wpbb_child_v140_rendered_content', 9999 );
remove_filter( 'render_block_data', 'wpbb_child_v140_render_block_data', 9999 );
remove_action( 'admin_init', 'wpbb_child_v140_repair_managed_front_page', 999 );
remove_action( 'wp_theme_after_demo_import', 'wpbb_child_v140_repair_managed_front_page', 1200 );

if ( ! function_exists( 'wpbb_child_v141_sector_key' ) ) {
    function wpbb_child_v141_sector_key() {
        $stylesheet = strtolower( (string) get_stylesheet() );
        return false !== strpos( $stylesheet, 'building-services' ) ? 'building' : 'business';
    }
}

if ( ! function_exists( 'wpbb_child_v141_sector_cards' ) ) {
    function wpbb_child_v141_sector_cards() {
        if ( 'building' === wpbb_child_v141_sector_key() ) {
            return array(
                array(
                    'title' => 'Multi-trade coordination',
                    'text'  => 'Plumbing, electrical, handyman and maintenance work are coordinated through one clear job workflow.',
                ),
                array(
                    'title' => 'Clear call-out expectations',
                    'text'  => 'Emergency availability, response targets, coverage and call-out pricing are visible before an enquiry.',
                ),
                array(
                    'title' => 'Property portfolio support',
                    'text'  => 'Planned maintenance, snagging and repeat works stay organised across homes, rentals and managed sites.',
                ),
            );
        }

        return array(
            array(
                'title' => 'Strategy that sets direction',
                'text'  => 'Audiences, journeys, propositions and content priorities are agreed before design and build begin.',
            ),
            array(
                'title' => 'Reusable WordPress system',
                'text'  => 'Flexible Gutenberg patterns keep pages consistent while giving the team control of day-to-day publishing.',
            ),
            array(
                'title' => 'Growth after launch',
                'text'  => 'Campaigns, optimisation and measurement continue in the same system instead of becoming a separate rebuild.',
            ),
        );
    }
}

if ( ! function_exists( 'wpbb_child_v141_enqueue' ) ) {
    function wpbb_child_v141_enqueue() {
        /* v140's broad runtime row-to-grid conversion is intentionally retired. */
        wp_dequeue_style( 'wpbb-suite-v140' );
        wp_deregister_style( 'wpbb-suite-v140' );
        wp_dequeue_script( 'wpbb-suite-v140' );
        wp_deregister_script( 'wpbb-suite-v140' );

        $dir = get_stylesheet_directory();
        $uri = get_stylesheet_directory_uri();
        $css = '/assets/suite-v141.css';
        $js  = '/assets/suite-v141.js';

        wp_enqueue_style(
            'wpbb-suite-v141',
            $uri . $css,
            array( 'wpbb-suite-v139' ),
            is_file( $dir . $css ) ? filemtime( $dir . $css ) : '3.8.11.41'
        );
        wp_enqueue_script(
            'wpbb-suite-v141',
            $uri . $js,
            array(),
            is_file( $dir . $js ) ? filemtime( $dir . $js ) : '3.8.11.41',
            true
        );
        wp_localize_script(
            'wpbb-suite-v141',
            'wpbbSuiteV141',
            array(
                'version' => '3.8.11.41',
                'sector'  => wpbb_child_v141_sector_key(),
                'cards'   => wpbb_child_v141_sector_cards(),
            )
        );
    }
}
add_action( 'wp_enqueue_scripts', 'wpbb_child_v141_enqueue', PHP_INT_MAX );

if ( ! function_exists( 'wpbb_child_v141_body_class' ) ) {
    function wpbb_child_v141_body_class( $classes ) {
        $classes[] = 'wpbb-v141';
        return array_values( array_unique( $classes ) );
    }
}
add_filter( 'body_class', 'wpbb_child_v141_body_class', PHP_INT_MAX );

if ( ! function_exists( 'wpbb_child_v141_replace_sequence' ) ) {
    function wpbb_child_v141_replace_sequence( $content, $needle, $replacements ) {
        $content = (string) $content;
        $replacements = array_values( array_filter( array_map( 'strval', (array) $replacements ), 'strlen' ) );
        if ( '' === $content || '' === (string) $needle || ! $replacements ) return $content;

        $i = 0;
        $guard = 0;
        while ( false !== ( $pos = strpos( $content, (string) $needle ) ) && $guard < 50 ) {
            $replacement = $replacements[ $i % count( $replacements ) ];
            $content = substr_replace( $content, $replacement, $pos, strlen( (string) $needle ) );
            $i++;
            $guard++;
        }
        return $content;
    }
}

if ( ! function_exists( 'wpbb_child_v141_repair_placeholder_string' ) ) {
    function wpbb_child_v141_repair_placeholder_string( $content ) {
        if ( ! is_string( $content ) || '' === $content ) return $content;
        if ( false === strpos( $content, 'Card title' ) && false === strpos( $content, 'Add a short description.' ) ) return $content;

        $cards = wpbb_child_v141_sector_cards();
        $titles = array();
        $texts = array();
        foreach ( $cards as $card ) {
            $titles[] = (string) $card['title'];
            $texts[]  = (string) $card['text'];
        }
        $content = wpbb_child_v141_replace_sequence( $content, 'Card title', $titles );
        $content = wpbb_child_v141_replace_sequence( $content, 'Add a short description.', $texts );
        return $content;
    }
}

/* Repair serialized icon-card attributes before the BBuilder block renders. */
if ( ! function_exists( 'wpbb_child_v141_render_block_data' ) ) {
    function wpbb_child_v141_render_block_data( $parsed_block ) {
        if ( is_admin() || empty( $parsed_block['attrs'] ) || ! is_array( $parsed_block['attrs'] ) ) return $parsed_block;

        $title = isset( $parsed_block['attrs']['title'] ) ? trim( (string) $parsed_block['attrs']['title'] ) : '';
        $text  = isset( $parsed_block['attrs']['text'] ) ? trim( (string) $parsed_block['attrs']['text'] ) : '';
        if ( 'Card title' !== $title && 'Add a short description.' !== $text ) return $parsed_block;

        static $index = 0;
        $cards = wpbb_child_v141_sector_cards();
        $card  = $cards[ $index % count( $cards ) ];
        if ( 'Card title' === $title ) $parsed_block['attrs']['title'] = $card['title'];
        if ( 'Add a short description.' === $text ) $parsed_block['attrs']['text'] = $card['text'];
        $parsed_block['attrs']['className'] = trim( (string) ( $parsed_block['attrs']['className'] ?? '' ) . ' wpbb-v141-sector-proof-card' );
        $index++;
        return $parsed_block;
    }
}
add_filter( 'render_block_data', 'wpbb_child_v141_render_block_data', 10000 );

if ( ! function_exists( 'wpbb_child_v141_mark_rendered_card' ) ) {
    function wpbb_child_v141_mark_rendered_card( $html ) {
        if ( false !== strpos( $html, 'wpbb-v141-sector-proof-card' ) ) return $html;
        if ( preg_match( '/\bclass=("|\')/i', $html ) ) {
            return preg_replace( '/\bclass=("|\')/i', 'class=$1wpbb-v141-sector-proof-card ', $html, 1 );
        }
        return preg_replace( '/<([a-z][a-z0-9:-]*)(\s|>)/i', '<$1 class="wpbb-v141-sector-proof-card"$2', $html, 1 );
    }
}

/* Fallback for icon-card implementations that expose defaults only at render time. */
if ( ! function_exists( 'wpbb_child_v141_render_block' ) ) {
    function wpbb_child_v141_render_block( $block_content, $block ) {
        if ( is_admin() || ! is_string( $block_content ) || '' === $block_content ) return $block_content;
        $has_title = false !== strpos( $block_content, 'Card title' );
        $has_text  = false !== strpos( $block_content, 'Add a short description.' );
        if ( ! $has_title && ! $has_text ) return $block_content;

        static $index = 0;
        $cards = wpbb_child_v141_sector_cards();
        $card  = $cards[ $index % count( $cards ) ];
        if ( $has_title ) $block_content = str_replace( 'Card title', esc_html( $card['title'] ), $block_content );
        if ( $has_text ) $block_content = str_replace( 'Add a short description.', esc_html( $card['text'] ), $block_content );

        $name = isset( $block['blockName'] ) ? (string) $block['blockName'] : '';
        if ( 'wpbb/icon-card' === $name || ( $has_title && $has_text ) ) {
            $block_content = wpbb_child_v141_mark_rendered_card( $block_content );
            $index++;
        }
        return $block_content;
    }
}
add_filter( 'render_block', 'wpbb_child_v141_render_block', 10000, 2 );

/* Final content-level fallback. This is deliberately not front-page-only so no demo page can leak stock card copy. */
if ( ! function_exists( 'wpbb_child_v141_rendered_content' ) ) {
    function wpbb_child_v141_rendered_content( $content ) {
        if ( is_admin() ) return $content;
        return wpbb_child_v141_repair_placeholder_string( $content );
    }
}
add_filter( 'the_content', 'wpbb_child_v141_rendered_content', 10000 );

/* Persist the sector copy into the stored front page after an upgrade/import. */
if ( ! function_exists( 'wpbb_child_v141_repair_managed_front_page' ) ) {
    function wpbb_child_v141_repair_managed_front_page() {
        if ( ! current_user_can( 'manage_options' ) ) return;
        $front = absint( get_option( 'page_on_front' ) );
        if ( ! $front ) return;
        $raw = (string) get_post_field( 'post_content', $front, 'raw' );
        if ( false === strpos( $raw, 'Card title' ) && false === strpos( $raw, 'Add a short description.' ) ) return;
        $fixed = wpbb_child_v141_repair_placeholder_string( $raw );
        if ( $fixed === $raw ) return;
        wp_update_post( wp_slash( array( 'ID' => $front, 'post_content' => $fixed ) ) );
        update_post_meta( $front, '_wpbb_child_v141_sector_copy', '3.8.11.41' );
        clean_post_cache( $front );
    }
}
add_action( 'admin_init', 'wpbb_child_v141_repair_managed_front_page', 1000 );
add_action( 'wp_theme_after_demo_import', 'wpbb_child_v141_repair_managed_front_page', 1300 );
