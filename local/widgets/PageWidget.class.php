<?php
class PageWidget extends WidgetBase{
    public static $aryNav = array();
    public static $aryPostList = array();
    public static $intHomeId = null;
    public static $intSelectedPostId = null;
    
    public static function main($source, $strTemplate = null, $blnAjax = false, $blnSuccess = false, $aryExtra = array()){
        $objPage = get_page_by_path($source);
        return self::render(array('page' => $objPage), $strTemplate, $blnAjax, $blnSuccess, $aryExtra);
    }
    
    public static $arySiteSettings = null;
    public static function settings($source, $strTemplate = null, $blnAjax = false, $blnSuccess = false, $aryExtra = array()){
        if(!self::$arySiteSettings){
            /*If you add the setting page in cms, you can also add the retrieve method in Page Model*/ 
            //self::$arySiteSettings = PageModel::retrieveSettings();
        }
        return self::render(self::$arySiteSettings, $strTemplate, $blnAjax, $blnSuccess, $aryExtra);
    }
}