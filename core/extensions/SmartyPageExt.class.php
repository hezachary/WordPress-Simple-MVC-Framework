<?php
class SmartyPageExt extends Smarty {
    function SmartyPageExt(){
        $aryUploadPath = wp_upload_dir();
        $this->template_dir = dirname(__FILE__).'/../templates';
        $this->compile_dir  = $aryUploadPath['basedir'].'/compile';
        parent::Smarty();
    }
}
?>