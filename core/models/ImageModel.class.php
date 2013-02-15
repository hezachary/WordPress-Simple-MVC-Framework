<?php
class ImageModel extends ModelBase{
    
    public static $aryImageSizeList = array();
    public static function loadAdmin($aryImageSizeList){
        self::$aryImageSizeList = $aryImageSizeList;
        add_filter('image_size_names_choose', array('ImageModel','adminPanelImageSizesSettings'));
        //add_filter('wp_ajax_image-editor', array('ImageModel','check'), 0);
    }
    
    public static function check(){
        if($_REQUEST['do'] == 'save') {
            @ini_set( 'memory_limit', apply_filters( 'admin_memory_limit', WP_MAX_MEMORY_LIMIT ) );
            
            $post_id = (int)$_POST['postid'];
            $post = get_post($post_id);
            $post_id = $post->ID;
            $path = get_attached_file($post->ID);
            $meta = wp_get_attachment_metadata($post->ID);
            
            $aryCustomSizeList = self::retrieveImageSize(self::$aryImageSizeList);
            
            $arySetting = $aryCustomSizeList[$_POST['target']];
            
            if($arySetting && !empty($_REQUEST['history']) ) {
                $changes = json_decode( stripslashes($_REQUEST['history']) );
                
                /*
                foreach($changes as $k => $objChange){
                    if($objChange->c->w){
                        $changes[$k]->c->w = $arySetting[1];
                    }
                    if($objChange->c->h){
                        $changes[$k]->c->h = $arySetting[2];
                    }
                }
                */
                if ( $changes ){
                    require_once(ABSPATH.'wp-admin'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'image-edit.php');
                    $img = load_image_to_edit($post_id, $post->post_mime_type);
                    $img = image_edit_apply_changes($img, $changes);
                    $path_parts = pathinfo( $path );
                    $new_path = $path_parts['dirname'].DIRECTORY_SEPARATOR.$meta['sizes'][$_POST['target']]['file'];
                	// save the full-size file, also needed to create sub-sizes
                	if ( !wp_save_image_file($new_path, $img, $post->post_mime_type, $post_id) ) {
                		$return->error = esc_js( __('Unable to save the image.') );
                	}else{
                    	$return->msg = esc_js( __('Image saved') );
                	}
                }
            }else{
                $return->error = esc_js( __('Please edit image and select type you want apply.') );
            }
            
            echo json_encode($return->error ? $return->error : $return->msg);
            //$resized = image_make_intermediate_size($path, $arySetting[1], $arySetting[2], $arySetting[3] );
            die();
        }
        $aryCustomSizeList = array();
        foreach(self::$aryImageSizeList as $strFieldName => $arySettingList){
            foreach($arySettingList as $arySettings){
                $aryCustomSizeList[$strFieldName.'-'.$arySettings[0]] = ucwords(str_replace(array('-','_'), ' ', $strFieldName.'-'.$arySettings[0]));
            }
        }
        ?>
    <script type="text/javascript">
    // <![CDATA[ 
        if(typeof jq == 'undefined') var jq = jQuery.noConflict();
        jq("div.imgedit-wrap").ready(function(){
            /**/
            var image_list = <?php echo json_encode($aryCustomSizeList);?>;
            var target_node = jq('label.imgedit-label:eq(1)');
            jq('label.imgedit-label:not(:eq(1))').remove();
            var target_node_input = target_node.find('input');
            
            for(i in image_list){
                target_node.empty();
                target_node_input.val(i);
                target_node.append(target_node_input);
                target_node.append(image_list[i]);
                target_node.after(target_node.clone());
            }
            target_node.after('<label><input type="checkbox" name="no_wh" value="1" checked="checked" />Ignore Width and Height</lable>');
            
            target_node.remove();
            /**/
        });
    // ]]>
    </script>
        <?php
    }
    
    public static function loadImageSize($aryImageSizeList){
        if (!function_exists( 'add_image_size' ) ) return;
        $aryCustomSizeList = self::retrieveImageSize($aryImageSizeList);
        foreach($aryCustomSizeList as $strFieldName => $arySettings){
            add_image_size( $strFieldName, $arySettings[1], $arySettings[2], $arySettings[3] );
        }
        add_theme_support( 'post-thumbnails' );
    }
    
    public static function retrieveImageSize($aryImageSizeList){
        $aryCustomSizeList = array();
        foreach($aryImageSizeList as $strFieldName => $arySettingList){
            foreach($arySettingList as $arySettings){
                $aryCustomSizeList[$strFieldName.'-'.$arySettings[0]] = $arySettings;
            }
        }
        return $aryCustomSizeList;
    }
    
    public static function adminPanelImageSizesSettings($aryOrgImageSizeList){
        $aryCustomSizeList = array();
        foreach(self::$aryImageSizeList as $strFieldName => $arySettingList){
            foreach($arySettingList as $arySettings){
                $aryCustomSizeList[$strFieldName.'-'.$arySettings[0]] = ucwords(str_replace(array('-','_'), ' ', $strFieldName.'-'.$arySettings[0]));
            }
        }
        return array_merge($aryOrgImageSizeList, $aryCustomSizeList);
    }
    
    public static function wp_get_attachment_image_src($intImageId, $size='thumbnail', $icon = false, $strCdnHost){
        $aryImageAttr = wp_get_attachment_image_src($intImageId, $size, $icon);
        //aryCDNSettings
        if($strCdnHost){
            $strCdnDomain = sprintf('%s://%s', is_ssl() ? 'https' : 'http', $strCdnHost);
            $aryImageAttr[0] = str_replace(site_url(), $strCdnDomain, $aryImageAttr[0]);
        }
        return $aryImageAttr;
    }
}