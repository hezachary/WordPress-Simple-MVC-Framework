<?php
set_time_limit(3600);
define('GIT_BASEPATH', ABSPATH ? realpath(ABSPATH.'..') : realpath(dirname(__FILE__)));
/**
 * GIT_USERNAME from conf file
 * GIT_PASSWORD from conf file
 * GIT_BASEPATH has to out side the project web root
 **/
class GitExt{
    public $blnGitTesting = false;
    public $blnGitExisting = false;
    
    public $strTargetSubDirectory = '';
    public $strDirectory = '';
    public $strGitDir = '';
    public $strDirectoryBackup = '';
    public $strLogPath = '';
    public $strGitRemote = '';
    public $strGitBranch = '';
    public function __construct(){
        exec('git', $output);
        $this->blnGitTesting = $output ? true : false;
        
        if($this->blnGitTesting){
            $this->iniPath();
            $this->blnGitExisting = file_exists($this->strGitDir) ? true : false;
            $this->getBranchInfo();
        }
        
        return $this->blnGitTesting && $this->blnGitExisting;
    }
    
    private $blnExecRight = true;
    private function _exec($strCommand, $strDesc, $blnSkipExecCheck = false, $strSafeCommand = null){
        $output = null;
        $blnSkipped = true;
        if($this->blnExecRight || $blnSkipExecCheck){
            exec($strCommand, $output, $return_var);
            //_d(array($strCommand, $output, $return_var));
            $blnSkipped = false;
        }
        $this->error_log( sprintf('%s - %s ... %s %s', !$return_var ? 'INFO' : ($blnSkipped ? 'SKIPED' : 'ERROR - '.$return_var), $strDesc, "\n\t\t".($strSafeCommand ? $strSafeCommand : $strCommand), $output ? "\n\t\t".implode("\n\t\t", $output) : '') );
        $this->blnExecRight = $this->blnExecRight ? ($return_var ? false : $this->blnExecRight) : $this->blnExecRight;
        
        return $output;
    }
    
    private $_strDeployPath = false;
    private $_strBackupPathInfo = false;
    /**
     * $strTargetSubDirectory = 'www' / 'www-staging'
     **/
    public function iniPath($strTargetSubDirectory = null){
        $this->strTargetSubDirectory = $strTargetSubDirectory ? $strTargetSubDirectory : array_pop(explode(DIRECTORY_SEPARATOR, substr(ABSPATH, 0, strlen(ABSPATH) - 1)));
        $this->_strDeployPath = realpath(GIT_BASEPATH.DIRECTORY_SEPARATOR.$this->strTargetSubDirectory);
        $this->_strBackupPathInfo = '-'.date('YmdHis').'-'.uniqid();
        $this->_strLogPath = sprintf('%s'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'git-%s-%s.log', GIT_BASEPATH, $this->strTargetSubDirectory, date('Y-m-d'));
        
        $this->strDirectory = $this->_strDeployPath.DIRECTORY_SEPARATOR;
        $this->strGitDir = $this->_strDeployPath.'-git'.DIRECTORY_SEPARATOR;
        $this->strDirectoryBackup = $this->_strDeployPath.$this->_strBackupPathInfo.DIRECTORY_SEPARATOR;
        $this->strGitBackup = $this->_strDeployPath.'-git'.$this->_strBackupPathInfo.DIRECTORY_SEPARATOR;
        $this->strLogPath = $this->_strLogPath;
    }
    
    public function getStatus(){
        $strCommand = sprintf('cd %s', escapeshellarg($this->strDirectory));
        $this->_exec($strCommand, 'Changing working directory');
        
        $strCommand = sprintf('git --git-dir=%s --work-tree=%s status -s -b', escapeshellarg($this->strGitDir), escapeshellarg($this->strDirectory));
        $aryStatus = $this->_exec($strCommand, 'Retrieve Status');
        return $aryStatus;
    }
    
    public function getBranchInfo(){
        $strCommand = sprintf('cd %s', escapeshellarg($this->strDirectory));
        $this->_exec($strCommand, 'Changing working directory');
        
        $strCommand = sprintf('git --git-dir=%s --work-tree=%s branch -r', escapeshellarg($this->strGitDir), escapeshellarg($this->strDirectory));
        $aryRemoteBranch = $this->_exec($strCommand, 'Retrieve Remote/Branch');
        $aryRemoteBranch = explode('/', array_pop($aryRemoteBranch));
        
        $this->strGitRemote = trim($aryRemoteBranch[0]);
        $this->strGitBranch = trim($aryRemoteBranch[1]);
        
        return $aryRemoteBranch;
    }
    
    public function fetchRepositories(){
        $strCommand = sprintf('cd %s', escapeshellarg($this->strDirectory));
        $this->_exec($strCommand, 'Changing working directory');
        
        $strCommand = sprintf('git --git-dir=%s --work-tree=%s fetch -t %s %s', escapeshellarg($this->strGitDir), escapeshellarg($this->strDirectory), escapeshellarg($this->strGitRemote), escapeshellarg($this->strGitBranch));
        $this->_exec($strCommand, 'Fetch Latest History');
    }
    
    public function retrieveTagList(){
        $strCommand = sprintf('cd %s', escapeshellarg($this->strDirectory));
        $this->_exec($strCommand, 'Changing working directory');
        
        $strCommand = sprintf('git --git-dir=%s --work-tree=%s tag -l', escapeshellarg($this->strGitDir), escapeshellarg($this->strDirectory));
        $aryTagList = $this->_exec($strCommand, 'Tag Listing');
        return $aryTagList;
    }
    
