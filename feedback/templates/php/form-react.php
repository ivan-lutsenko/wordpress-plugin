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
$wpdb->causes    = $wpdb->get_blog_prefix() . 'causes';
$quantity_causes = intval(
	$wpdb->get_var( $wpdb->prepare( "SELECT id, subject, email FROM {$wpdb->causes} WHERE %d", 1 ) )
); // db call ok; no-cache ok.
?>

<div>
	<?php if ( 0 === $quantity_causes ) : ?>
		Добавьте Ваши причины в админке
	<?php else : ?>
		<div id="app"></div>
		<?php
			wp_enqueue_script( 'react.js', '/wp-content/plugins/feedback/templates/js/react.js', '', '2.0', true );
			wp_enqueue_script( 'react-dom.js', '/wp-content/plugins/feedback/templates/js/react-dom.js', '', '2.0', true );
			wp_enqueue_script( 'babel.js', '/wp-content/plugins/feedback/templates/js/babel.js', '', '2.0', true );
			wp_enqueue_script( 'form-react.js', '/wp-content/plugins/feedback/templates/js/form-react.js', '', '5.0', true );
		?>
	<?php endif; ?>
</div>
