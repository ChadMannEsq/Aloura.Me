<?php
/*
 * Template Name: Home Page Template
 * Description: Template for displaying the home page with selected posts.
 */

get_header();

// Start the loop
while (have_posts()):
    the_post();

    // Get selected posts
    $selected_posts = get_post_meta(get_the_ID(), 'home_page_selected_posts', true);

    if ($selected_posts) { ?>
        <section class="banner_section">
            <div class="banner_slider">
                <?php foreach ($selected_posts as $selected_post) { ?>
                    <?php
                    $post_id = $selected_post['post_id'];
                    $post_title = get_the_title($post_id);
                    $post_link = get_permalink($post_id);
                    $post_image_url = get_the_post_thumbnail_url($post_id, 'full');
                    $post_type = get_post_type($post_id);
                    $vendor_terms = get_the_term_list($post_id, 'vendor', 'with ', ', ', '');
                    ?>
                    <div class="banner_slide" style="background-image: url('<?php echo $post_image_url; ?>');">
                        <div class="banner_content">
                            <h2><a href="<?php echo $post_link; ?>"><?php echo $post_title; ?></a></h2>
                            <span><?php echo $vendor_terms; ?> </span>
                        </div>
                        <?php
                        if ($post_type = 'film') {
                            $video_url = get_post_meta($post_id, 'model_film_video', true);
                            if ($video_url) { ?>
                                <video autoplay muted loop poster="<?php echo $post_image_url; ?>">
                                    <source src="<?php echo $video_url; ?>" type="video/mp4">
                                </video>
                            <?php }
                        }
                        ?>
                    </div>
                <?php } ?>
            </div>
            <button class="slick_prev slick_arrow">
                <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/slider-prev-arrow.png"
                    alt="Previous">
            </button>
            <button class="slick_next slick_arrow">
                <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/slider-next-arrow.png"
                    alt="Next">
            </button>
        </section>

        <!-- <section class="get_access">
            <p class="get_access_details">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, </p>
            <h4 class="get_access_title">GET ACCESS <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/svg/right-pink-arrow.svg" /> </h4>
        </section> -->

        <section>
            <div class="common_header">
                <h1 class="header_title">Popular</h1>
                <p class="header_text">Enjoy top films from Putri</p>
            </div>

            <div class="popular_wrapper">
                <div class="popular_list">
                    <?php
                    $args = array(
                        'post_type' => 'film',
                        'posts_per_page' => 8, // Limit to 8 posts
                    );
                    $films_query = new WP_Query($args);

                    if ($films_query->have_posts()) {
                        while ($films_query->have_posts()) {
                            $films_query->the_post();
                            $title = get_the_title();
                            $permalink = get_permalink();
                            $featured_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                            $terms = wp_get_post_terms(get_the_ID(), 'vendor');
                            $film_vendor = '';

                            if (!is_wp_error($terms) && !empty($terms)) {
                                $term_names = wp_list_pluck($terms, 'name');
                                $film_vendor = implode(', ', $term_names);
                            } ?>
                            <div class="popular_item">
                                <div class="popular_item_content"
                                    style="background-image: url('<?php echo esc_url($featured_image_url); ?>');">
                                    <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/play-icon.png"
                                        class="play_icon" />
                                    <a href="<?php echo esc_url($permalink); ?>">
                                        <h4 class="overlay-content"><?php echo esc_html($title); ?></h4>
                                        <span class="film_vendor_name">With <?php echo esc_html($film_vendor); ?></span>
                                    </a>
                                </div>
                            </div>
                        <?php }
                    } else {
                        echo '<p>No films found.</p>';
                    }
                    wp_reset_postdata();
                    ?>
                </div>
                <div class="load_more_btn">
                    <a href="<?php echo esc_url(home_url('/films')); ?>" class="load_more_text">VIEW ALL <img
                            src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/right-symbol.png" /></a>
                </div>
            </div>
        </section>
        <hr class="explore_separator">

        <section>
            <div class="common_header">
                <h1 class="header_title">NEW IN</h1>
                <p class="header_text">Excepteur sint occaecat cupidatat</p>
            </div>

            <div class="explor_films_container">
                <div class="photosets_list">
                    <?php
                    $args = array(
                        'post_type' => 'film',
                        'posts_per_page' => 3, // Limit to 8 posts
                    );
                    $films_query = new WP_Query($args);

                    if ($films_query->have_posts()) {
                        while ($films_query->have_posts()) {
                            $films_query->the_post();
                            $title = get_the_title();
                            $permalink = get_permalink();
                            $featured_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                            $terms = wp_get_post_terms(get_the_ID(), 'vendor');
                            $film_vendor = '';

                            if (!is_wp_error($terms) && !empty($terms)) {
                                $term_names = wp_list_pluck($terms, 'name');
                                $film_vendor = implode(', ', $term_names);
                            } ?>
                            <div class="photoset_item">
                                <div class="photoset_item_content new_in_item_content"
                                    style="background-image: url('<?php echo esc_url($featured_image_url); ?>');">
                                    <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/play-icon.png"
                                        class="play_icon" />
                                    <a href="<?php echo esc_url($permalink); ?>">
                                        <h4 class="overlay-content"><?php echo esc_html($title); ?></h4>
                                        <span class="film_vendor_name">With <?php echo esc_html($film_vendor); ?></span>
                                    </a>
                                </div>
                            </div>
                        <?php }
                    } else {
                        echo '<p>No films found.</p>';
                    }
                    wp_reset_postdata();
                    ?>
                    <?php
                    $args = array(
                        'post_type' => 'photoset',
                        'posts_per_page' => 3, // Limit to 8 posts
                    );
                    $films_query = new WP_Query($args);

                    if ($films_query->have_posts()) {
                        while ($films_query->have_posts()) {
                            $films_query->the_post();
                            $title = get_the_title();
                            $permalink = get_permalink();
                            $featured_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                            $terms = wp_get_post_terms(get_the_ID(), 'vendor');
                            $film_vendor = '';

                            if (!is_wp_error($terms) && !empty($terms)) {
                                $term_names = wp_list_pluck($terms, 'name');
                                $film_vendor = implode(', ', $term_names);
                            } ?>
                            <div class="photoset_item">
                                <div class="photoset_item_content new_in_item_content"
                                    style="background-image: url('<?php echo esc_url($featured_image_url); ?>');">
                                    <a href="<?php echo esc_url($permalink); ?>">
                                        <h4 class="overlay-content"><?php echo esc_html($title); ?></h4>
                                        <span class="film_vendor_name">With <?php echo esc_html($film_vendor); ?></span>
                                    </a>
                                </div>
                            </div>
                        <?php }
                    } else {
                        echo '<p>No films found.</p>';
                    }
                    wp_reset_postdata();
                    ?>
                </div>
                <div class="load_more_btn">
                    <a href="<?php echo esc_url(home_url('/films')); ?>" class="load_more_text">VIEW ALL <img
                            src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/right-symbol.png" /></a>
                </div>
            </div>
        </section>
        <hr class="explore_separator">

        <section class="feed_page_content">
            <div class="common_header">
                <h1 class="header_title">DAILY POSTS</h1>
                <p class="header_text">Excepteur sint occaecat cupidatat</p>
            </div>
            <div class="feed_list_content">
                <div class="feed_list">
                    <?php
                    $args = array(
                        'post_type' => 'daily-feed',
                        'posts_per_page' => 6,
                    );
                    $feed_query = new WP_Query($args);

                    if ($feed_query->have_posts()) {
                        while ($feed_query->have_posts()) {
                            $feed_query->the_post();
                            $date = get_the_date('j M');
                            $permalink = get_permalink();
                            $featured_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                            $content = get_the_content();
                            ?>
                            <div class="feed_item">
                                <div class="feed_item_content"
                                    style="background-image: url('<?php echo esc_url($featured_image_url); ?>');">
                                    <a href="<?php echo esc_url($permalink); ?>">
                                        <h4 class="overlay-content"><?php echo esc_html($date); ?></h4>
                                        <p class="single_Feed_vendor_name"><?php echo $content; ?></p>
                                    </a>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<p>No feed found.</p>';
                    }
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
            <a href="<?php echo esc_url(home_url('/films')); ?>" class="view_button">View all <img
                    src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/right-symbol.png" /></a>
        </section>
        <hr class="explore_separator">

        <section class="categories_section">
            <div class="categories_wrapper">
                <div class="common_header">
                    <h2 class="header_title">categories</h2>
                    <p class="header_text">Excepteur sint occaecat cupidatat</p>
                </div>
                <div class="categories_list">
                    <?php
                    // Define the specific tags you want to display
                    $specific_tags = array('Fashion', 'Designer', 'Catwalk', 'Modeling', 'Runway', 'Elegance'); // Replace with your desired tag names
            
                    // Get all post IDs associated with the 'film', 'photoset', and 'bts' post types
                    $post_ids = get_posts(array(
                        'post_type' => array('film', 'photoset', 'bts'),
                        'posts_per_page' => -1, // Get all posts
                        'fields' => 'ids', // Only get post IDs
                    ));

                    // Check if post IDs are found
                    if (!empty($post_ids)) {
                        // Get all tags associated with these posts
                        $tags = get_terms(array(
                            'taxonomy' => 'post_tag',
                            'object_ids' => $post_ids,
                        ));

                        // Check if tags are found and not an error
                        if (!empty($tags) && !is_wp_error($tags)) {
                            foreach ($tags as $tag) {
                                // Check if the tag is in the specific tags list
                                if (in_array($tag->name, $specific_tags)) {
                                    $tag_link = get_tag_link($tag->term_id);
                                    $tag_image = get_field('tag_image', 'post_tag_' . $tag->term_id);
                                    ?>
                                    <div class="categorie_item">
                                        <a href="<?php echo esc_url($tag_link); ?>" style="background-image: url('<?php echo $tag_image; ?>');">
                                            <h4>#<?php echo esc_html($tag->name); ?></h4>
                                            <span><?php echo esc_html($tag->count); ?> Posts</span>
                                        </a>
                                    </div>
                                    <?php
                                }
                            }
                        } else {
                            echo '<p>No tags found or an error occurred.</p>';
                            // Debugging information
                            if (is_wp_error($tags)) {
                                echo '<p>Error: ' . $tags->get_error_message() . '</p>';
                            }
                        }
                    } else {
                        echo '<p>No posts found.</p>';
                        // Debugging information
                        echo '<p>Query: ';
                        print_r($post_ids);
                        echo '</p>';
                    }
                    ?>

                </div>
            </div>
        </section>
        <hr class="explore_separator">
        <section>
            <div class="common_header">
                <h1 class="header_title">Friends</h1>
                <p class="header_text">Excepteur sint occaecat cupidatat</p>
            </div>
            <div class="model_list">
                <?php
                $terms = get_terms(array(
                    'taxonomy' => 'model',
                    'hide_empty' => false,
                    'number' => 16,
                ));
                $fallback_image_url = get_stylesheet_directory_uri() . '/assets/images/noimg.png'; // URL for fallback image
        
                if (!empty($terms) && !is_wp_error($terms)) { ?>
                    <?php foreach ($terms as $term) { ?>
                        <?php
                        $image_id = get_term_meta($term->term_id, 'model-image-id', true);
                        $image_url = wp_get_attachment_image_src($image_id, 'full');
                        $term_link = get_term_link($term);

                        // Use fallback image if no image URL is set
                        $background_image_url = !empty($image_url[0]) ? $image_url[0] : $fallback_image_url;
                        ?>
                        <div class="model_item">
                            <div class="model_item_content"
                                style="background-image: url('<?php echo esc_url($background_image_url); ?>');">
                                <a href="<?php echo esc_url($term_link); ?>">
                                    <h4 class="modals_name"><?php echo esc_html($term->name); ?></h4>
                                </a>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
            <div class="load_more_btn">
                <a href="#" class="load_more_text">VIEW ALL <img
                        src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/right-symbol.png" /></a>
            </div>
            <hr class="explore_separator">
        </section>
        <hr class="explore_separator">

        <section>
            <div class="common_header">
                <h1 class="header_title">COUPLES</h1>
                <p class="header_text">Excepteur sint occaecat cupidatat</p>
            </div>
            <div>
                <div class="popular_list">
                    <?php
                    for ($i = 0; $i < 8; $i++):
                        ?>
                        <div class="popular_item">
                            <div class="popular_item_content"
                                style="background-image: url('<?php echo esc_url($featured_image_url); ?>');">
                                <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/play-icon.png"
                                    class="play_icon" />
                                <a href="<?php echo esc_url($permalink); ?>">
                                    <h4 class="overlay-content">lorem ipsum</h4>
                                    <span class="film_vendor_name">With PUTRI</span>
                                </a>
                            </div>
                        </div>
                    <?php
                    endfor;
                    ?>
                </div>
            </div>
            <div class="load_more_btn">
                <a href="#" class="load_more_text">VIEW ALL <img
                        src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/right-symbol.png" /></a>
            </div>
        </section>
        <hr class="explore_separator">

        <section class="upcoming_section">
            <div class="common_header">
                <h1 class="header_title">UPCOMING</h1>
                <p class="header_text">Excepteur sint occaecat cupidatat</p>
            </div>
            <div>
                <div class="upcoming_list">
                    <div class="upcoming_item">
                        <div class="upcoming_item_content"
                            style="background-image: url('<?php echo esc_url($featured_image_url); ?>');">
                            <div class="upcoming_wrap">
                                <a href="#">
                                    <h4 class="overlay-content">lorem ipsum</h4>
                                    <span class="film_vendor_name">With PUTRI</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="upcoming_item">
                        <div class="upcoming_item_content"
                            style="background-image: url('<?php echo esc_url($featured_image_url); ?>');">
                            <div class="upcoming_wrap">
                                <a href="#">
                                    <h3>Tue, 18 Jun</h3>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


    <?php }
    the_content();
endwhile;

get_footer();
?>