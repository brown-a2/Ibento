<?php


/**
 * Calculate the age on the date of the given birthday date.
 *
 * @param string $birthday_date The birthday date in 'YYYYMMDD' format.
 * @return string The age in a string format "X years".
 */
function ibento_calculate_age_on_birthday($birthday_date) {
    // Create DateTime objects for the birthday date
    $birthday = DateTime::createFromFormat('Ymd', $birthday_date);

    if (!$birthday) {
        return 'Invalid date'; // Return an error message if the date format is incorrect
    }

    // Get today's date
    $today = new DateTime();

    // Calculate the age at the end of this year
    $current_year_birthday = new DateTime($today->format('Y') . $birthday->format('-m-d'));

    // If the birthday has already occurred this year, calculate for the next year
    if ($current_year_birthday < $today) {
        $age_on_birthday = $today->format('Y') - $birthday->format('Y') + 1;
    } else {
        $age_on_birthday = $today->format('Y') - $birthday->format('Y');
    }

    if ($current_year_birthday == $today) {
        return $age_on_birthday . ' years old today! &#127880;';
    }

    // Format the age as a string
    return $age_on_birthday . ' years old';
}

/**
 * Convert date format from 'YYYYMMDD' to 'F j, Y'.
 *
 * @param string $postDate The date in 'YYYYMMDD' format.
 * @return string The formatted date.
 */
function ibentoConvertDate($postDate) {
    // Check if the date is set
    if (isset($postDate)) {
        // Create a DateTime object from the input date
        $date = DateTime::createFromFormat('Ymd', $postDate);
        
        // Check if the date is valid
        if ($date) {
            // Format the date to 'F j, Y'
            return $date->format('F j, Y');
        } else {
            return 'Invalid date format';
        }
    } else {
        return 'Date not provided';
    }
}


function ibento_birthday_block_render_callback() {
    // Get today's date and current day-month
    $posts_to_show = 16;

    $today_day_month = date('md'); // Format today's day and month as 'MMDD'

    $args = array(
        'post_type' => 'birthday',
        'posts_per_page' => -1, // Retrieve all posts for custom sorting
        'meta_key' => 'birthday_date',
        'orderby' => 'meta_value',
        'order' => 'DEC', // Order ascending to get nearest future dates
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
                    'ID' => get_the_ID(),
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
        $birthday_posts = array_slice($birthday_posts, 0, $posts_to_show);

        // Generate output
        if (!empty($birthday_posts)) {
            $output = '<ul class="birthday-list">';
            foreach ($birthday_posts as $post) {
                $age_on_birthday = ibento_calculate_age_on_birthday($post['date']);
                $output .= '<li class="birthday-item">';
                $output .= '<span class="birthday-title"><a href="' . get_permalink($post['ID']) . '">' . esc_html($post['title']) . '</a></span> ';
                $output .= '<span class="birthday-age"> Will be turning <span class="birthday-date-number">' . esc_html($age_on_birthday) . '</span></span>';
                $output .= '<span class="birthday-date"> | <span class="birthday-date-title">Birthday</span>:<br> | ' . esc_html(ibentoConvertDate($post['date'])) . '</span> ';
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
