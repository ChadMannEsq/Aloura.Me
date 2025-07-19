<?php get_header(); ?>

<div class="content-area">
    <main class="site-main">
        <div class="single-page-container">
            <?php while (have_posts()) :
                the_post(); ?>
                <section class="section-bts-heading common_header">
                <span class="header_text_bold">BEHIND THE SCENES</span>
                    <h1 class="header_title"><?php the_title(); ?></h1>
                    <div class="bts-date-content single_page_header_text">
                      
                        <!-- custom taxonomy "vendor" -->
                        <?php $vendor_terms = get_the_term_list(get_the_ID(), 'vendor', 'with ', ', ', ''); ?>  
                        <span class="header_text_bold"> <?php echo $vendor_terms; ?> </span>&nbsp;| &nbsp;
                          <!-- Date -->
                          <span class="header_text_bold film-date-border"> <?php echo get_the_date('d M Y'); ?></span>
                    </div>
                </section>

               
                <div class="singlepage_content_wrap">
                    <section class="section-film-video">
                        <?php $video_url = get_post_meta(get_the_ID(), 'bts_video', true); ?>
                        <div class="video_wrap">
                            <?php if ($video_url) { ?>
                                <p class="page_preview">Preview</p>
                                <video controls style="width: 100%;">
                                    <source src="<?php echo $video_url; ?>" type="video/mp4">
                                </video>
                            <?php } ?>
                        </div>
                        <div class="film-video-content">
                            <?php the_content(); ?>
                        </div>
                    </section>
                    <!-- custom taxonomy "model" -->
                    <section class="film-feature-model">

                        <div class="more_modal_content">
                            <p>FEATURED MODEL:</p>
                        </div>
                        <div class="bts-model-inner featured_model_inner">
                            <?php
                            $terms = get_the_terms($post->ID, 'model');
                            if (!empty($terms) && !is_wp_error($terms)) {
                                foreach ($terms as $term) {
                                    $image_id = get_term_meta($term->term_id, 'model-image-id', true);
                                    if ($image_id) {
                                        $image_url = wp_get_attachment_image_src($image_id, 'full');
                                        if (!empty($image_url)) {
                                            echo '<div class="model-image">';
                                            echo '<img src="' . esc_url($image_url[0]) . '" alt="' . esc_attr($term->name) . '" />';
                                            echo '<div class="model-name">';
                                            echo '<a href="' . esc_url(get_term_link($term)) . '">' . esc_html($term->name) . '</a>';
                                            echo '</div>';
                                            echo '</div>';
                                        }
                                    }
                                }
                            }
                            ?>
                        </div>
                    </section>
                    <section class="film-feature-model">
                        <div class="more_modal_content">
                            <p> RELATED:</p>
                        </div>
                        <div class="film-model-inner related_model_inner">
                            <div class="image_wrap">
                                <a href="#">
                                    <img src="wp-content/themes/astra/inc/assets/images/starter-content/branding.jpg" alt="">
                                    <div class="related_details">
                                        <p>Lorem Ipsum (BTS FILM)</p>
                                    </div>
                                </a>
                            </div>
                            <div class="image_wrap">
                                <a href="#">
                                    <img src="wp-content/themes/astra/inc/assets/images/starter-content/branding.jpg" alt="">
                                    <div class="related_details">
                                        <p>Lorem Ipsum (BTS FILM)</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </section>
	                <hr class="single_photoset_seprator">
                    <section class="film-feature-model">
                        <div class="more_modal_content">
                            <div class="more_film_title_wrap">
                                <p>MORE BTS:</p>
                                <button class="more_films_button"><a href="#">SEE ALL <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/right-symbol.png" /></a></button>
                            </div>
                        </div>
                        <div class="film-model-inner related_model_inner">
                            <div class="more_films_image_wrap">
                                <a class="cusrsor-pointer" href="#">
                                    <img src="wp-content/themes/astra/inc/assets/images/starter-content/branding.jpg" alt="">
                                    <div class="more_film_details">
                                        <p class="film_vendor_name">With Aloura.Me</p>
                                        <h4 class="vendor_title">lOREM iPSUM (FILM)</h4>
                                    </div>
                                </a>
                            </div>
                            <div class="more_films_image_wrap">
                                <a class="cusrsor-pointer" href="#">
                                    <img src="wp-content/themes/astra/inc/assets/images/starter-content/branding.jpg" alt="">
                                    <div class="more_film_details">
                                        <p class="film_vendor_name">With Aloura.Me</p>
                                        <h4 class="vendor_title">lOREM iPSUM (FILM)</h4>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </section>
                </div>
            <?php endwhile; ?>
        </div>
    </main>
</div>

<?php get_footer(); ?>