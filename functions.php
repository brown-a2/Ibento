<?php

// Enqueue parent theme styles
function ibento_enqueue_styles()
{
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');

        // Enqueue child theme styles
        wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style'), wp_get_theme()->get('Version'));
}

add_action('wp_enqueue_scripts', 'ibento_enqueue_styles');

/**
 * Dashboard/backend modifications
 */
require get_stylesheet_directory() . '/inc/dashboard.php';
require get_stylesheet_directory() . '/inc/default-post.php';

/**
 * Disable comments
 */
require get_stylesheet_directory() . '/inc/disable-comments.php';

/**
 * ACF custom fields
 */
require get_stylesheet_directory() . '/inc/acf/acf-custom-fields.php';

/**
 * Custom blocks
 */
require get_stylesheet_directory() . '/inc/featured-birthdays.php';




