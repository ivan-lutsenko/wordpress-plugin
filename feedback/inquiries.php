<?php

require_once("../../../wp-load.php");

class Inquiries
{
    public static function Causes()
    {
        $mas = array();
        $sql = array();

        global $wpdb;

        $table_name = $wpdb->get_blog_prefix() . 'causes';
        $results = $wpdb->get_results("SELECT * FROM $table_name");
        
        if ($results) {
            foreach ($results as $value) {
                $sql['id'] = $value->id;
                $sql['subject'] = $value->subject;
                $sql['email'] = $value->email;

                $mas[] = $sql;
            }
        }
        
        echo json_encode($mas);
    }
    public static function Messages()
    {
        $mas = array();
        $sql = array();

        global $wpdb;

        $table_name = $wpdb->get_blog_prefix() . 'feedback';
        $results = $wpdb->get_results("SELECT * FROM $table_name");
        
        if ($results) {
            foreach ($results as $value) {
                $sql['id'] = $value->id;
                $sql['name'] = $value->name;
                $sql['email'] = $value->email;
                $sql['subject'] = $value->subject;
                $sql['description'] = $value->description;
                $sql['version'] = $value->version;
                $sql['link'] = $value->link;

                $mas[] = $sql;
            }
        }
        
        echo json_encode($mas);
    }
    public static function AddCauses()
    {
        $_POST = json_decode(file_get_contents('php://input'), true);

        if (!empty($_POST)) {
            try {
                if (!empty($_POST['data']['subject']) && !empty($_POST['data']['email'])) {
                    global $wpdb;
                    $table_name = $wpdb->get_blog_prefix() . 'causes';
                    $wpdb->insert(
                        $table_name,
                        array( 'subject' => $_POST['data']['subject'], 'email' => $_POST['data']['email'] )
                    );
                    echo "true";
                } else {
                    echo "false";
                }
        
            } catch (Exception $e) {
                echo "false";
            }
        }else{
            echo "Ошибка выполнения";
        }
    }
    public static function DeleteCauses()
    {
        $_POST = json_decode(file_get_contents('php://input'), true);

        if (!empty($_POST)) {
            try {
                if (!empty($_POST['data']['id'])) {
                    global $wpdb;
                    $table_name = $wpdb->get_blog_prefix() . 'causes';
                    $wpdb->delete($table_name, array('id' => $_POST['data']['id']));
                    echo "true";
                } else {
                    echo "false";
                }
        
            } catch (Exception $e) {
                echo "false";
            }
        }else{
            echo "Ошибка выполнения";
        }
    }
    public static function DeleteMessages()
    {
        $_POST = json_decode(file_get_contents('php://input'), true);

        if (!empty($_POST)) {
            try {
                if (!empty($_POST['data']['id'])) {
                    global $wpdb;
                    $table_name = $wpdb->get_blog_prefix() . 'feedback';
                    $wpdb->delete($table_name, array('id' => $_POST['data']['id']));
                    echo "true";
                } else {
                    echo "false";
                }
        
            } catch (Exception $e) {
                echo "false";
            }
        }else{
            echo "Ошибка выполнения";
        }
    }
}

if (isset($_GET)) {
    if (!empty($_GET)) {
        if ($_GET['inquiries'] == 'causes') {
            Inquiries::Causes();
        }
        if ($_GET['inquiries'] == 'add_causes') {
            Inquiries::AddCauses();
        }
        if ($_GET['inquiries'] == 'delete_causes') {
            Inquiries::DeleteCauses();
        }
        if ($_GET['inquiries'] == 'messages') {
            Inquiries::Messages();
        }
        if ($_GET['inquiries'] == 'delete_messages') {
            Inquiries::DeleteMessages();
        }
    } else {
        echo 'Ошибка выполнения';
    }
}
