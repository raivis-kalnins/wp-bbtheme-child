## 3.8.11.41 targeted grid and sector-card repair

- Retires v140's broad 2-4 child row-to-grid conversion and restores targeted card grids only.
- Keeps normal homepage sections aligned to the measured header/footer content edge without changing unrelated Bootstrap rows.
- Replaces stock `Card title` / `Add a short description.` output with sector-specific Business or Building content server-side, with a frontend fallback for stale imported markup.
- Repairs the three-card proof row into a stable 1/2/3 responsive grid and persists corrected copy into the managed front page after upgrade/import.

## 3.8.11.16 reset-safe final release fixes
- Demo reset/import now re-runs the canonical managed BBuilder page rebuild and all v116 repairs automatically.
- The old migration that removed legitimate responsive BBuilder column widths is disabled; desktop multi-column layouts survive a clean demo reset.
- Desktop mega menus use the measured header bottom plus a hover bridge, matching the close Jobs positioning across all children.
- Home hero sliders use three distinct child-owned images, visible pagination, 8.5-second autoplay and pause-on-hover with sharp natural-scale rendering.
- Managed demo/editorial/catalogue/gallery/Woo media is restored from bundled files; Woo Clothes keeps the complete bundled product-image pool.
- Quote drawers use resilient trigger detection and sit flush to the right viewport edge on quote-enabled themes.
- Cookie-consent acceptance persists across reloads using a stable browser marker.
- Legal pages remain left-aligned on the normal grid; Latest Thinking media/card edges are normalized.
- Partner/brand serialization is normalized idempotently to prevent repeated wrappers and the `wpbb/column` validation warning.
- Theme Settings retain child-owned controls for disabling dark mode and keeping English-only Polylang content.

## 3.8.11.14 child-only settings, editor, legal, editorial and hero finish

- Latest Thinking card rows now use the same 1320px grid as their headings; the 1440px row override that shifted the first card left has been removed.
- Hero images use a direct child-owned native source, stronger left-edge gradient masking and no CSS blur/viewport stretching.
- Appearance > Theme Settings adds switches to disable dark mode and disable translations/keep English only. Enabling English-only moves non-English Polylang Pages and Posts to Trash and hides the language switcher.
- Privacy/Terms/Cookies content spans the normal site grid and is left-aligned instead of being forced into a centered narrow column.
- The known raw partner-heading serialization defect is repaired in imported pages and on future page saves, resolving the wpbb/column validation error.
- Child editor CSS is moved from enqueue_block_editor_assets to enqueue_block_assets for the WordPress editor iframe.

## 3.8.11.13 child-only hero and editorial grid finish

- Latest Thinking / related editorial cards use the full 1440px site grid; the legacy outer BBuilder row and list start padding can no longer create a first-card left inset.
- Homepage heroes use a native-resolution child asset without viewport-width stretching.
- A stronger white-to-transparent hero gradient crosses the photograph's left edge so the image seam is hidden.
- Existing and translated managed hero blocks are refreshed from the same child-owned asset after upgrade.

## 3.8.11.12 child-only visual/media fixes

- Latest Thinking card grid now inherits the section grid with no first-card left inset.
- Sharper child-owned hero source and late frontend override.
- Managed catalogue and editorial media are re-synchronised from bundled child assets.
- Media/text CTA buttons align to the copy edge.

## 3.8.11.11 media and WooCommerce finalisation

- Repairs missing demo media from bundled local assets, including cloned-site WooCommerce product images and Automotive vehicle finder thumbnails.
- Forces WooCommerce filters, ranges, compare controls and product actions to the child theme accent instead of the plugin blue fallback.
- Uses a two-column desktop Basket, Checkout and My Account shell with mobile stacking only below 821px.
- Uses the highest-resolution bundled hero source during managed demo rebuilds; Business uses the 1600x1000 office source.
- Requires parent WP BBTheme 3.8.10.23 for reliable My Account header URLs on cloned sites.

## 3.8.10.82 suite consistency

Requires WP BBuilder 5.6.9+ for palette inheritance and the shared hCaptcha verifier. This release keeps the sector's individual brand colour while using the same 1440px canvas, card/form rhythm, dark-mode baseline and footer/newsletter hierarchy as the rest of the 15-theme suite. The one-time cleanup is restricted to records explicitly marked as theme-managed demo content.

