<?php
class PageController extends ControllerBase{
    public $strTemplateName = 'page.default.tpl';
        
    public static function load($objPost, $blnAjax){
        return parent::load($objPost, $blnAjax, array('Page', $objPost->post_name, 'Controller'), get_class());
    }
    
    public function index(){
        if($strClassName){
            $this->$strClassName->index();
        }
    }
    
    public function process(){
        
    }
    
}