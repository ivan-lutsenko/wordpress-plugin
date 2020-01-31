<?php
/**
 * Feedback plugin WordPress
 *
 * @category Plugin
 * @package  WordPress plugin
 */

/*
Plugin Name: Feedback WordPress
Description: Feedback plugin WordPress
Version: 1.0
Author: Ivan Lutsenko
*/

require_once 'classes/class-feedback.php';
require_once ABSPATH . 'wp-admin/includes/upgrade.php';

register_activation_hook( __FILE__, array( 'Feedback', 'activation_feedback' ) );
register_deactivation_hook( __FILE__, array( 'Feedback', 'deactivation_feedback' ) );
add_action( 'init', array( 'Feedback', 'init_plugin' ) );
