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

use Inquiries\Inquiries;

if (
	isset( $_GET['inquiries'], $_GET['tkn'] )
	&& ! empty( $_GET ) && ! empty( $_GET['inquiries'] )
	&& wp_verify_nonce( sanitize_key( $_GET['tkn'] ), 'token' ) ) {
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
} else {
	echo 'Ошибка выполнения';
}
