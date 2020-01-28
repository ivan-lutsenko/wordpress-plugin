<?php

require_once("../../../wp-load.php");
require_once("../../../wp-includes/class-phpmailer.php");

$post = true;

if (!empty($_POST) && !empty($_FILES["userfile"]["name"])) {
    foreach ($_POST as $value) {
        if (empty($value)) {
            $post = false;
            break;
        }
    }
} else {
    $post = false;
}

if ($post) {
    $uploaddir = "images/";
    $uploadfile = $uploaddir . basename($_FILES["userfile"]["name"]);

    $sql = array();

    $table_name = $wpdb->get_blog_prefix() . "causes";
    $results = $wpdb->get_results("SELECT * FROM $table_name WHERE id = {$_POST['causes']}");
    
    if ($results) {
        foreach ($results as $value) {
            $sql['id'] = $value->id;
            $sql['subject'] = $value->subject;
            $sql['email'] = $value->email;
        }
    }

    move_uploaded_file($_FILES["userfile"]["tmp_name"], $uploadfile);

    $mail = new PHPMailer();

    $mail->isSMTP();
    $mail->CharSet = "UTF-8";
    $mail->SMTPAuth = true;

    $mail->Host = "";
    $mail->Username = "";
    $mail->Password = "";
    $mail->SMTPSecure = "";
    $mail->Port = 465;
    $mail->setFrom("", "");

    $mail->addAddress($sql["email"]);

    $mail->isHTML(true);

    $mail->Subject = $sql["subject"];
    $mail->Body = "
        <div>Имя: {$_POST['name']}</div>
        <div>Адрес электронной почты: {$_POST['email']}</div>
        <div>Причина: {$sql['subject']}</div>
        <div>Описание причины: {$_POST['comment']}</div>
        <div>Версия браузера: {$_SERVER['HTTP_USER_AGENT']}</div>
    ";

    $mail->addAttachment($uploadfile);

    if ($mail->send()) {
        $table_name = $wpdb->get_blog_prefix() . "feedback";
        $wpdb->insert(
            $table_name,
            array("name" => $_POST["name"], "email" => $_POST["email"],
            "subject" => $sql["subject"], "description" => $_POST["comment"],
            "version" => $_SERVER["HTTP_USER_AGENT"], "link" => $uploadfile )
        );
        
        wp_redirect($_POST["redirect"] . "#app", 301);
        exit;
    }
} else {
    echo "Ошибка выполнения";
}
