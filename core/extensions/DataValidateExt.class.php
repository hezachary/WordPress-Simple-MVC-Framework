<?php
class DataValidateExt{
    
    /**
     * @Define This is the defalut validation entry.
     * 
     * @paramater $aryData array, source $_GET/$_POST/input_array 
     * Example:
     *  $aryData = array(
     *      'name'  => 'Zachary',
     *      'email' => 'peter@reborn.com',
     *      'password' => '123456',
     *      'password_confirm' => '123456',
     *      'permission' => '1',
     *  );
     * 
     * 
     * @paramater $arySettingList array
     * Base format:
     * -- $arySettingList = array(
     *       'field_name' => array(
     *           [Array of attributes],
     *           'msg'   => Default message, if not pass the validation,
     *           'optional', <-- only put the value, when the field is an optional field; without the value, the field will be take as required field
     *       ),
     *    );
     * 
     * [Array of attributes] Base format:
     * -- [Array of attributes] = array(
     *      attributes_1,
     *      attributes_2,
     *      ...,
     *      'msg' => Specify message, if not pass current validation <-- it is not a required field
     *   ),
     * 
     * [Array of attributes] Accept:
     * 1. Default Validating, please read the comments of self::validateField for detail
     *    For the options of range define, please read the comments of self::rangeValidation for detail
     * -- Example:
     *    array(
     *      'options'   => 'password::{5,,32}',
     *    ),
     *    array(
     *      'options'   => 'pair::password', <-- pair is the action, password is the compare field name
     *    ),
     *    array(
     *      'options'   => 'text::{1,,8000}',
     *    ),
     *    array(
     *      'options'   => 'option::[male,,female]',
     *    ),
     *    array(
     *      'options'   => 'option::[1,,2,,3,,4]',
     *    ),
     * 2. PHP Data Filtering, please read http://www.php.net/manual/en/book.filter.php
     * -- Example:
     *    array(
     *      'filter'    => FILTER_SANITIZE_STRING,
     *      'options'   => FILTER_FLAG_ENCODE_HIGH|FILTER_FLAG_ENCODE_LOW,
     *    ),
     *    array(
     *      'filter'    => FILTER_VALIDATE_REGEXP,
     *      'options'   => array('regexp' => '/^(\w+){3,30}$/'),
     *      'msg'       => 'Field is not valid, 3 more charactors required',
     *    ),
     * 3. User custom validation, the format will triger php call_user_func_array, please read http://www.php.net/manual/en/function.call-user-func-array.php
     * -- Base format:
     *    array(
     *      'call_func' => the callable callback field for call_user_func_array (the first attribute for call_user_func_array),
     *      'options'   => extra value need to pass into custom validation,
     *      'msg'       => Specify message, if not pass current validation <-- it is not a required field
     *    ),
     * -- User custom validation method/function paramaters format:
     *    function User_custom_validation(field_value, options_from_define, aryData){
     *      //field_value is from aryData[field_name]
     *      //options_from_define is from above 'options'
     *      //aryData is from @paramater $aryData
     *      return data if pass the validation, otherwise, return false
     *    }
     * -- Example (field name [email]):
     *    array(
     *      'call_func' => array('DataValidateExt','uniquEmail'),
     *      'options'   => array('mode' => 'test'),
     *      'msg'       => 'Email is not unique',
     *    ),
     *    will triger to call: DataValidateExt::uniquEmail($aryData['email'], array('mode' => 'test'), $aryData);
     * 
     * 
     * @return array((boolean)success, array of messages, array of filtered data)
     * -- Data retrieve suggest: list(), please read http://www.php.net/manual/en/function.list.php
     * -- Example:
     *    list($success, $aryMsg, $aryResultData) = DataValidateExt::validate($aryData, $arySettingList);
     * 
     **/
    public static function validate($aryData, $arySettingList){
        $aryReturn = array();
        $aryMsg = array();
        foreach($arySettingList as $fieldName => $arySubSettingList){
            if(in_array('optional', $arySubSettingList)
               && (
                !isset($aryData[$fieldName])
                || $aryData[$fieldName] === null
                || (is_string($aryData[$fieldName]) && !strlen($aryData[$fieldName]))
                || (is_array($aryData[$fieldName]) && !strlen(implode('', $aryData[$fieldName])))
               )
            ) continue;
            
            $aryReturn[$fieldName] = is_string($aryData[$fieldName]) ? trim($aryData[$fieldName]) : $aryData[$fieldName];
             
            foreach($arySubSettingList as $key => $arySetting){
                if(!is_array($arySetting)) continue;
                
                $tmpData = false;
                if($arySetting['filter']){
                    $tmpData = filter_var($aryReturn[$fieldName], $arySetting['filter'], $arySetting);
                }elseif($arySetting['call_func']){
                    $tmpData = call_user_func_array($arySetting['call_func'], array($aryReturn[$fieldName], $arySetting['options'], $aryData));
                }else{
                    $tmpData = self::validateField($aryReturn[$fieldName], $arySetting['options'], $aryData);
                }
                
                if($tmpData === false){
                    $strMsg = $arySetting['msg'] ? $arySetting['msg'] : $arySubSettingList['msg'];
                    if($strMsg) $aryMsg[$fieldName] = $strMsg;
                    break;
                }else{
                    $aryReturn[$fieldName] = $tmpData;
                }
            }
        }
        
        return array(sizeof($aryMsg) ? false: true, $aryMsg, $aryReturn);
    }
    
