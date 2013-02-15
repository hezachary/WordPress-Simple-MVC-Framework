<?php
/**
 * Disabled by Zac He @ eborn
 *
 * @package WordPress
 * @subpackage MVC
 * @since Wordpress Simple MVC 1.0
 */

$objPost = new stdClass();
$objPost->post_name = '404';
echo mvc::app()->run('page', $objPost);