## 3.8.10.65

- Fixes the Theme Settings frontend-protection panel so its CSS is loaded in the admin head instead of appearing as visible text.
- Makes sector media repair load the WordPress image API safely before generating attachment metadata.
- Refines shared card, directory, gallery and responsive alignment.

## 3.8.10.47

- More compact and consistent section spacing, cards and responsive layouts.
- Smaller in-frame gallery thumbnail pagination and improved light/dark contrast.
- Reliable child-owned WooCommerce product shells where the theme includes commerce.

# WP BBTheme Child Business 3.8.10.65
Non-WooCommerce business/agency starter built on WP BBTheme Core, Bootstrap grid and WP BBuilder/Gutenberg.

## v3.7

- New 1200×900 office/agency Appearance screenshot and matching local office demo media.
- Office-led Hero/About/Gallery/editorial demo rather than the older generic visual.
- More native BBuilder composition for proof metrics, badges, contact links and final CTA.
- Retains the v3.5 AJAX editorial blog, responsive mobile drawer, dark mode, forms, Contact/map, About timeline, gallery Swiper, case studies and scroll-to-top.

## Build

Source lives in `src/scss` and `src/js`. Run `yarn prod` (the package script executes `node tools/build.mjs`). Child presentation uses explicit breakpoints and no `clamp()` sizing.

After upgrading, run **Appearance → Starter Setup → Import / Refresh Starter Website** to refresh stored demo content.

### 3.8.10.46
- Dashboard-safe, resumable sector media repair; no synchronous bulk image regeneration on `admin_init`.
- Password protection controls live under **Theme Settings → General**.
- Thumbnail navigation is overlaid inside the main gallery image.
- Active-sector Blog and directory media are repaired after child-theme switching.

## SCSS structure (3.8.10.9)

Frontend styles are split into `tokens`, `tools`, `base`, `header`, `footer`, `components`, `swiper`, `motion`, `forms`, `blog`, `quality`, `sector`, `responsive` and `features`. Fluid typography uses the suite `fluid-font()` mixin and explicit viewport guards rather than `clamp()`. The generated production CSS intentionally contains no `!important` declarations.

### Build compatibility

The child build is dependency-free and works with Yarn 1.22.x as well as newer Yarn versions. No Corepack step is required. Use:

```sh
yarn prod
```

The command runs `node tools/build.mjs` and rebuilds the hashed CSS/JS manifest directly.


### 3.8.10.45
- Consistent 80/64/52px section rhythm and explicit light/dark card contrast.
- Active-theme sector media repair for demo pages, blogs, directories and galleries.
- Top-aligned About imagery plus thumbnail and modal galleries on supported directory cards and single pages.

### 3.8.10.44
- Frontend password protection is enabled by default with password `wp@demo`.
- Administrators can disable it or set a new password in **Settings → Theme Settings** at `/wp-admin/options-general.php?page=wp-theme-settings`.
- Successful visitors receive a signed access cookie valid for 24 hours by default.
- Purge full-page/server/CDN caches after changing the protection setting.

### 3.8.10.42
- Replaced demo feature icons with Tabler Icons v3.46.0 outline SVGs, sized for normal UI use and coloured from the child-theme brand token.
- Single-column imported demo rows are repaired to 12 columns at every breakpoint.
- Dark-mode demo cards use explicit dark surfaces/readable text.
- Optional frontend-only demo password protection is available in Settings → Theme Settings (default password `wp@demo`).
### 3.8.10.43
- Shared alignment and dark-mode contrast fixes across service, solution, process, directory, blog and commerce cards.
- Current child-theme media is reapplied after child-theme switches, including optimised AVIF/WebP files.
- Visible slider/grid images are loaded deterministically and duplicate single-item summary text is removed.

## 3.8.10.65 BBuilder demo system
This release expects WP BBuilder 5.6.4+ and standardises demo editing around BBuilder Row/Column, Div, Icon Card, Swiper and selected native WordPress content blocks. Legacy Group/Columns demo markup is migrated automatically.


## 3.8.11.08
WooCommerce shop, basket, checkout and account layouts were normalised across the sector suite; theme preview artwork was refreshed and package documentation was reduced to this README.
