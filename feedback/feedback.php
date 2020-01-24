<?php

/*
Plugin Name: Feedback WordPress
Description: Feedback plugin WordPress
Version: 1.0
Author: Ivan Lutsenko
*/

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

define('FEEDBACK_DIR', plugin_dir_path(__FILE__));

class Feedback
{
    public function init()
    {
        add_action('admin_menu', array('Feedback', 'create_menu'));

        add_shortcode('feedback', array('Feedback', 'create_shortcode'));
    }

    public function create_menu()
    {
        add_menu_page('Сообщения', 'Обратная связь', 'manage_options', 'new-feedback-menu', array('Feedback', 'new_feedback_menu_main'), 'dashicons-testimonial', 25);
    }

    public function new_feedback_menu_main()
    {
        echo file_get_contents(FEEDBACK_DIR . 'templates/html/feedback-admin.html');
    }

    public function create_shortcode()
    {
        ob_start();
        include(FEEDBACK_DIR . 'templates/php/form-react.php');
        $buffer = ob_get_clean();
        return $buffer;
    }

    public function activation_feedback()
    {
        global $wpdb;
        $table_name_1 = $wpdb->get_blog_prefix() . 'causes';
        $table_name_2 = $wpdb->get_blog_prefix() . 'feedback';
        $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";

        $sql = "CREATE TABLE {$table_name_1} (
            id BIGINT NOT NULL AUTO_INCREMENT,
            subject TEXT NOT NULL,
            email TEXT NOT NULL,
            PRIMARY KEY (id)
            ){$charset_collate};";

        $sql .= "CREATE TABLE {$table_name_2} (
            id BIGINT NOT NULL AUTO_INCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL,
            subject TEXT NOT NULL,
            description TEXT NOT NULL,
            version TEXT NOT NULL,
            link TEXT NOT NULL,
            PRIMARY KEY (id)
            ){$charset_collate};";

        dbDelta($sql);
    }

    public function deactivation_feedback()
    {
        remove_shortcode('feedback');

        global $wpdb;
        $table_name_1 = $wpdb->get_blog_prefix() . 'causes';
        $sql = "DROP TABLE IF EXISTS $table_name_1;";
        $wpdb->query($sql);

        $table_name_2 = $wpdb->get_blog_prefix() . 'feedback';
        $sql = "DROP TABLE IF EXISTS $table_name_2;";
        $wpdb->query($sql);
    }
}

register_activation_hook(__FILE__, array('Feedback', 'activation_feedback'));

register_deactivation_hook(__FILE__, array('Feedback', 'deactivation_feedback'));

add_action('init', array('Feedback', 'init'));
