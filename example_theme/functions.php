<?php
/**
 * MVC functions and definitions
 *
 */
require_once(dirname(__FILE__).'/framework/mvc.ini.php');

function _d($v, $s = true, $d=false){
    $debug_backtrace = debug_backtrace();
    ToolsExt::_d($v,$s,$d, $debug_backtrace);
}