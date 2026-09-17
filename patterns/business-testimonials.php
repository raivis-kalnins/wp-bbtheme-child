<?php
/**
 * Title: Business client stories
 * Slug: wp-bbtheme-child/business-testimonials
 * Categories: wp-patterns-main, wp-theme-current
 */
$slides = array(
    array( 'type'=>'card', 'eyebrow'=>__( 'Professional services', 'wp-bbtheme-child' ), 'title'=>__( 'Clearer decisions and a site the team can actually edit.', 'wp-bbtheme-child' ), 'text'=>__( 'A complete demo quote showing how client proof can be structured without a bespoke testimonials plugin.', 'wp-bbtheme-child' ) ),
    array( 'type'=>'card', 'eyebrow'=>__( 'Technology', 'wp-bbtheme-child' ), 'title'=>__( 'Fast enough to feel simple.', 'wp-bbtheme-child' ), 'text'=>__( 'Strategy, content and delivery presented as one coherent system rather than disconnected pages.', 'wp-bbtheme-child' ) ),
    array( 'type'=>'card', 'eyebrow'=>__( 'Growth team', 'wp-bbtheme-child' ), 'title'=>__( 'A useful base for the next campaign.', 'wp-bbtheme-child' ), 'text'=>__( 'Reusable patterns, sensible components and lightweight motion make future changes much easier.', 'wp-bbtheme-child' ) ),
    array( 'type'=>'card', 'eyebrow'=>__( 'Delivery partner', 'wp-bbtheme-child' ), 'title'=>__( 'A dependable system for the next release.', 'wp-bbtheme-child' ), 'text'=>__( 'Clear components, sensible defaults and reusable editorial patterns keep future delivery moving.', 'wp-bbtheme-child' ) ),
);
$attrs = array( 'slides'=>$slides, 'slidesPerView'=>3, 'slidesTablet'=>2, 'slidesMobile'=>1, 'spaceBetween'=>24, 'rewind'=>true, 'demoStyle'=>'cards', 'showPagination'=>true, 'showNavigation'=>true );
?>
<!-- wp:wpbb/bootstrap-div {"containerClass":"","utilityClasses":"wp-theme-section-shell business-testimonials"} --><!-- wp:wpbb/row {"containerClass":"container","customClasses":"wp-theme-section-heading"} --><!-- wp:wpbb/column {"xs":12,"lg":8} --><!-- wp:paragraph {"className":"wp-theme-sector-eyebrow"} --><p class="wp-theme-sector-eyebrow"><?php echo esc_html__( 'Client results', 'wp-bbtheme-child' ); ?></p><!-- /wp:paragraph --><!-- wp:heading --><h2 class="wp-block-heading"><?php echo esc_html__( 'Trusted for work that has to perform.', 'wp-bbtheme-child' ); ?></h2><!-- /wp:heading --><!-- /wp:wpbb/column --><!-- /wp:wpbb/row --><!-- wp:wpbb/row {"containerClass":"container"} --><!-- wp:wpbb/column {"xs":12} --><?php echo '<!-- wp:wpbb/swiper ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES ) . ' /-->'; ?><!-- /wp:wpbb/column --><!-- /wp:wpbb/row --><!-- /wp:wpbb/bootstrap-div -->
