<?php defined( 'ABSPATH' ) || exit; get_header(); ?>
<main id="wp-theme-main" class="wp-theme-main wp-theme-woo-legacy wp-theme-woo-legacy--cart">
<section class="wp-theme-woo-legacy__hero"><div class="container"><p class="wp-theme-sector-eyebrow"><?php esc_html_e( 'Basket', 'wp-theme' ); ?></p><h1><?php esc_html_e( 'Review your basket.', 'wp-theme' ); ?></h1><p><?php esc_html_e( 'Check quantities and totals before moving to checkout.', 'wp-theme' ); ?></p></div></section>
<div class="container wp-theme-woo-legacy__body"><div class="wp-theme-woo-cart-shell"><?php if ( class_exists( 'WC_Shortcode_Cart' ) ) WC_Shortcode_Cart::output( array() ); else echo do_shortcode( '[woocommerce_cart]' ); ?></div></div>
</main><?php get_footer(); ?>
