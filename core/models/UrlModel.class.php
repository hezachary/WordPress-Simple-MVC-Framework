<?php
class UrlModel extends ModelBase{
    
    public static $aryRouterList = array();
    public static function loadRule($aryRouterList){
        self::$aryRouterList = $aryRouterList;
        add_filter('rewrite_rules_array', array('UrlModel','setRule'));
        add_filter('query_vars', array('UrlModel','insertQueryVars'));
    }
    
    public static function setRule($orgRules){
        $orgRules = self::$aryRouterList[0] + $orgRules;
        return $orgRules;
    }
    
    // Adding the id var so that WP recognizes it
    public static function insertQueryVars( $vars ){
        foreach(self::$aryRouterList[1] as $strCustomVar){
            array_push($vars, $strCustomVar);
        }
        return $vars;
    }
}