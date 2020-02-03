<?php
/**
 * Feedback plugin WordPress
 *
 * @category Plugin
 * @package  WordPress plugin
 */

?>

<?php global $wpdb; ?>

<?php
$wpdb->causes = $wpdb->get_blog_prefix() . 'causes';
$total        = intval(
	$wpdb->get_var( $wpdb->prepare( "SELECT * FROM {$wpdb->causes} WHERE %d", 1 ) )
); // db call ok; no-cache ok.
?>

<div>
	<?php if ( 0 === $total ) : ?>
		Добавьте Ваши причины в админке
	<?php else : ?>
		<div id="app"></div>
		<div id="tkn" style="display: none;"><?php echo esc_html( wp_create_nonce( 'token' ) ); ?></div>
		<?php
			wp_enqueue_script( 'form-react-1', '/wp-content/plugins/feedback/templates/js/react.js', '', '2.0', true );
			wp_enqueue_script( 'form-react-2', '/wp-content/plugins/feedback/templates/js/react-dom.js', '', '2.0', true );
			wp_enqueue_script( 'form-react-3', '/wp-content/plugins/feedback/templates/js/babel.js', '', '2.0', true );
			wp_enqueue_script( 'form-react-4', '/wp-content/plugins/feedback/templates/js/form-react.js', '', '3.0', true );
		?>
	<?php endif; ?>
</div>
