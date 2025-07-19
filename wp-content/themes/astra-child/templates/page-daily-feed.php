<?php
/*
    * Template Name: Daily Feed Page Template
    * Description: Template for displaying the daily-feed page with selected posts.
    */
get_header(); ?>

<section class="common_header">
    <h1 class="header_title"><?php the_title(); ?></h1>
    <p class="header_text">Excepteur sint occaecat cupidatat</p>
</section>

<section class="feed_page_content">
    <div>
    <div class="feed_list_content">
        <div class="feed_list">
            <?php
            $args = array(
                'post_type' => 'daily-feed',
                'posts_per_page' => -1,
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
                            <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/play-icon.png" class="play_icon" />
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
            <button class="load_more_text">VIEW ALL <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/right-symbol.png" /></a>
        </div>
</section>


<?php get_footer(); ?>