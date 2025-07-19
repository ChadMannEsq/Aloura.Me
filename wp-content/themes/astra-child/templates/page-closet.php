<?php
/*
    * Template Name: Closet Page Template
    * Description: Template for displaying the closet page with selected posts.
    */
get_header(); ?>

<section class="common_header closet_header ">
    <h1 class="header_title"><?php the_title(); ?></h1>
    <?php
    $args = array(
        'post_type' => 'my-closet',
        'posts_per_page' => 1,
    );
    $closet_query = new WP_Query($args);

    if ($closet_query->have_posts()) {
        while ($closet_query->have_posts()) {
            $closet_query->the_post();
            $date = get_the_date('l d F, Y');
            $video_url = get_post_meta(get_the_ID(), 'closet_video', true);
    ?>
            <p class="closet_header_title header_text"><?php echo esc_html($date); ?></p>
    <?php
        }
    } else {
        echo '<p>No closet found.</p>';
    }
    wp_reset_postdata();
    ?>
</section>

<section class="single_closet_video_wrapper">
    <div class="video_wrap">
        <p class="page_preview">Preview</p>
        <div class="closet_video">
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
            $args = array(
                'post_type' => 'my-closet',
                'posts_per_page' => -1,
            );
            $closet_query = new WP_Query($args);

            if ($closet_query->have_posts()) {
                $first_post = true;
                while ($closet_query->have_posts()) {
                    $closet_query->the_post();
                    if ($first_post) {
                        $first_post = false; // Skip the first post
                        continue;
                    }
                    $date = get_the_date('l d F, Y');
                    $permalink = get_permalink();
                    $featured_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>
                    <div class="closet_item closet_single_page">
                        <div class="closet_item_content">
                            <a href="<?php echo $permalink; ?>">
                                <img class="closet_img" src="<?php echo $featured_image_url; ?>" />
                                <h4 class="signle_closet_date"><?php echo $date; ?></h4>
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