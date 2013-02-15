<?php
class EmaillogModel extends ModelBase{
    const TABLE = 'ext_email_log';
    const EMAIL_SUBJECT = 'This is the default email subject';
    const EMAIL_FROM = 'noreply@dev.com.net';
    const EMAIL_FROM_NAME = 'noreply';
    
    public static $aryFlagDefine = array(
        'from'      => 'From: ',
        'to'        => null,
        'reply_to'  => 'Reply-To: ',
        'cc'        => 'CC: ',
        'bcc'       => 'BCC: ',
    );
    
    protected $id;
    
    public $refer_email;
    public $refer_email_name; 
    public $register_id; 
        
    public $email_subject; 
    
    public $datetime;
    public $ip_long;
    public $uniqueId;
    
    public $aryRegister;
    
    public static $aryFormField = array('refer_email',
                                        );
                                        
    public function __construct(){
        parent::__construct(self::TABLE);
    }

    public function setId($id){
        $this->id = (int)$id;
    }
    
    public function retrieveId(){
        return $this->id;
    }
    
    public function retrieveUniqueId(){
        if(!$this->uniqueId){
            $this->uniqueId = sha1(base64_encode(uniqid()));
        }
        return $this->uniqueId;
    }

    public function retrieveEmailFrom( $from_email = null ) {
        return self::EMAIL_FROM;
    }
    
    public function retrieveEmailFromName( $from_name = null ) {
        return self::EMAIL_FROM_NAME;
    }

    public function sendEmail($strEmailTemplate){
        $this->retrieveUniqueId();
        //1. retrieve data
        $aryItemList = array();
        
        //2. populate email
        $smtEmail = new SmartyPageExt();
        $smtEmail->assign('SITEURL', site_url());
        $smtEmail->assign('THEMEPATH', get_bloginfo('stylesheet_directory'));
        
        //2.1 send mail
        $smtEmail->assign('aryRegister', $this->aryRegister);
        $smtEmail->assign('refer_email', $this->refer_email);
        $smtEmail->assign('refer_email_name', $this->refer_email_name);
        $smtEmail->assign('uniqueId', $this->uniqueId);
        $strEmail = $smtEmail->fetch('email.'.$strEmailTemplate.'.tpl');
        $strEmailSubject = $this->email_subject ? $this->email_subject : self::EMAIL_SUBJECT;
        
        add_filter('wp_mail_from_name', array($this, 'retrieveEmailFromName'));
        add_filter('wp_mail_from', array($this, 'retrieveEmailFrom'));
        add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
        
        if (!wp_mail($this->aryRegister['Email'], $strEmailSubject, $strEmail)){
            wp_die( __('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function...') );
        }
        
        //3. Insert record
        $this->insertEmailLog();
        return array(true, array());
    }
    
    public function insertEmailLog(){
        $this->retrieveUniqueId();
        $this->datetime = date('Y-m-d H:i:s');
        $aryRow = array(                'refer_email'       => $this->refer_email,
                                        'refer_email_name'  => $this->refer_email_name,
                                        'register_id'       => $this->register_id,
                                        'register_name'     => $this->aryRegister['Name'],
                                        'register_email'    => $this->aryRegister['Email'],
                                        'mail_opened'       => 0,
                                        'mail_clicked'      => 0,
                                        'datetime'          => $this->datetime,
                                        'uniqueId'          => $this->uniqueId,
                    );
        $intLastInsertId = $this->insert($aryRow);
        $this->setId($intLastInsertId);
    }

    public function updateEmailLogMailOpen($uniqueId){
        $aryRow = array('mail_opened' => date('Y-m-d H:i:s'),);
        $aryWhere = array();
        $aryWhere['uniqueId'] = $uniqueId;
        $aryWhere['mail_opened'] = 0;
        $intRowsAffected = $this->update($aryRow, $aryWhere);
        return;
    }
    
    public function updateEmailLogMailClicked($uniqueId){
        $aryRow = array('mail_clicked' => date('Y-m-d H:i:s'),);
        $aryWhere = array();
        $aryWhere['uniqueId'] = $uniqueId;
        $aryWhere['mail_clicked'] = 0;
        $intRowsAffected = $this->update($aryRow, $aryWhere);
        return;
    }
}