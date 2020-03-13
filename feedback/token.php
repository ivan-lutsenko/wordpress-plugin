<?php
/**
 * Feedback plugin WordPress
 *
 * @category Plugin
 * @package  WordPress plugin
 */

/*
Сonnecting a WordPress file
*/
require_once '../../../wp-load.php';

echo esc_html( wp_create_nonce( 'token' ) );
