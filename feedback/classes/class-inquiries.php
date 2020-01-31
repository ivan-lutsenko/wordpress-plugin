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
		$table_name = $wpdb->get_blog_prefix() . 'causes';
		$results    = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM $table_name WHERE %d", 1 )
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
		$table_name = $wpdb->get_blog_prefix() . 'feedback';
		$results    = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM $table_name WHERE %d", 1 )
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
			&& wp_verify_nonce( sanitize_key( $_GET['tkn'] ), 'token' ) ) {
			if ( ! empty( $_POST['data']['subject'] ) && ! empty( $_POST['data']['email'] ) ) {
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
	}
	/**
	 * Function to remove the cause
	 */
	public static function delete_causes() {
		$_POST = json_decode( file_get_contents( 'php://input' ), true );
		if (
			isset( $_POST['data']['id'], $_GET['tkn'] )
			&& wp_verify_nonce( sanitize_key( $_GET['tkn'] ), 'token' ) ) {
			if ( ! empty( $_POST['data']['id'] ) ) {
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
	}
	/**
	 * Function to delete a message
	 */
	public static function delete_messages() {
		$_POST = json_decode( file_get_contents( 'php://input' ), true );
		if (
			isset( $_POST['data']['id'], $_GET['tkn'] )
			&& wp_verify_nonce( sanitize_key( $_GET['tkn'] ), 'token' ) ) {
			if ( ! empty( $_POST['data']['id'] ) ) {
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
	}
}
