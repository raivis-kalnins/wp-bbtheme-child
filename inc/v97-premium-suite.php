<?php
/** Final premium Jobs-aligned sector presentation for 3.8.10.97. */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v97_sector_config' ) ) {
    function wpbb_child_v97_sector_config() {
        $map = array(
            'wp-bbtheme-child-automotive'        => array( 'placeholder' => 'Make, model or keyword', 'where' => 'Location or postcode', 'button' => 'Search cars', 'chips' => array( 'SUV', 'Electric', 'Used cars', 'Servicing' ) ),
            'wp-bbtheme-child-building-services'=> array( 'placeholder' => 'What service do you need?', 'where' => 'Location', 'button' => 'Search', 'chips' => array( 'Repairs', 'Maintenance', 'Renovation', 'Emergency' ) ),
            'wp-bbtheme-child-business'          => array( 'placeholder' => 'Service or keyword', 'where' => 'Location', 'button' => 'Search', 'chips' => array( 'Strategy', 'Web design', 'Content', 'Growth' ) ),
            'wp-bbtheme-child-elearning'         => array( 'placeholder' => 'What do you want to learn?', 'where' => 'Category', 'button' => 'Search', 'chips' => array( 'Business', 'Technology', 'Design', 'Languages' ) ),
            'wp-bbtheme-child-hotel'             => array( 'placeholder' => 'City, hotel or destination', 'where' => 'Dates', 'button' => 'Search hotels', 'chips' => array( 'City breaks', 'Beach', 'Spa', 'Family' ) ),
            'wp-bbtheme-child-insurance'         => array( 'placeholder' => 'Insurance type', 'where' => 'Location', 'button' => 'Get quotes', 'chips' => array( 'Home', 'Car', 'Life', 'Business' ) ),
            'wp-bbtheme-child-logistics'         => array( 'placeholder' => 'Freight type', 'where' => 'From / to', 'button' => 'Search', 'chips' => array( 'Road', 'Air', 'Sea', 'Warehousing' ) ),
            'wp-bbtheme-child-medicine'          => array( 'placeholder' => 'Speciality or doctor', 'where' => 'Location', 'button' => 'Search', 'chips' => array( 'GP', 'Dental', 'Physio', 'Diagnostics' ) ),
            'wp-bbtheme-child-realestate'        => array( 'placeholder' => 'Buy or rent', 'where' => 'Location', 'button' => 'Search', 'chips' => array( 'For sale', 'To rent', 'New homes', 'Commercial' ) ),
            'wp-bbtheme-child-restaurant'        => array( 'placeholder' => 'Cuisine, restaurant or dish', 'where' => 'Location', 'button' => 'Search', 'chips' => array( 'Italian', 'Asian', 'Brunch', 'Fine dining' ) ),
            'wp-bbtheme-child-travel'            => array( 'placeholder' => 'Destination or keyword', 'where' => 'Travel dates', 'button' => 'Search', 'chips' => array( 'Beach', 'City', 'Adventure', 'Family' ) ),
            'wp-bbtheme-child-woo-clouthes'      => array( 'placeholder' => 'Search products', 'where' => 'Category', 'button' => 'Search', 'chips' => array( 'New in', 'Women', 'Men', 'Accessories' ) ),
            'wp-bbtheme-child-woo-events'        => array( 'placeholder' => 'Event, artist or venue', 'where' => 'Location', 'button' => 'Find events', 'chips' => array( 'Today', 'This weekend', 'Music', 'Workshops' ) ),
            'wp-bbtheme-child-woo-tech-shop'     => array( 'placeholder' => 'Search products', 'where' => 'Category', 'button' => 'Search', 'chips' => array( 'Laptops', 'Audio', 'Smart home', 'Accessories' ) ),
        );
        $stylesheet = get_stylesheet();
        if ( ! isset( $map[ $stylesheet ] ) ) return array();
        $cfg = $map[ $stylesheet ];
        $cfg['hero'] = get_stylesheet_directory_uri() . '/assets/img/demo/hero-premium.jpg';
        return $cfg;
    }
}

