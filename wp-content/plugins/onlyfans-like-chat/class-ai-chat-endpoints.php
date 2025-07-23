<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class OFL_AI_Chat_Endpoints {
    public static function register_routes() {
        register_rest_route('ofl-chat/v1', '/send', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'handle_send'),
            'permission_callback' => function () {
                return is_user_logged_in();
            },
        ));
    }

    public static function handle_send(WP_REST_Request $request) {
        $message   = sanitize_text_field($request->get_param('message'));
        $creator_id = sanitize_text_field($request->get_param('creator_id'));

        if (empty($message) || empty($creator_id)) {
            return new WP_Error('missing_params', 'creator_id and message are required', array('status' => 400));
        }

        if (!get_option('ofl_chat_enable_ai')) {
            return array('response' => '');
        }

        $api_url = rtrim(get_option('ofl_chat_api_url'), '/');
        if (!$api_url) {
            return new WP_Error('no_api_url', 'API URL not configured', array('status' => 500));
        }
        $api_key = get_option('ofl_chat_api_key');

        $user_id = get_current_user_id();
        $spend   = (float) get_user_meta($user_id, 'ofl_total_spend', true);
        $spend  += 1; // simple per-message cost
        update_user_meta($user_id, 'ofl_total_spend', $spend);
        $last = current_time('mysql');
        update_user_meta($user_id, 'ofl_last_ai_chat', $last);

        $body = wp_json_encode(array(
            'creator_id' => $creator_id,
            'message'    => $message,
            'traits'     => array(
                'total_spend'     => $spend,
                'last_interaction'=> $last,
            ),
        ));
        $args = array(
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'body'    => $body,
            'timeout' => 20,
        );
        if (!empty($api_key)) {
            $args['headers']['Authorization'] = 'Bearer ' . $api_key;
        }

        $response = wp_remote_post($api_url, $args);
        if (is_wp_error($response)) {
            return new WP_Error('api_error', $response->get_error_message(), array('status' => 500));
        }
        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        $reply = $data['response'] ?? '';

        if ($code >= 300 || !$reply) {
            return new WP_Error('api_error', 'Invalid response from AI service', array('status' => 500));
        }

        $post_id = wp_insert_post(array(
            'post_type'   => 'private_message',
            'post_title'  => 'AI Reply to ' . $creator_id,
            'post_content'=> $reply,
            'post_status' => 'publish',
            'post_author' => get_current_user_id(),
            'meta_input'  => array(
                'recipient'        => $creator_id,
                'original_message' => $message,
            ),
        ));

        return array(
            'id'       => $post_id,
            'response' => $reply,
        );
    }
}
