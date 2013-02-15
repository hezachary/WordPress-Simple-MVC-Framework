A simple MVC framework for Wordpress, support static class methods, merged with Smarty Template.
WordPress-Simple-MVC-Framework is under MIT Copyright (c) 2012 Zhehai He <hezahcary@gmail.com>
version 0.91

1. Installation:
    a.  Please copy all the file to your theme folder.
        Example:
        Your project theme : \wp-content\themes\twentyeleven
        The MVC framework : \wp-content\themes\twentyeleven\framework
        After you install the mvc, the [mvc.ini.php] is at [\wp-content\themes\twentyeleven\framework\mvc.ini.php]
    b.  The MVC also support Zend Framework and Smarty by default, to install them, please:
        I.  Zend Framework - Copy all the Zend framework files in \core\libs\Zend
        II. Smarty - Copy all the Smarty files in \core\libs\Smarty
            -- http://www.smarty.net/download

2. Settings:
    a.  Please read the file comments in framework\config
        I. For Url Rewrite: router.config.php
           http://codex.wordpress.org/Class_Reference/WP_Rewrite

3. How to use:
    a. In you functions.php: 
        <?php
        require_once(dirname(__FILE__).'/framework/mvc.ini.php');
        ImageModel::loadImageSize(mvc::app()->aryImageSizeList);
        ImageModel::loadAdmin(mvc::app()->aryImageSizeList);
        UrlModel::loadRule(mvc::app()->aryRouterList);
        TaxonomyModel::loadTaxonomy(mvc::app()->aryTaxonomyList);
        /**
         * For load image via cloudfront cdn
         * @usage: 
         * $aryImageAttr = cdn_get_attachment_image_src($intImageId, $size);
         * echo '<img src="'.aryImageAttr[0].'" alt=""/>';
         **/
        function cdn_get_attachment_image_src($intImageId, $size='thumbnail', $icon = false){
            return ImageModel::wp_get_attachment_image_src($intImageId, $size, $icon, mvc::app()->aryCDNSettings['cloudfront_host']);
        }
        /**
         * For easy debug
         **/
        function _d($value, $blnDumpValues = true, $blnDieAfterDebug = false){
            $debug_backtrace = debug_backtrace();
            ToolsExt::_d($value,$blnDumpValues,$blnDieAfterDebug, $debug_backtrace);
        }
        ?>
    b.  In your theme files, example: page.php
        <?php
        echo mvc::app()->run('page', $post);
        ?>

