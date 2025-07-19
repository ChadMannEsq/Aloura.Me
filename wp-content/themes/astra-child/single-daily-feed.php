<?php get_header(); ?>

<div class="content-area">
    <main class="site-main">
        <div class="single-page-container">
            <?php while (have_posts()) :
                the_post(); ?>
                <div class="section-feed-heading common_header">
                    <h1 class="header_title">Daily Feed: <span class="feed_date"><?php echo get_the_date('j M'); ?></span></h1>
                    <span class="header_text">
                        <?php the_content(); ?>
                    </span>
                </div>


                <!-- Feed Video -->
                <section class="section-feed-video">
                    <?php $video_url = get_post_meta(get_the_ID(), 'feed_video', true); ?>
                    <?php if ($video_url) { ?>
                        <video controls style="width: 100%;">
                            <source src="<?php echo $video_url; ?>" type="video/mp4">
                        </video>
                    <?php } ?>
                </section>

                <hr class="single_photoset_seprator">

                <section class="feed_list_section">
                    <div class="feed_list_wrapper">
                        <div class="more_modal_content">
                            <div class="more_film_title_wrap">
                                <p>PREVIOUSLY:</p>
                            </div>
                        </div>
                        <div class="feed_list">
                            <?php
                            $current_post_id = get_the_ID();

                            $args = array(
                                'post_type' => 'daily-feed',
                                'posts_per_page' => 12,
                                'post__not_in' => array($current_post_id),
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
                </section>
            <?php endwhile; ?>
        </div>
    </main>
</div>

<?php get_footer(); ?>