<?php
class PageRobotsController extends ControllerBase{
    public $strTemplateName = 'page.robots.tpl';
    
    public static function load($objPage, $blnAjax, $aryClassName){
        return parent::load($objPage, $blnAjax, get_class(), get_class());
    }
    
    public function index(){
        define('NO_CACHE_FOOTER', true);
        $this->aryDisallowList = array();
        
        $objSearchResult = get_page_by_path('/home/search-results/');
        $arySearchResultUrl = parse_url(get_permalink($objSearchResult->ID));
        $this->aryDisallowList[] = $arySearchResultUrl['path'];
        $this->aryDisallowList[] = str_replace('/home/search-results/', '/wp-admin/', $arySearchResultUrl['path']);
        $this->aryDisallowList[] = str_replace('/home/search-results/', '/wp-includes/', $arySearchResultUrl['path']);
        $this->aryDisallowList[] = '/*function.';
        
        $objSitemap = get_page_by_path('/sitemap_xml');
        $this->strSitemapUrl = str_replace(array('sitemap_xml/', 'sitemap_xml'), 'sitemap.xml', get_permalink($objSitemap->ID));
        
    }
    
}