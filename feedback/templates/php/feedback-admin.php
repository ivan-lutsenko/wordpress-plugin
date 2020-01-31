<?php
/**
 * Feedback plugin WordPress
 *
 * @category Plugin
 * @package  WordPress plugin
 */

?>

<div id="app"></div>
<div id="tkn" style="display: none;"><?php echo esc_html( wp_create_nonce( 'token' ) ); ?></div>

<?php
	wp_enqueue_style( 'feedback-admin-style', '/wp-content/plugins/feedback/templates/css/style.css', '', '2.0', false );
	wp_enqueue_script( 'feedback-admin-1', '/wp-content/plugins/feedback/templates/js/react.js', '', '2.0', true );
	wp_enqueue_script( 'feedback-admin-2', '/wp-content/plugins/feedback/templates/js/react-dom.js', '', '2.0', true );
	wp_enqueue_script( 'feedback-admin-3', '/wp-content/plugins/feedback/templates/js/axios.js', '', '2.0', true );
	wp_enqueue_script( 'feedback-admin-4', '/wp-content/plugins/feedback/templates/js/babel.js', '', '2.0', true );
	wp_enqueue_script( 'feedback-admin-5', '/wp-content/plugins/feedback/templates/js/feedback-admin.js', '', '2.0', true );
?>
