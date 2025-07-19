<?php

function add_feed_video_meta_box() {
    add_meta_box(
        'feed_video_meta_box',       // Unique ID
        'Daily Feed Video',                // Box title
        'feed_video_meta_box_html',  // Content callback
        'daily-feed',                      // Post type
        'normal',                    // Context
        'high'                       // Priority
    );
}
add_action('add_meta_boxes', 'add_feed_video_meta_box');

function feed_video_meta_box_html($post) {
    $video_url = get_post_meta($post->ID, 'feed_video', true);
    ?>
    <label for="feed_video">Upload Video</label>
    <input type="text" id="feed_video" name="feed_video" value="<?php echo esc_attr($video_url); ?>" style="width: 100%;" />
    <input type="button" id="feed_video_button" class="button" value="<?php _e('Upload or Select Video', 'textdomain'); ?>" />
    <div id="feed_video_preview" style="margin-top: 10px;">
        <?php if ($video_url) : ?>
            <video width="320" height="240" controls>
                <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
                <?php _e('Your browser does not support the video tag.', 'textdomain'); ?>
            </video>
        <?php endif; ?>
    </div>
    <script>
        jQuery(document).ready(function($) {
            $('#feed_video_button').on('click', function(e) {
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
                    $('#feed_video').val(attachment.url);
                    $('#feed_video_preview').html('<video width="320" height="240" controls><source src="' + attachment.url + '" type="video/mp4"><?php _e('Your browser does not support the video tag.', 'textdomain'); ?></video>');
                });
                video_frame.open();
            });
        });
    </script>
    <?php
}

function save_feed_video_meta_box($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }
    if (isset($_POST['feed_video'])) {
        update_post_meta($post_id, 'feed_video', esc_url_raw($_POST['feed_video']));
    }
}
add_action('save_post', 'save_feed_video_meta_box');