    protected static function uniquEmail($strFieldValueOrg, $strFieldSettings = null, $aryFieldList = array()){
        return $strFieldValueOrg == 'zac@reborn.com' ? false : $strFieldValueOrg;
    }
    
    /**
     * Validate Field
     * @pramaters $strFieldValueOrg string  
     * Base format:
     * strMode::expression
     * -- strMode
     *      -- option   expression = [option_1,,option_2,,option_3,,...]
     *      -- password expression = {string_length_min,,string_length_max}, min & max can become '~'
     *      -- text     expression = {string_length_min,,string_length_max}, min & max can become '~' 
     *      -- pair     expression = the name of the field to compare 
     *      -- phone    expression = N/A
     *      -- date     expression = {date_string_1,,date_string_2}
     *          -- example: date::{2012-01-30,,~}  - since 30/01/2012
     *          -- example: date::{~,,yesterday}   - till yesterday
     *          -- example: date::{2012-01-30,,2012-02-28} - from 2012-01-30 00:00:00 to 2012-02-28 00:00:00
     * @return value/false
     **/
    protected static function validateField($strFieldValueOrg, $strFieldSettings, $aryFieldList = array()){
        $strMode = strtok($strFieldSettings, '::');
        $strValidateFieldSettings = strtok('::');
        $strFieldValue = false;
        switch($strMode){
            case 'option':
                $strFieldValue = self::rangeValidation($strFieldValueOrg, $strValidateFieldSettings) ? $strFieldValueOrg :  false;
                break;
            case 'password':
                $strFieldValueOrg = trim(htmlentities(strip_tags($strFieldValueOrg), ENT_COMPAT, 'UTF-8'));
                $strFieldValue = self::rangeValidation(strlen($strFieldValueOrg), $strValidateFieldSettings) ? $strFieldValueOrg :  false;
                break;
            case 'text':
                $strFieldValueOrg = trim(htmlentities(strip_tags($strFieldValueOrg), ENT_COMPAT, 'UTF-8'));
                $strFieldValue = self::rangeValidation(strlen($strFieldValueOrg), $strValidateFieldSettings) ? $strFieldValueOrg :  false;
                break;
            case 'pair':
                $strFieldValue = $strFieldValueOrg == $aryFieldList[$strValidateFieldSettings] ? $strFieldValueOrg : false;
                break;
            case 'date':
                if(strtotime($strFieldValueOrg)){
                    $strFieldValue = $strFieldValueOrg;
                    if($strFieldSettings){
                        $strSign = substr($strValidateFieldSettings, 0, 1).substr($strValidateFieldSettings, -1, 1);
                        $aryRange = explode(',,', substr($strValidateFieldSettings, 1, strlen($strValidateFieldSettings) - 2));
                        foreach($aryRange as $key => $strDate){
                            if($strDate != '~'){
                                $aryRange[$key] = strtotime($strDate);
                            }
                        }
                        $strValidateFieldSettings = $strSign{0}.implode(',,', $aryRange).$strSign{1};
                        $strFieldValue = self::rangeValidation(strtotime($strFieldValueOrg), $strValidateFieldSettings) ? $strFieldValueOrg : false;
                    }
                }
                break;
            case 'phone':
                $phoneReg = "/^(\+\d{2}[ \-]{0,1}){0,1}(((\({0,1}[ \-]{0,1})0{0,1}\){0,1}[2|3|7|8]{1}\){0,1}[ \-]*(\d{4}[ \-]{0,1}\d{4}))|(1[ \-]{0,1}(300|800|900|902)[ \-]{0,1}((\d{6})|(\d{3}[ \-]{0,1}\d{3})))|(13[ \-]{0,1}([\d \-]{5})|((\({0,1}[ \-]{0,1})0{0,1}\){0,1}4{1}[\d \-]{8,10})))$/";
                $strFieldValue = preg_match($phoneReg, $strFieldValueOrg) ? $strFieldValueOrg : false;
                break;
        }
        return $strFieldValue;
    }
    
