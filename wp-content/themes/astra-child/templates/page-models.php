<?php
/*
 * Template Name: Models Page Template
 * Description: Template for displaying the models page with selected posts.
 */
get_header(); ?>

<section class="common_header">
    <h1 class="header_title"><?php the_title(); ?></h1>
    <p class="header_text">Excepteur sint occaecat cupidatat</p>
</section>

<section class="model_list_section">
    <div class="model_list">
        <?php
        $terms = get_terms(array(
            'taxonomy' => 'model',
            'hide_empty' => false,
        ));

        $fallback_image_url = get_stylesheet_directory_uri() . '/assets/images/noimg.png'; // URL for fallback image
        
        if (!empty($terms) && !is_wp_error($terms)) { ?>
            <?php foreach ($terms as $term) { ?>
                <?php
                $image_id = get_term_meta($term->term_id, 'model-image-id', true);
                $image_url = wp_get_attachment_image_src($image_id, 'full');
                $term_link = get_term_link($term);

                // Use fallback image if no image URL is set
                $background_image_url = !empty($image_url[0]) ? $image_url[0] : $fallback_image_url;
                ?>
                <div class="model_item">
                    <div class="model_item_content"
                        style="background-image: url('<?php echo esc_url($background_image_url); ?>');">
                        <a href="<?php echo esc_url($term_link); ?>">
                            <h4 class="modals_name"><?php echo esc_html($term->name); ?></h4>
                        </a>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>

    </div>
</section>

<?php get_footer(); ?>