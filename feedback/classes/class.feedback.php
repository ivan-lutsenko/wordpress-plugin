<?php

class Feedback
{
    public function initPlugin()
    {
        add_action("admin_menu", array("Feedback", "createMenu"));
        add_shortcode("feedback", array("Feedback", "createShortcode"));
    }

    public function createMenu()
    {
        add_menu_page(
            "Сообщения",
            "Обратная связь",
            "manage_options",
            "new-feedback-menu",
            array("Feedback", "menuOutput"),
            "dashicons-testimonial",
            25
        );
    }

    public function menuOutput()
    {
        echo file_get_contents(plugin_dir_path(__FILE__) . "../templates/html/feedback-admin.html");
    }

    public function createShortcode()
    {
        ob_start();
        include(plugin_dir_path(__FILE__) . "../templates/php/form-react.php");
        $buffer = ob_get_clean();
        return $buffer;
    }

    public function activationFeedback()
    {
        global $wpdb;
        $table_name_1 = $wpdb->get_blog_prefix() . "causes";
        $table_name_2 = $wpdb->get_blog_prefix() . "feedback";
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

    public function deactivationFeedback()
    {
        remove_shortcode("feedback");

        global $wpdb;
        $table_name_1 = $wpdb->get_blog_prefix() . "causes";
        $sql = "DROP TABLE IF EXISTS $table_name_1;";
        $wpdb->query($sql);

        $table_name_2 = $wpdb->get_blog_prefix() . "feedback";
        $sql = "DROP TABLE IF EXISTS $table_name_2;";
        $wpdb->query($sql);
    }
}
