<?php
/**
 * WPSMVC base widget class.
 * 
 * @author Zhehai He <hezahcary@gmail.com>
 * @link N/A
 * @version 0.9
 * @package System
 */
class WidgetBase{
    protected static $_smarty;
    
    public static function render($data, $strTemplate, $blnAjax = false, $blnSuccess = false, $aryExtra = array()){
        return $strTemplate ? ($blnAjax ? self::ajax($strTemplate, $data, $blnSuccess, $aryExtra) : self::view($strTemplate, $data)) : $data;
    }
    
    public static function smarty(){
        if(!(self::$_smarty instanceof Smarty)){
            self::$_smarty = new Smarty();
            self::$_smarty->compile_dir = mvc::app()->getUploadPath() . '/compile';
            self::$_smarty->cache_dir = mvc::app()->getUploadPath() . '/cache';
            self::$_smarty->force_compile = true;
        }
        return self::$_smarty;
    }
    
    public function view($strTemplateName = null, $data){
        self::smarty()->template_dir = dirname(mvc::app()->loadByPath(mvc::app()->view_path.'/'.$strTemplateName), 1);
        self::smarty()->assign($data);
        return self::smarty()->fetch($strTemplateName);
    }
    
    public function ajax($strTemplateName = null, $data, $blnSuccess = false, $aryExtra = array()){
        $objExport = new stdClass();
        
        $objExport->html = self::view($strTemplateName, $data);
        $objExport->success = $blnSuccess;
        
        if(is_array($aryExtra)){
            foreach($aryExtra as $strKey => $value){
                $objExport->$strKey = $value;
            }
        }
        
        return json_encode($objExport);
    }
}