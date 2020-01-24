<?php global $wpdb; ?>

<?php
    $table_name = $wpdb->get_blog_prefix() . 'causes';
	$total = intval($wpdb->get_var(
		"SELECT * FROM $table_name"
    ));
?>

<div>
    <?php if ($total == 0): ?>
        Добавьте Ваши причины в админке
    <?php else: ?>
        <div id="app"></div>
        <script src="/wp-content/plugins/feedback/templates/js/react.js"></script>
        <script src="/wp-content/plugins/feedback/templates/js/react-dom.js"></script>
        <script src="/wp-content/plugins/feedback/templates/js/babel.js"></script>

        <script src="/wp-content/plugins/feedback/templates/js/form-react.js" type="text/babel"></script>
    <?php endif; ?>
</div>
