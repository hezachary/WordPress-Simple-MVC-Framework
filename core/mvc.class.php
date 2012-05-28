<?php
/**
 * WPSMVC app entry file.
 *
 * MIT Copyright (c) 2012 Zhehai He <hezahcary@gmail.com>
 * 
 * @author Zhehai He <hezahcary@gmail.com>
 * @link N/A
 * @version 0.9
 * @package System
 */
class mvc extends mvc_ini{
    public $upload_base_path;
    public $request;
    public $is_moblie;
    
    public static $_instance;
    
    public static function app(){
        if(!(self::$_instance instanceof self)){
            self::$_instance = new self();
            self::$_instance->router = basename($_REQUEST['r']);
            self::$_instance->loadConfig();
            
            $aryUploadPath = wp_upload_dir();
            self::$_instance->upload_base_path = $aryUploadPath['basedir'];
            
            self::$_instance->is_moblie = MobileDectectExt::checkMobile();
        }
        return self::$_instance;
    }
    
    public function run($strType, $objData = null, $blnAjax = false){
        if($strType){
            $objControler = ControllerBase::load($objData, $blnAjax, array($strType, 'Controller'));
            if($this->router && method_exists($objControler, $this->router)){
                call_user_func_array(array($objControler, $this->router), $this->parseMethod(get_class($objControler), $this->router));
            }else{
                $objControler->index();
            }
            return $objControler->render();
        }else{
            return false;
        }
    }
    
    private function parseMethod($class, $method){
        $objClass = new ReflectionMethod($class, $method);
        $strSource = '_GET';
        $blnPacked = false;
        $aryParamPreset = array();
        if(preg_match_all('/\@(.+)/', $objClass->getDocComment(), $aryMatchList)){
            foreach($aryMatchList[1] as $strMatch){
                $strKeyword = trim(strtok(trim($strMatch), '$'));
                switch($strKeyword){
                    case 'source':
                        $strSource = trim(strtok(' '));
                        break;
                    case 'packed':
                        $blnPacked = true;
                        break;
                    case 'param':
                        $aryParamPreset[trim(strtok(' '))] = trim(strtok(' '));
                        break;
                }
            }
        }
        $aryParameterList = $objClass->getParameters();
        $aryValueList = array();
        $arySourceList = array();
        foreach($aryParameterList as $objParameter){
            if($blnPacked){
                $strSourceTmp = '_'.strtoupper($objParameter->name);
                $aryValueList[] = $GLOBALS[$strSourceTmp];
                $arySourceList[] = $strSourceTmp;
            }else{
                if($objParameter->isArray()){
                    $aryParamPreset[$objParameter->name] = 'array';
                }else{
                    $strType = strtok($objParameter->__toString(), '[');
                    $strType = trim(strip_tags(strtok('$')));
                    $aryParamPreset[$objParameter->name] = $strType ? $strType : $aryParamPreset[$objParameter->name];
                }
                if(!$objParameter->getClass() && $aryParamPreset[$objParameter->name]){
                    settype($GLOBALS[strtoupper($strSource)][$objParameter->name], $aryParamPreset[$objParameter->name]);
                }
                
                $aryValueList[] = $GLOBALS[strtoupper($strSource)][$objParameter->name];
            }
        }
        return array($aryValueList, $blnPacked, $arySourceList);
    }
    
    public function getUploadPath(){
        return $this->upload_base_path;
    }
    
    public function loadClassByPath($strFileId){
        $strFileId = str_replace('.', self::DIR_SEP, basename($strFileId)); 
        return $this->loadByPath($strFileId.'.class.php');
    }
    
    private $blnLibClassPathIncluded = false;
    public function loadClass($classname){
        if(!$this->blnLibClassPathIncluded){
            set_include_path($this->loadByPath('libs', 1));
            $this->blnLibClassPathIncluded = true;
        }
        $aryClassName = explode('_', $classname);
        switch($aryClassName[0]){
            case 'Zend':
                $this->loadByPath('libs' . self::DIR_SEP . 'Zend' . self::DIR_SEP . 'Loader.php');
                Zend_Loader::loadClass($classname, $this->loadByPath('libs', 1));
                break;
            case 'Smarty':
                $this->loadByPath('libs' . self::DIR_SEP . 'Smarty' . self::DIR_SEP . 'Smarty.class.php');
                break;
            case 'Facebook':
                $this->loadByPath('libs' . self::DIR_SEP . 'Facebook' . self::DIR_SEP . 'facebook.php');
                break;
            default:
                foreach($this->aryClassDefine as $strClassBase => $strBasePath){
                    if(substr($classname, 0 - strlen($strClassBase)) == $strClassBase){
                        preg_match_all('/[A-Z][^A-Z]+/', $classname, $aryResult);
                        $this->loadFile($strBasePath, $aryResult[0], $strClassBase);
                        break;
                    }
                }
                break;
        }
    }
      
    // Do not allow an explicit call of the constructor: $v = new Singleton();
    final protected function __construct() { }

    // Do not allow the clone operation: $x = clone $v;
    final protected function __clone() { }
}

function mvc_class_autoload($classname){
    mvc::app()->loadClass($classname);
}

spl_autoload_register('mvc_class_autoload');