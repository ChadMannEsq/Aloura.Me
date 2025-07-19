<?php get_header(); ?>


<section class="common_header">
    <span class="header_text_bold"><?php echo get_the_date('d M Y'); ?></span>
    <h1 class="header_title"><?php the_title(); ?></h1>
</section>
<div class="photoset_gallery_section">
    <section>
        <div class="singlepage_content_wrap single_dairy_wrapper">
            <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>" alt="">

            <div class="single_dairy_details">
                <?php the_content(); ?>
            </div>
        </div>
    </section>

    <section class="next_posts_section">
        <div class="more_modal_content">
            <div class="next_posts_heading more_film_title_wrap">
                <p>KEEP READING</p>
                <button class="more_films_button">
                    <a href="<?php echo home_url(); ?>/secret-diary/">See all  <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/right-symbol.png" /></a>
                </button>
            </div>
        </div>
        <div class="next_post_list">
            <?php
            // Get the current post ID and all post IDs in ascending order
            $current_post_id = get_the_ID();
            $all_posts = get_posts(array(
                'post_type'      => 'diary',
                'posts_per_page' => -1,
                'orderby'        => 'date',
                'order'          => 'ASC',
                'fields'         => 'ids',
            ));
            $total_posts = count($all_posts);
            $current_post_index = array_search($current_post_id, $all_posts);

            // Determine the next posts to display
            $next_posts = array();
            if ($current_post_index == $total_posts - 2) {
                $next_posts = array($all_posts[$total_posts - 1], $all_posts[0]);
            } elseif ($current_post_index == $total_posts - 1) {
                $next_posts = array_slice($all_posts, 0, 2);
            } else {
                $next_posts = array_slice($all_posts, $current_post_index + 1, 2);
                if (count($next_posts) < 2) {
                    $next_posts[] = $all_posts[0];
                }
            }

            // Display the next posts
            foreach ($next_posts as $post_id) :
                $post = get_post($post_id);
                setup_postdata($post); ?>

                    <div class="next_post_item">
                        <?php if (has_post_thumbnail($post_id)) : ?>
                            <div class="next_post_thumbnail">
                                <a href="<?php echo get_permalink($post_id); ?>">
                                    <?php echo get_the_post_thumbnail($post_id, 'thumbnail'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="next_post_content">
                            <p class="next_post_title">
                                <a href="<?php echo get_permalink($post_id); ?>"><?php echo get_the_title($post_id); ?></a>
                            </p>
                            <p class="next_post_date"><?php echo get_the_date('', $post_id); ?></p>
                        </div>
                    </div>
            <?php endforeach;
            wp_reset_postdata(); ?>
        </div>
 
    </section>
</div>

<?php get_footer(); ?>