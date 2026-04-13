<?php
if (!defined('ABSPATH')) {
    exit;
}

function wp_theme_child_enqueue_assets() {
    $theme = wp_get_theme();
    wp_enqueue_style('wp-theme-child-style', get_stylesheet_uri(), ['wp-theme-style'], $theme->get('Version'));

    $manifest = get_stylesheet_directory() . '/dist/.vite/manifest.json';
    if (file_exists($manifest)) {
        $data = json_decode((string) file_get_contents($manifest), true);
        if (is_array($data)) {
            if (!empty($data['src/scss/public.scss']['file'])) {
                wp_enqueue_style('wp-theme-child-dist', get_stylesheet_directory_uri() . '/dist/' . ltrim($data['src/scss/public.scss']['file'], '/'), ['wp-theme-child-style'], null);
            }
            if (!empty($data['src/js/main.js']['file'])) {
                wp_enqueue_script('wp-theme-child-app', get_stylesheet_directory_uri() . '/dist/' . ltrim($data['src/js/main.js']['file'], '/'), [], null, true);
            }
        }
    }
}
add_action('wp_enqueue_scripts', 'wp_theme_child_enqueue_assets', 30);

function wp_theme_child_acf_value($key, $default = '') {
    if (!function_exists('get_field')) {
        return $default;
    }
    $value = get_field($key, 'option');
    return ($value !== null && $value !== false && $value !== '') ? $value : $default;
}

function wp_theme_child_dynamic_css() {
    $brand = wp_theme_child_acf_value('brand_color', '#1d4ed8');
    $accent = wp_theme_child_acf_value('accent_color', '#0f172a');
    $surface = wp_theme_child_acf_value('surface_color', '#f8fafc');
    $content_width = wp_theme_child_acf_value('content_width', '840px');
    $wide_width = wp_theme_child_acf_value('wide_width', '1280px');
    $button_radius = wp_theme_child_acf_value('button_radius', '999px');
    $css = ':root{' .
        '--wp-theme-primary:' . sanitize_hex_color($brand ?: '#1d4ed8') . ';' .
        '--wp-theme-text:' . sanitize_hex_color($accent ?: '#0f172a') . ';' .
        '--wp-theme-surface:' . sanitize_hex_color($surface ?: '#f8fafc') . ';' .
        '--wp-theme-content-width:' . preg_replace('/[^0-9a-zA-Z.%\-]/', '', (string) $content_width) . ';' .
        '--wp-theme-wide-width:' . preg_replace('/[^0-9a-zA-Z.%\-]/', '', (string) $wide_width) . ';' .
        '--wp-theme-radius:' . preg_replace('/[^0-9a-zA-Z.%\-]/', '', (string) $button_radius) . ';' .
    '}';
    wp_add_inline_style('wp-theme-child-style', $css);
}
add_action('wp_enqueue_scripts', 'wp_theme_child_dynamic_css', 40);

function wp_theme_child_maybe_load_aos() {
    if (!wp_theme_child_acf_value('enable_animations', false) || !wp_theme_child_acf_value('enable_aos_cdn', false)) {
        return;
    }
    wp_enqueue_style('aos', 'https://unpkg.com/aos@2.3.4/dist/aos.css', [], '2.3.4');
    wp_enqueue_script('aos', 'https://unpkg.com/aos@2.3.4/dist/aos.js', [], '2.3.4', true);
    wp_add_inline_script('aos', 'document.addEventListener("DOMContentLoaded",function(){if(window.AOS){AOS.init({once:true,duration:600});}});');
}
add_action('wp_enqueue_scripts', 'wp_theme_child_maybe_load_aos', 50);

function wp_theme_child_seo_meta() {
    if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION')) {
        return;
    }
    if (is_singular()) {
        $description = wp_theme_child_acf_value('default_meta_description', '');
        if (!$description) {
            $description = wp_trim_words(wp_strip_all_tags(get_post_field('post_content', get_the_ID())), 28, '…');
        }
        if ($description) {
            echo '<meta name="description" content="' . esc_attr($description) . '">';
        }
        $og = wp_theme_child_acf_value('default_og_image', '');
        if (is_array($og)) {
            $og = $og['url'] ?? '';
        }
        if ($og) {
            echo '<meta property="og:image" content="' . esc_url($og) . '">';
        }
    }
}
add_action('wp_head', 'wp_theme_child_seo_meta', 5);


