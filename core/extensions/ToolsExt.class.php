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
    public static function arySetTree($ary, $node = 0, $aryRst = array()){
    	foreach ($ary as $key => $value){
    		if ($value['node']== $node){
    			$aryTmp[(string)$value['pid']][(string)$value['id']] = array();
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
    			$aryRst[$key_Rst] = self::arySetTree($ary, $node+1, $value_Rst);
    		}
    	}
    	return $aryRst;
    }
    public static function arySetNode($ary, $aryOrg, $node = 0){
    	$ary_1 = $ary;
    	$ary_2 = $ary;
    	$ary_3 = array();
    	foreach($ary_1 as $key_1 => $value_1){
    		foreach($ary_2 as $key_2 => $value_2){
    			if($value_1['pid']==$value_2['id']){
    				$ary_3[]=$ary_1[$key_1];
    				unset($ary_1[$key_1]);
    			}
    		}
    	}
    	foreach($ary_1 as $key_1 => $value_1){
    		foreach($aryOrg as $key_Org => $value_Org){
    			if ($value_1['id']==$value_Org['id']){
    				$aryOrg[$key_Org]['node']=$node;
    			}
    		}
    	}
    	if(sizeof($ary_3) > 0){
    		$aryOrg = self::arySetNode($ary_3, $aryOrg, $node+1);
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
}