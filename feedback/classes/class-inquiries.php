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
	 * Username
	 *
	 * @var string
	 */
	public $username = '';

	/**
	 * Email
	 *
	 * @var string
	 */
	public $email = '';

	/**
	 * Message text
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * Type of reason
	 *
	 * @var string
	 */
	public $cause = '';

	/**
	 * File name
	 *
	 * @var string
	 */
	public $screenshot = '';

	/**
	 * Sending link
	 *
	 * @var string
	 */
	public $redirect_page = '';

	/**
	 * Reason output function
	 */
	public static function causes_list() {
		$causes = array();
		$sql    = array();
		global $wpdb;
		$wpdb->causes   = $wpdb->get_blog_prefix() . 'causes';
		$causes_results = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$wpdb->causes} WHERE %d", 1 )
		); // db call ok; no-cache ok.
		foreach ( $causes_results as $value ) {
			$sql['id']      = $value->id;
			$sql['subject'] = $value->subject;
			$sql['email']   = $value->email;
			$causes[]       = $sql;
		}
		echo wp_json_encode( $causes );
	}

	/**
	 * Message output function
	 */
	public static function messages_list() {
		$messages = array();
		$sql      = array();
		global $wpdb;
		$wpdb->feedback   = $wpdb->get_blog_prefix() . 'feedback';
		$messages_results = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$wpdb->feedback} WHERE %d", 1 )
		); // db call ok; no-cache ok.
		foreach ( $messages_results as $value ) {
			$sql['id']          = $value->id;
			$sql['name']        = $value->name;
			$sql['email']       = $value->email;
			$sql['subject']     = $value->subject;
			$sql['description'] = $value->description;
			$sql['version']     = $value->version;
			$sql['link']        = $value->link;
			$messages[]         = $sql;
		}
		echo wp_json_encode( $messages );
	}

	/**
	 * Function to add reason
	 */
	public static function add_causes() {
		if (
			isset( $_POST['subject'], $_POST['email'], $_GET['token'] )
			&& wp_verify_nonce( sanitize_key( $_GET['token'] ), 'token' )
			&& ! empty( $_POST['subject'] ) && ! empty( $_POST['email'] )
		) {
			global $wpdb;
			$table_name = $wpdb->get_blog_prefix() . 'causes';
			$wpdb->insert(
				$table_name,
				array(
					'subject' => sanitize_text_field( wp_unslash( $_POST['subject'] ) ),
					'email'   => sanitize_text_field( wp_unslash( $_POST['email'] ) ),
				)
			); // db call ok; no-cache ok.
		}
	}

	/**
	 * Function to remove the cause
	 */
	public static function delete_causes() {
		if (
			isset( $_POST['id'], $_GET['token'] )
			&& wp_verify_nonce( sanitize_key( $_GET['token'] ), 'token' )
			&& ! empty( $_POST['id'] )
		) {
			global $wpdb;
			$table_name = $wpdb->get_blog_prefix() . 'causes';
			$wpdb->delete(
				$table_name,
				array(
					'id' => sanitize_text_field( wp_unslash( $_POST['id'] ) ),
				)
			); // db call ok; no-cache ok.
		}
	}

	/**
	 * Function to delete a message
	 */
	public static function delete_messages() {
		if (
			isset( $_POST['id'], $_GET['token'] )
			&& wp_verify_nonce( sanitize_key( $_GET['token'] ), 'token' )
			&& ! empty( $_POST['id'] )
		) {
			global $wpdb;
			$table_name = $wpdb->get_blog_prefix() . 'feedback';
			$wpdb->delete(
				$table_name,
				array(
					'id' => sanitize_text_field( wp_unslash( $_POST['id'] ) ),
				)
			); // db call ok; no-cache ok.
		}
	}

	/**
	 * Form submission
	 */
	public function submit_form() {
		global $wpdb;
		$image_directory = 'images/' . basename( $this->screenshot );
		$causes          = array();
		$wpdb->causes    = $wpdb->get_blog_prefix() . 'causes';
		$causes_results  = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$wpdb->causes} WHERE id = %d", $this->cause )
		); // db call ok; no-cache ok.
		foreach ( $causes_results as $value ) {
			$causes['id']      = $value->id;
			$causes['subject'] = $value->subject;
			$causes['email']   = $value->email;
		}
		if ( isset( $_FILES['userfile']['tmp_name'] ) && isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$temporary_name  = sanitize_text_field( wp_unslash( $_FILES['userfile']['tmp_name'] ) );
			$browser_version = sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) );
			move_uploaded_file( $temporary_name, $image_directory );
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
			$mail->addAddress( $causes['email'] );
			$mail->isHTML( true );
			$mail->Subject = $causes['subject'];
			$mail->Body    = '
				<div>Имя: ' . $this->username . '</div>
				<div>Адрес электронной почты: ' . $this->email . '</div>
				<div>Причина: ' . $causes['subject'] . '</div>
				<div>Описание причины: ' . $this->description . '</div>
				<div>Версия браузера: ' . $browser_version . '</div>
			';
			$mail->addAttachment( $image_directory );
			if ( $mail->send() ) {
				$table_name = $wpdb->get_blog_prefix() . 'feedback';
				$wpdb->insert(
					$table_name,
					array(
						'name'        => $this->username,
						'email'       => $this->email,
						'subject'     => $causes['subject'],
						'description' => $this->description,
						'version'     => $browser_version,
						'link'        => $image_directory,
					)
				); // db call ok; no-cache ok.
				wp_safe_redirect( $this->redirect_page . '#app', 301 );
				exit;
			}
		}
	}
}
