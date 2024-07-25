<?php

// Disable support for comments and trackbacks in posts
function herrnaset_disable_comments_on_posts() {
    remove_post_type_support('post', 'comments');
    remove_post_type_support('post', 'trackbacks');
}
add_action('init', 'herrnaset_disable_comments_on_posts');

// Close comments on the front-end for posts
function herrnaset_disable_comments_status_for_posts($open, $post_id) {
    $post = get_post($post_id);
    if ($post->post_type === 'post') {
        return false;
    }
    return $open;
}
add_filter('comments_open', 'herrnaset_disable_comments_status_for_posts', 20, 2);
add_filter('pings_open', 'herrnaset_disable_comments_status_for_posts', 20, 2);

// Hide existing comments for posts
function herrnaset_hide_existing_comments_for_posts($comments, $post_id) {
    $post = get_post($post_id);
    if ($post->post_type === 'post') {
        $comments = array();
    }
    return $comments;
}
add_filter('comments_array', 'herrnaset_hide_existing_comments_for_posts', 10, 2);

// Remove comments page in menu
function herrnaset_disable_comments_admin_menu_for_posts() {
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'herrnaset_disable_comments_admin_menu_for_posts');

// Redirect any user trying to access comments page
function herrnaset_disable_comments_admin_menu_redirect_for_posts() {
    global $pagenow;
    if ($pagenow === 'edit-comments.php') {
        wp_redirect(admin_url());
        exit;
    }
}
add_action('admin_init', 'herrnaset_disable_comments_admin_menu_redirect_for_posts');

// Remove comments metabox from dashboard
function herrnaset_disable_comments_dashboard_for_posts() {
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}
add_action('admin_init', 'herrnaset_disable_comments_dashboard_for_posts');

// Remove comments links from admin bar
function herrnaset_disable_comments_admin_bar_for_posts() {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
}
add_action('init', 'herrnaset_disable_comments_admin_bar_for_posts');
