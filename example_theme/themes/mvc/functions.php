<?php
/**
 * MVC functions and definitions
 *
 */
require_once(dirname(__FILE__).'/framework/mvc.ini.php');
ImageModel::loadImageSize(mvc::app()->aryImageSizeList);
ImageModel::loadAdmin(mvc::app()->aryImageSizeList);
UrlModel::loadRule(mvc::app()->aryRouterList);
TaxonomyModel::loadTaxonomy(mvc::app()->aryTaxonomyList);

/**
 * For load image via cloudfront cdn
 * @usage:
 * $aryImageAttr = cdn_get_attachment_image_src($intImageId, $size);
 * echo '<img src="'.aryImageAttr[0].'" alt=""/>';
 **/
function cdn_get_attachment_image_src($intImageId, $size='thumbnail', $icon = false){
    return ImageModel::wp_get_attachment_image_src($intImageId, $size, $icon, mvc::app()->aryCDNSettings['cloudfront_host']);
}

/**
 * For easy debug
 **/
function _d($v, $s = true, $d=false){
    $debug_backtrace = debug_backtrace();
    ToolsExt::_d($v,$s,$d, $debug_backtrace);
}