<?php


function add_photoset_meta_boxes() {
    add_meta_box(
        'photoset_images',
        'Photoset Images',
        'photoset_images_callback',
        'photoset',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_photoset_meta_boxes');

function photoset_images_callback($post) {
    wp_nonce_field(basename(__FILE__), 'photoset_nonce');
    $photoset_images = get_post_meta($post->ID, 'photoset_images', true);
    ?>
    <div>
        <a class="button" id="photoset_images_button"><?php _e('Add Images', 'textdomain'); ?></a>
        <ul id="photoset_images_list">
            <?php if (!empty($photoset_images)) {
                foreach ($photoset_images as $image) {
                    echo '<li><img src="' . esc_url(wp_get_attachment_url($image)) . '" width="100" /><input type="hidden" name="photoset_images[]" value="' . esc_attr($image) . '"><a href="#" class="remove-image">Remove</a></li>';
                }
            } ?>
        </ul>
    </div>
    <script>
        jQuery(document).ready(function($){
            var frame;
            $('#photoset_images_button').on('click', function(e) {
                e.preventDefault();
                if (frame) {
                    frame.open();
                    return;
                }
                frame = wp.media({
                    title: 'Select Images',
                    button: {
                        text: 'Add Images'
                    },
                    multiple: true
                });
                frame.on('select', function() {
                    var attachments = frame.state().get('selection').toJSON();
                    attachments.forEach(function(attachment) {
                        $('#photoset_images_list').append('<li><img src="' + attachment.url + '" width="100" /><input type="hidden" name="photoset_images[]" value="' + attachment.id + '"><a href="#" class="remove-image">Remove</a></li>');
                    });
                });
                frame.open();
            });
            $('#photoset_images_list').on('click', '.remove-image', function(e) {
                e.preventDefault();
                $(this).closest('li').remove();
            });
            // Add sortable functionality
            $('#photoset_images_list').sortable();
        });
    </script>
    <style>
        #photoset_images_list {
            display: flex;
            flex-wrap: wrap;
        }
        #photoset_images_list li {
            margin: 5px;
            list-style: none;
            cursor: move;
            position: relative;
        }
        #photoset_images_list .remove-image {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            text-decoration: none;
        }
    </style>
    <?php
}


function save_photoset_meta($post_id) {
    if (!isset($_POST['photoset_nonce']) || !wp_verify_nonce($_POST['photoset_nonce'], basename(__FILE__))) {
        return $post_id;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    if ('photoset' !== $_POST['post_type'] || !current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    $photoset_images = (isset($_POST['photoset_images'])) ? array_map('sanitize_text_field', $_POST['photoset_images']) : array();
    update_post_meta($post_id, 'photoset_images', $photoset_images);
}
add_action('save_post', 'save_photoset_meta');
