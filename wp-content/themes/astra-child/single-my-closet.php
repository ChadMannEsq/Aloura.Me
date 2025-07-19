<?php get_header(); ?>

<section class="common_header closet_header">
    <h1 class="header_title">My Closet</h1>
    <p class="header_text"><?php echo get_the_date('l d F, Y'); ?></p>
</section>
<section class="single_closet_video_wrapper">
    <div class="video_wrap">
        <p class="page_preview">Preview</p>
        <div class="closet_video">
            <?php $video_url = get_post_meta(get_the_ID(), 'closet_video', true); ?>
            <?php if ($video_url) { ?>
                <video controls style="width: 100%;">
                    <source src="<?php echo $video_url; ?>" type="video/mp4">
                </video>
            <?php } ?>
        </div>
    </div>
</section>


<hr class="single_photoset_seprator">
<section class="closet_page_content">
    <div class="closet_wrap">
        <div class="closet_list">
            <?php
            $current_post_id = get_the_ID();

            $args = array(
                'post_type' => 'my-closet',
                'posts_per_page' => 6,
                'post__not_in' => array($current_post_id),
            );
            $closet_query = new WP_Query($args);

            if ($closet_query->have_posts()) {
                while ($closet_query->have_posts()) {
                    $closet_query->the_post();
                    $date = get_the_date('l d F, Y');
                    $permalink = get_permalink();
                    $featured_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
            ?>
                    <div class="closet_item">
                        <div class="closet_item_content">
                            <a href="<?php echo esc_url($permalink); ?>">
                                <img class="closet_img" src="<?php echo esc_url($featured_image_url); ?>" />
                                <h4 class="signle_closet_date"><?php echo esc_html($date); ?></h4>
                            </a>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo '<p>No closet found.</p>';
            }
            wp_reset_postdata();
            ?>
        </div>
    </div>
</section>


<?php get_footer(); ?>