<?php
/**
 * Title: Story timeline
 * Slug: wp-theme-current/history-timeline
 * Categories: wp-theme-current
 */
$profile = function_exists('wp_theme_get_demo_profile') ? wp_theme_get_demo_profile() : array();
echo function_exists('wp_theme_demo_timeline_markup') ? wp_theme_demo_timeline_markup($profile) : '';