4. How to developing:
    a. Load local class first:
        I.  framework\core\ is for core code
        II. framework\local\ is for project code
        III.If local and core has same file and class, core will be overwritten by local.
            This rule almost apply to all the classes and files.
    b. Locate class by CamelCase:
        I.  Last CamelCase name become the base load folder
        II. Classname1Classname2Classname3, will be load in :
            i.  classname3\Classname1Classname2Classname3 - right
            ii. classname3\classname1\Classname2Classname3 - right
            iii.classname3\classname1\classname2\Classname3 - wrong
    c. Controler:
        I.  Use post-type as controler name:
            i.  mvc::app()->run('page', $post) will load controlers\PageController.class.php
        II. Use static method [load($objPage, $blnAjax, $aryClassName)] to locate proper class to ini the controler object.
            Please read : framework\core\base\ControllerBase.class.php comments for [public static function load]
            Samples: 
                \framework\local\controlers\PageController.class.php
                \framework\local\controlers\page\HomeController.class.php
                \framework\local\controlers\page\HomeStatusController.class.php
        III. Use Post Name (post-slug) as \page\ controller sub name, [-] will be replace with [_]
             The name conversion in code: preg_replace('/\W/', '_', ucfirst(strtolower($post_name)))
             Samples:
                Post name [product-detail]
                 - Controller Location: \framework\local\controlers\page\Product_detailController.class.php
                 - In file Class: class PageProduct_detailController extends ControllerBase { ... }
             Caution:
                Do not use any invalid value name as Post Name, such as:
                 - 2way-drive - wrong
                 - 10-speed-bicycle - wrong
                 - drive-2way - right
        III. Retrieve values from defined source with simple filter:
            i. Like most modern mvc, WordPress-Simple-MVC-Framework also support simple way to pass value direct from PHP magic global values
               Such as: $_GET, $_POST, etc
               The data source is base on supplied info in comments for the method
               There are two type format involved: 
               @packed and (@source + @param + @param + @param + ... etc)
               Example: [local\controlers\page\HomeStatusController.class.php]
                /**
                 * @source $_GET
                 * @param $page_id int # you can only put native type here, no object type
                 **/
                public function ajax($page_id)
                
                /**
                 * @packed
                 * If you use packed, please defined the source (in lower case) as the paramater
                 * In here, it is [$post] - public function form(array $post)
                 **/
                public function form(array $post){//Inline area support auto convert array
                
                Reminder: If you use Wordpress Url Rewrite, the value will auto pass into the packed data or the source you defined
                
        IIIV. Router for choose a method in a controler:
            i. $_REQUEST['r'] is the name of the method in the controler
                Example: $_REQUEST['r'] = 'form', $objControler->form(array $post)
            ii. By default, $objControler->index() will be call, if nothing is match the router defined controler
        V.  Load View:
            i. Please use $this->strTemplateName to define view file.
            ii. All the view files are under [\framework\local\views\] directory
        VI. Load the Controller as you want, such as - use it in a plugin, or a cron job:
            i. Create you custom Controller base and Controller, example: \framework\local\controlers\my_custom_admin\TestController.class.php
            ii.In the code to load the controller:
                $objTest = new stdClass();
                $objTest->router = 'Test'; //The controller you want to load
                mvc::app()->resetRouter('the_method');//The method you want to load
                echo mvc::app()->run('my_custom_admin', $objTest);//pass in the controler base and object, export page as string
                exit();
        VII. Suggestion about ACL (Access Control Lists):
            i. Use the controller entry as the filter:
                public static function load($objPage, $blnAjax, $aryClassName){
                    //you may put you logical in here
                    //decided to load current controller or redirect
                    //example:
                    if(is_user_logged_in()){
                        parent::load($objPage, $blnAjax, get_class(), get_class());
                    }else{
                        $aryUrlQuery['redirect_to'] = urlencode($_SERVER['REQUEST_URI']);
                        wp_redirect(str_replace('http://', 'http://', get_permalink(get_page_by_path('/login')->ID)).'?'.http_build_query($aryUrlQuery));
                        exit();
                    }
                }
                
    d. Model: you can write any model you want, the rules is same as above [4.b]
    e. View:
        I.  All the view files is under [\framework\local\views\] directory
        II. WordPress-Simple-MVC-Framework only support Smarty as view at the moment, all the smarty files please name the extension as [.tpl] uner [\views\]
    
5. Build-in useful extensions:
    a. Data Validate + Filter Ext, please read the comments in :\framework\core\extensions\DataValidateExt.class.php
    b. Mobile Dectect Ext, please read the comments in :\framework\core\extensions\MobileDectectExt.class.php
    c. Tools Ext: \framework\core\extensions\ToolsExt.class.php
        I. Build array tree:
            * $aryTest = ToolsExt::arySetNode($aryTest, $aryTest);
            * $aryTree = ToolsExt::arySetTree($aryTest);
        II. Retrieve User IP
            ToolsExt::retrieveUserIp();
        III. Debug + Tracing
            ToolsExt::_d($value, $blnDumpValues = true, $blnDieAfterDebug = false);

6. Widgets for smarty template:
    a. Suggest in smarty template : {NavWidget::main($data_try_to_send_to, 'widget.you_want_to_call.tpl', $blnAjax, $blnSuccess, $aryExtratrue)}
    b. Example for PHP: \framework\core\widgets\NavWidget.class.php

7. About AJAX:
    a. Ajax is supported by default, in - [mvc::app()->run('controler_base_name', $data, $blnAjax)]
       [$blnAjax = true] will pass back the result in json format:
       {html : 'export data you want', success : true/false, extra_data_name : extra_data_value, extra_data_name : extra_data_value, etc}

8. Extra suggestion:
    a. Use as much Wordpress default supported function as possible, such as: $wpdb for db operation, wp_mail for sending email
    b. If you are looking some more powerful tools, you may install Zend framework. It has lots useful tools.

9. The WP plugins highly recommanded for developing with WordPress-Simple-MVC-Framework
    a. Advanced Custom Fields
    b. CPT-onomies: Using Custom Post Types as Taxonomies
    c. Rewrite Rules Inspector

I hope you enjoy the framework.
If you have any suggestion or find any bug, please contact me: hezachary@gmail.com

Cheers,

Zac