<?php
class PageHomeStatusController extends ControllerBase{
    public $strTemplateName = 'page.home.tpl';
    
    public static function load($objPage, $blnAjax, $aryClassName){
        return parent::load($objPage, $blnAjax, get_class(), get_class());
    }
    
    public function filter($aryData, $strField){
        $strField = array_pop(explode('::', $strField));
        $aryControlFilterList = array(
            'form' => array(
                'password' => array(
                    array(
                    'options'   => 'password::{5,,32}',
                    ),
                    'msg'   => 'Password is not valid',
                ),
                'password_confirm' => array(
                    array(
                    'options'   => 'pair::password',
                    ),
                    'msg'   => 'Password is not match',
                ),
                'name' => array(
                    array(
                    'call_func' => array('DataValidateExt','clear_html_string'),
                    ),
                    array(
                    'filter'    => FILTER_SANITIZE_STRING,
                    'options'   => FILTER_FLAG_ENCODE_HIGH|FILTER_FLAG_ENCODE_LOW,
                    ),
                    array(
                    'filter'    => FILTER_VALIDATE_REGEXP,
                    'options'   => array('regexp' => '/^(\w+){3,30}$/'),
                    'msg'       => 'Name is not valid, 3 more charactors required',
                    ),
                    'msg'   => 'Name is not valid, a-z, 0-9, _, 5 ~ 30 charactors only',
                ),
                'email' => array(
                    array(
                    'filter'    => FILTER_SANITIZE_EMAIL,
                    ),
                    array(
                    'filter'    => FILTER_VALIDATE_EMAIL,
                    ),
                    array(
                    'call_func' => array('DataValidateExt','uniquEmail'),
                    'options'   => array(),
                    'msg'       => 'Email is not unique',
                    ),
                    'msg'   => 'Email is not valid',
                ),
                'permission' => array(
                    'optional',
                    array(
                    'filter'    => FILTER_VALIDATE_INT,
                    'flags'     => FILTER_FLAG_ALLOW_OCTAL,
                    'options'   => array('min_range' => 0),
                    ),
                    array(
                    'call_func' => array('DataValidateExt','validateField'),
                    'options'   => 'option::[0,,1]',
                    'msg'       => 'Permission is not in list',
                    ),
                    'msg'   => 'Permission is not valid',
                ),
            ),
        );
        
        return DataValidateExt::validate($aryData, $aryControlFilterList[$strField]);
    }
        
    /**
     * All Only
     * 
     **/
    public function index(){
        $this->strTemplateName = 'page.home.status.tpl';
    }
    
    /**
     * @source $_GET
     * @param $page_id int # you can only put native type here, no object type
     **/
    public function ajax($page_id){
        $this->strTemplateName = 'page.home.status.tpl';
    }
    
    /**
     * @packed
     **/
    public function form(array $post){//Inline area support auto convert array
        $this->strTemplateName = 'page.home.status.tpl';
        list($success, $aryMsg, $aryResultData) = $this->filter($post, __METHOD__);
    }
}