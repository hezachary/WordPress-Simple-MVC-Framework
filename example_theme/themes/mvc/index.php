<?php
/**
 * The main template file.
 *
 * @package 
 * @subpackage 
 * @since 
 */
if(!$post){
    $post = get_page_by_path('home');
}
echo mvc::app()->run('page', $post);
?>
