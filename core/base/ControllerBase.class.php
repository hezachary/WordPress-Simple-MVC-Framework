<?php
/**
 * WPSMVC base control class.
 * 
 * @author Zhehai He <hezahcary@gmail.com>
 * @link N/A
 * @version 0.9
 * @package System
 */
class ControllerBase{
    const DIR_SEP = DIRECTORY_SEPARATOR;
    
    protected $_smarty;
    public $blnAjax;
    public $source_data;
    public $objParent;
    public $strExport;
    
    public function __construct($objData, $blnAjax = false){
        $this->blnAjax = $objData->source_data ? $objData->blnAjax : $blnAjax;
        $this->source_data = $objData->source_data ? $objData->source_data : $objData;
    }
    
    public static function slug2ClassName($arySlug){
        $arySlug = is_array($arySlug) ? $arySlug : array($arySlug);
        foreach($arySlug as $k => $strSlug){
            $arySlug[$k] = preg_replace('/\W/', '_', ucfirst(strtolower($strSlug)));
        }
        return implode('', $arySlug);
    }
    
    public static function load($objData, $blnAjax, $aryClassName, $strDefalutName = null){
        $strClassName = is_string($aryClassName) ? $aryClassName : self::slug2ClassName($aryClassName);
        $strDefalutName = function_exists('get_called_class') ? get_called_class() : $strDefalutName;
        if ($strClassName != $strDefalutName && class_exists($strClassName)) {
            return call_user_func_array(array($strClassName, 'load'), array($objData, $blnAjax, $strClassName));
        }else{
            return new $strDefalutName($objData, $blnAjax);
        }
    }
    
    public function loadExistTemplate($strTemplateFileName){
        $strTemplateFileName = mvc::app()->loadByPath(mvc::app()->view_path.self::DIR_SEP.$strTemplateFileName, 1);
        $this->strTemplateName = $strTemplateFileName ? basename($strTemplateFileName) : $this->strTemplateName;
    }
    
    public function render($blnStatic = true){
        $this->strExport = $blnStatic && $this->strExport ? $this->strExport : ($this->blnAjax ? $this->ajax() : $this->view());
        return $this->strExport;
    }
    
    public function smarty(){
        if(!($this->_smarty instanceof Smarty)){
            $this->_smarty = new Smarty();
            $this->_smarty->compile_dir = mvc::app()->getUploadPath() . self::DIR_SEP . 'compile';
            $this->_smarty->cache_dir = mvc::app()->getUploadPath() . self::DIR_SEP . 'cache';
            $this->_smarty->force_compile = false;
            $this->_smarty->force_cache = false;
            $this->_smarty->template_dir = mvc::app()->loadByPath(mvc::app()->view_path, true);
            
        }
        return $this->_smarty;
    }
    
    public function view($strTemplateName = null){
        $strTemplateName = $strTemplateName ? $strTemplateName : $this->strTemplateName;
        
        $this->smarty()->template_dir = dirname(mvc::app()->loadByPath(mvc::app()->view_path.self::DIR_SEP.$strTemplateName, 1));
        
        $this->smarty()->assign('SITEURL', site_url());
        $this->smarty()->assign('THEMEPATH', get_bloginfo('stylesheet_directory'));
        $this->smarty()->assign('this', $this->smarty());
        $this->smarty()->assign('objController', $this);
        $this->smarty()->assign(get_object_vars($this));
        
        return $strTemplateName ? $this->smarty()->fetch($strTemplateName) : '';
    }
    
    public $aryExtra = array();
    public $blnSuccess = false;
    public function ajax($strTemplateName = null){
        $objExport = new stdClass();
        
        $objExport->html = $this->view($strTemplateName);
        $objExport->success = $this->blnSuccess;
        
        if(is_array($this->aryExtra)){
            foreach($this->aryExtra as $strKey => $value){
                $objExport->$strKey = $value;
            }
        }
        
        return json_encode($objExport);
    }
}