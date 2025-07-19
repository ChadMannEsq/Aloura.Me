<?php
get_header(); ?>

<!-- <div id="primary" class="content-area custom_content_area"> -->
<!-- <main id="main" class="site-main"> -->
<?php
$term = get_queried_object();
$matching_user_email = '';

$users = get_users();
foreach ($users as $user) {
    // Fetch user type meta data
    $user_type = get_user_meta($user->ID, 'user_registration_user_type', true);
    $user_type = strtolower($user_type); // Convert to lowercase if needed

    // Check if the user type is "model" and if the display name matches the term name
    if ($user_type === 'model' && $user->display_name === $term->name) {
        $matching_user_email = $user->user_email; // Store the matching user's email
        break; // Stop the loop once a match is found
    }
}

if ($term): ?>
    <header class="model_header common_header">
        <h4 class="modals_title">Model</h4>
        <h1 class="header_title"><?php echo esc_html($term->name); ?></h1>
        <div class="social_details">
            <a class="social-inner" href="#">
                <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/website-icon.png" />
                <span class="header_text">Website</span>
            </a>
            <a class="social-inner" href="#">
                <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/instagram-icon.png" />
                <span class="header_text">Instagram</span>
            </a>
            <?php if (is_user_logged_in()): ?>
                <?php
                $current_user = wp_get_current_user();
                ?>
                <a class="social-inner"
                    href="https://putri-chat.hupp.in/conversation?email=<?php echo esc_html($matching_user_email); ?>">
                    <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/chat-icon.png" />
                    <span class="header_text">Chat with me</span>
                </a>
            <?php endif; ?>
        </div>
    </header><!-- .page-header -->
    <?php
    $image_id = get_term_meta($term->term_id, 'model-image-id', true);
    $image_url = wp_get_attachment_image_src($image_id, 'full');
    ?>
    <section class="models_list_page_wrap">
        <div class="model_image">
            <?php
            // Define the fallback image URL
            $fallback_image_url = get_stylesheet_directory_uri() . '/assets/images/noimg.png';
            ?>

            <?php if (!empty($image_url[0])): ?>
                <img src="<?php echo htmlspecialchars($image_url[0], ENT_QUOTES, 'UTF-8'); ?>" />
            <?php else: ?>
                <img src="<?php echo htmlspecialchars($fallback_image_url, ENT_QUOTES, 'UTF-8'); ?>" class=""/>
            <?php endif; ?>
        </div>
        <div class="model_description film-video-content"><?php echo term_description($term); ?></div>

        <div class="taxonomy-posts">
            <div class="more_modal_content taxonomy_title">
                <p>LATEST UPDATES:</p>
            </div>
            <div class="explor_films_list">
                <div class="films_list">
                    <?php
                    $args = array(
                        'post_type' => array('film', 'photoset'),
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'model',
                                'field' => 'term_id',
                                'terms' => $term->term_id,
                            ),
                        ),
                    );
                    $bts_query = new WP_Query($args);

                    if ($bts_query->have_posts()) {
                        while ($bts_query->have_posts()) {
                            $bts_query->the_post();
                            $title = get_the_title();
                            $permalink = get_permalink();
                            $featured_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                            $post_type = get_post_type();
                            $terms = wp_get_post_terms(get_the_ID(), 'vendor');
                            $bts_vendor = '';

                            if (!is_wp_error($terms) && !empty($terms)) {
                                $term_names = wp_list_pluck($terms, 'name');
                                $bts_vendor = implode(', ', $term_names);
                            }
                            ?>
                            <div class="film_item">
                                <a href="<?php echo esc_url($permalink); ?>">
                                    <div class="film_item_content"
                                        style="background-image: url('<?php echo esc_url($featured_image_url); ?>');">
                                        <div class="vendor_details_wrapper">
                                            <p class="film_vendor_name">WITH <?php echo esc_html($bts_vendor); ?></p>
                                            <h4 class="vendor_title"><?php echo esc_html($title); ?> (<?php echo $post_type; ?>)
                                            </h4>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<p>No update found.</p>';
                    }
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <p><?php _e('No term found.', 'your-theme-textdomain'); ?></p>
    <?php endif; ?>
</section>

<section class="single_page_wrap">
    <div class="more_modal_content">
        <div class="more_film_title_wrap">
            <p>MORE MODELS:</p>
            <button class="more_films_button"><a href="#">SEE MORE <img
                        src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/right-symbol.png" /></a></button>
        </div>
    </div>
    <div class="model_list">
        <?php
        $current_term = get_queried_object();

        $all_terms = get_terms(array(
            'taxonomy' => 'model',
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => false,
        ));

        // Get the current term index
        $total_terms = count($all_terms);
        $current_term_index = array_search($current_term->term_id, wp_list_pluck($all_terms, 'term_id'));

        // Initialize next terms array
        $next_terms = array();

        if ($current_term_index == $total_terms - 3) {
            // If it's the third last term, get the last two and the first term
            $next_terms = array($all_terms[$total_terms - 2], $all_terms[$total_terms - 1], $all_terms[0]);
        } elseif ($current_term_index == $total_terms - 2) {
            // If it's the second last term, get the last and the first two terms
            $next_terms = array($all_terms[$total_terms - 1], $all_terms[0], $all_terms[1]);
        } elseif ($current_term_index == $total_terms - 1) {
            // If it's the last term, get the first three terms
            $next_terms = array_slice($all_terms, 0, 3);
        } else {
            // Otherwise, get the next 3 terms
            $next_terms = array_slice($all_terms, $current_term_index + 1, 3);
            if (count($next_terms) < 3) {
                $next_terms = array_merge($next_terms, array_slice($all_terms, 0, 3 - count($next_terms)));
            }
        }



        if (!empty($next_terms) && !is_wp_error($next_terms)) { ?>
            <?php foreach ($next_terms as $term) { ?>
                <?php
                $image_id = get_term_meta($term->term_id, 'model-image-id', true);
                $image_url = wp_get_attachment_image_src($image_id, 'full');
                $term_link = get_term_link($term);
                ?>
                <div class="model_item">
                    <div class="model_item_content" style="background-image: url('<?php echo $image_url[0]; ?>');">
                        <a href="<?php echo esc_url($term_link); ?>">
                            <h4 class="modals_name"><?php echo esc_html($term->name); ?></h4>
                        </a>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
</section>
<!-- </main> -->
<!-- </div> -->

<?php
get_footer();
