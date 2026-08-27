# wp-theme-child

Optimized child theme for wp-theme with ACF Pro options and brand overrides.


## Booking
- Demo import now creates a Booking page.
- Booking requests are stored in admin and shown in Theme Settings > Booking Dashboard.


## Parent theme compatibility

This child theme is intended to sit on top of the parent theme with:

- Theme Settings under Settings -> Theme Settings
- optional performance/cache controls
- improved header/menu/dark-mode behavior
- enhanced search results and AJAX search suggestions

## Assets and builds

This child theme is the only owner of theme presentation and JavaScript. It contains the parent presentation migrated from `wp-bbtheme`, project overrides, admin assets and the Vite entry points.

```bash
yarn install
yarn prod
```

The generated manifest is read from `dist/.vite/manifest.json`.

Storefront presentation, including the demo header/footer shell and responsive WooCommerce product cards, is compiled from `src/scss/_components/_commerce.scss`. Reusable WooCommerce behaviour remains in the plugin.

## Demo import

Enable Demo Import in `Settings -> Theme Settings -> General`, then use the single **Import Active Demo** button. This default child imports a grey business website when WooCommerce support is unavailable. When both WooCommerce and `wp-theme-woo-support` are active, the same import also creates 18 products, shop/account/cart/checkout pages, a product filter and AJAX load more.

The importer always creates separate Header and Footer menus. The responsive hamburger is controlled by `assets/js/theme.js` with `aria-expanded`, Escape-key, outside-click and resize handling.

Reusable editor patterns are in `patterns/`, and the child `theme.json` exposes the matching neutral palette and layout widths.

## Sector children

All sector themes are direct children of `wp-bbtheme` because WordPress does not support child-of-child inheritance:

- `wp-bbtheme-child-woo-tech`: technology catalog and blue storefront
- `wp-bbtheme-child-woo-clouthes`: clothing catalog and editorial neutral storefront (directory spelling retained for compatibility)
- `wp-bbtheme-child-woo-realestate`: non-commerce property profile with a REST-enabled Property CPT, Property Type taxonomy, archive and demo listings

## WooCommerce

Install `wp-theme-woo-support` for reusable WooCommerce behaviour. Existing WP BBTheme shops use its legacy profile; new or third-party themes use the portable profile and enable only required modules.
