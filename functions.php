<?php

add_theme_support( 'post-thumbnails' );

function ibento_enqueue_styles() {
    // Enqueue parent theme styles
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');

    // Get the child theme stylesheet path
    $child_style_path = get_stylesheet_directory() . '/style.css';

    // Get the modification time of the child theme stylesheet
    $child_style_version = filemtime($child_style_path);

    // Enqueue child theme styles with the modification time as the version
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style'), $child_style_version);
}

add_action('wp_enqueue_scripts', 'ibento_enqueue_styles');


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




