<?php
class PageHomeController extends ControllerBase{
    public $strTemplateName = 'page.home.tpl';
    
    public static function load($objPage, $blnAjax, $aryClassName){
        return parent::load($objPage, $blnAjax, array('Page', 'Home', 'Status', 'Controller'), get_class());
    }
    
    public function index(){
        if($strClassName){
            $this->$strClassName->index();
        }
        
    }
    
}