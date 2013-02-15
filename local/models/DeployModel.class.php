<?php
class DeployModel extends ModelBase{
    const TABLE         = 'ext_deploy';
    
    public $objGit = null;
    public function __construct(){
        parent::__construct(self::TABLE);
    }
    
    public function fetchRepositories(){
        return $this->get_obj_git()->fetchRepositories();
    }
    
    public function retrieveGitStatus(){
        return $this->get_obj_git()->getStatus();
    }
    
    public function retrieveGitTagList(){
        return $this->get_obj_git()->retrieveTagList();
    }
    
    public function pullRepo(){
        return $this->get_obj_git()->pullRepo();
    }
    
    public function resetRepo(){
        return $this->get_obj_git()->resetRepo();
    }
    
    private $_objGit = null;
    private function get_obj_git(){
        if(!$this->_objGit) $this->_objGit = new GitExt();
        return $this->_objGit;
    }
}