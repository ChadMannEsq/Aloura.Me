<?php

// Add the meta box
function home_page_selected_posts_meta_box() {
    add_meta_box(
        'home_page_selected_posts_meta_box', // ID
        'Select Posts', // Title
        'render_home_page_selected_posts_meta_box', // Callback
        'page', // Post type
        'normal', // Context
        'high' // Priority
    );
}
add_action('add_meta_boxes', 'home_page_selected_posts_meta_box');

// Render the meta box
function render_home_page_selected_posts_meta_box($post) {
    // Retrieve existing selected posts
    $selected_posts = get_post_meta($post->ID, 'home_page_selected_posts', true);

    // WordPress nonce for verification
    wp_nonce_field('home_page_selected_posts_nonce', 'home_page_selected_posts_nonce');

    echo '<label for="home_page_selected_posts">Select Posts:</label>';
    echo '<ul id="home_page_selected_posts_container" class="sortable">';
    
    if ($selected_posts) {
        foreach ($selected_posts as $index => $selected_post) {
            $post_title = get_the_title($selected_post['post_id']);
            $post_type_label = $selected_post['post_type'] === "film" ? "Film" : "Photoset";
            echo '<li>';
            echo '<input type="hidden" name="home_page_selected_posts[' . $index . '][post_id]" value="' . esc_attr($selected_post['post_id']) . '" />';
            echo '<input type="hidden" name="home_page_selected_posts[' . $index . '][post_type]" value="' . esc_attr($selected_post['post_type']) . '" />';
            echo '<strong>' . esc_html($post_title) . '</strong> - ' . esc_html($post_type_label);
            echo ' <a href="#" class="remove-selected-post">Remove</a>';
            echo '</li>';
        }
    }
    
    echo '</ul>';
    echo '<input type="hidden" id="home_page_selected_posts" name="home_page_selected_posts" value="' . esc_attr(json_encode($selected_posts)) . '" />';
    echo '<input type="button" id="select_posts_button" class="button" value="Select Posts" />';
    echo '<div id="custom-posts-modal" style="display: none;">';
    echo '<h2>Select Posts</h2>';
    echo '<ul id="custom-posts-list">';
    // Placeholder for posts fetched via AJAX
    echo '</ul>';
    echo '<button id="save_custom_posts_modal" class="button button-primary">Save</button>';
    echo '<button id="close_custom_posts_modal" class="button">Close</button>';
    echo '</div>';
    echo '<script>
            jQuery(document).ready(function($) {
                var selectedPostsData = ' . json_encode($selected_posts) . ';

                $("#select_posts_button").on("click", function(e) {
                    e.preventDefault();
                    $("#custom-posts-modal").fadeIn();
                    loadCustomPosts();
                });

                $("#close_custom_posts_modal").on("click", function(e) {
                    e.preventDefault();
                    $("#custom-posts-modal").fadeOut();
                });

                $("#save_custom_posts_modal").on("click", function(e) {
                    e.preventDefault();
                    var selectedPostIds = [];
                    $("#custom-posts-list input[type=\'checkbox\']:checked").each(function() {
                        selectedPostIds.push({
                            post_id: $(this).val(),
                            post_type: $(this).data("post-type"),
                            post_title: $(this).data("post-title")
                        });
                    });
                    updateSelectedPosts(selectedPostIds);
                    $("#custom-posts-modal").fadeOut();
                });

                function loadCustomPosts() {
                    $.ajax({
                        url: ajaxurl, // WordPress AJAX URL
                        type: "POST",
                        data: {
                            action: "get_custom_posts"
                        },
                        success: function(response) {
                            $("#custom-posts-list").html(response);
                            // Check previously selected posts
                            selectedPostsData.forEach(function(post) {
                                $("#custom-posts-list input[type=\'checkbox\'][value=\'" + post.post_id + "\']").prop("checked", true);
                            });
                        }
                    });
                }

                function updateSelectedPosts(selectedPosts) {
                    var container = $("#home_page_selected_posts_container");
                    container.empty();
                    var hiddenField = $("#home_page_selected_posts");
                    selectedPostsData = selectedPosts; // Update global variable

                    selectedPosts.forEach(function(post, index) {
                        var postId = post.post_id;
                        var postType = post.post_type;
                        var postTitle = post.post_title;
                        var postTypeLabel = postType === "film" ? "Film" : "Photoset";

                        container.append("<li><input type=\'hidden\' name=\'home_page_selected_posts[" + index + "][post_id]\' value=\'" + postId + "\'><input type=\'hidden\' name=\'home_page_selected_posts[" + index + "][post_type]\' value=\'" + postType + "\'><strong>" + postTitle + "</strong> - " + postTypeLabel + " <a href=\'#\' class=\'remove-selected-post\'>Remove</a></li>");
                    });

                    hiddenField.val(JSON.stringify(selectedPostsData));
                }

                $("#home_page_selected_posts_container").on("click", ".remove-selected-post", function(e) {
                    e.preventDefault();
                    $(this).parent().remove();
                    updateHiddenField();
                });

                // Ensure hidden field is properly set before form submission
                function updateHiddenField() {
                    var selectedPosts = [];
                    $("#home_page_selected_posts_container li").each(function() {
                        selectedPosts.push({
                            post_id: $(this).find("input[name*=\'post_id\']").val(),
                            post_type: $(this).find("input[name*=\'post_type\']").val()
                        });
                    });
                    $("#home_page_selected_posts").val(JSON.stringify(selectedPosts));
                }

                // Ensure hidden field is properly set before form submission
                $("#post").on("submit", function() {
                    updateHiddenField();
                });

                // Make list sortable
                $("#home_page_selected_posts_container").sortable({
                    update: function(event, ui) {
                        updateHiddenField();
                    }
                });
            });
          </script>';
}

// AJAX handler to fetch custom posts
add_action('wp_ajax_get_custom_posts', 'get_custom_posts_callback');
function get_custom_posts_callback() {
    $args = array(
        'post_type' => array('film', 'photoset'),
        'posts_per_page' => -1,
    );

    $posts = get_posts($args);

    if ($posts) {
        foreach ($posts as $post) {
            echo '<li><label><input type="checkbox" name="selected_custom_posts[]" value="' . esc_attr($post->ID) . '" data-post-type="' . esc_attr($post->post_type) . '" data-post-title="' . esc_attr($post->post_title) . '"> ' . esc_html($post->post_title) . ' - ' . esc_html($post->post_type) . '</label></li>';
        }
    } else {
        echo '<li>No posts found.</li>';
    }

    wp_die();
}

// Save the selected posts
function save_home_page_selected_posts_meta_box($post_id) {
    // Check nonce
    if (!isset($_POST['home_page_selected_posts_nonce']) || !wp_verify_nonce($_POST['home_page_selected_posts_nonce'], 'home_page_selected_posts_nonce')) {
        return;
    }

    // Check if this is an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save selected posts
    if (isset($_POST['home_page_selected_posts'])) {
        $selected_posts = json_decode(stripslashes($_POST['home_page_selected_posts']), true);
        update_post_meta($post_id, 'home_page_selected_posts', $selected_posts);
    } else {
        delete_post_meta($post_id, 'home_page_selected_posts');
    }
}
add_action('save_post', 'save_home_page_selected_posts_meta_box');

// Enqueue scripts
add_action('admin_enqueue_scripts', 'enqueue_custom_scripts');
function enqueue_custom_scripts($hook) {
    global $post;
    if ($post && $post->post_type === 'page') {
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-sortable');
    }
}