function wp_theme_child_additional_cpt_rules() {
    if (!function_exists('get_field')) {
        return [];
    }

    $rows = get_field('theme_additional_cpt_rules', 'option');
    $items = [];

    if (is_array($rows)) {
        foreach ($rows as $row) {
            $slug = sanitize_key((string) ($row['slug'] ?? ''));
            if ($slug === '' || in_array($slug, ['post', 'page', 'attachment'], true)) {
                continue;
            }
            $items[$slug] = [
                'slug' => $slug,
                'enabled' => !empty($row['enabled']),
            ];
        }
    }

    if (!empty($items)) {
        return $items;
    }

    $raw = (string) get_field('theme_custom_cpt_slugs', 'option');
    if ($raw === '') {
        return [];
    }

    foreach (preg_split('/[\r\n,]+/', $raw) as $part) {
        $slug = sanitize_key((string) $part);
        if ($slug === '' || in_array($slug, ['post', 'page', 'attachment'], true)) {
            continue;
        }
        $items[$slug] = [
            'slug' => $slug,
            'enabled' => false,
        ];
    }

    return $items;
}

function wp_theme_child_saved_cpt_slugs() {
    return array_values(array_unique(array_merge(array_values(array_keys(wp_theme_child_additional_cpt_rules())), wp_theme_child_acf_json_cpt_slugs())));
}

function wp_theme_child_acf_json_cpt_slugs() {
    $items = [];
    foreach (glob(get_stylesheet_directory() . '/acf-json/post_type_*.json') ?: [] as $file) {
        $payload = json_decode((string) file_get_contents($file), true);
        $slug = sanitize_key((string) ($payload['post_type'] ?? ''));
        if ($slug && !in_array($slug, ['post', 'page', 'attachment'], true)) {
            $items[$slug] = $slug;
        }
    }
    return array_values($items);
}

function wp_theme_child_cpt_option_field_name($post_type) {
    return 'theme_enable_cpt_' . sanitize_key(str_replace('-', '_', (string) $post_type));
}

function wp_theme_child_is_cpt_enabled($post_type) {
    $post_type = sanitize_key((string) $post_type);

    if (in_array($post_type, ['post', 'page', 'attachment'], true)) {
        return true;
    }

    if (!function_exists('get_field')) {
        return false;
    }

    if ($post_type === 'booking') {
        return (bool) get_field('theme_enable_booking_cpt', 'option');
    }

    $additional = wp_theme_child_additional_cpt_rules();
    if (isset($additional[$post_type])) {
        return !empty($additional[$post_type]['enabled']);
    }

    $value = get_field(wp_theme_child_cpt_option_field_name($post_type), 'option');
    return !empty($value);
}

function wp_theme_child_filter_custom_post_type_args($args, $post_type) {
    $post_type = sanitize_key((string) $post_type);

    if (in_array($post_type, ['post', 'page', 'attachment'], true)) {
        return $args;
    }

    $is_custom = !empty($args['public']) || !empty($args['show_ui']) || !empty($args['show_in_rest']) || !empty($args['show_in_menu']);
    if (!$is_custom) {
        return $args;
    }

    if (wp_theme_child_is_cpt_enabled($post_type)) {
        return $args;
    }

    $args['public'] = false;
    $args['publicly_queryable'] = false;
    $args['show_ui'] = false;
    $args['show_in_menu'] = false;
    $args['show_in_admin_bar'] = false;
    $args['show_in_nav_menus'] = false;
    $args['show_in_rest'] = false;
    $args['has_archive'] = false;
    $args['exclude_from_search'] = true;
    return $args;
}
add_filter('register_post_type_args', 'wp_theme_child_filter_custom_post_type_args', 100, 2);
