<?php
class NavWidget extends WidgetBase{
    
    public static function main($source, $strTemplate = null, $blnAjax = false, $blnSuccess = false, $aryExtra = array()){
        $aryNav = array(
            'home' => 'Home',
            'aboutus' => 'About Us',
        );
        return self::render(array('nav' => $aryNav, 'source' => $source), $strTemplate, $blnAjax, $blnSuccess, $aryExtra);
    }
}