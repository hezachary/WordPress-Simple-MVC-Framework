<?php
class ToolsExt{
    /**
     * The array has to have 'id' and 'pid' (parent id) fields.
     * 
     * $aryTest = array(
     *       array(
     *       ’id’ => 9,
     *       ’pid’=> 8,
     *       ),
     *       array(
     *       ’id’ => 1,
     *       ’pid’=> 0,
     *       ),
     *       array(
     *       ’id’ => 7,
     *       ’pid’=> 6,
     *       ),
     *       array(
     *       ’id’ => 2,
     *       ’pid’=> 0,
     *       ),
     *       array(
     *       ’id’ => 3,
     *       ’pid’=> 0,
     *       ),
     *       array(
     *       ’id’ => 4,
     *       ’pid’=> 1,
     *       ),
     *       array(
     *       ’id’ => 5,
     *       ’pid’=> 1,
     *       ),
     *       array(
     *       ’id’ => 6,
     *       ’pid’=> 4,
     *       ),
     *       array(
     *       ’id’ => 8,
     *       ’pid’=> 7,
     *       ),
     *       array(
     *       ’id’ => 11,
     *       ’pid’=> 2,
     *       ),
     *      );
     * $aryTest = ToolsExt::arySetNode($aryTest, $aryTest);
     * $aryTree = ToolsExt::arySetTree($aryTest);
     * var_dump($aryTree)
     */
    public static function arySetTree($strIdField, $strPidField, $ary, $node = 0, $aryRst = array()){
    	foreach ($ary as $key => $value){
    		if ($value['node']== $node){
    			$aryTmp[(string)$value[$strPidField]][(string)$value[$strIdField]] = array();
    			unset($ary[$key]);
    		}
    	}
    	if($node == 0){
    		$aryRst = $aryTmp;
    
    	}
    	foreach($aryRst as $key_Rst => $value_Rst){
    		foreach($aryTmp as $key_Tmp => $value_Tmp){
    			if($key_Rst==$key_Tmp){
    				$aryRst[$key_Rst]=$value_Tmp;
    			}
    		}
    	}
    
    	if(sizeof($ary) > 0){
    		foreach($aryRst as $key_Rst => $value_Rst){
    			$aryRst[$key_Rst] = self::arySetTree($strIdField, $strPidField, $ary, $node+1, $value_Rst);
    		}
    	}
    	return $aryRst;
    }
    public static function arySetNode($strIdField, $strPidField, $ary, $aryOrg, $node = 0){
    	$ary_1 = $ary;
    	$ary_2 = $ary;
    	$ary_3 = array();
    	foreach($ary_1 as $key_1 => $value_1){
    		foreach($ary_2 as $key_2 => $value_2){
    			if($value_1[$strPidField]==$value_2[$strIdField]){
    				$ary_3[]=$ary_1[$key_1];
    				unset($ary_1[$key_1]);
    			}
    		}
    	}
    	foreach($ary_1 as $key_1 => $value_1){
    		foreach($aryOrg as $key_Org => $value_Org){
    			if ($value_1[$strIdField]==$value_Org[$strIdField]){
    				$aryOrg[$key_Org]['node']=$node;
    			}
    		}
    	}
    	if(sizeof($ary_3) > 0){
    		$aryOrg = self::arySetNode($strIdField, $strPidField, $ary_3, $aryOrg, $node+1);
    	}
    	return $aryOrg;
    }
    
    /**
     * Retrieve user IP address
     **/
    public static function retrieveUserIp(){
    	if (empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
    		$userip = $_SERVER['REMOTE_ADDR'];
    	}else{
    		$userip = explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']);
    		$userip = $userip[0];
    	}
    	return $userip;
    }
    
    /**
     * Filter Html Tags for content
     **/
    public static function mb_html_filter($str, $encoding = 'utf-8') {
        return htmlentities(self::mb_filter($str, $encoding), ENT_COMPAT, 'UTF-8');
    }
    
    public static function mb_filter($str, $encoding = 'utf-8') {
        mb_regex_encoding($encoding);
        $pattern = array('<[^>]+>', '\[[^\]]+', '(\r\n)|(\n)|(\r)|\&nbsp\;', '\s+');
        $replacement = array('', '', ' ', ' ');
        for ($i=0; $i<sizeof($pattern); $i++) {
            $str = mb_ereg_replace($pattern[$i], $replacement[$i], $str);
        }
        return trim($str);
    }
    
