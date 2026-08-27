<?php
if (!defined('ABSPATH')) {
    exit;
}

function wp_theme_child_enqueue_assets() {
    $theme = wp_get_theme();
    wp_enqueue_style('wp-theme-child-style', get_stylesheet_uri(), ['wp-theme-style'], $theme->get('Version'));

	$generated_vars = get_stylesheet_directory() . '/assets/css/acf-theme-vars.css';
	if (file_exists($generated_vars)) {
		wp_enqueue_style('wp-theme-acf-vars', get_stylesheet_directory_uri() . '/assets/css/acf-theme-vars.css', ['wp-theme-child-style'], filemtime($generated_vars));
	}

	$runtime_js = get_stylesheet_directory() . '/assets/js/theme.js';
	if (file_exists($runtime_js)) {
		wp_enqueue_script('wp-theme-inline', get_stylesheet_directory_uri() . '/assets/js/theme.js', [], filemtime($runtime_js), true);
		wp_add_inline_script('wp-theme-inline', 'window.wpThemeHome=' . wp_json_encode(home_url('/')) . ';', 'before');
	}

    $manifest = get_stylesheet_directory() . '/dist/.vite/manifest.json';
    if (file_exists($manifest)) {
        $data = json_decode((string) file_get_contents($manifest), true);
        if (is_array($data)) {
            if (!empty($data['src/scss/public.scss']['file'])) {
                wp_enqueue_style('wp-theme-child-dist', get_stylesheet_directory_uri() . '/dist/' . ltrim($data['src/scss/public.scss']['file'], '/'), ['wp-theme-child-style', 'wp-theme-acf-vars'], null);
            }
            if (!empty($data['src/js/main.js']['file'])) {
                wp_enqueue_script('wp-theme-child-app', get_stylesheet_directory_uri() . '/dist/' . ltrim($data['src/js/main.js']['file'], '/'), ['jquery'], null, true);
            }
        }
    }

	if (function_exists('wp_theme_acf_get')) {
		$smart_library_loading = (bool) wp_theme_acf_get('theme_smart_library_loading', 'option', 1);
		if (wp_theme_acf_get('alpine_js', 'option') === 'true' && (!$smart_library_loading || (function_exists('wp_theme_page_uses_library') && wp_theme_page_uses_library('alpine')))) {
			wp_enqueue_script('alpine-js', 'https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js', [], '3.14.3', true);
		}
		if (wp_theme_acf_get('media_glightbox', 'option') === 'true' && (!$smart_library_loading || (function_exists('wp_theme_page_uses_library') && wp_theme_page_uses_library('lightbox')))) {
			wp_enqueue_style('glightbox-css', 'https://cdnjs.cloudflare.com/ajax/libs/glightbox/3.3.1/css/glightbox.min.css', [], '3.3.1');
			wp_enqueue_script('glightbox-js', 'https://cdnjs.cloudflare.com/ajax/libs/glightbox/3.3.1/js/glightbox.min.js', [], '3.3.1', true);
		}
	}
}
add_action('wp_enqueue_scripts', 'wp_theme_child_enqueue_assets', 30);

// Theme-option typography and design tokens are presentation, so this child
// explicitly opts in after the parent has loaded their reusable generators.
add_action('after_setup_theme', function () {
	if (function_exists('wp_theme_enqueue_font_imports')) {
		add_action('wp_enqueue_scripts', 'wp_theme_enqueue_font_imports', 12);
	}
	if (function_exists('wp_theme_output_style_tokens')) {
		add_action('wp_head', 'wp_theme_output_style_tokens', 8);
	}
}, 20);

function wp_theme_child_acf_value($key, $default = '') {
    if (!function_exists('get_field') || (function_exists('acf') && !did_action('acf/init'))) {
        return $default;
    }
    $value = get_field($key, 'option');
    return ($value !== null && $value !== false && $value !== '') ? $value : $default;
}

