<?php
/*
    * Template Name: BTS Page Template
    * Description: Template for displaying the bts page with selected posts.
    */
get_header(); ?>

<section class="common_header">
    <h1 class="header_title"><?php the_title(); ?></h1>
    <p class="header_text">Excepteur sint occaecat cupidatat</p>
</section>
<button class="popup_open_btn">Filter</button>

<section class="list_page_content">
    <div class="filter_container">
        <div class="filter_wrap">
            <button class="popup_close_btn">X</button>
            <div class="search-container">
                <form action="/action_page.php">
                    <input type="text" placeholder="Search.." name="search">
                </form>
                <hr class="separator">
            </div>
            <div class="bts_tags_list  tag_list_style">
                <?php
                // Get all tags associated with the 'bts' post type
                $tags = get_terms(array(
                    'taxonomy' => 'post_tag',
                    'object_ids' => get_posts(array(
                        'post_type' => 'bts',
                        'posts_per_page' => -1, // Get all posts
                        'fields' => 'ids', // Only get post IDs
                    )),
                ));

                if (!empty($tags) && !is_wp_error($tags)) {
                    echo '<ul class="remove_list_style">';
                    foreach ($tags as $tag) {
                        $tag_link = get_tag_link($tag->term_id);
                        echo '<li># <a href="' . esc_url($tag_link) . '">' . esc_html($tag->name) . '</a></li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<p>No tags found.</p>';
                }
                ?>
            </div>
            <!-- <hr class="separator"> -->
            <hr class="separator">
            <div class="bts_models_list  tag_list_style">
                <?php
                // Get all terms from the 'model' taxonomy associated with the 'bts' post type
                $models = get_terms(array(
                    'taxonomy' => 'model',
                    'object_ids' => get_posts(array(
                        'post_type' => 'bts',
                        'posts_per_page' => -1, // Get all posts
                        'fields' => 'ids', // Only get post IDs
                    )),
                ));

                if (!empty($models) && !is_wp_error($models)) {
                    echo '<ul class="remove_list_style">';
                    foreach ($models as $model) {
                        $model_link = get_term_link($model->term_id, 'model');
                        echo '<li># <a href="' . esc_url($model_link) . '">' . esc_html($model->name) . '</a></li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<p>No models found.</p>';
                }
                ?>
            </div>
        </div>
    </div>
    <div class="list_container">
        <div>
            <div class="films_list" id="ajax-posts">
              
            </div>
        </div>
        <div class="load_more_btn">
            <button id="more_posts" data-type="bts" class="load_more_text">LOAD MORE <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/right-symbol.png" /></a>
        </div>
    </div>

</section>
<script>
    load_posts('bts');
</script>


<?php get_footer(); ?>