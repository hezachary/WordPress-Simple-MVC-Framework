<?php
class PageHomeController extends ControllerBase{
    public $strTemplateName = 'page.home.tpl';
    
    public static function load($objPage, $blnAjax, $aryClassName){
        return parent::load($objPage, $blnAjax, get_class(), get_class());
    }
    
    public function filter($aryData, $strField){
        $aryStatusList = ReminderModel::$aryStatusList;
        $strStatusList = implode(',,', $aryStatusList);
        $strField = array_pop(explode('::', $strField));
        $aryControlFilterList = array(
            'reminder_register' => array(
                'Email' => array(
                    array(
                    'filter'    => FILTER_SANITIZE_EMAIL,
                    ),
                    array(
                    'filter'    => FILTER_VALIDATE_EMAIL,
                    ),
                    array(
                    'call_func' => array('UserModel','uniquEmail'),
                    'options'   => array(),
                    'msg'       => 'It looks like your email address is already registered with us. Please <a href="'.site_url().'/home/login">Sign In</a> now.',
                    ),
                    'msg'   => 'You have entered an invalid e-mail address.',
                ),
            ),
            'register' => array(
                'user_login' => array(
                    array(
                    'filter'    => FILTER_SANITIZE_EMAIL,
                    ),
                    array(
                    'filter'    => FILTER_VALIDATE_EMAIL,
                    ),
                    array(
                    'call_func' => array('UserModel','uniquEmail'),
                    'options'   => array(),
                    'msg'       => 'It looks like your email address is already registered with us. Please <a href="'.site_url().'/home/login">Sign In</a> now.',
                    ),
                    'msg'   => 'Please provide a valid email address.',
                ),
                'user_pass' => array(
                    array(
                    'options'   => 'password::{5,,32}',
                    ),
                    'msg'   => 'Please provide your password.',
                ),
                'user_pass_confirm' => array(
                    array(
                    'options'   => 'pair::user_pass',
                    ),
                    'msg'   => 'Please check that your passwords match and try again.',
                ),
                'Postcode' => array(
                    array(
                        'filter'    => FILTER_VALIDATE_REGEXP,
                        'options'   => array('regexp' => '/^\d{3,4}$/'),
                    ),
                    'msg'   => 'Please provide your postcode.',
                ),
                'State' => array(
                    array(
                    'options'   => 'option::['.PageRegister_detailController::$aryCountryStateList[$aryData['Country']].']',
                    ),
                    'msg'   => 'Please provide your state.',
                ),
                'Suburb' => array(
                    array(
                    'options'   => 'text::{1,,250}',
                    ),
                    'msg'   => 'Please provide your suburb.',
                ),
            ),
            'password_update' => array(
                'user_pass_org' => array(
                    array(
                    'options'   => 'password::{5,,32}',
                    ),
                    array(
                    'call_func' => array('UserModel','checkPassword'),
                    'options'   => array('password_hashed' => $current_user->user_pass),
                    'msg'       => 'Password is incorrect',
                    ),
                    'msg'   => 'That doesn\'t match the password that we have on file. Please try again.',
                ),
                'user_pass' => array(
                    array(
                    'options'   => 'password::{5,,32}',
                    ),
                    'msg'   => 'Please confirm your password.',
                ),
                'user_pass_confirm' => array(
                    array(
                    'options'   => 'pair::user_pass',
                    ),
                    'msg'   => 'Please check that your passwords match and try again.',
                ),
            ),
        );
        return DataValidateExt::validate($aryData, $aryControlFilterList[$strField]);
    }
    
    public $arySubPostList = array();
    public function index(){
        //You can load your model
        //$objPageModel = new PageModel();
        $this->content = 'hello world';
    }
    
    /**
     * @packed
     **/
    public function login(array $request){//Inline area support auto convert array
        $this->strTemplateName = 'page.reminder.tpl';
        list($success, $aryMsg, $aryResultData) = $this->filter($post, 'reminder_register');
        
        if($success){
            //$objReminder = new ReminderModel();
            //$objReminder->Email = $aryResultData['Email'];
            //$objReminder->updateRegister();
        }
        $this->aryPost = $aryResultData;
        $this->msg = $aryMsg;
        $this->success = $success;
    }
}