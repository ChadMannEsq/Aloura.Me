<?php
/*
    * Template Name: Explore Page Template
    * Description: Template for displaying the explore page with selected posts.
    */
get_header(); ?>


<!----------------- film section --------------->
<section>
    <div class="common_header">
        <h1 class="header_title">FILMS</h1>
        <p class="header_text">Excepteur sint occaecat cupidatat</p>
    </div>
    <div class="explor_films_container">
        <div class="explor_films_list">
            <div class="films_list">
                <?php
                $args = array(
                    'post_type' => 'film',
                    'posts_per_page' => 6,
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
                        <div class="film_item">
                            <div class="film_item_content" style="background-image: url('<?php echo esc_url($featured_image_url); ?>');">
                                <a href="<?php echo esc_url($permalink); ?>">
                                    <div class="vendor_details_wrapper">
                                        <p class="film_vendor_name">WITH <?php echo esc_html($film_vendor); ?></p>
                                        <h4 class="vendor_title"><?php echo esc_html($title); ?></h4>
                                    </div>
                                </a>
                                <?php $video_url = get_post_meta(get_the_ID(), 'model_film_video', true); ?>
                                <?php if ($video_url) { ?>
                                    <a href="<?php echo esc_url($permalink); ?>" class="video-link">
                                        <video id="video" class="film_video" id="video" data-play="hover" muted="muted" autoplay loop>
                                            <source src="<?php echo $video_url; ?>" type="video/mp4">
                                        </video>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo '<p>No films found.</p>';
                }
                wp_reset_postdata();
                ?>

            </div>
        </div>
        <div class="load_more_btn">
            <div class="load_more_btn">
                <a href="<?php echo home_url(); ?>/films" class="load_more_text">VIEW ALL <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/right-symbol.png" /></a>
            </div>
        </div>
    </div>
</section>
<hr class="explore_separator">

<!----------------- photoset section --------------->

<section>
    <div class="common_header">
        <h1 class="header_title">PHOTOSET</h1>
        <p class="header_text">Excepteur sint occaecat cupidatat</p>
    </div>
    <div class="explor_films_container">
        <div class="photosets_list">
            <?php
            $args = array(
                'post_type' => 'photoset',
                'posts_per_page' => 8,
            );
            $photosets_query = new WP_Query($args);

            if ($photosets_query->have_posts()) {
                while ($photosets_query->have_posts()) {
                    $photosets_query->the_post();
                    $title = get_the_title();
                    $permalink = get_permalink();
                    $featured_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                    $terms = wp_get_post_terms(get_the_ID(), 'vendor');
                    $photoset_vendor = '';

                    if (!is_wp_error($terms) && !empty($terms)) {
                        $term_names = wp_list_pluck($terms, 'name');
                        $photoset_vendor = implode(', ', $term_names);
                    }
            ?>
                    <div class="photoset_item explore_photoset">
                        <div class="photoset_item_content" style="background-image: url('<?php echo esc_url($featured_image_url); ?>');">
                            <a href="<?php echo esc_url($permalink); ?>">
                                <h4 class="overlay-content"><?php echo esc_html($title); ?></h4>
                                <span class="film_vendor_name"><?php echo esc_html($photoset_vendor); ?></span>
                            </a>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo '<p>No photosets found.</p>';
            }
            wp_reset_postdata();
            ?>
        </div>
        <div class="load_more_btn">
            <a href="<?php echo home_url(); ?>/photosets" class="load_more_text">VIEW ALL <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/right-symbol.png" /></a>
        </div>
    </div>
</section>
<hr class="explore_separator">

<!----------------- BTS section --------------->

<section>
    <div class="common_header">
        <h1 class="header_title">BTS</h1>
        <p class="header_text">Excepteur sint occaecat cupidatat</p>
    </div>
    <div class="explor_films_container">
        <div class="explor_films_list">
            <div class="films_list">
                <?php
                $args = array(
                    'post_type' => 'bts',
                    'posts_per_page' => 6,
                );
                $bts_query = new WP_Query($args);

                if ($bts_query->have_posts()) {
                    while ($bts_query->have_posts()) {
                        $bts_query->the_post();
                        $title = get_the_title();
                        $permalink = get_permalink();
                        $featured_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                        $terms = wp_get_post_terms(get_the_ID(), 'vendor');
                        $bts_vendor = '';

                        if (!is_wp_error($terms) && !empty($terms)) {
                            $term_names = wp_list_pluck($terms, 'name');
                            $bts_vendor = implode(', ', $term_names);
                        }
                ?>
                        <div class="film_item">
                            <a href="<?php echo esc_url($permalink); ?>">
                                <div class="film_item_content" style="background-image: url('<?php echo esc_url($featured_image_url); ?>');">
                                    <div class="vendor_details_wrapper">
                                        <p class="film_vendor_name">WITH<?php echo esc_html($bts_vendor); ?></p>
                                        <h4 class="vendor_title"><?php echo esc_html($title); ?></h4>
                                    </div>
                                </div>
                            </a>
                        </div>
                <?php
                    }
                } else {
                    echo '<p>No bts found.</p>';
                }
                wp_reset_postdata();
                ?>
            </div>
        </div>
        <div class="load_more_btn">
            <a href="<?php echo home_url(); ?>/behind-the-scenes" class="load_more_text">VIEW ALL <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/right-symbol.png" /></a>
        </div>
    </div>
</section>
<hr class="explore_separator">

<!----------------- Daily feed section --------------->

<section>

    <div class="common_header">
        <h1 class="header_title">DAILY FEED</h1>
        <p class="header_text">Excepteur sint occaecat cupidatat</p>
    </div>
    <div>
        <div class="explor_films_container">
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
                            <div class="feed_item_content" style="background-image: url('<?php echo esc_url($featured_image_url); ?>');">
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
    </div>

    <div class="load_more_btn">
        <a href="#" class="load_more_text">VIEW ALL <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/right-symbol.png" /></a>
    </div>
</section>
<hr>

<!----------------- categories --------------->

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

<!----------------- friends --------------->

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
        if (!empty($terms) && !is_wp_error($terms)) { ?>
            <?php foreach ($terms as $term) { ?>
                <?php
                $image_id = get_term_meta($term->term_id, 'model-image-id', true);
                $image_url = wp_get_attachment_image_src($image_id, 'full');
                $term_link = get_term_link($term);
                ?>
                <div class="model_item">
                    <div class="model_item_content" style="background-image: url('<?php echo $image_url[0]; ?>');">
                        <a href="<?php echo esc_url($term_link); ?>">
                            <h4 class="modals_name"><?php echo esc_html($term->name); ?></h4>
                        </a>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
    <div class="load_more_btn">
        <a href="#" class="load_more_text">VIEW ALL <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/right-symbol.png" /></a>
    </div>
</section>
<hr class="explore_separator">

<!----------------- MY CLOSET --------------->
<section>
    <div class="common_header">
        <h1 class="header_title">MY CLOSET</h1>
        <p class="header_text">Excepteur sint occaecat cupidatat</p>
    </div>
    <div class="explor_films_container">
        <div class="explor_films_list">
            <div class="films_list">
                <?php
                $args = array(
                    'post_type' => 'my-closet',
                    'posts_per_page' => 6,
                );
                $bts_query = new WP_Query($args);

                if ($bts_query->have_posts()) {
                    while ($bts_query->have_posts()) {
                        $bts_query->the_post();
                        $date = get_the_date('l d F, Y');
                        $permalink = get_permalink();
                        $featured_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                ?>
                        <div class="film_item">
                            <a href="<?php echo esc_url($permalink); ?>">
                                <div class="film_item_content" style="background-image: url('<?php echo esc_url($featured_image_url); ?>');">
                                    <div class="vendor_details_wrapper">
                                        <p class="film_vendor_name"><?php echo $date; ?></p>
                                        <h4 class="vendor_title">My Closet Videos</h4>
                                    </div>
                                </div>
                            </a>
                        </div>
                <?php
                    }
                } else {
                    echo '<p>No bts found.</p>';
                }
                wp_reset_postdata();
                ?>
            </div>
        </div>
        <div class="load_more_btn">
            <a href="<?php echo home_url(); ?>/behind-the-scenes" class="load_more_text">VIEW ALL <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/right-symbol.png" /></a>
        </div>
    </div>
</section>



<?php get_footer(); ?>