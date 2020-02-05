<?php
/**
 * Feedback plugin WordPress
 *
 * @category Plugin
 * @package  WordPress plugin
 */

?>

<div id="app"></div>

<?php
	wp_enqueue_style( 'page-design', '/wp-content/plugins/feedback/templates/css/style.css', '', '2.0', false );
	wp_enqueue_script( 'react.js', '/wp-content/plugins/feedback/templates/js/react.js', '', '2.0', true );
	wp_enqueue_script( 'react-dom.js', '/wp-content/plugins/feedback/templates/js/react-dom.js', '', '2.0', true );
	wp_enqueue_script( 'axios.js', '/wp-content/plugins/feedback/templates/js/axios.js', '', '2.0', true );
	wp_enqueue_script( 'babel.js', '/wp-content/plugins/feedback/templates/js/babel.js', '', '2.0', true );
	wp_enqueue_script( 'feedback-admin.js', '/wp-content/plugins/feedback/templates/js/feedback-admin.js', '', '5.0', true );
?>