function wp_theme_child_dynamic_css() {
	$brand = get_theme_mod('wp_theme_sector_brand_color', wp_theme_child_acf_value('brand_color', '#1d4ed8'));
    $accent = wp_theme_child_acf_value('accent_color', '#0f172a');
    $surface = wp_theme_child_acf_value('surface_color', '#f8fafc');
    $content_width = wp_theme_child_acf_value('content_width', '840px');
    $wide_width = wp_theme_child_acf_value('wide_width', '1280px');
	$button_radius = get_theme_mod('wp_theme_sector_card_radius', wp_theme_child_acf_value('button_radius', '999px'));
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

function wp_theme_child_dark_mode_bootstrap() {
	echo '<script>(function(){try{var m=localStorage.getItem("wpThemeMode");if(m==="dark"){document.documentElement.classList.add("is-dark-theme");document.documentElement.setAttribute("data-theme","dark");}}catch(e){}})();</script>';
}
add_action('wp_head', 'wp_theme_child_dark_mode_bootstrap', 1);

function wp_theme_child_enqueue_demo_homepage_assets() {
	if (!is_singular()) {
		return;
	}
	$post_id = get_queried_object_id();
	if (!$post_id || !get_post_meta($post_id, '_wp_theme_demo_homepage', true)) {
		return;
	}
	$file = get_stylesheet_directory() . '/assets/css/homepage-demo.css';
	if (file_exists($file)) {
		wp_enqueue_style('wp-theme-homepage-demo', get_stylesheet_directory_uri() . '/assets/css/homepage-demo.css', ['wp-theme-child-style'], filemtime($file));
	}
}
add_action('wp_enqueue_scripts', 'wp_theme_child_enqueue_demo_homepage_assets', 40);

/**
 * Default child demo. Commerce is automatic: it is included only when both
 * WooCommerce and WP Theme Woo Support are active.
 */
function wp_theme_child_demo_profile($profile) {
	$profile['id'] = 'business';
	$profile['name'] = __('Modern Business', 'wp-bbtheme-child');
	$profile['eyebrow'] = __('Business, agency or online store', 'wp-bbtheme-child');
	$profile['hero_title'] = __('A calm, capable website for your next stage of growth.', 'wp-bbtheme-child');
	$profile['hero_text'] = __('Use it as a focused business website or activate WooCommerce and the support plugin to add a complete, filterable shop.', 'wp-bbtheme-child');
	$profile['commerce'] = 'auto';
	$profile['palette'] = [
		'theme_brand_color' => '#475569',
		'theme_accent_color' => '#64748b',
		'theme_text_color' => '#1f2937',
		'theme_heading_color' => '#111827',
		'theme_background_color' => '#ffffff',
		'theme_surface_color' => '#ffffff',
		'theme_surface_alt_color' => '#f4f4f5',
		'theme_border_color' => '#e4e4e7',
		'theme_grey_dark_color' => '#3f3f46',
		'theme_grey_light_color' => '#f4f4f5',
		'theme_success_color' => '#475569',
		'theme_link_color' => '#334155',
		'theme_link_hover_color' => '#111827',
		'theme_radius' => '16px',
		'theme_font_provider' => 'system',
		'theme_body_font' => "system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
		'theme_heading_font' => "system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
		'theme_ui_font' => "system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
	];
	$profile['services'] = [
		[__('Digital strategy', 'wp-bbtheme-child'), __('Plan the content, journeys and technology needed to move the business forward.', 'wp-bbtheme-child')],
		[__('Web design', 'wp-bbtheme-child'), __('A restrained visual system built from editable WordPress patterns.', 'wp-bbtheme-child')],
		[__('Growth support', 'wp-bbtheme-child'), __('Keep publishing, testing and improving after the first launch.', 'wp-bbtheme-child')],
	];

	return $profile;
}
add_filter('wp_theme_demo_profile', 'wp_theme_child_demo_profile');

$wp_theme_child_animation_frontend = get_stylesheet_directory() . '/inc/animations/frontend.php';
if (file_exists($wp_theme_child_animation_frontend)) {
	require_once $wp_theme_child_animation_frontend;
}
