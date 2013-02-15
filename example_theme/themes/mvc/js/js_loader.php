<?php
/**
 * Add to .htaccess in web root

# BEGIN JS Loader
<IfModule mod_rewrite.c>
RewriteEngine On
#RewriteCond %{REQUEST_FILENAME} !-f
#RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^wp-content/themes/mvc/js/.+\.js /wp-content/themes/mvc/js/js_loader.php [L]
RewriteRule ^wp-content/themes/mvc/lib/js/js_loader/.+\.js /wp-content/themes/mvc/lib/js/js_loader.php [L]
</IfModule>
# END JS Loader

 * 
 * Load javascript:
 * /wp-content/themes/mvc/lib/js/javascript_filename_1/javascript_filename_2/javascript_filename_3/javascript_filename_4/.../javascript_filename_n.js
 * 
 * Load minify javascript
 * /wp-content/themes/mvc/lib/js/mini/javascript_filename_1/javascript_filename_2/javascript_filename_3/javascript_filename_4/.../javascript_filename_n.js
 * 
 * The library also auto-support latest version of jquery/jquery-ui from google : https://developers.google.com/speed/libraries/devguide
 * And jquery-tools from : http://jquerytools.org
 * Jquery Name : jquery__(version number)
 * Jquery UI name : jquery-ui__(version number)
 * Jquery Tools name : jquery-tools__(version number)[__(tiny/form/all/full)]
 * 
 * Also, it support packaged js libs, by calling name: tools
 * Tools include:
 * jquery.placehold;
 * jquery.json;
 * jquery.base64;
 * jquery.glossary;
 * jquery.background-position-animations
 * However, you can still call them individually.
 * 
 * Important:
 * Jquery has de-conflict by default, you can use [jq] instead of [$]
 * Jquery-UI and Jquery-Tools has conflict, if you try to load them both, : which one load first, the [tabs] api will become [uitabs], and [tooltip] will become [uitooltip].
 * 
 * Example:
 * /wp-content/themes/mvc/lib/js/mini/jquery__1.8.1/jquery-ui__1.8.23/tools.js
 **/
 
define('DIR', dirname(__FILE__));
define('API', 'http://marijnhaverbeke.nl/uglifyjs/');
$strQuery = parse_url(substr($_SERVER['REQUEST_URI'], strlen(dirname($_SERVER['SCRIPT_NAME'])) + 1), PHP_URL_PATH);
$strQuery = strpos($strQuery, '.js') === false ? $strQuery : substr($strQuery, 0, strpos($strQuery, '.js'));
$aryJsList = explode('/', $strQuery);

$blnMini = strtolower($aryJsList[0]) == 'mini' ? true : false;

if($blnMini){
    array_shift($aryJsList);
}

