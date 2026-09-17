<?php
/**
 * Title: Sector gallery slider
 * Slug: wp-theme-current/gallery-slider
 * Categories: wp-theme-current
 */
$profile = function_exists('wp_theme_get_demo_profile') ? wp_theme_get_demo_profile() : array();
echo function_exists('wp_theme_demo_gallery_markup') ? wp_theme_demo_gallery_markup($profile) : '';
