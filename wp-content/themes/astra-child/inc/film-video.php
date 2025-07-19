<?php

function add_film_video_meta_box() {
    add_meta_box(
        'film_video_meta_box',       // Unique ID
        'Film Video',                // Box title
        'film_video_meta_box_html',  // Content callback
        'film',                      // Post type
        'normal',                    // Context
        'high'                       // Priority
    );
}
add_action('add_meta_boxes', 'add_film_video_meta_box');

function film_video_meta_box_html($post) {
    $video_url = get_post_meta($post->ID, 'model_film_video', true);
    ?>
    <label for="model_film_video">Upload Video</label>
    <input type="text" id="model_film_video" name="model_film_video" value="<?php echo esc_attr($video_url); ?>" style="width: 100%;" />
    <input type="button" id="model_film_video_button" class="button" value="<?php _e('Upload or Select Video', 'textdomain'); ?>" />
    <div id="model_film_video_preview" style="margin-top: 10px;">
        <?php if ($video_url) : ?>
            <video width="320" height="240" controls>
                <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
                <?php _e('Your browser does not support the video tag.', 'textdomain'); ?>
            </video>
        <?php endif; ?>
    </div>
    <script>
        jQuery(document).ready(function($) {
            $('#model_film_video_button').on('click', function(e) {
                e.preventDefault();
                var video_frame;
                if (video_frame) {
                    video_frame.open();
                    return;
                }
                video_frame = wp.media({
                    title: 'Select or Upload Video',
                    button: {
                        text: 'Use this video'
                    },
                    library: { type: 'video' },
                    multiple: false
                });
                video_frame.on('select', function() {
                    var attachment = video_frame.state().get('selection').first().toJSON();
                    $('#model_film_video').val(attachment.url);
                    $('#model_film_video_preview').html('<video width="320" height="240" controls><source src="' + attachment.url + '" type="video/mp4"><?php _e('Your browser does not support the video tag.', 'textdomain'); ?></video>');
                });
                video_frame.open();
            });
        });
    </script>
    <?php
}

function save_film_video_meta_box($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }
    if (isset($_POST['model_film_video'])) {
        update_post_meta($post_id, 'model_film_video', esc_url_raw($_POST['model_film_video']));
    }
}
add_action('save_post', 'save_film_video_meta_box');

/**
 * Validate uploaded video files.
 */
function am_limit_video_upload($file) {
    $type = wp_check_filetype($file['name']);
    $allowed = array('mp4', 'mov', 'avi');
    if (!in_array($type['ext'], $allowed)) {
        $file['error'] = 'Invalid video type';
        return $file;
    }
    $max = 100 * 1024 * 1024; // 100MB
    if ($file['size'] > $max) {
        $file['error'] = 'Video exceeds maximum size of 100MB';
    }
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'am_limit_video_upload');
