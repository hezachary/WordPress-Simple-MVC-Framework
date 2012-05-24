WordPress-Simple-MVC-Framework
==============================

A simple MVC framework for Wordpress, support static class methods, merged with Smarty Template.
WordPress-Simple-MVC-Framework is under MIT Copyright (c) 2012 Zhehai He <hezahcary@gmail.com>
version 0.9

Installation:
------------
a.  Please copy all the file to your theme folder.

Example:

Your project theme : `\wp-content\themes\twentyeleven`

The MVC framework : `\wp-content\themes\twentyeleven\framework`

After you install the mvc, the `mvc.ini.php` is at `\wp-content\themes\twentyeleven\framework\mvc.ini.php`

b.  The MVC also support Zend Framework and Smarty by default, to install them, please:

1. Zend Framework - Copy all the Zend framework files in \core\libs\Zend
2. Smarty - Copy all the Smarty files in \core\libs\Smarty

Settings:
------------
a.  Please read the file comments in framework\config

How to use:
------------
a. In you functions.php: 

        <?php
        require_once(dirname(__FILE__).'/framework/mvc.ini.php');
        
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
------------
a. Load local class first:

1. `framework\core\` is for core code
2. `framework\local\` is for project code
3. If local and core has same file and class, local will be load. This rule almost apply to all the classes and files.

b. Locate class by CamelCase:

1. Last CamelCase name become the base load folder
2. `Classname1Classname2Classname3`, will be load in :

 i. `classname3\Classname1Classname2Classname3` - right
 
 ii. `classname3\classname1\Classname2Classname3` - right
 
 iii. `classname3\classname1\classname2\Classname3` - wrong

c. Controler:

1. Use `post-type` as controler name:

i. `mvc::app()->run('page', $post)` will load `controlers\PageController.class.php`

2. Use static method `load($objPage, $blnAjax, $aryClassName)` to locate proper class to ini the controler object.

Please read : `framework\core\base\ControllerBase.class.php` comments for `public static function load`

    Samples:
                \framework\core\controlers\PageController.class.php
                \framework\core\controlers\page\HomeController.class.php
                \framework\core\controlers\page\HomeStatusController.class.php

3. Retrieve values from defined source with simple filter:

i. Like most modern mvc, WordPress-Simple-MVC-Framework also support simple way to pass value direct from PHP magic global values

Such as: $_GET, $_POST, etc

The data source is base on supplied info in comments for the method

There are two type format involved: `@packed and (@source + @param + @param + @param + ... etc)`

    Example: `core\controlers\page\HomeStatusController.class.php`
                /**
                 * @source $_GET
                 * @param $page_id int # you can only put native type here, no object type
                 **/
                public function ajax($page_id)
                
                /**
                 * @packed
                 * If you use packed, please defined the source (in lower case) as the paramater
                 * In here, it is `$post` - public function form(array $post)
                 **/
                public function form(array $post){//Inline area support auto convert array

4. Router for choose a method in a controler:

i. $_REQUEST`'r'` is the name of the method in the controler

                Example: $_REQUEST`'r'` = 'form', $objControler->form(array $post)
                
ii. By default, $objControler->index() will be call, if nothing is match the router defined controler

d. Model, you can write any model you want, the rules is same as above `4.b`

e. WordPress-Simple-MVC-Framework only support Smarty as view at the moment, all the smarty files please name the extension as `.tpl` uner `\views\`
    
Build in useful extension:
------------
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
------------
    a. Suggest in smarty template : {NavWidget::main($data_try_to_send_to, 'widget.you_want_to_call.tpl', $blnAjax, $blnSuccess, $aryExtratrue)}
    b. Example for PHP: \framework\core\widgets\NavWidget.class.php

7. About AJAX:
------------
    a. Ajax is supported by default, in - `mvc::app()->run('controler_base_name', $data, $blnAjax)`
       `$blnAjax = true` will pass back the result in json format:
       {html : 'export data you want', success : true/false, extra_data_name : extra_data_value, extra_data_name : extra_data_value, etc}

8. Extra suggestion:
------------
    a. Use as much Wordpress default supported function as possible, such as: $wpdb for db operation, wp_mail for sending email
    b. If you are looking some more powerful tools, you may install Zend framework. It has lots useful tools.


I hope you enjoy the framework.
If you have any suggestion or find any bug, please contact me: hezachary@gmail.com

Cheers,

Zac