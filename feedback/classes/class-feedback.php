<?php
/**
 * Feedback plugin WordPress
 *
 * @category Plugin
 * @package  WordPress plugin
 */

/**
 * Class initialization with hooks
 */
class Feedback {
	/**
	 * Plugin initialization
	 */
	public function init_plugin() {
		add_action( 'admin_menu', array( 'Feedback', 'create_menu' ) );
		add_shortcode( 'feedback', array( 'Feedback', 'create_shortcode' ) );
	}
	/**
	 * Creating a menu in the admin panel
	 */
	public function create_menu() {
		add_menu_page(
			'Сообщения',
			'Обратная связь',
			'manage_options',
			'new-feedback-menu',
			array( 'Feedback', 'menu_output' ),
			'dashicons-testimonial',
			25
		);
	}
	/**
	 * Initialization of the page in the admin pane
	 */
	public function menu_output() {
		include plugin_dir_path( __FILE__ ) . '../templates/php/feedback-admin.php';
	}
	/**
	 * Shortcode creation
	 */
	public function create_shortcode() {
		ob_start();
		include plugin_dir_path( __FILE__ ) . '../templates/php/form-react.php';
		$buffer = ob_get_clean();
		return $buffer;
	}
	/**
	 * Plugin activation function
	 */
	public function activation_feedback() {
		global $wpdb;
		$table_name_1    = $wpdb->get_blog_prefix() . 'causes';
		$table_name_2    = $wpdb->get_blog_prefix() . 'feedback';
		$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";
		$sql             = "CREATE TABLE {$table_name_1} (
			id BIGINT NOT NULL AUTO_INCREMENT,
			subject TEXT NOT NULL,
			email TEXT NOT NULL,
			PRIMARY KEY (id)
		){$charset_collate};";
		$sql            .= "CREATE TABLE {$table_name_2} (
			id BIGINT NOT NULL AUTO_INCREMENT,
			name TEXT NOT NULL,
			email TEXT NOT NULL,
			subject TEXT NOT NULL,
			description TEXT NOT NULL,
			version TEXT NOT NULL,
			link TEXT NOT NULL,
			PRIMARY KEY (id)
		){$charset_collate};";
		dbDelta( $sql );
	}
	/**
	 * Plugin deactivation function
	 */
	public function deactivation_feedback() {
		remove_shortcode( 'feedback' );
		global $wpdb;
		$table_name_1 = $wpdb->get_blog_prefix() . 'causes';
		$sql          = "DROP TABLE IF EXISTS $table_name_1;";
		$wpdb->query( $sql ); // db call ok; no-cache ok.
		$table_name_2 = $wpdb->get_blog_prefix() . 'feedback';
		$sql          = "DROP TABLE IF EXISTS $table_name_2;";
		$wpdb->query( $sql ); // db call ok; no-cache ok.
	}
}
