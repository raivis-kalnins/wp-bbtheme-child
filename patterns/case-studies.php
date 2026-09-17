<?php
/**
 * Title: Case studies / proof cards
 * Slug: wp-theme-current/case-studies
 * Categories: wp-theme-current
 */
$profile = function_exists('wp_theme_get_demo_profile') ? wp_theme_get_demo_profile() : array();
echo function_exists('wp_theme_demo_case_studies_markup') ? wp_theme_demo_case_studies_markup($profile) : '';
