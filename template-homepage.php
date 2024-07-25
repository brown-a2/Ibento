<?php
/*
Template Name: Homepage Template
*/

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

    <?php
    // Arguments for WP_Query
    $args = array(
        'post_type' => 'post', // Replace with your custom post type if needed
        'posts_per_page' => -1, // Number of posts to retrieve, -1 for all posts
        'meta_key' => 'event_date', // Custom field key
        'orderby' => 'meta_value', // Order by custom field value
        'order' => 'ASC', // Order ascending, use 'DESC' for descending
        'meta_type' => 'DATE' // Specify the meta type to ensure proper date sorting
    );

    // Create a new WP_Query
    $query = new WP_Query($args);

    // Check if there are posts
    if ($query->have_posts()) : 
        echo '<ul>'; // Start the list
        // Loop through the posts
        while ($query->have_posts()) : $query->the_post();
            // Display each post
            echo '<li>';
            the_title();
            echo ' - ' . get_field('event_date'); // Display the custom date field
            echo '</li>';
        endwhile;
        echo '</ul>'; // End the list
    else :
        // No posts found
        echo 'No posts found.';
    endif;

    // Reset Post Data
    wp_reset_postdata();
    ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_sidebar();
get_footer();

