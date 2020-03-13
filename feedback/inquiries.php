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
require_once 's3/vendor/autoload.php';
require_once '../../../wp-load.php';

use Inquiries\Inquiries;

if (
	isset( $_GET['inquiries'], $_GET['token'] )
	&& wp_verify_nonce( sanitize_key( $_GET['token'] ), 'token' )
	&& ! empty( $_GET ) && ! empty( $_GET['inquiries'] )
) {
	$inquiries            = sanitize_text_field( wp_unslash( $_GET['inquiries'] ) );
	$collection_functions = array(
		'causes'          => Inquiries::causes_list(),
		'messages'        => Inquiries::messages_list(),
		'add_causes'      => Inquiries::add_causes(),
		'token_create'    => Inquiries::create_token(),
		'delete_causes'   => Inquiries::delete_causes(),
		'delete_messages' => Inquiries::delete_messages(),
	);
	if ( ( 'causes' === $inquiries ) || ( 'messages' === $inquiries ) ) {
		echo wp_json_encode( $collection_functions[ $inquiries ] );
	} elseif ( 'token_create' === $inquiries ) {
		echo esc_html( $collection_functions[ $inquiries ] );
	} else {
		$collection_functions[ $inquiries ];
	}
} elseif (
	isset( $_POST['causes'], $_POST['name'], $_POST['email'], $_POST['comment'], $_POST['redirect'], $_POST['token'], $_FILES['userfile']['name'] )
	&& wp_verify_nonce( sanitize_key( $_POST['token'] ), 'token' )
	&& ! empty( $_POST['causes'] )
	&& ! empty( $_POST['name'] )
	&& ! empty( $_POST['email'] )
	&& ! empty( $_POST['comment'] )
	&& ! empty( $_POST['redirect'] )
	&& ! empty( $_FILES['userfile']['name'] )
) {
	$inquiry                = new Inquiries();
	$inquiry->cause         = sanitize_text_field( wp_unslash( $_POST['causes'] ) );
	$inquiry->username      = sanitize_text_field( wp_unslash( $_POST['name'] ) );
	$inquiry->email         = sanitize_text_field( wp_unslash( $_POST['email'] ) );
	$inquiry->description   = sanitize_text_field( wp_unslash( $_POST['comment'] ) );
	$inquiry->screenshot    = sanitize_text_field( wp_unslash( $_FILES['userfile']['name'] ) );
	$inquiry->redirect_page = sanitize_text_field( wp_unslash( $_POST['redirect'] ) );
	$inquiry->submit_form();
} else {
	echo 'Ошибка выполнения';
}
