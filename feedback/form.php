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

require_once '../../../wp-load.php';
require_once '../../../wp-includes/class-phpmailer.php';

if (
	isset( $_POST['causes'], $_POST['name'], $_POST['email'], $_POST['comment'], $_POST['redirect'], $_POST['tkn'], $_SERVER['HTTP_USER_AGENT'], $_FILES['userfile']['name'], $_FILES['userfile']['tmp_name'] )
	&& wp_verify_nonce( sanitize_key( $_POST['tkn'] ), 'token' ) ) {
	if ( ! empty( $_POST['causes'] )
		&& ! empty( $_POST['name'] )
		&& ! empty( $_POST['email'] )
		&& ! empty( $_POST['comment'] )
		&& ! empty( $_FILES['userfile']['name'] ) ) {
		$uploaddir  = 'images/';
		$uploadfile = $uploaddir . basename( sanitize_text_field( wp_unslash( $_FILES['userfile']['name'] ) ) );
		$sql        = array();
		$table_name = $wpdb->get_blog_prefix() . 'causes';
		$results    = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", sanitize_text_field( wp_unslash( $_POST['causes'] ) ) )
		); // db call ok; no-cache ok.
		if ( $results ) {
			foreach ( $results as $value ) {
				$sql['id']      = $value->id;
				$sql['subject'] = $value->subject;
				$sql['email']   = $value->email;
			}
		}
		move_uploaded_file( sanitize_text_field( wp_unslash( $_FILES['userfile']['tmp_name'] ) ), $uploadfile );
		$mail = new PHPMailer();
		$mail->isSMTP();
		$mail->CharSet    = 'UTF-8';
		$mail->SMTPAuth   = true;
		$mail->Host       = '';
		$mail->Username   = '';
		$mail->Password   = '';
		$mail->SMTPSecure = '';
		$mail->Port       = 465;
		$mail->setFrom( '', '' );
		$mail->addAddress( $sql['email'] );
		$mail->isHTML( true );
		$mail->Subject = $sql['subject'];
		$mail->Body    = '
			<div>Имя: ' . sanitize_text_field( wp_unslash( $_POST['name'] ) ) . '</div>
			<div>Адрес электронной почты: ' . sanitize_text_field( wp_unslash( $_POST['email'] ) ) . '</div>
			<div>Причина: ' . sanitize_text_field( wp_unslash( $sql['subject'] ) ) . '</div>
			<div>Описание причины: ' . sanitize_text_field( wp_unslash( $_POST['comment'] ) ) . '</div>
			<div>Версия браузера: ' . sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) . '</div>
		';
		$mail->addAttachment( $uploadfile );
		if ( $mail->send() ) {
			$table_name = $wpdb->get_blog_prefix() . 'feedback';
			$wpdb->insert(
				$table_name,
				array(
					'name'        => sanitize_text_field( wp_unslash( $_POST['name'] ) ),
					'email'       => sanitize_text_field( wp_unslash( $_POST['email'] ) ),
					'subject'     => sanitize_text_field( wp_unslash( $sql['subject'] ) ),
					'description' => sanitize_text_field( wp_unslash( $_POST['comment'] ) ),
					'version'     => sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ),
					'link'        => $uploadfile,
				)
			); // db call ok; no-cache ok.
			wp_safe_redirect( sanitize_text_field( wp_unslash( $_POST['redirect'] ) ) . '#app', 301 );
			exit;
		}
	} else {
		echo 'Ошибка выполнения';
	}
} else {
	echo 'Ошибка выполнения';
}
