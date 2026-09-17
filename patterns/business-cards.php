<?php /** Title: Business service cards / Slug: wp-bbtheme-child/business-cards / Categories: wp-patterns-main, wp-theme-current */ ?>
<!-- wp:wpbb/row {"gutterX":"gx-4","gutterY":"gy-4","containerClass":"container","customClasses":"wp-theme-section-shell"} -->
<?php
$cards = array(
    array( __( 'Plan with purpose', 'wp-bbtheme-child' ), __( 'Audience, content and priorities translated into a focused delivery plan.', 'wp-bbtheme-child' ) ),
    array( __( 'Build a system', 'wp-bbtheme-child' ), __( 'Bootstrap components and Gutenberg patterns that stay easy to edit.', 'wp-bbtheme-child' ) ),
    array( __( 'Improve with evidence', 'wp-bbtheme-child' ), __( 'Performance, content and conversion improvements after launch.', 'wp-bbtheme-child' ) ),
);
foreach ( $cards as $i => $card ) : ?>
<!-- wp:wpbb/column {"xs":12,"md":4} --><!-- wp:wpbb/bootstrap-div {"containerClass":"","utilityClasses":"wp-theme-sector-card motion-fade-up"} --><!-- wp:paragraph {"className":"wp-theme-card-number"} --><p class="wp-theme-card-number">0<?php echo esc_html( $i + 1 ); ?></p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3 class="wp-block-heading"><?php echo esc_html( $card[0] ); ?></h3><!-- /wp:heading --><!-- wp:paragraph --><p><?php echo esc_html( $card[1] ); ?></p><!-- /wp:paragraph --><!-- /wp:wpbb/bootstrap-div --><!-- /wp:wpbb/column -->
<?php endforeach; ?><!-- /wp:wpbb/row -->
