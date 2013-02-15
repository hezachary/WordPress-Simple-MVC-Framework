<?php
/*
Template Name: Image Template
*/

$objPostQuery = new WP_Query(array('name' => $_REQUEST['_name'], 'post_status' => 'publish','post_type' => 'page', ));
$objPost = new stdClass();
if ( $objPostQuery->have_posts() ){
    global $post;
    $objPostQuery->the_post();
    $post->short_description = get_post_meta($post->ID, 'short_description', true);
    $post->article_image = get_post_meta($post->ID, 'article_image', true);
    $objPost = clone $post;
};
wp_reset_postdata();
mvc::app()->run('page', $objPost, false);


?>