<?php
/**
 *     require_once (TEMPLATEPATH . '/class/smarty_string.class.php');
 *     $objSmartyString = new smarty_string();
 *     $objSmartyString->ary_tpl_source['post_title'] = $aryPost['post_title'];
 *     $objSmartyString->assign($aryAssignData);
 *     $strSubject = $objSmartyString->fetch('post_title');
 **/
class smarty_string extends Smarty {
    public $ary_tpl_source = array();
    function smarty_string(){
        global $CFG;
        $this->register_resource("string", array("string_get_template", "string_get_timestamp", "string_get_secure", "string_get_trusted"));
        $aryUploadPath = wp_upload_dir();
        $this->compile_dir    = $aryUploadPath['basedir'].'/compile';
        $this->register_resource('post', 
        	array(
        			'post_get_template',
							'post_get_timestamp', 
							'post_get_secure', 
							'post_get_trusted')
				);

        $this->register_resource('smarty_code', 
        	array(
        			'smartycode_get_template',
							'smartycode_get_timestamp', 
							'smartycode_get_secure', 
							'smartycode_get_trusted')
				);				
        parent::Smarty();
    }
    function fetch($string){
        return parent::fetch('string:'.$string);
    }
}
function string_get_template ($tpl_name, &$tpl_source, &$smarty_obj) {
    $tpl_source = $smarty_obj->ary_tpl_source[$tpl_name];
    //var_dump($tpl_source);
    //die();
    return true;
}
function string_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj) {
    // do database call here to populate $tpl_timestamp.
    $tpl_timestamp = time();
    return true;
}
function string_get_secure($tpl_name, &$smarty_obj) {
    // assume all templates are secure
    return true;
}
function string_get_trusted($tpl_name, &$smarty_obj) {
    // not used for templates
}


function post_get_template ($tpl_name, &$tpl_source, &$smarty_obj) {
		list($strPostSlug, $strFieldName) = explode('$', $tpl_name);
		$aryPost = live_cache::getPageData($strPostSlug, 'post');
		
		$aryFieldDefined = array_pad(explode(',', $strFieldName), 2, 0);
		$strContent = ($strFieldName != 'post_title' && $strFieldName != 'post_content') ? $aryPost['meta_list'][$aryFieldDefined[0]][$aryFieldDefined[1]] : $aryPost[$strFieldName];
    $tpl_source = $strContent;
    
    return true;
}
function post_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj) {
    // do database call here to populate $tpl_timestamp.
    $tpl_timestamp = time();
    return true;
}
function post_get_secure($tpl_name, &$smarty_obj) {
    // assume all templates are secure
    return true;
}
function post_get_trusted($tpl_name, &$smarty_obj) {
    // not used for templates
}


function smartycode_get_template ($tpl_name, &$tpl_source, &$smarty_obj) {
		list($strPostSlug, $strFieldName) = explode('$', $tpl_name);
		$aryPost = live_cache::getPageData($strPostSlug, 'smarty_code');
		
		$aryFieldDefined = array_pad(explode(',', $strFieldName), 2, 0);
		$strContent = ($strFieldName != 'post_title' && $strFieldName != 'post_content') ? $aryPost['meta_list'][$aryFieldDefined[0]][$aryFieldDefined[1]] : $aryPost[$strFieldName];
    $tpl_source = $strContent;
    
    return true;
}
function smartycode_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj) {
    // do database call here to populate $tpl_timestamp.
    $tpl_timestamp = time();
    return true;
}
function smartycode_get_secure($tpl_name, &$smarty_obj) {
    // assume all templates are secure
    return true;
}
function smartycode_get_trusted($tpl_name, &$smarty_obj) {
    // not used for templates
}
?>