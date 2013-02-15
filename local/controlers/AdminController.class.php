<?php
class AdminController extends ControllerBase{
    public $strTemplateName = 'page.default.tpl';
        
    public static function load($objAdmin, $blnAjax){
        return parent::load($objAdmin, $blnAjax, array('Admin', $objAdmin->router, 'Controller'), get_class());
    }
    
    public function index(){
        if($strClassName){
            $this->$strClassName->index();
        }
    }
    
    public function process(){
        
    }
    
}