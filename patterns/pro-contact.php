<?php
/**
 * Title: Contact page — form + map
 * Slug: wp-theme-current/pro-contact
 * Categories: wp-theme-current
 */
$profile = function_exists('wp_theme_get_demo_profile') ? wp_theme_get_demo_profile() : array();
echo function_exists('wp_theme_sector_page_content') ? wp_theme_sector_page_content('contact',$profile) : '';
