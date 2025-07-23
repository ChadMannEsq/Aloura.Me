<?php
/*
Plugin Name: OnlyFans-like Chat Features
Description: Adds subscription tiers, pay-per-message, and tipping features for chat.
Version: 0.1.0
Author: ChatGPT
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class OnlyFansLikeChat {
    public function __construct() {
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        add_action('init', array($this, 'register_post_types'));
        add_action('admin_menu', array($this, 'register_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('rest_api_init', array($this, 'load_rest_endpoints'));
    }

    public function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'ofl_subscriptions';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            tier varchar(191) NOT NULL,
            start_date datetime NOT NULL,
            end_date datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function deactivate() {
        // Cleanup tasks can be placed here
    }

    public function register_post_types() {
        register_post_type('exclusive_message', array(
            'labels' => array(
                'name' => __('Exclusive Messages', 'ofl'),
                'singular_name' => __('Exclusive Message', 'ofl'),
            ),
            'public' => false,
            'show_ui' => true,
            'supports' => array('title', 'editor', 'author'),
        ));
    }

    public function load_rest_endpoints() {
        require_once plugin_dir_path(__FILE__) . 'class-ai-chat-endpoints.php';
        OFL_AI_Chat_Endpoints::register_routes();
    }

    public function register_settings_page() {
        add_options_page(
            'OnlyFans-like Chat',
            'OnlyFans-like Chat',
            'manage_options',
            'onlyfans-like-chat',
            array($this, 'settings_page_html')
        );
        add_submenu_page(
            'options-general.php',
            'High Value Fans',
            'High Value Fans',
            'manage_options',
            'ofl-high-value-fans',
            array($this, 'high_value_fans_page_html')
        );
    }

    public function register_settings() {
        register_setting('ofl_chat_settings', 'ofl_chat_api_url');
        register_setting('ofl_chat_settings', 'ofl_chat_api_key');
        register_setting('ofl_chat_settings', 'ofl_chat_enable_ai', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => false,
        ));
    }

    public function settings_page_html() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('OnlyFans-like Chat Settings', 'ofl'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('ofl_chat_settings');
                ?>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">
                            <label for="ofl_chat_api_url"><?php esc_html_e('API URL', 'ofl'); ?></label>
                        </th>
                        <td>
                            <input name="ofl_chat_api_url" type="text" id="ofl_chat_api_url" value="<?php echo esc_attr(get_option('ofl_chat_api_url')); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ofl_chat_api_key"><?php esc_html_e('API Key', 'ofl'); ?></label>
                        </th>
                        <td>
                            <input name="ofl_chat_api_key" type="text" id="ofl_chat_api_key" value="<?php echo esc_attr(get_option('ofl_chat_api_key')); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ofl_chat_enable_ai"><?php esc_html_e('Enable Auto-Chat', 'ofl'); ?></label>
                        </th>
                        <td>
                            <input name="ofl_chat_enable_ai" type="checkbox" id="ofl_chat_enable_ai" value="1" <?php checked(1, get_option('ofl_chat_enable_ai', 0)); ?> />
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function high_value_fans_page_html() {
        $users = get_users(array(
            'meta_key' => 'ofl_total_spend',
            'orderby'  => 'meta_value_num',
            'order'    => 'DESC',
            'number'   => 50,
        ));
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('High Value Fans', 'ofl'); ?></h1>
            <table class="widefat">
                <thead>
                    <tr>
                        <th><?php esc_html_e('User', 'ofl'); ?></th>
                        <th><?php esc_html_e('Total Spend', 'ofl'); ?></th>
                        <th><?php esc_html_e('Last AI Chat', 'ofl'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $u) : ?>
                    <tr>
                        <td><?php echo esc_html($u->user_login); ?></td>
                        <td><?php echo esc_html(get_user_meta($u->ID, 'ofl_total_spend', true)); ?></td>
                        <td><?php echo esc_html(get_user_meta($u->ID, 'ofl_last_ai_chat', true)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}

new OnlyFansLikeChat();
