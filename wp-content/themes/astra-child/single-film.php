<?php get_header(); ?>

<div class="content-area">
    <main class="site-main">
        <div class="single-page-container">
            <?php while (have_posts()) :
                the_post(); ?>
                <section class="section-film-heading common_header">
                    <h1 class="header_title"><?php the_title(); ?></h1>
                    <div class="film-date-content single_page_header_text">
                        <!-- Date -->
                        <span class="film-date-border"><?php echo get_the_date('d M Y'); ?></span>
                        <!-- custom taxonomy "vendor" -->
                        <?php $vendor_terms = get_the_term_list(get_the_ID(), 'vendor', 'with ', ', ', ''); ?> &nbsp; |
                        &nbsp; <span class="single_page_header_text"><?php echo $vendor_terms; ?> </span>
                    </div>
                </section>
                <div class="singlepage_content_wrap">
                    <!-- Film Video -->
                    <section class="section-film-video">
                        <?php $video_url = get_post_meta(get_the_ID(), 'model_film_video', true); ?>
                        <?php if ($video_url) { ?>
                            <div class="video_wrap">
                                <p class="page_preview">Preview</p>
                                <div class="video-container">
                                    <video class="preview_video" controls>
                                        <source src="<?php echo $video_url; ?>" type="video/mp4">
                                    </video>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="film-video-content">
                            <?php the_content(); ?>
                        </div>
                    </section>
                    <!-- custom taxonomy "model" -->
                    <section class="film-feature-model">

                        <div class="more_modal_content">
                            <p>FEATURED MODEL:</p>
                        </div>
                        <div class="film-model-inner featured_model_inner">
                            <?php
                            $terms = get_the_terms($post->ID, 'model');
                            if (!empty($terms) && !is_wp_error($terms)) {
                                foreach ($terms as $term) {
                                    $image_id = get_term_meta($term->term_id, 'model-image-id', true);
                                    if ($image_id) {
                                        $image_url = wp_get_attachment_image_src($image_id, 'full');
                                        if (!empty($image_url)) {
                                            ?>
                                                <div class="model-image">
                                                    <a href="<?php echo get_term_link($term); ?>">
                                                        <img src="<?php echo $image_url[0]; ?>" />
                                                        <span class="model-name">
                                                            <?php echo $term->name; ?>
                                                        </span>
                                                    </a>
                                                </div>
                                            <?php
                                        }
                                    }
                                }
                            }
                            ?>
                        </div>
                    </section>
                                      
                    <section class="film-feature-model">
                        <div class="more_modal_content">
                            <div class="more_film_title_wrap">
                                <p>MORE FILMS:</p>
                                <button class="more_films_button"><a href="<?php echo home_url(); ?>/films">SEE ALL <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/right-symbol.png" /></a></button>
                            </div>
                        </div>
                        <div class="film-model-inner related_model_inner">
                            <?php
                            // Get the current post ID and all post IDs in ascending order
                            $current_post_id = get_the_ID();
                            $all_posts = get_posts(array(
                                'post_type'      => 'film',
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
                                $terms = wp_get_post_terms($post_id, 'vendor');
                                $film_vendor = '';
                    
                                if (!is_wp_error($terms) && !empty($terms)) {
                                    $term_names = wp_list_pluck($terms, 'name');
                                    $film_vendor = implode(', ', $term_names);
                                }
                                setup_postdata($post); ?>
                                    
                                    <div class="more_films_image_wrap">
                                        <?php if (has_post_thumbnail($post_id)) : ?>
                                            <a href="<?php echo get_permalink($post_id); ?>">
                                                <?php echo get_the_post_thumbnail($post_id, 'full'); ?>
                                            </a>
                                        <?php endif; ?>
                                        <div class="more_film_details">
                                            <a class="cusrsor-pointer" href="<?php echo get_permalink($post_id); ?>">
                                                <p class="film_vendor_name">With <?php echo $film_vendor; ?></p>
                                                <h4 class="vendor_title"><?php echo get_the_title($post_id); ?></h4>
                                            </a>
                                        </div>
                                    </div>
                            <?php endforeach;
                            wp_reset_postdata(); ?>
                        </div>
                    </section>
                </div>
            <?php endwhile; ?>
        </div>
    </main>
</div>

<?php get_footer(); ?>