$strJsContent = '';
$aryJsListResult = array();
$getFirst = false; //for jquery-ui and jquery-tools conflict
foreach($aryJsList as $i => $js){
    $js = basename($js);
    $tmpJsContent = null;
    list($type, $version, $mode) = explode('__', $js);
    $version = preg_replace('/[^\d\.]+/', '', basename($version));
    
    $strRemotePath = false;
    $strLocalName = false;
    switch($type){
        case 'jquery':
            $strRemotePath = sprintf('http://ajax.googleapis.com/ajax/libs/jquery/%s/jquery.min.js', $version);
            $strLocalName = sprintf('%s__%s', $type, $version);
            $tmpJsContent = file_get_contents($strRemotePath);
            
            if($tmpJsContent && !$blnMini) $strJsContent .= '/*jquery_ini*/'."\n";
            if($tmpJsContent) $tmpJsContent .= "var jq = jQuery.noConflict();\n\n";
            break;
        case 'jquery-ui':
            $strRemotePath = sprintf('http://ajax.googleapis.com/ajax/libs/jqueryui/%s/jquery-ui.min.js', $version);
            $strLocalName = sprintf('%s__%s', $type, $version);
            $tmpJsContent = file_get_contents($strRemotePath);
            
            if($getFirst){
                if($tmpJsContent){
                    $tmpJsContent = "if(typeof jq.fn.tabs != 'undefined'){jq.fn.uitabs = jq.fn.tabs;delete jq.fn.tabs;}if(typeof jq.fn.tooltip != 'undefined'){jq.fn.uitooltip = jq.fn.tooltip;delete jq.fn.tooltip;}\n\n".$tmpJsContent;
                    if(!$blnMini) $tmpJsContent = '/*jquery tools no conflict with jquery ui */'."\n".$tmpJsContent;
                }
            }else{
                $getFirst = true;
            }
            break;
        case 'jquery-tools':
            switch($mode){
                case 'tiny':
                    //UI Tools: Tabs, Tooltip, Scrollable and Overlay (4.45 Kb)
                case 'form':
                    //Form tools: Dateinput, Rangeinput and Validator. No jQuery library. ( Kb)
                case 'all':
                    //ALL jQuery Tools. No jQuery library
                case 'full':
                    //jQuery Library + ALL jQuery Tools
                    //echo sprintf('http://cdn.jquerytools.org/%s/%s/jquery.tools.min.js', preg_replace('/[^\d\.]+/', '', $version), $mode);die();
                    $mode = basename($mode);
                    $strRemotePath = sprintf('http://cdn.jquerytools.org/%s/%s/jquery.tools.min.js', $version, $mode);
                    $strLocalName = sprintf('%s__%s__%s', $type, $version, $mode);
                    break;
                default:
                    //jQuery Library + UI Tools
                    $strRemotePath = sprintf('http://cdn.jquerytools.org/%s/jquery.tools.min.js', $version);
                    $strLocalName = sprintf('%s__%s', $type, $version);
                    break;
            }
            
            $tmpJsContent = file_get_contents($strRemotePath);
            
            if($getFirst){
                if($tmpJsContent){
                    $tmpJsContent = "if(typeof jq.fn.tabs != 'undefined'){jq.fn.uitabs = jq.fn.tabs;delete jq.fn.tabs;}if(typeof jq.fn.tooltip != 'undefined'){jq.fn.uitooltip = jq.fn.tooltip;delete jq.fn.tooltip;}\n\n".$tmpJsContent;
                    if(!$blnMini) $tmpJsContent = '/*jquery-ui no conflict with jquery tools*/'."\n".$tmpJsContent;
                }
            }else{
                $getFirst = true;
            }
            break;
        case 'tools':
            $aryDefedined  = array(
                'jquery.placehold',
                'jquery.json',
                'jquery.base64',
                'jquery.glossary',
                'jquery.background-position-animations',
            );
            
            $tmpSubJsContent = '';
            foreach($aryDefedined as $js_file){
                $tmpSubJsContent = file_get_contents(DIR.DIRECTORY_SEPARATOR.$js_file.'.js');
                
                if($tmpSubJsContent){
                    if(!$blnMini) $tmpJsContent .= '/*'.$js_file.'.js'.'*/'."\n";
                    $tmpJsContent .= $tmpSubJsContent."\n;\n\n";
                }
            }
            $strLocalName = 'tools';
            break;
        default:
            $strLocalPath = DIR.DIRECTORY_SEPARATOR.$js.'.js';
            if(file_exists($strLocalPath)){
                $tmpJsContent = file_get_contents($strLocalPath);
                $strLocalName = $js;
            }
            break;
    }
    
    if($tmpJsContent){
        if(!$blnMini) $strJsContent .= '/*'.$js.'.js'.'*/'."\n";
        $strJsContent .= $tmpJsContent."\n;\n\n";
        $aryJsListResult[] = $strLocalName;
    }
}
if($blnMini){
    parseMini($aryJsListResult, $strJsContent);
}else{
    header('Content-type: application/javascript');
    echo $strJsContent;
}
exit();

function parseMini($aryJsListResult, $strJsContent){
    $strLocalPath = DIR.DIRECTORY_SEPARATOR.'mini'.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $aryJsListResult).'.js';
    if(sizeof($aryJsListResult)){
        $strContent = '';
        if(file_exists($strLocalPath)){
            $strContent = file_get_contents($strLocalPath);
        }else{
            $ch = curl_init(API);
            curl_setopt_array($ch, array(
                CURLOPT_HEADER => false,
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => 'js_code='.urlencode($strJsContent),
                CURLOPT_RETURNTRANSFER => 1,
            ));
            $strContent = curl_exec($ch); 
            curl_close($ch);
            
            $strDir = dirname($strLocalPath);
            if(!is_dir($strDir)){
                mkdir($strDir, 0777, 1);
            } 
            file_put_contents($strLocalPath, $strContent);
        }
        header('Content-type: application/javascript');
        echo $strContent;
    }else{
        header('HTTP/1.0 404 Not Found');
    }
}