    public static function exportFile($strFilePath, $blnAttachment = false){
        $strMime = function_exists('finfo_file') ? finfo_file(finfo_open(FILEINFO_MIME_TYPE), $strFilePath) : mime_content_type($strFilePath);
        $aryInfo = stat($strFilePath);
        header('Content-Type: '.$strMime);
        header('Content-Length: '.$aryInfo['size']);
        header('Content-Disposition: '.($blnAttachment ? 'attachment' : 'inline').'; filename="'.basename($strFilePath).'"');
        readfile($strFilePath);
        return array($strMime, $strFilePath);
    }
    
    /**
     * $aryRatio = array(width, height)
     * $strTargetPath = path/path # no "/" in the end
     **/
    public static function resize_image($aryRatio, $strSourceFilePath, $strBaseFileName, $strTargetPath = null, $inQuality = 100, $blnDisplay = true, $blnAttachment = false){
        if(!file_exists($strTargetPath)){
            mkdir($strTargetPath, 0755);
        }
        
    	$arySourceFileInfo = pathinfo($strSourceFilePath);
    	$strSourceFileDir = $arySourceFileInfo['dirname'];
    	$strSourceFileExt = $arySourceFileInfo['extension'];
        $strBaseFileRawName = wp_basename($strBaseFileName, '.'.$strSourceFileExt);
        
        $strNewFilePath = !$strTargetPath ? null : $strTargetPath.DIRECTORY_SEPARATOR.$strBaseFileRawName.'-'.implode('=', $aryRatio).'.'.$strSourceFileExt;
        
        switch(true){
            case $strNewFilePath && file_exists($strNewFilePath) && $blnDisplay:
                //read file & export screen
                return self::exportFile($strNewFilePath, $blnAttachment);
                break;
            case $strNewFilePath && file_exists($strNewFilePath) && !$blnDisplay:
            case !$strNewFilePath && !$blnDisplay:
                //do nothing
                return array();
                break;
            case $strNewFilePath && !file_exists($strNewFilePath) && $blnDisplay:
                //populate + save + export screen
                break;
            case $strNewFilePath && !file_exists($strNewFilePath) && !$blnDisplay:
                //populate + save + no export screen
                break;
            case !$strNewFilePath && $blnDisplay:
                //populate + no save + export screen
                break;
        }
        
        //A. Populate
        $aryImageInfo = getimagesize($strSourceFilePath);
        switch ($aryImageInfo['mime']) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($strSourceFilePath);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($strSourceFilePath);
                break;
            case 'image/png':
                $source = imagecreatefrompng($strSourceFilePath);
                break;
            default:
                return array($aryImageInfo['mime'], null);
                break;
        }
        
        $intWidth =  imagesx($source);
        $intHeight = imagesy($source);
        
        $intNewWidth = $intWidth;
        $intNewHeight = $intHeight;
        switch(true){
            case $intWidth/$intHeight > $aryRatio[0]/$aryRatio[1]:
                $intNewHeight = (int)round($intWidth * $aryRatio[1] / $aryRatio[0]);
                break;
            case $intWidth/$intHeight < $aryRatio[0]/$aryRatio[1]:
                $intNewWidth = (int)round($intHeight * $aryRatio[0] / $aryRatio[1]);
                break;
        }
        
        $final = imagecreatetruecolor($intNewWidth, $intNewHeight);
        $backgroundColor = imagecolorallocate($final, 255, 255, 255);
        imagefill($final, 0, 0, $backgroundColor);
        imagecopy($final, $source, (($intNewWidth - $intWidth)/ 2), (($intNewHeight - $intHeight) / 2), 0, 0, $intWidth, $intHeight);
        
        
        if(!$blnDisplay){
            ob_start();
        }
        
        if(!$strNewFilePath){
            header('Content-Type: '.$aryImageInfo['mime']);
            header('Content-Disposition: '.($blnAttachment ? 'attachment' : 'inline').'; filename="'.basename($strNewFilePath).'"');
        }
        