    /**
     * validate range
     * Format: 
     * $strRangeSettings = ?    -- 1/0
     * $strRangeSettings = +    -- 1 and greater
     * $strRangeSettings = *    -- 0 and greater
     * $strRangeSettings = [Yes,,No,,Else]   -- option list, defined by '[]', split by ',,'
     * $strRangeSettings = {1,,255}   -- range validate, defined by '{}', split by ',,' 
     * $strRangeSettings = {1,,~}   -- range validate, defined by '{}', split by ',,', '~' means any
     * $strRangeSettings = {~,,255}   -- range validate, defined by '{}', split by ',,', '~' means any
     * 
     * $value int(for range)/string(for option)
     * 
     * return true/false
     **/
    protected static function rangeValidation($value, $strRangeSettings){
        $blnReturn = false;
        switch($strRangeSettings){
            case '?':
                if($value == 0 || $value == 1){
                    $blnReturn = true;
                }
                break;
            case '+':
                if($value > 0){
                    $blnReturn = true;
                }
                break;
            case '*':
                if($value > -1){
                    $blnReturn = true;
                }
                break;
            default:
                $strSign = substr($strRangeSettings, 0, 1).substr($strRangeSettings, -1, 1);
                $aryRange = explode(',,', substr($strRangeSettings, 1, strlen($strRangeSettings) - 2));
                switch($strSign){
                    case '{}'://range
                        if($aryRange[0] == '~' && $value <= $aryRange[1]){
                            $blnReturn = true;
                        }else if($aryRange[0] <= $value && $aryRange[1] == '~'){
                            $blnReturn = true;
                        }else if($aryRange[0] <= $value && $value <= $aryRange[1]){
                            $blnReturn = true;
                        }
                        break;
                    case '[]'://option
                        if(in_array($value, $aryRange)){
                            $blnReturn = true;
                        }
                        break;
                }
                break;
        }
        return $blnReturn;
    }
    
    /**
     * Remove the string characters are not \w|.| |&|;|,
     **/
    public static function clean_text($text) {
        if (empty($text) or is_numeric($text)) {
           return (string)$text;
        }
        $text = preg_replace('/[^0-9A-Za-z_\. &;,]*/', '', $text);
        return $text;
    }
    
    /**
     * Remove possible javascript attributes,
     * But it is not safe, xss can still be applied 
     **/
    public static function clean_value_js($text) {
        $strJsProtocol = 'javascript\s*:';
        return self::clean_js($text, $strJsProtocol);
    }
    
    /**
     * Remove possible javascript attributes in <a href>,
     * But it is not safe, xss can still be applied 
     **/
    public static function clean_content_js($text) {
        $strJsProtocol = '((href\s*=\s*(\'|\")?)\s*javascript\s*:)';
        return self::clean_js($text, $strJsProtocol, '$2');
    }
    
