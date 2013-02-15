<?php
class PageController extends ControllerBase{
    public $strTemplateName = 'page.default.tpl';
    
    public static $aryDefinedPage = array(
        'home','about-colgate','sitemap','404','sitemap_xml','robots',
    );
    public static function load($objPost, $blnAjax){
        $strRouter = $objPost->post_name;
        $category = array_pop(get_the_category($objPost->ID));
        switch(true){
            case in_array($objPost->post_name, self::$aryDefinedPage):
                break;
            case in_array($category->slug, array('article','product')):
                $strRouter = $category->slug;
                break;
            case !(int)trim(get_post_meta($objPost->ID, 'viewable', true)):
                $strRouter = 'List';
                break;
        }
        return parent::load($objPost, $blnAjax, array('Page', $strRouter, 'Controller'), get_class());
    }
    
    public function index(){
        $this->loadExistTemplate(sprintf('page.%s.tpl', preg_replace('/\W/', '_', $this->source_data->post_name)));
    }
    
    public function process(){
        
    }
    
}