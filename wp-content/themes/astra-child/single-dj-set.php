<?php get_header(); ?>

<section class="common_header">
    <h1 class="header_title">DJ Live Stream</h1>
    <p class="dj_header_title"><?php echo get_the_date('l d F, Y'); ?></p>
</section>
<section class="single_dj_video_wrapper">
    <div class="video_wrap">
        <p class="page_preview">Preview</p>
        <div class="dj_video">
            <?php $video_url = get_post_meta(get_the_ID(), 'dj_video', true); ?>
            <?php if ($video_url) { ?>
                <video controls style="width: 100%;">
                    <source src="<?php echo $video_url; ?>" type="video/mp4">
                </video>
            <?php } ?>
        </div>
    </div>
</section>


<hr class="single_photoset_seprator">
<section class="dj_page_content">
    <div class="dj_list">
        <?php
        $current_post_id = get_the_ID();

        $args = array(
            'post_type' => 'dj-set',
            'posts_per_page' => 6,
            'post__not_in' => array($current_post_id),
        );
        $dj_query = new WP_Query($args);

        if ($dj_query->have_posts()) {
            while ($dj_query->have_posts()) {
                $dj_query->the_post();
                $date = get_the_date('l d F, Y');
                $permalink = get_permalink();
                $featured_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
        ?>
                <div class="dj_item">
                    <div class="dj_item_content">
                        <a href="<?php echo esc_url($permalink); ?>">
                            <img src="<?php echo esc_url($featured_image_url); ?>" />
                            <h4 class="dj_stream_date"><?php echo esc_html($date); ?></h4>
                        </a>
                    </div>
                </div>
        <?php
            }
        } else {
            echo '<p>No dj found.</p>';
        }
        wp_reset_postdata();
        ?>
    </div>
</section>


<?php get_footer(); ?>