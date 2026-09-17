<?php /** Title: Business contact CTA / Slug: wp-bbtheme-child/business-contact-cta / Categories: wp-patterns-main, wp-theme-current */ ?>
<!-- wp:wpbb/bootstrap-div {"containerClass":"","utilityClasses":"wp-theme-home-cta"} -->
<!-- wp:wpbb/row {"gutterX":"gx-5","gutterY":"gy-4","containerClass":"container","customClasses":"align-items-center"} -->
<!-- wp:wpbb/column {"xs":12,"lg":8} -->
<!-- wp:paragraph {"className":"wp-theme-sector-eyebrow"} --><p class="wp-theme-sector-eyebrow"><?php echo esc_html__( 'Next step', 'wp-bbtheme-child' ); ?></p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading"><?php echo esc_html__( 'Build the next version with a stronger system underneath it.', 'wp-bbtheme-child' ); ?></h2><!-- /wp:heading -->
<!-- wp:paragraph --><p><?php echo esc_html__( 'Use the starter as a complete base, then make the content and global settings unmistakably yours.', 'wp-bbtheme-child' ); ?></p><!-- /wp:paragraph -->
<!-- /wp:wpbb/column -->
<!-- wp:wpbb/column {"xs":12,"lg":4,"horizontalAlign":"end"} -->
<?php echo '<!-- wp:wpbb/button ' . wp_json_encode( array( 'text' => __( 'Start a conversation', 'wp-bbtheme-child' ), 'url' => home_url( '/contact/' ), 'btnClass' => 'btn btn-primary' ), JSON_UNESCAPED_SLASHES ) . ' /-->'; ?>
<!-- /wp:wpbb/column -->
<!-- /wp:wpbb/row -->
<!-- /wp:wpbb/bootstrap-div -->
