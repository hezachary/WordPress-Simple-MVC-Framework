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
    const DIR_SEP = DIRECTORY_SEPARATOR;
    public $view_path;
    public $strMvcRootPath = array();
    public $aryPathList = array();
    public $aryClassDefine = array();
    
    public function loadConfig(){
        $this->strMvcRootPath = dirname(__FILE__);
        $this->view_path = require_once(dirname(__FILE__) . self::DIR_SEP .'config' . self::DIR_SEP . 'views_path.config.php');
        $this->aryPathList = require_once(dirname(__FILE__) . self::DIR_SEP .'config' . self::DIR_SEP . 'path.config.php');
        $this->aryClassDefine = require_once(dirname(__FILE__) . self::DIR_SEP .'config' . self::DIR_SEP . 'class_path.config.php');
        
        $this->setProjectSecurity(require_once(dirname(__FILE__) . self::DIR_SEP .'config' . self::DIR_SEP . 'project_security.config.php'));
    }
    
    public function loadByPath($strLoadPath, $blnReturnPath = false, $blnOnceOnly = true){
        foreach($this->aryPathList as $strPath){
            $strFullPath = $this->strMvcRootPath . self::DIR_SEP .$strPath . self::DIR_SEP .$strLoadPath;
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
        if(!$this->loadByPath($strBasePath . self::DIR_SEP .$strClassName.'.class.php')){
            return $this->loadFile($strBasePath . self::DIR_SEP .strtolower(array_shift($aryClassName)), $aryClassName, $strClassBase);
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
                    $strFullPath = $dir . self::DIR_SEP .$file;
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
                if($strCurrentFileHtaccess != $fileHtaccess && $fileHtaccess) copy($fileHtaccess, $dir . self::DIR_SEP .'.htaccess');
                if($strCurrentFileIndexHtml != $fileIndexHtml && $fileIndexHtml) copy($fileIndexHtml, $dir . self::DIR_SEP .'index.html');
                foreach($aryDirList as $strDir){
                    $this->resetProjectSecurity($strDir, $fileHtaccess, $fileIndexHtml);
                }
                closedir($dh);
            }
        }
    }
}
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR .'core' . DIRECTORY_SEPARATOR .'mvc.class.php');


