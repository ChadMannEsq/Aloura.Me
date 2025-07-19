<?php

/**
 * The template for displaying tag pages
 *
 * @package Astra Child
 */
get_header(); ?>

<?php
// Modify the query to include custom post types
$tag = get_queried_object();
$args = array(
    'post_type' => array('post', 'film', 'photoset', 'bts'), // Include custom post types
    'tag' => $tag->slug,
    'posts_per_page' => -1 // Adjust this as needed
);
$custom_query = new WP_Query($args);

if ($custom_query->have_posts()) : ?>
    <div class="tag_page_header">
        <section class="common_header">
            <h1 class="header_title">
                <?php
                /* translators: %s: Tag name. */
                printf(esc_html__('Tag: #%s', 'astra-child'), single_tag_title('', false));
                ?>
            </h1>
            <?php the_archive_description('<div class="archive-description">', '</div>'); ?>
        </section><!-- .page-header -->
    </div>

    <section class="tag_post_list">
        <?php
        /* Start the Loop */
        while ($custom_query->have_posts()) :
            $custom_query->the_post();
            $post_type = get_post_type(); // Get the post type
            $featured_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
            $date = get_the_date('d M Y');
        ?>

            <div class="tag_post_item">
                <div class="tag_post_image">
                    <img src="<?php echo $featured_image_url; ?>" alt="">
                </div>
                <div class="tag_post_content_wrapper">
                    <div class="tag_post_content">

                        <p class="post_type_wrap">
                            <span class="post_type">
                                <?php echo esc_html($post_type); ?>
                            </span>
                        </p>
                        <div>
                            <h2 class="post_title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
                            <hr class="post_title_border">
                        </div>
                        <div>
                            <p class="post_date"><img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/date-icon.png" /> <?php echo $date; ?></p>

                        </div>
                    </div>
                </div>

            </div><!-- #post-## -->
        <?php
        endwhile;

        the_posts_navigation();

        // Reset post data
        wp_reset_postdata();
        ?>
    </section>
<?php
else :

    get_template_part('template-parts/content', 'none');

endif;
?>
<div class="tag_pagination">
    <div class="tag_pagination_content">1</div>
    <div class="tag_pagination_content">2</div>
    <div class="tag_pagination_content">3</div>
    <div class="tag_pagination_content">...</div>
    <div class="tag_pagination_content">next</div>
</div>
<?php
get_footer();
