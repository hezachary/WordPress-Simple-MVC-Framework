<?php
class PageSitemap_xmlController extends ControllerBase{
    public $strTemplateName = 'page.sitemap_xml.tpl';
    
    public static function load($objPage, $blnAjax, $aryClassName){
        return parent::load($objPage, $blnAjax, get_class(), get_class());
    }
    
    public function index(){
        define('NO_CACHE_FOOTER', true);
    }
    
}