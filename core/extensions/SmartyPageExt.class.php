<?php
class SmartyPageExt extends Smarty {
    function SmartyPageExt(){
        parent::__construct();
        
        $aryUploadPath = wp_upload_dir();
        $this->setCompileDir(mvc::app()->getUploadPath() . mvc::DIR_SEP . 'compile');
        $this->setCacheDir(mvc::app()->getUploadPath() . mvc::DIR_SEP . 'cache');
        $this->force_compile = false;
        $this->force_cache = false;
        $this->setTemplateDir(mvc::app()->loadByPath(mvc::app()->view_path, true));
    }
}
?>