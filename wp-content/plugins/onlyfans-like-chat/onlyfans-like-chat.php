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
}

new OnlyFansLikeChat();