        switch ($aryImageInfo['mime']) {
            case 'image/jpeg':
                imagejpeg($final, $strNewFilePath, $inQuality);
                break;
            case 'image/gif':
                imagegif($final, $strNewFilePath);
                break;
            case 'image/png':
                imagepng($final, $strNewFilePath, $inQuality);
                break;
        }
        
        if(!$blnDisplay){
            ob_end_clean();
        }
        
        imagedestroy($final);
        imagedestroy($source);
        
        if($strNewFilePath){
            $stat = stat($strTargetPath);
            $perms = $stat['mode'] & 0000666; //same permissions as parent folder, strip off the executable bits
            @ chmod( $strNewFilePath, $perms );
            if($blnDisplay){
                self::exportFile($strNewFilePath, $blnAttachment);
            }
        }
        
        return array($aryImageInfo['mime'], $strNewFilePath);
    }
    
    
    /**
     * Debug
     * @param $v : the value to trace
     * @param $s : dump value for sure, by default it will not dump value for object (in case it is too big)
     * @param $d : die after debug
     * @param $backtrace : if you call directly from your code, leave it as empty
     **/
    public static function _d($v, $s = true, $d=false, $backtrace = false){
    	if(true || $_REQUEST['_d']){
    		$r = dechex(rand(200, 230));
    		$g = dechex(rand(200, 230));
    		$b = dechex(rand(200, 230));
    		echo "<pre style='background-color:#$r$g$b;'>";
            $doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
            $backtrace = $backtrace ? $backtrace : debug_backtrace();
            // you may want not to htmlspecialchars here
            $line = htmlspecialchars($backtrace[0]['line']);
            $file = htmlspecialchars(str_replace(array('\\', $doc_root), array('/', ''), $backtrace[0]['file']));
            $class = !empty($backtrace[1]['class']) ? htmlspecialchars($backtrace[1]['class']) . '::' : '';
            $function = !empty($backtrace[1]['function']) ? htmlspecialchars($backtrace[1]['function']) . '() ' : '';
            echo '<b>'.$class.$function.' =&gt;'.$file.' ['.$line.']</b><br/>';
    		var_dump(get_class($v));
    		if(get_class($v))var_dump(get_class_methods($v));
    		if($s)var_dump($v);
            
            for ($k = 1; $k < sizeof($backtrace); $k++ ){
                $r = array('#'.$k.': ',$backtrace[$k]['class'], $backtrace[$k]['type'],$backtrace[$k]['function'].'()','  '.$backtrace[$k]['file']. ' ['.$backtrace[$k]['line'].']' );
                echo implode("",$r)."\n";
            }
    		echo "</pre><hr/>";
    		if($d)die();
    	}
    }
    
    /**
     * Retrieve List from ACF settings
     * @param $post : the source
     * @param $strFieldName : the select field name
     * @param $strAcfPath : the acf slug
     **/
    public static function retrieveListFromACFSettings($post, $strFieldName, $strAcfPath){
        $aryField = get_field($strFieldName, $post->ID);
        $strFieldKey = get_post_meta($post->ID, '_'.$strFieldName, true);
        
        $objDataDefine = get_page_by_path($strAcfPath, OBJECT, 'acf');
        $aryFieldSetting = get_post_meta($objDataDefine->ID, $strFieldKey, true);
        
        $aryExport = array();
        foreach($aryField as $value){
            $aryExport[$value] = $aryFieldSetting['choices'][$value];
        }
        return $aryExport;
    }
     
    /**
     * Parse Respons Header
     * @param $header : incoming header string
     * @return array()
     **/
    public static function http_parse_headers( $header )
    {
        $retVal = array();
        $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));
        foreach( $fields as $field ) {
            if( preg_match('/([^:]+): (.+)/m', $field, $match) ) {
                $match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', '"\0"', trim($match[1]));
                if( isset($retVal[$match[1]]) ) {
                    $retVal[$match[1]] = array($retVal[$match[1]], $match[2]);
                } else {
                    $retVal[$match[1]] = trim($match[2]);
                }
            }
        }
        return $retVal;
    }
    
    public static function log($name, $data){
        @file_put_contents(mvc::app()->getUploadPath() . mvc::DIR_SEP . 'log' . mvc::DIR_SEP .basename($name).'_'.date('YmdHis').'.txt', print_r($data, 1) );
    }
}