<?php
class AdminDeployController extends ControllerBase{
    public $strTemplateName = 'admin.deploy.tpl';
    
    public static function load($objAdmin, $blnAjax, $aryClassName){
        return parent::load($objAdmin, $blnAjax, get_class(), get_class());
    }
    
    public function filter($aryData, $strField){
        $aryControlFilterList = array(
        );
        return DataValidateExt::validate($aryData, $aryControlFilterList[$strField]);
    }
    
    public $aryGitStatus = array();
    public function index(){
        $this->get_obj_deploy()->fetchRepositories();
        $this->aryGitStatus = $this->get_obj_deploy()->retrieveGitStatus();
        $this->aryTagList = $this->get_obj_deploy()->retrieveGitTagList();
    }
    
    /**
     * @source $_POST
     * @param $page_num int # you can only put native type here, no object type
     **/
    public function git_pull(){
        $this->get_obj_deploy()->pullRepo();
        $this->index();
    }
    
    /**
     * @source $_POST
     * @param tag string # you can only put native type here, no object type
     **/
    public function reversion_update($tag){
        //$this->get_obj_deploy()->pullRepo();
        $this->index();
    }
    
    private $_objDeploy = null;
    private function get_obj_deploy(){
        if(!$this->_objDeploy) $this->_objDeploy = new DeployModel();
        return $this->_objDeploy;
    }
}