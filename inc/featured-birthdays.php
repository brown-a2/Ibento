<?php


/**
 * Calculate the age on the date of the given birthday date.
 *
 * @param string $birthday_date The birthday date in 'YYYYMMDD' format.
 * @return string The age in a string format "X years, Y months, Z days".
 */
function ibento_calculate_age_on_birthday($birthday_date) {
    // Create DateTime objects for the birthday date
    $birthday = DateTime::createFromFormat('Ymd', $birthday_date);

    if (!$birthday) {
        return 'Invalid date'; // Return an error message if the date format is incorrect
    }

    // Get today's date and create a DateTime object for the next birthday
    $today = new DateTime();
    $current_year_birthday = new DateTime($today->format('Y') . $birthday->format('-m-d'));

    // If the birthday has already occurred this year, calculate for the next year
    if ($current_year_birthday < $today) {
        $current_year_birthday->modify('+1 year');
    }

    // Calculate the age
    $age = $current_year_birthday->diff($birthday);

    // Format the age as a string
    return $age->y . ' years, ' . $age->m . ' months, ' . $age->d . ' days';
}


// Render callback function for the dynamic block
function ibento_birthday_block_render_callback($attributes) {

    // Get today's date and current day-month
    $today = date('Ymd'); // Format today's date in 'YYYYMMDD'
    $today_day_month = date('md'); // Format today's day and month as 'MMDD'

    $args = array(
        'post_type' => 'birthday',
        'posts_per_page' => 5, // Retrieve all posts for custom sorting
        'meta_key' => 'birthday_date',
        'orderby' => 'meta_value',
        'order' => 'ASC', // Order ascending to get nearest future dates
        'meta_type' => 'DATE'
    );

    $query = new WP_Query($args);

    $birthday_posts = [];

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $birthday_date = get_field('birthday_date');
            if ($birthday_date) {
                $birthday_day_month = date('md', strtotime($birthday_date));
                // Include all dates for the rolling year
                $birthday_posts[] = [
                    'title' => get_the_title(),
                    'date' => $birthday_date,
                    'day_month' => $birthday_day_month,
                ];
            }
        }

        // Filter future dates within the current year and include dates in the next year
        $current_year_posts = array_filter($birthday_posts, function($post) use ($today_day_month) {
            return $post['day_month'] >= $today_day_month;
        });

        $next_year_posts = array_filter($birthday_posts, function($post) use ($today_day_month) {
            return $post['day_month'] < $today_day_month;
        });

        // Sort the posts by day and month
        usort($current_year_posts, function($a, $b) {
            return $a['day_month'] <=> $b['day_month'];
        });

        usort($next_year_posts, function($a, $b) {
            return $a['day_month'] <=> $b['day_month'];
        });

        // Merge the current year and next year posts
        $birthday_posts = array_merge($current_year_posts, $next_year_posts);

        // Limit to the nearest 10 birthdays
        $birthday_posts = array_slice($birthday_posts, 0, 10);

        // Generate output
        if (!empty($birthday_posts)) {
            $output = '<ul class="birthday-list">';
            foreach ($birthday_posts as $post) {
                $age_on_birthday = ibento_calculate_age_on_birthday($post['date']);
                $output .= '<li class="birthday-item">';
                $output .= '<span class="birthday-title">' . esc_html($post['title']) . '</span> - ';
                $output .= '<span class="birthday-date">' . esc_html($post['date']) . '</span> ';
                $output .= '<span class="birthday-age">(' . esc_html($age_on_birthday) . ')</span>';
                $output .= '</li>';
            }
            $output .= '</ul>';
        } else {
            $output = 'No posts found.';
        }
    } else {
        $output = 'No posts found.';
    }

    wp_reset_postdata();

    return $output;
}

// Register the dynamic block
function ibento_register_birthday_block() {
    register_block_type('ibento/birthday-block', [
        'render_callback' => 'ibento_birthday_block_render_callback',
    ]);
}

add_action('init', 'ibento_register_birthday_block');
