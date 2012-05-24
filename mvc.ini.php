<?php
/**
 * WPSMVC app entry base methods and definitions
 *
 * MIT Copyright (c) 2012 Zhehai He <hezahcary@gmail.com>
 * 
 * @author Zhehai He <hezahcary@gmail.com>
 * @link N/A
 * @version 0.9
 * @package System
 */
class mvc_ini{
    public $view_path;
    public $strMvcRootPath = array();
    public $aryPathList = array();
    public $aryClassDefine = array();
    
    public function loadConfig(){
        $this->strMvcRootPath = dirname(__FILE__);
        $this->view_path = require_once(dirname(__FILE__).'/config/views_path.config.php');
        $this->aryPathList = require_once(dirname(__FILE__).'/config/path.config.php');
        $this->aryClassDefine = require_once(dirname(__FILE__).'/config/class_path.config.php');
        
        $this->setProjectSecurity(require_once(dirname(__FILE__).'/config/project_security.config.php'));
    }
    
    public function loadByPath($strLoadPath, $blnReturnPath = false, $blnOnceOnly = true){
        foreach($this->aryPathList as $strPath){
            $strFullPath = $this->strMvcRootPath.'/'.$strPath.'/'.$strLoadPath;
            if(file_exists($strFullPath)){
                if($blnReturnPath) return $strFullPath;
                $blnOnceOnly ? require_once($strFullPath) : include($strFullPath);
                return true;
            }
        }
        return false;
    }
    
    protected function loadFile($strBasePath, $aryClassName, $strClassBase){
        if(!is_array($aryClassName) || sizeof($aryClassName) < 1) return false;
        $strClassName = basename(implode('', $aryClassName));
        if(!$this->loadByPath($strBasePath.'/'.$strClassName.'.class.php')){
            return $this->loadFile($strBasePath.'/'.strtolower(array_shift($aryClassName)), $aryClassName, $strClassBase);
        }else{
            return true;
        }
    }
    
    private function setProjectSecurity($blnProjectSecurited){
        if(!$blnProjectSecurited){
            $this->resetProjectSecurity(dirname(__FILE__));
        }
    }
    
    private function resetProjectSecurity($dir, $fileHtaccess = null, $fileIndexHtml = null){
        $strCurrentFileHtaccess = null;
        $strCurrentFileIndexHtml = null;
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                $aryDirList = array();
                while (($file = readdir($dh)) !== false) {
                    $strFullPath = $dir.'/'.$file;
                    switch(true){
                        case $file == '.htaccess':
                            if($fileHtaccess === null) $fileHtaccess = $strFullPath;
                            $strCurrentFileHtaccess = $strFullPath;
                            break;
                        case $file == 'index.html':
                            if($fileIndexHtml === null) $fileIndexHtml = $strFullPath;
                            $strCurrentFileIndexHtml = $strFullPath;
                            break;
                        case is_dir($strFullPath) && !in_array($file, array('.','..')):
                            $aryDirList[] = $strFullPath;
                            break;
                    }
                }
                if($strCurrentFileHtaccess != $fileHtaccess && $fileHtaccess) copy($fileHtaccess, $dir.'/.htaccess');
                if($strCurrentFileIndexHtml != $fileIndexHtml && $fileIndexHtml) copy($fileIndexHtml, $dir.'/index.html');
                foreach($aryDirList as $strDir){
                    $this->resetProjectSecurity($strDir, $fileHtaccess, $fileIndexHtml);
                }
                closedir($dh);
            }
        }
    }
}
require_once(dirname(__FILE__).'/core/mvc.class.php');


