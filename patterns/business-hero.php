<?php
/**
 * Title: Business hero slider
 * Slug: wp-bbtheme-child/business-hero
 * Categories: wp-patterns-main, wp-theme-current
 * Description: Responsive Bootstrap/BBuilder hero powered by Swiper.
 */
$slides = array(
    array(
        'type'       => 'hero',
        'eyebrow'    => __( 'Strategy + delivery', 'wp-bbtheme-child' ),
        'title'      => __( 'Digital work with a clearer point of view.', 'wp-bbtheme-child' ),
        'text'       => __( 'A high-quality Bootstrap and Gutenberg system for ambitious services, agencies and professional teams.', 'wp-bbtheme-child' ),
        'buttonText' => __( 'Start a project', 'wp-bbtheme-child' ),
        'buttonUrl'  => '/contact/',
        'image'      => get_stylesheet_directory_uri() . '/assets/img/demo/office-wide.jpg',
    ),
    array(
        'type'       => 'hero',
        'eyebrow'    => __( 'Built to evolve', 'wp-bbtheme-child' ),
        'title'      => __( 'A publishing system your team can actually use.', 'wp-bbtheme-child' ),
        'text'       => __( 'Reusable BBuilder patterns, thoughtful motion and clean responsive rules without a locked page-builder workflow.', 'wp-bbtheme-child' ),
        'buttonText' => __( 'See our approach', 'wp-bbtheme-child' ),
        'buttonUrl'  => '/about/',
        'image'      => get_stylesheet_directory_uri() . '/assets/img/demo/office-planning.jpg',
    ),
);
$attrs = array( 'slides'=>$slides, 'slidesPerView'=>1, 'slidesTablet'=>1, 'slidesMobile'=>1, 'spaceBetween'=>0, 'speed'=>700, 'rewind'=>true, 'autoplay'=>true, 'autoplayDelay'=>8500, 'pauseOnHover'=>true, 'effect'=>'slide', 'demoStyle'=>'hero', 'showPagination'=>true, 'showNavigation'=>true );
?>
<!-- wp:wpbb/row {"containerClass":"container","customClasses":"wp-theme-sector-hero"} --><!-- wp:wpbb/column {"xs":12} --><?php echo '<!-- wp:wpbb/swiper ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES ) . ' /-->'; ?><!-- /wp:wpbb/column --><!-- /wp:wpbb/row -->