    /**
     * Remove possible javascript attributes in all html tags,
     * But it is not safe, xss can still be applied 
     **/
    public static function clean_js($text, $arySettings = array()){
        if (empty($text) or is_numeric($text)) {
           return (string)$text;
        }
        $strJsProtocol = $arySettings['strJsProtocol'] ? $arySettings['strJsProtocol'] : '';
        $strReplace = $arySettings['strReplace'] ? $arySettings['strReplace'] : '';
        
        $aryJsEventList = array(	'onAbort',
    							    'onBlur',
    							    'onChange',
    							    'onClick',
    							    'onDblClick',
    							    'onDragDrop',
    							    'onError',
    							    'onFocus',
    							    'onKeyDown',
    							    'onKeyPress',
    							    'onKeyUp',
    							    'onLoad',
    							    'onMouseDown',
    							    'onMouseMove',
    							    'onMouseOut',
    							    'onMouseOver',
    							    'onMouseUp',
    							    'onMove',
    							    'onReset',
    							    'onResize',
    							    'onSelect',
    							    'onSubmit',
    							    'Unload',
    							    'onUnload');
    	array_push($aryJsEventList, $strJsProtocol);
        $strJsEventList = implode('\s*=|', $aryJsEventList);
        
        return preg_replace('/'.$strJsEventList.'/i', $strReplace, $text);
    }
    
    /**
     * Remove possible html tags and encode all html special characters in a string
     **/
    public static function clear_html_string($strKey) {
        return current(self::clear_html(array($strKey)));
    }
    
    /**
     * Check is there is any profanity language in a string
     * The exteral dictionary is in settings\badwords.txt
     **/
    public static function check_profanity($string) {
            $combinations = array(
                    'like'=>array('boy','men','kid','child'),
                    'touch'=>array('boy','girl','kid','child'),
                    'fid'=>array('kid','child'),
                    'fondle'=>array('boy','girl','kid','child'),
                    'beat'=>array('wife','meat','mis'),
                    'rug'=>array('munch')
            );
            $strict = array(
                    'fuck','fk','fark','fcuk','blow','suck','lick','piss','wank','masti','masterbate', //adjectives
                    'shit','ass','arse','bitch','bastard','retard','rapist', //nouns
                    'dick','penis','cock','knob', //male genitals
                    'vagina','cunt','cnt','pussy','snatch','minge', //female genitals
                    'fag','homo','gay','pedo','lesb','dyke', //sexual preference
                    'nigger' //race
            );
            foreach($combinations as $key => $val) {
                    $regex[] = "($key\s+".implode("\s+|$key\s+",$val)."\s+|".implode("\s+$key\s+|",$val)."\s+$key\s+)";
            }
            
            //$regex[] = '('.implode("|",$strict).')';
            $strWordList = str_replace(array("\r\n", "\n", "\r"), ";;;;", file_get_contents(dirname(__FILE__).'/settings/badwords.txt'));
    		$strWordList = preg_replace(array('/\s+/'), array('\s+'), $strWordList);
    		$aryWordList = explode(";;;;", $strWordList);
    		
            //$regex = array();
            $regex[] = '('.implode("\s+|\s+",$aryWordList).')';
            $profanityReg = "/".implode("|",$regex)."/ix";
            
            return preg_match($profanityReg, $string) ? false : $string;
    }
    
    /**
     * Encode all html special characters in an array
     **/
    public static function safe_html($aryTextList, $aryWhiteList = array()) {
        foreach ($aryTextList as $strKey => $strText){
        	if(in_array($strKey, $aryWhiteList) || empty($strText) || is_numeric($strText)){
        		continue;
        	}else{
        		$aryTextList[$strKey] = htmlentities($strText, ENT_QUOTES);
        	}
        }
        return $aryTextList;
    }
    
