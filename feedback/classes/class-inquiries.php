<?php
/**
 * Feedback plugin WordPress
 *
 * @category Plugin
 * @package  WordPress plugin
 */

namespace Inquiries;

/**
 * Request class initialization
 */
class Inquiries {
	/**
	 * Reason output function
	 */
	public static function causes_list() {
		$mas = array();
		$sql = array();
		global $wpdb;
		$wpdb->causes = $wpdb->get_blog_prefix() . 'causes';
		$results      = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$wpdb->causes} WHERE %d", 1 )
		); // db call ok; no-cache ok.
		if ( $results ) {
			foreach ( $results as $value ) {
				$sql['id']      = $value->id;
				$sql['subject'] = $value->subject;
				$sql['email']   = $value->email;
				$mas[]          = $sql;
			}
		}
		echo wp_json_encode( $mas );
	}
	/**
	 * Message output function
	 */
	public static function messages_list() {
		$mas = array();
		$sql = array();
		global $wpdb;
		$wpdb->feedback = $wpdb->get_blog_prefix() . 'feedback';
		$results        = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$wpdb->feedback} WHERE %d", 1 )
		); // db call ok; no-cache ok.
		if ( $results ) {
			foreach ( $results as $value ) {
				$sql['id']          = $value->id;
				$sql['name']        = $value->name;
				$sql['email']       = $value->email;
				$sql['subject']     = $value->subject;
				$sql['description'] = $value->description;
				$sql['version']     = $value->version;
				$sql['link']        = $value->link;
				$mas[]              = $sql;
			}
		}
		echo wp_json_encode( $mas );
	}
	/**
	 * Function to add reason
	 */
	public static function add_causes() {
		$_POST = json_decode( file_get_contents( 'php://input' ), true );
		if (
			isset( $_POST['data']['subject'], $_POST['data']['email'], $_GET['tkn'] )
			&& wp_verify_nonce( sanitize_key( $_GET['tkn'] ), 'token' )
			&& ! empty( $_POST['data']['subject'] ) && ! empty( $_POST['data']['email'] ) ) {
			global $wpdb;
			$table_name = $wpdb->get_blog_prefix() . 'causes';
			$wpdb->insert(
				$table_name,
				array(
					'subject' => sanitize_text_field( wp_unslash( $_POST['data']['subject'] ) ),
					'email'   => sanitize_text_field( wp_unslash( $_POST['data']['email'] ) ),
				)
			); // db call ok; no-cache ok.
		}
	}
	/**
	 * Function to remove the cause
	 */
	public static function delete_causes() {
		$_POST = json_decode( file_get_contents( 'php://input' ), true );
		if (
			isset( $_POST['data']['id'], $_GET['tkn'] )
			&& wp_verify_nonce( sanitize_key( $_GET['tkn'] ), 'token' )
			&& ! empty( $_POST['data']['id'] ) ) {
			global $wpdb;
			$table_name = $wpdb->get_blog_prefix() . 'causes';
			$wpdb->delete(
				$table_name,
				array(
					'id' => sanitize_text_field( wp_unslash( $_POST['data']['id'] ) ),
				)
			); // db call ok; no-cache ok.
		}
	}
	/**
	 * Function to delete a message
	 */
	public static function delete_messages() {
		$_POST = json_decode( file_get_contents( 'php://input' ), true );
		if (
			isset( $_POST['data']['id'], $_GET['tkn'] )
			&& wp_verify_nonce( sanitize_key( $_GET['tkn'] ), 'token' )
			&& ! empty( $_POST['data']['id'] ) ) {
			global $wpdb;
			$table_name = $wpdb->get_blog_prefix() . 'feedback';
			$wpdb->delete(
				$table_name,
				array(
					'id' => sanitize_text_field( wp_unslash( $_POST['data']['id'] ) ),
				)
			); // db call ok; no-cache ok.
		}
	}
	/**
	 * Form submission
	 */
	public static function submit_form() {
		global $wpdb;
		if (
			isset( $_POST['causes'], $_POST['name'], $_POST['email'], $_POST['comment'], $_POST['redirect'], $_POST['tkn'], $_FILES['userfile']['name'] )
			&& wp_verify_nonce( sanitize_key( $_POST['tkn'] ), 'token' )
			&& ! empty( $_POST['causes'] )
			&& ! empty( $_POST['name'] )
			&& ! empty( $_POST['email'] )
			&& ! empty( $_POST['comment'] )
			&& ! empty( $_POST['redirect'] )
			&& ! empty( $_FILES['userfile']['name'] ) ) {
			$causes       = sanitize_text_field( wp_unslash( $_POST['causes'] ) );
			$name         = sanitize_text_field( wp_unslash( $_POST['name'] ) );
			$email        = sanitize_text_field( wp_unslash( $_POST['email'] ) );
			$text         = sanitize_text_field( wp_unslash( $_POST['comment'] ) );
			$userfile     = sanitize_text_field( wp_unslash( $_FILES['userfile']['name'] ) );
			$redirect     = sanitize_text_field( wp_unslash( $_POST['redirect'] ) );
			$uploaddir    = 'images/';
			$uploadfile   = $uploaddir . basename( $userfile );
			$sql          = array();
			$wpdb->causes = $wpdb->get_blog_prefix() . 'causes';
			$results      = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM {$wpdb->causes} WHERE id = %d", $causes )
			); // db call ok; no-cache ok.
			if ( $results ) {
				foreach ( $results as $value ) {
					$sql['id']      = $value->id;
					$sql['subject'] = $value->subject;
					$sql['email']   = $value->email;
				}
			}
			if ( isset( $_FILES['userfile']['tmp_name'] ) && isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
				$userfile_tmp = sanitize_text_field( wp_unslash( $_FILES['userfile']['tmp_name'] ) );
				$version      = sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) );
				move_uploaded_file( $userfile_tmp, $uploadfile );
				$mail = new \PHPMailer();
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
					<div>Имя: ' . $name . '</div>
					<div>Адрес электронной почты: ' . $email . '</div>
					<div>Причина: ' . $sql['subject'] . '</div>
					<div>Описание причины: ' . $text . '</div>
					<div>Версия браузера: ' . $version . '</div>
				';
				$mail->addAttachment( $uploadfile );
				if ( $mail->send() ) {
					$table_name = $wpdb->get_blog_prefix() . 'feedback';
					$wpdb->insert(
						$table_name,
						array(
							'name'        => $name,
							'email'       => $email,
							'subject'     => $sql['subject'],
							'description' => $text,
							'version'     => $version,
							'link'        => $uploadfile,
						)
					); // db call ok; no-cache ok.
					wp_safe_redirect( $redirect . '#app', 301 );
					exit;
				}
			}
		}
	}
}
