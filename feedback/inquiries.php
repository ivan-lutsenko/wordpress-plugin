<?php

require_once("classes/class.inquiries.php");
require_once("../../../wp-load.php");

use Inquiries\Inquiries;

if (!empty($_GET)) {
    switch ($_GET["inquiries"]) {
        case "causes":
            Inquiries::causesList();
            break;
        case "messages":
            Inquiries::messagesList();
            break;
        case "add_causes":
            Inquiries::addCauses();
            break;
        case "delete_causes":
            Inquiries::deleteCauses();
            break;
        case "delete_messages":
            Inquiries::deleteMessages();
            break;
    }
} else {
    echo "Ошибка выполнения";
}