    /**
     * Remove possible html tags and encode all html special characters in an array
     * @paramater $aryWhiteList is key list for $aryTextList, the related field in $aryTextList will not be filtered.
     * @paramater $aryBlackList is key list for $aryTextList, the related field in $aryTextList will be filtered.
     **/
    public static function clear_html($aryTextList, $aryWhiteList = array(), $aryBlackList = array()) {
        foreach ($aryTextList as $strKey => $strText){
        	if(isset($aryBlackList) && is_array($aryBlackList) && sizeof($aryBlackList) > 0){
    	    	if(in_array($strKey, $aryBlackList) && !empty($strText) && !is_numeric($strText)){
    	    		$aryTextList[$strKey] = htmlentities(strip_tags($strText), ENT_QUOTES);
    	    	}
    	    	continue;
        	}
        	if(isset($aryWhiteList) && is_array($aryWhiteList) && sizeof($aryWhiteList) > 0){
    	    	if(!in_array($strKey, $aryWhiteList) && !empty($strText) && !is_numeric($strText)){
    	    		$aryTextList[$strKey] = htmlentities(strip_tags($strText), ENT_QUOTES);
    	    	}
    	    	continue;
        	}
        	
        	$aryTextList[$strKey] = htmlentities(strip_tags($strText), ENT_QUOTES);
        }
        return $aryTextList;
    }
    
    public static function clear_wrong_text($aryTextList, $aryFieldList = array()) {
    	$aryWordMappingList = array("\x92" => '\'', "\x96" => '-',);
    	$aryKeyList = array_keys($aryWordMappingList);
    	$aryReplaceList = array_values($aryWordMappingList);
        foreach ($aryTextList as $strKey => $strText){
        	if(is_string($strText)){
        		$aryTextList[$strKey] = str_replace($aryKeyList, $aryReplaceList, $strText);
        	}else{
    	    	if(is_array($aryFieldList)){
    	    		if(sizeof($aryFieldList) > 0){
    	    			foreach ($aryFieldList as $strFieldName){
    	    				$aryTextList[$strKey][$strFieldName] = str_replace($aryKeyList, $aryReplaceList, $strText[$strFieldName]);
    	    			}
    	    		}else{
    	    			foreach ($strText as $strFieldName => $strValue){
    	    				$aryTextList[$strKey][$strFieldName] = str_replace($aryKeyList, $aryReplaceList, $strValue);
    	    			}
    	    		}
    	    	}
        	}
        }
        return $aryTextList;
    }
    
}
/*
Example.php

        $aryData = array(
        'name'  => 'Zachary',
        'email' => 'peter@reborn.com',
        'permission' => '1',
        'password' => '123456',
        'password_confirm' => '123456',
        );

        $arySettingList = array(
            'password' => array(
                array(
                'options'   => 'password::{5,,32}',
                ),
                'msg'   => 'Password is not valid',
            ),
            'password_confirm' => array(
                array(
                'options'   => 'pair::password',
                ),
                'msg'   => 'Password is not match',
            ),
            'name' => array(
                array(
                'filter'    => FILTER_SANITIZE_STRING,
                'options'   => FILTER_FLAG_ENCODE_HIGH|FILTER_FLAG_ENCODE_LOW,
                ),
                array(
                'filter'    => FILTER_VALIDATE_REGEXP,
                'options'   => array('regexp' => '/^(\w+){3,30}$/'),
                'msg'       => 'Name is not valid, 3 more charactors required',
                ),
                'msg'   => 'Name is not valid, a-z, 0-9, _, 5 ~ 30 charactors only',
            ),
            'email' => array(
                array(
                'filter'    => FILTER_SANITIZE_EMAIL,
                ),
                array(
                'filter'    => FILTER_VALIDATE_EMAIL,
                ),
                array(
                'call_func' => array('DataValidateExt','uniquEmail'),
                'options'   => array(),
                'msg'       => 'Email is not unique',
                ),
                'msg'   => 'Email is not valid',
            ),
            'permission' => array(
                'optional',
                array(
                'filter'    => FILTER_VALIDATE_INT,
                'flags'     => FILTER_FLAG_ALLOW_OCTAL,
                'options'   => array('min_range' => 0),
                ),
                array(
                'call_func' => array('DataValidateExt','validateField'),
                'options'   => 'option::[0,,1]',
                'msg'       => 'Permission is not in list',
                ),
                'msg'   => 'Permission is not valid',
            ),
        );

list($success, $aryMsg, $aryResultData) = DataValidateExt::validate($aryData, $arySettingList);
echo "<pre>";
var_dump($success);
var_dump($aryMsg);
var_dump($aryResultData);
die();
*/

?>