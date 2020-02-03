<?php
/**
 * Feedback plugin WordPress
 *
 * @category Plugin
 * @package  WordPress plugin
 */

/*
Loading class php
*/

require_once 'classes/class-inquiries.php';
require_once '../../../wp-load.php';
require_once '../../../wp-includes/class-phpmailer.php';

use Inquiries\Inquiries;

if (
	isset( $_GET['inquiries'], $_GET['tkn'] )
	&& wp_verify_nonce( sanitize_key( $_GET['tkn'] ), 'token' )
	&& ! empty( $_GET ) && ! empty( $_GET['inquiries'] ) ) {
	switch ( $_GET['inquiries'] ) {
		case 'causes':
			Inquiries::causes_list();
			break;
		case 'messages':
			Inquiries::messages_list();
			break;
		case 'add_causes':
			Inquiries::add_causes();
			break;
		case 'delete_causes':
			Inquiries::delete_causes();
			break;
		case 'delete_messages':
			Inquiries::delete_messages();
			break;
	}
} elseif (
	isset( $_POST['causes'], $_POST['name'], $_POST['email'], $_POST['comment'], $_POST['redirect'], $_POST['tkn'], $_FILES['userfile']['name'] )
	&& wp_verify_nonce( sanitize_key( $_POST['tkn'] ), 'token' )
	&& ! empty( $_POST['causes'] )
	&& ! empty( $_POST['name'] )
	&& ! empty( $_POST['email'] )
	&& ! empty( $_POST['comment'] )
	&& ! empty( $_POST['redirect'] )
	&& ! empty( $_FILES['userfile']['name'] ) ) {
	Inquiries::submit_form();
} else {
	echo 'Ошибка выполнения';
}
