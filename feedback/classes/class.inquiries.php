<?php

namespace Inquiries;

class Inquiries
{
    public static function causesList()
    {
        $mas = array();
        $sql = array();

        global $wpdb;

        $table_name = $wpdb->get_blog_prefix() . "causes";
        $results = $wpdb->get_results("SELECT * FROM $table_name");
        
        if ($results) {
            foreach ($results as $value) {
                $sql["id"] = $value->id;
                $sql["subject"] = $value->subject;
                $sql["email"] = $value->email;

                $mas[] = $sql;
            }
        }
        
        echo json_encode($mas);
    }
    public static function messagesList()
    {
        $mas = array();
        $sql = array();

        global $wpdb;

        $table_name = $wpdb->get_blog_prefix() . "feedback";
        $results = $wpdb->get_results("SELECT * FROM $table_name");
        
        if ($results) {
            foreach ($results as $value) {
                $sql["id"] = $value->id;
                $sql["name"] = $value->name;
                $sql["email"] = $value->email;
                $sql["subject"] = $value->subject;
                $sql["description"] = $value->description;
                $sql["version"] = $value->version;
                $sql["link"] = $value->link;

                $mas[] = $sql;
            }
        }
        
        echo json_encode($mas);
    }
    public static function addCauses()
    {
        $_POST = json_decode(file_get_contents("php://input"), true);

        if (!empty($_POST["data"]["subject"]) && !empty($_POST["data"]["email"])) {
            global $wpdb;
            $table_name = $wpdb->get_blog_prefix() . "causes";
            $wpdb->insert(
                $table_name,
                array("subject" => $_POST["data"]["subject"], "email" => $_POST["data"]["email"])
            );
        }
    }
    public static function deleteCauses()
    {
        $_POST = json_decode(file_get_contents("php://input"), true);

        if (!empty($_POST["data"]["id"])) {
            global $wpdb;
            $table_name = $wpdb->get_blog_prefix() . "causes";
            $wpdb->delete($table_name, array("id" => $_POST["data"]["id"]));
        }
    }
    public static function deleteMessages()
    {
        $_POST = json_decode(file_get_contents("php://input"), true);

        if (!empty($_POST["data"]["id"])) {
            global $wpdb;
            $table_name = $wpdb->get_blog_prefix() . "feedback";
            $wpdb->delete($table_name, array('id' => $_POST["data"]["id"]));
        }
    }
}
