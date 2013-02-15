<?php
//$fileName = 'data1.csv';
//$config['db_name'] = 'xxx';
//$db=new db_sql();
//$q="SELECT *
//	FROM tblABC
//	WHERE abc = 'XYZ'
//	LIMIT 10";
//
//$db->query($q);
//
//$test = new fileExtra();
//$test->fnStartCSV($fileName);
//$test->fnResetCSV();
//$test->fnUnsetInfo();
//$products = array();
//
//$i = 0;
//while ($products[] = $db->next_record()) {
//	if($i > 500){
//		$test->fnCreateCSV($products);
//		$products = array();
//		$i = 0;
//	}
//	$i++;
//}
//
//$test->fnCreateCSV($products);
//$test->fnCloseFiles();
//$test->fnShow();
//exit;

class CsvExt {
	var $infoFileExt = '.inf';
	var $fileHandle;
	var $fileInfoHandle;
	var $aryInfo;
	var $fnCreateRowCSV;
	var $fileName;
	var $content;
	var $mode = 'file';
	
	function fnStartCSV($fileName, $folderName = null, $infoFileExt = null){
		$fileName = str_ireplace('.csv', '', basename($fileName));
		if (!empty($folderName)){
			$folderName = (substr($folderName, -1)!='/')?$folderName.'/':$folderName;	
		}

		$infoFile = !empty($infoFileExt)?$fileName.$infoFileExt:$fileName.$this->infoFileExt;
		
		$fileName = $folderName.$fileName.'.csv';
		$this->fileName = $fileName;
		
		$infoFile = $folderName.$infoFile;
		
//		if ($this->mode == 'file'){
//			$this->fileHandle = fopen($fileName, 'a');
//			$this->fileInfoHandle = fopen($infoFile, 'w+');
//			$infoFileSize = filesize($infoFile);
//			$info = $infoFileSize>0?fread($this->fileInfoHandle, $infoFileSize):'';
//			
//			$this->aryInfo = @unserialize($info);
//			
//			if (!is_array($this->aryInfo)){
//				$this->aryInfo = array();
//			}
//		}
		
		$this->fnCreateRowCSV();
	}
	
	function fnCreateCSV($aryRecorde){
		$aryRowSample = current($aryRecorde);
		if (!isset($this->aryInfo['colNum'])){
			$this->aryInfo['colNum'] = sizeof($aryRowSample);
		}elseif (isset($this->aryInfo['colNum']) && sizeof($aryRowSample)!=$this->aryInfo['colNum']){
			//return false;
		}
		$aryRecorde = $this->fnFilteCSV($aryRecorde);
		$aryRow = array_map($this->fnCreateRowCSV, $aryRecorde);
		$export = implode("\n", $aryRow);
		
		$this->content .= $export."\n";
//		if ($this->mode == 'file'){
//			fwrite($this->fileHandle, $export."\n");
//		}
		return true;
	}
	
	function fnResetCSV(){
		$this->content = '';
//		ftruncate($this->fileHandle, 0);
	}
	
	function fnUnsetInfo(){
		$this->aryInfo = array();
	}
	
	function fnCloseFiles(){
//		fwrite($this->fileInfoHandle, serialize($this->aryInfo));
//		fclose($this->fileHandle);
//		fclose($this->fileInfoHandle);
	}
	
	function fnFilteCSV($value){
		if (is_array($value)){
			return array_map(array($this, __FUNCTION__), $value);
		}else{
			return str_replace('"','""', $value);
		}
	}
	
	function fnCreateRowCSV(){
		$this->fnCreateRowCSV = create_function('$value', 'return \'"\'.(is_array($value)?implode(\'","\', $value):\'\').\'"\';');
	}
	
	function fnShow(){
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition:  attachment; filename=".basename($this->fileName));
		echo $this->content;
	}
}
?>