    public function resetRepo($strTag, $aryPostDeploy = array()){
        if(!$this->strGitDir){
            $this->error_log('ERROR - Git Directory Not exist ... '.$this->strDirectory);
            return;
        }
        
        $strCommand = sprintf('cd %s', escapeshellarg($this->strDirectory));
        $this->_exec($strCommand, 'Changing working directory');
        
        $strCommand = sprintf('git reset --hard %s', escapeshellarg($strTag));
        $this->_exec($strCommand, 'Reset to Selected Version');
        
        $this->securityDirectory();
        
        if(sizeof($aryPostDeploy)){
            call_user_func_array($aryPostDeploy[0], $aryPostDeploy[1]);
        }
        
        $this->error_log('INFO - Deployment via Re-Version done.');
    }
    
    /**
     * $aryPostDeploy = call_user_func_array, [0] = function, [1] = parameters
     **/
    public function pullRepo($aryPostDeploy = array()){
        if(!$this->strGitDir){
            $this->error_log('ERROR - Git Directory Not exist ... '.$this->strDirectory);
            return;
        }
        
        $strCommand = sprintf('cd %s', escapeshellarg($this->strDirectory));
        $this->_exec($strCommand, 'Changing working directory');
        
        // Discard any changes to tracked files since our last deploy
        //$strCommand = sprintf('git --git-dir=%s --work-tree=%s reset --hard HEAD', escapeshellarg($this->strGitDir), escapeshellarg($this->strDirectory));
        //$this->_exec($strCommand, 'Reseting repository');
        
        // Update the local repository
        $strCommand = sprintf('git --git-dir=%s --work-tree=%s pull %s %s', escapeshellarg($this->strGitDir), escapeshellarg($this->strDirectory), escapeshellarg($this->strGitRemote), escapeshellarg($this->strGitBranch));
        $this->_exec($strCommand, 'Pulling in changes');
        
        $this->securityDirectory();
        
        if(sizeof($aryPostDeploy)){
            call_user_func_array($aryPostDeploy[0], $aryPostDeploy[1]);
        }
        
        $this->error_log('INFO - Deployment via pull done.');
    }
    
    public static function cloneRepo($aryPostDeploy = array()){
        if(!$this->strDirectory){
            $this->error_log('ERROR - Directory Not exist ... '.$strDirectory);
            return;
        }
        
        if(!GIT_USERNAME || !GIT_PASSWORD || !GIT_PROJECT){
            $this->error_log('ERROR - Missing Define GIT_USERNAME, or GIT_PASSWORD, or GIT_PROJECT ... '.$strDirectory);
            return;
        }
        
        $strCommand = sprintf('cd %s', escapeshellarg(GIT_BASEPATH));
        $this->_exec($strCommand, 'Changing working directory');
        
        if(file_exists($this->strDirectory)){
            $strCommand = sprintf('mv %s %s', escapeshellarg($this->strDirectory), escapeshellarg($this->strDirectoryBackup));
            $this->_exec($strCommand, 'Move current Site Directory');
        }
        
        if(file_exists($strGitDir)){
            $strCommand = sprintf('mv %s %s', escapeshellarg($this->strGitDir), escapeshellarg($this->strGitBackup));
            $this->_exec($strCommand, 'Move current Git Directory');
        }
        
        $strCommand = sprintf('git --git-dir=%s --work-tree=%s clone %s -o %s -b %s %s', escapeshellarg($this->strGitDir), escapeshellarg($this->strDirectory), escapeshellarg(sprintf('https://%s:%s@bitbucket.org/reborn/%s', GIT_USERNAME, GIT_PASSWORD, GIT_PROJECT)), escapeshellarg($this->strGitRemote), escapeshellarg($this->strGitBranch), escapeshellarg($this->strGitDir));
        $this->_exec($strCommand, 'Clone Project', false, str_replace(array(GIT_USERNAME, GIT_PASSWORD), array('xxxx', 'xxxx'), $strCommand));
        
        $this->securityDirectory();
        
        if(sizeof($aryPostDeploy)){
            call_user_func_array($aryPostDeploy[0], $aryPostDeploy[1]);
        }
        
        $this->error_log('INFO - Deployment via clone done.');
    }
    
    private $_aryLogMessage = array();
    public function error_log($strMsg = null){
        if(!$strMsg) return $this->_aryLogMessage;
        //error_log($strMsg."\n", 3, $this->strLogPath);
        $this->_aryLogMessage[] = $strMsg;
    }
    
    public function securityDirectory(){
        $strHtaccessPath = $this->strGitDir.DIRECTORY_SEPARATOR.'.htaccess';
        $strIndexHtmlPath = $this->strGitDir.DIRECTORY_SEPARATOR.'index.html';
        
        $strCommand = sprintf('cd %s', escapeshellarg($this->strGitDir));
        $this->_exec($strCommand, 'Changing working directory for securing .git directory');
        
        if(!file_exists($strHtaccessPath)){
            file_put_contents($strHtaccessPath, 'order allow,deny'."\n".'deny from all');
            $this->error_log('INFO - Securing .git directory, add htaccess deny from all... ');
        }
        if(!file_exists($strIndexHtmlPath)){
            file_put_contents($strIndexHtmlPath, '');
            $this->error_log('INFO - Securing .git directory, add blank html... ');
        }
    }
    
    
}