if ( ! function_exists( 'wpbb_child_v97_enqueue' ) ) {
    function wpbb_child_v97_enqueue() {
        $css = get_stylesheet_directory() . '/assets/sector-v97.css';
        $js  = get_stylesheet_directory() . '/assets/sector-v97.js';
        $ver = wp_get_theme()->get( 'Version' );
        if ( is_readable( $css ) ) wp_enqueue_style( 'wpbb-child-sector-v97', get_stylesheet_directory_uri() . '/assets/sector-v97.css', array(), $ver );
        if ( is_readable( $js ) ) wp_enqueue_script( 'wpbb-child-sector-v97', get_stylesheet_directory_uri() . '/assets/sector-v97.js', array(), $ver, true );
        $cfg = wpbb_child_v97_sector_config();
        if ( $cfg && ! empty( $cfg['hero'] ) ) {
            $hero = esc_url_raw( $cfg['hero'] );
            wp_add_inline_style( 'wpbb-child-sector-v97', ':root{--sector-v97-hero:url("' . esc_url( $hero ) . '");--sector-v97-accent:var(--sector-v83-accent,var(--sector-primary,#078f6a));--sector-v97-soft:var(--sector-v83-soft,#eef8f5);}' );
        }
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v97_enqueue', 360 );
}

if ( ! function_exists( 'wpbb_child_v97_translate_ui' ) ) {
    function wpbb_child_v97_translate_ui( $text ) {
        $lang = function_exists( 'wpbb_child_v83_language' ) ? wpbb_child_v83_language() : strtolower( substr( get_locale(), 0, 2 ) );
        $t = array(
            'de' => array( 'Popular' => 'Beliebt', 'Search' => 'Suchen' ), 'es' => array( 'Popular' => 'Popular', 'Search' => 'Buscar' ),
            'fr' => array( 'Popular' => 'Populaire', 'Search' => 'Rechercher' ), 'pl' => array( 'Popular' => 'Popularne', 'Search' => 'Szukaj' ),
            'ru' => array( 'Popular' => 'Популярное', 'Search' => 'Поиск' ), 'lv' => array( 'Popular' => 'Populāri', 'Search' => 'Meklēt' ),
            'lt' => array( 'Popular' => 'Populiaru', 'Search' => 'Ieškoti' ), 'et' => array( 'Popular' => 'Populaarne', 'Search' => 'Otsi' ),
            'da' => array( 'Popular' => 'Populært', 'Search' => 'Søg' ), 'sv' => array( 'Popular' => 'Populärt', 'Search' => 'Sök' ),
            'nb' => array( 'Popular' => 'Populært', 'Search' => 'Søk' ), 'fi' => array( 'Popular' => 'Suosittua', 'Search' => 'Hae' ),
            'is' => array( 'Popular' => 'Vinsælt', 'Search' => 'Leita' ),
        );
        return isset( $t[ $lang ][ $text ] ) ? $t[ $lang ][ $text ] : $text;
    }
}

if ( ! function_exists( 'wpbb_child_v97_finder_markup' ) ) {
    function wpbb_child_v97_finder_markup() {
        $cfg = wpbb_child_v97_sector_config();
        if ( ! $cfg ) return '';
        $button = (string) $cfg['button'];
        if ( 'Search' === $button ) $button = wpbb_child_v97_translate_ui( 'Search' );
        $action = home_url( '/' );
        $chips = '';
        foreach ( array_slice( (array) $cfg['chips'], 0, 4 ) as $chip ) {
            $chips .= '<a href="' . esc_url( add_query_arg( 's', $chip, $action ) ) . '">' . esc_html( $chip ) . '</a>';
        }
        return '<div class="wpbb-v97-hero-finder"><form class="wpbb-v97-hero-finder__form" method="get" action="' . esc_url( $action ) . '">'
            . '<label class="wpbb-v97-hero-finder__field"><span class="screen-reader-text">' . esc_html( $cfg['placeholder'] ) . '</span><input type="search" name="s" placeholder="' . esc_attr( $cfg['placeholder'] ) . '"></label>'
            . '<label class="wpbb-v97-hero-finder__field"><span class="screen-reader-text">' . esc_html( $cfg['where'] ) . '</span><input type="text" name="location" placeholder="' . esc_attr( $cfg['where'] ) . '"></label>'
            . '<button class="wpbb-v97-hero-finder__submit" type="submit">' . esc_html( $button ) . ' →</button></form>'
            . '<div class="wpbb-v97-hero-finder__chips"><span>' . esc_html( wpbb_child_v97_translate_ui( 'Popular' ) ) . ':</span>' . $chips . '</div></div>';
    }
}

if ( ! function_exists( 'wpbb_child_v97_render_hero_finder' ) ) {
    function wpbb_child_v97_render_hero_finder( $block_content, $block ) {
        if ( is_admin() || ! is_front_page() || empty( $block['blockName'] ) || 'wpbb/swiper' !== $block['blockName'] ) return $block_content;
        if ( false === strpos( $block_content, 'wpbb-swiper--hero' ) || false !== strpos( $block_content, 'wpbb-v97-hero-finder' ) ) return $block_content;
        $finder = wpbb_child_v97_finder_markup();
        if ( '' === $finder ) return $block_content;
        return str_replace( '</div></article>', $finder . '</div></article>', $block_content );
    }
    add_filter( 'render_block', 'wpbb_child_v97_render_hero_finder', 180, 2 );
}

if ( ! function_exists( 'wpbb_child_v97_profile' ) ) {
    function wpbb_child_v97_profile( $profile ) {
        $cfg = wpbb_child_v97_sector_config();
        if ( ! $cfg ) return $profile;
        $profile['hero_image'] = $cfg['hero'];
        if ( ! empty( $profile['hero_slides'] ) && is_array( $profile['hero_slides'] ) ) {
            foreach ( $profile['hero_slides'] as $i => $slide ) {
                if ( is_array( $slide ) ) $profile['hero_slides'][ $i ]['image'] = $cfg['hero'];
            }
        }
        return $profile;
    }
    add_filter( 'wp_theme_demo_profile', 'wpbb_child_v97_profile', 1400 );
}

if ( ! function_exists( 'wpbb_child_v97_refresh_managed_demo' ) ) {
    function wpbb_child_v97_refresh_managed_demo() {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) return;
        $key = 'wpbb_child_v97_refresh_' . sanitize_key( get_stylesheet() );
        if ( '3.8.10.97' === (string) get_option( $key ) ) return;
        if ( function_exists( 'wpbb_child_v62_rebuild_demo_pages' ) ) wpbb_child_v62_rebuild_demo_pages( true );
        $ids = array();
        $front = absint( get_option( 'page_on_front' ) );
        if ( $front && get_post_meta( $front, '_wp_theme_demo_managed', true ) ) $ids[] = $front;
        foreach ( array( 'about', 'services', 'industries', 'contact', 'blog' ) as $slug ) {
            $page = get_page_by_path( $slug );
            if ( $page instanceof WP_Post && get_post_meta( $page->ID, '_wp_theme_demo_managed', true ) ) $ids[] = $page->ID;
        }
        if ( $ids && function_exists( 'wpbb_child_v71_sync_managed_translations' ) ) wpbb_child_v71_sync_managed_translations( array_values( array_unique( $ids ) ) );
        update_option( $key, '3.8.10.97', false );
    }
    add_action( 'admin_init', 'wpbb_child_v97_refresh_managed_demo', 330 );
    add_action( 'wp_theme_after_demo_import', 'wpbb_child_v97_refresh_managed_demo', 330 );
}
