<?php
/**
 * WPSMVC base model class.
 * 
 * @author Zhehai He <hezahcary@gmail.com>
 * @link N/A
 * @version 0.91
 * @package System
 */
class ModelBase{
    const DIR_SEP = DIRECTORY_SEPARATOR;
    
    const SELECT         = 'SELECT';
    const FROM           = 'FROM';
    const WHERE          = 'WHERE';
    const ORDER          = 'ORDER BY';
    const LIMIT          = 'LIMIT';
    const SQL_AS         = 'AS';
    const SQL_ASC        = 'ASC';
    const SQL_DESC       = 'DESC';
    
    public $db;
    public $table;
    public function __construct($table){
        global $wpdb, $table_prefix;
        $this->db = $wpdb;
        $this->table = $table_prefix . $table;
    }
    
    public function insert( $data){
        $this->db->insert( $this->table, $data );
        return $this->db->insert_id;
    }
    
    public function update( $data, $where){
        $this->db->update( $this->table, $data, $where );
    }
    
    public function delete( $aryCondition ){
        $this->db->query('
            DELETE '.$this->_from().'
            '.$this->_where($aryCondition).'
		');
    }
    
    public function retrieveList( $aryCondition, $intPageNum, $intPageStep, $arySelectFieldList = array(), $blnCountMode = false, $aryOrder = array(), $itemFormat = OBJECT){
        //1. retrieve lists
        $intTotalPage = 1;
        $intPageStep = (int)$intPageStep;
        //global $CFG; echo $CFG->_d == 1 ? $objSelect->__toString() : null ;
        if( $intPageStep > 0 || $blnCountMode == true){
            
            $intTotalRow = $this->db->get_var(
                $this->db->prepare('
                    SELECT COUNT(*) AS count_id
                    '.$this->_from().'
                    '.$this->_where($aryCondition)
                )
            );
            
            if($blnCountMode == true){
                return $intTotalRow;
            }
            
            $intPageStep = abs($intPageStep)?abs($intPageStep):1;
            $intTotalPage =  (int)ceil($intTotalRow/$intPageStep);
            
            $intPageNum = $intPageNum > $intTotalPage ? $intTotalPage : $intPageNum;
            $intPageNum = $intPageNum < 1 ? 1 : $intPageNum;
            $aryResult = $this->db->get_results(
                $this->db->prepare('
                    '.$this->_select($arySelectFieldList).'
                    '.$this->_from().'
                    '.$this->_where($aryCondition).'
                    '.$this->_order($aryOrder).'
                    '.$this->_limitPage($intPageNum, $intPageStep).'
                '),
                $itemFormat
            );
            return array($aryResult, $intPageNum, $intTotalPage, $intTotalRow);
        }else if( $intPageStep == 0 ) {
            return $this->db->get_row(
                $this->db->prepare('
                    '.$this->_select($arySelectFieldList).'
                    '.$this->_from().'
                    '.$this->_where($aryCondition).'
                    '.$this->_order($aryOrder).'
                    '.$this->_limit(1).'
                '),
                $itemFormat
            );
        }else if( $intPageStep < 0 ) {
            return $this->db->get_results(
                $this->db->prepare('
                    '.$this->_select($arySelectFieldList).'
                    '.$this->_from().'
                    '.$this->_where($aryCondition).'
                    '.$this->_order($aryOrder).'
                    '.$this->_limit(abs($intPageStep)).'
                '),
                $itemFormat
            );
        }
    }
    
    public function escape($value){
        return mysql_real_escape_string( $value, $this->db->dbh );
    }
    
    public function escape_field($ident){
        if (is_string($ident)) {
            $ident = explode('.', $ident);
        }
        if (is_array($ident)) {
            $segments = array();
            foreach ($ident as $segment) {
                $segments[] = $this->_quoteIdentifier($segment, $auto);
            }
            $quoted = implode('.', $segments);
        } else {
            $quoted = $this->_quoteIdentifier($ident);
        }
        
        return $quoted;
    }
    
    protected function _quoteIdentifier($value){
        $q = "`";
        return ($q . str_replace("$q", "$q$q", $value) . $q);
    }
    
    protected function _select($arySelectFieldList){
        $parts = array();
        if (!is_array($arySelectFieldList)) {
            $arySelectFieldList = array($arySelectFieldList);
        }

        // force 'ASC' or 'DESC' on each order spec, default is ASC.
        foreach ($arySelectFieldList as $key => $val) {
            $parts[] = ($key && !(int)$key ? $this->escape_field($key).' '.self::SQL_AS.' ' : '').$val;
        }

        return self::SELECT.' '.(sizeof($parts) ? implode(', ', $parts) : '*');
    }
    
    protected function _from(){
        return self::FROM.' '.$this->escape_field($this->table);
    }
    
    protected function _where($aryCondition){
        $aryCondition = is_array($aryCondition) ? $aryCondition : array();
        $aryWhere = array();
        foreach($aryCondition as $strField => $strValue){
            if(is_array($strValue)){
                if(is_array(current($strValue))){
                    reset($strValue);
                    $arySubCondition = array();
                    foreach($strValue as $aryData){
                        switch(strtoupper($aryData[1])){
                            case '=':
                            case '<':
                            case '>':
                            case '>=':
                            case '<=':
                            case 'IS':
                            case 'IS NOT':
                            case 'LIKE':
                            break;
                            default:
                                $aryData[1] = '=';
                            break;
                        }
                        $arySubCondition[] = $this->escape_field($aryData[0]).' '.strtoupper($aryData[1]).' '.($aryData[2]=='NULL'?'NULL':($aryData[3] ? $this->escape_field($aryData[2]) : '\''.$this->escape($aryData[2]).'\''));
                    }
                    $aryWhere[] = implode(' OR ', $arySubCondition);
                }else{
                    $aryWhere[] = $this->db->prepare($this->escape_field($strField).' IN ('.implode(' , ', array_fill(0, sizeof($strValue), '%s')).')', $strValue);
                }
            }else{
                $aryWhere[] = $this->db->prepare($this->escape_field($strField) . ' = %s', $strValue);
            }
        }
        
        return sizeof($aryWhere) ? self::WHERE.' '.implode(' AND ', $aryWhere) : '';
    }
    
    protected function _order($spec)
    {
        $parts = array();
        
        if (!is_array($spec)) {
            $spec = array($spec);
        }

        // force 'ASC' or 'DESC' on each order spec, default is ASC.
        foreach ($spec as $val) {
            if (empty($val)) continue;
            $direction = self::SQL_ASC;
            if (preg_match('/(.*\W)(' . self::SQL_ASC . '|' . self::SQL_DESC . ')\b/si', $val, $matches)) {
                $val = trim($matches[1]);
                $direction = $matches[2];
            }
            if (preg_match('/\(.*\)/', $val)) {
                $val = (string)$val;
            }
            $parts[] = $this->escape_field($val).' '.$direction;
        }

        return sizeof($parts) ? self::ORDER.' '.implode(', ', $parts) : '';
    }
    
    
    protected function _limit($count = null, $offset = null)
    {
        $parts = array();
        $parts[]  = (int) $count;
        if((int) $offset){
            $parts[] = (int) $offset;
        }
        return self::LIMIT.' '.implode(', ', $parts);
    }
    
    protected function _limitPage($page, $intPageStep)
    {
        $page     = ($page > 0)     ? $page     : 1;
        $rowCount = ($rowCount > 0) ? $rowCount : 1;
        return $this->_limit((int) $intPageStep * ($page - 1), (int)$intPageStep);
    }
    
    protected function prepare( $query ){
        $args = func_get_args();
		array_shift( $args );
        return $this->db->prepare( $query, is_array($args[0]) ? $args[0] : $args );
    }
}