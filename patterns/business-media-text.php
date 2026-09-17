<?php /** Title: Business media and text / Slug: wp-bbtheme-child/business-media-text / Categories: wp-patterns-main, wp-theme-current */ ?>
<!-- wp:wpbb/bootstrap-div {"containerClass":"","utilityClasses":"wp-theme-section-shell"} -->
<!-- wp:wpbb/row {"containerClass":"container"} -->
<!-- wp:wpbb/column {"xs":12} -->
<!-- wp:media-text {"mediaWidth":50,"verticalAlignment":"center","isStackedOnMobile":true,"className":"wp-theme-sector-media-text motion-fade-up"} -->
<div class="wp-block-media-text is-stacked-on-mobile is-vertically-aligned-center wp-theme-sector-media-text motion-fade-up"><figure class="wp-block-media-text__media"><img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/demo/office-planning.jpg' ); ?>" alt="<?php echo esc_attr__( 'Collaborative digital workshop', 'wp-bbtheme-child' ); ?>"/></figure><div class="wp-block-media-text__content">
<!-- wp:paragraph {"className":"wp-theme-sector-eyebrow"} --><p class="wp-theme-sector-eyebrow"><?php echo esc_html__( 'How we work', 'wp-bbtheme-child' ); ?></p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading"><?php echo esc_html__( 'Good websites are systems, not a stack of pages.', 'wp-bbtheme-child' ); ?></h2><!-- /wp:heading -->
<!-- wp:paragraph {"className":"wp-theme-sector-lead"} --><p class="wp-theme-sector-lead"><?php echo esc_html__( 'Bring content, design and engineering together early. The result is clearer for visitors and easier for teams to maintain.', 'wp-bbtheme-child' ); ?></p><!-- /wp:paragraph -->
<?php echo '<!-- wp:wpbb/button ' . wp_json_encode( array( 'text' => __( 'Our approach', 'wp-bbtheme-child' ), 'url' => home_url( '/about/' ), 'btnClass' => 'btn btn-outline-primary' ), JSON_UNESCAPED_SLASHES ) . ' /-->'; ?>
</div></div>
<!-- /wp:media-text -->
<!-- /wp:wpbb/column -->
<!-- /wp:wpbb/row -->
<!-- /wp:wpbb/bootstrap-div -->
