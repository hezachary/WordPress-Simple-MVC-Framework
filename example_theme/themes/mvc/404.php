<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */
$objPost = new stdClass();
$objPost->post_name = '404';
echo mvc::app()->run('page', $objPost);
