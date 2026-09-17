<?php
defined( 'ABSPATH' ) || exit;
get_header();
$title = __( 'Your account.', 'wp-theme' );
if ( function_exists( 'is_wc_endpoint_url' ) ) {
    if ( is_wc_endpoint_url( 'orders' ) ) $title = __( 'Your orders.', 'wp-theme' );
    elseif ( is_wc_endpoint_url( 'downloads' ) ) $title = __( 'Your downloads.', 'wp-theme' );
    elseif ( is_wc_endpoint_url( 'edit-address' ) ) $title = __( 'Your addresses.', 'wp-theme' );
    elseif ( is_wc_endpoint_url( 'edit-account' ) ) $title = __( 'Account details.', 'wp-theme' );
    elseif ( is_wc_endpoint_url( 'lost-password' ) ) $title = __( 'Reset your password.', 'wp-theme' );
}
?>
<main id="wp-theme-main" class="wp-theme-main wp-theme-woo-legacy wp-theme-woo-legacy--account">
  <section class="wp-theme-woo-legacy__hero">
    <div class="container">
      <p class="wp-theme-sector-eyebrow"><?php esc_html_e( 'Account', 'wp-theme' ); ?></p>
      <h1><?php echo esc_html( $title ); ?></h1>
      <p><?php esc_html_e( 'Manage orders, addresses, downloads and account details.', 'wp-theme' ); ?></p>
    </div>
  </section>
  <div class="container wp-theme-woo-legacy__body">
    <?php if ( is_user_logged_in() ) : ?>
      <div class="woocommerce wpbb-v109-account-grid">
        <?php do_action( 'woocommerce_account_navigation' ); ?>
        <div class="woocommerce-MyAccount-content">
          <?php do_action( 'woocommerce_account_content' ); ?>
        </div>
      </div>
    <?php elseif ( class_exists( 'WC_Shortcode_My_Account' ) ) : ?>
      <?php WC_Shortcode_My_Account::output( array() ); ?>
    <?php else : ?>
      <?php echo do_shortcode( '[woocommerce_my_account]' ); ?>
    <?php endif; ?>
  </div>
</main>
<?php get_footer(); ?>
