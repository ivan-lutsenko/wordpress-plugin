<?php
/**
 * Feedback plugin WordPress
 *
 * @category Plugin
 * @package  WordPress plugin
 */

namespace Inquiries;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

/**
 * Request class initialization
 */
class Inquiries {

	/**
	 * S3 configuration
	 *
	 * @var array
	 */
	private $s3_configuration = array(
		'acl'                     => 'public-read',
		'key'                     => 'accessKey1',
		'secret'                  => 'verySecretKey1',
		'bucket'                  => 'feedback',
		'region'                  => 'us-west-2',
		'version'                 => 'latest',
		'endpoint'                => 'http://localhost:8090',
		'use_path_style_endpoint' => true,
	);

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
		global $wpdb;
		$wpdb->causes   = $wpdb->get_blog_prefix() . 'causes';
		$causes_results = $wpdb->get_results(
			$wpdb->prepare( "SELECT id, subject, email FROM {$wpdb->causes} WHERE %d", 1 )
		); // db call ok; no-cache ok.
		foreach ( $causes_results as $value ) {
			$causes[] = array(
				'id'      => $value->id,
				'subject' => $value->subject,
				'email'   => $value->email,
			);
		}
		return $causes;
	}

	/**
	 * Message output function
	 */
	public static function messages_list() {
		$messages = array();
		global $wpdb;
		$wpdb->feedback   = $wpdb->get_blog_prefix() . 'feedback';
		$messages_results = $wpdb->get_results(
			$wpdb->prepare( "SELECT id, name, email, subject, description, version, link FROM {$wpdb->feedback} WHERE %d", 1 )
		); // db call ok; no-cache ok.
		foreach ( $messages_results as $value ) {
			$messages[] = array(
				'id'          => $value->id,
				'name'        => $value->name,
				'email'       => $value->email,
				'subject'     => $value->subject,
				'description' => $value->description,
				'version'     => $value->version,
				'link'        => $value->link,
			);
		}
		return $messages;
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
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Function to remove the cause
	 */
	public static function delete_causes() {
		if (
			isset( $_POST['id_cause'], $_GET['token'] )
			&& wp_verify_nonce( sanitize_key( $_GET['token'] ), 'token' )
			&& ! empty( $_POST['id_cause'] )
		) {
			global $wpdb;
			$table_name = $wpdb->get_blog_prefix() . 'causes';
			$wpdb->delete(
				$table_name,
				array(
					'id' => sanitize_text_field( wp_unslash( $_POST['id_cause'] ) ),
				)
			); // db call ok; no-cache ok.
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Function to delete a message
	 */
	public static function delete_messages() {
		if (
			isset( $_POST['id_message'], $_GET['token'] )
			&& wp_verify_nonce( sanitize_key( $_GET['token'] ), 'token' )
			&& ! empty( $_POST['id_message'] )
		) {
			global $wpdb;
			$table_name = $wpdb->get_blog_prefix() . 'feedback';
			$wpdb->delete(
				$table_name,
				array(
					'id' => sanitize_text_field( wp_unslash( $_POST['id_message'] ) ),
				)
			); // db call ok; no-cache ok.
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Token creation
	 */
	public static function create_token() {
		return wp_create_nonce( 'token' );
	}

	/**
	 * Function of sending to the server by S3
	 *
	 * @param string $image_directory The path to the file.
	 */
	private function send_to_server( $image_directory ) {
		$s3_protocol = S3Client::factory(
			array(
				'key'                     => $this->s3_configuration['key'],
				'secret'                  => $this->s3_configuration['secret'],
				'region'                  => $this->s3_configuration['region'],
				'version'                 => $this->s3_configuration['version'],
				'endpoint'                => $this->s3_configuration['endpoint'],
				'use_path_style_endpoint' => $this->s3_configuration['use_path_style_endpoint'],
			)
		);
		try {
			$s3_protocol->putObject(
				array(
					'Bucket' => $this->s3_configuration['bucket'],
					'Key'    => basename( $this->screenshot ),
					'Body'   => fopen( $image_directory, 'rb' ),
					'ACL'    => $this->s3_configuration['acl'],
				)
			);
		} catch ( S3Exception $e ) {
			echo 'Ошибка выполнения';
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
			$wpdb->prepare( "SELECT id, subject, email FROM {$wpdb->causes} WHERE id = %d", $this->cause )
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
			$this->send_to_server( $image_directory );
			$message_text = '
				<div>Имя: ' . $this->username . '</div>
				<div>Адрес электронной почты: ' . $this->email . '</div>
				<div>Причина: ' . $causes['subject'] . '</div>
				<div>Описание причины: ' . $this->description . '</div>
				<div>Версия браузера: ' . $browser_version . '</div>
			';
			add_filter(
				'wp_mail_content_type',
				function() {
					return 'text/html';
				}
			);
			$sending_letter = wp_mail( $causes['email'], $causes['subject'], $message_text, 'text/html', $image_directory );
			remove_filter(
				'wp_mail_content_type',
				function() {
					return 'text/html';
				}
			);
			if ( $sending_letter ) {
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
