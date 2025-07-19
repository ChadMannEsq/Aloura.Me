<?php
/*
    * Template Name: Secret Diary Page Template
    * Description: Template for displaying the diary page with selected posts.
    */
get_header(); ?>

<section class="common_header secret_dairy_header">
    <h1 class="header_title"><?php the_title(); ?></h1>
    <span class="header_text">Excepteur sint occaecat cupidatat</span>
</section>

<section class="diary_page_content">
    <div>
        <div class="diary_list">
            <?php
            $args = array(
                'post_type' => 'diary',
                'posts_per_page' => -1,
            );
            $diary_query = new WP_Query($args);

            if ($diary_query->have_posts()) {
                while ($diary_query->have_posts()) {
                    $diary_query->the_post();

                    $title = get_the_title();
                    $content = get_the_content();
                    $limited_content = wp_trim_words($content, 69, '...');
                    $date = get_the_date('l d F, Y');
                    $permalink = get_permalink();
                    $featured_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>
                    <div class="diary_item">
                        <div class="diary_item_content">
                            <a href="<?php echo $permalink; ?>">
                                <img class="dairy_img" src="<?php echo $featured_image_url; ?>" />
                                <h2 class="dairy_title"><?php echo $title; ?></h2>
                                <h4 class="diary_stream_date"><?php echo $date; ?></h4>
                                <p class="dairy_text"><?php echo $limited_content; ?></p>
                            </a>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo '<p>No diary found.</p>';
            }
            wp_reset_postdata();
            ?>
        </div>
    </div>
</section>


<?php get_footer(); ?>