<?php 

// Add image upload field to taxonomy add form
function add_model_image_field() {
    ?>
    <div class="form-field term-group">
        <label for="model-image-id"><?php _e('Image', 'textdomain'); ?></label>
        <input type="hidden" id="model-image-id" name="model-image-id" class="custom_media_url" value="">
        <div id="model-image-wrapper"></div>
        <p>
            <input type="button" class="button button-secondary model_tax_media_button" id="model_tax_media_button" name="model_tax_media_button" value="<?php _e('Add Image', 'textdomain'); ?>" />
            <input type="button" class="button button-secondary model_tax_media_remove" id="model_tax_media_remove" name="model_tax_media_remove" value="<?php _e('Remove Image', 'textdomain'); ?>" />
        </p>
    </div>
    <?php
}
add_action('model_add_form_fields', 'add_model_image_field', 10, 2);

// Add image upload field to taxonomy edit form
function edit_model_image_field($term) {
    $image_id = get_term_meta($term->term_id, 'model-image-id', true);
    ?>
    <tr class="form-field term-group-wrap">
        <th scope="row">
            <label for="model-image-id"><?php _e('Image', 'textdomain'); ?></label>
        </th>
        <td>
            <input type="hidden" id="model-image-id" name="model-image-id" value="<?php echo esc_attr($image_id); ?>">
            <div id="model-image-wrapper">
                <?php if ($image_id) { ?>
                    <?php echo wp_get_attachment_image($image_id, 'thumbnail'); ?>
                <?php } ?>
            </div>
            <p>
                <input type="button" class="button button-secondary model_tax_media_button" id="model_tax_media_button" name="model_tax_media_button" value="<?php _e('Add Image', 'textdomain'); ?>" />
                <input type="button" class="button button-secondary model_tax_media_remove" id="model_tax_media_remove" name="model_tax_media_remove" value="<?php _e('Remove Image', 'textdomain'); ?>" />
            </p>
        </td>
    </tr>
    <?php
}
add_action('model_edit_form_fields', 'edit_model_image_field', 10, 2);

// Save image data
function save_model_image($term_id) {
    if (isset($_POST['model-image-id']) && '' !== $_POST['model-image-id']) {
        $image = esc_attr($_POST['model-image-id']);
        update_term_meta($term_id, 'model-image-id', $image);
    } else {
        update_term_meta($term_id, 'model-image-id', '');
    }
}
add_action('created_model', 'save_model_image', 10, 2);
add_action('edited_model', 'save_model_image', 10, 2);


function load_wp_media_files() {
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'load_wp_media_files');

function add_model_image_script() {
    ?>
    <script>
        jQuery(document).ready(function ($) {
            function model_media_upload(button_class) {
                var _custom_media = true,
                    _orig_send_attachment = wp.media.editor.send.attachment;

                $('body').on('click', button_class, function (e) {
                    var button_id = '#' + $(this).attr('id');
                    var send_attachment_bkp = wp.media.editor.send.attachment;
                    var button = $(button_id);
                    _custom_media = true;

                    wp.media.editor.send.attachment = function (props, attachment) {
                        if (_custom_media) {
                            $('#model-image-id').val(attachment.id);
                            $('#model-image-wrapper').html('<img src="' + attachment.url + '" style="max-width:100%;"/>');
                        } else {
                            return _orig_send_attachment.apply(button_id, [props, attachment]);
                        }
                    }

                    wp.media.editor.open(button);
                    return false;
                });

                $('body').on('click', '.model_tax_media_remove', function () {
                    $('#model-image-id').val('');
                    $('#model-image-wrapper').html('');
                });
            }

            model_media_upload('.model_tax_media_button.button');
        });
    </script>
    <?php
}
add_action('admin_footer', 'add_model_image_script');
