<?php
class NavWidget extends WidgetBase{
    public static $aryNav = array();
    public static $aryPostList = array();
    public static $intHomeId = null;
    public static $intSelectedPostId = null;
    
    public static function main($source, $strTemplate = null, $blnAjax = false, $blnSuccess = false, $aryExtra = array()){
        list($aryNav, $aryPostList, $intHomeId) = self::retrieveNavTree($source);
        return self::render(array('nav' => $aryNav[$intHomeId], 'items' => $aryPostList, 'source' => $source), $strTemplate, $blnAjax, $blnSuccess, $aryExtra);
    }
    
    public static function breadcrumb($source, $strTemplate = null, $blnAjax = false, $blnSuccess = false, $aryExtra = array()){
        list($aryNav, $aryPostList, $intHomeId, $intSelectedPostId) = self::retrieveNavTree($source);
        $aryParentIdList = self::retrieveParentId($aryPostList, $intSelectedPostId);
        return self::render(array('nav' => $aryParentIdList, 'items' => $aryPostList, 'source' => $source), $strTemplate, $blnAjax, $blnSuccess, $aryExtra);
    }
    
    public static function retrieveNavTree($strSelectedPostName){
        //the nav tree sample only work, if the root page slug is home, and all other page is start from home
        if(!sizeof(self::$aryNav)){
            $objPostListQuery = new WP_Query(array('post_type'=>'page', 'orderby' => 'menu_order', 'post_status'=>'publish', 'order' => 'ASC', 'posts_per_page'=>'-1'));
            global $post;
            $aryPostListTmp = array();
            $aryPostMenuList = array();
            while ( $objPostListQuery->have_posts() ){
                $objPostListQuery->the_post();
                if($post->post_name == 'home') self::$intHomeId = $post->ID;
                if($post->post_name == $strSelectedPostName) self::$intSelectedPostId = $post->ID;
                $post->permalink = get_permalink($post->ID);
                $aryPostListTmp[$post->ID] = get_object_vars($post);
                $aryPostMenuList[$post->ID] = $post->menu_order;
            };
            // Reset Post Data
            wp_reset_postdata();
            
            asort($aryPostMenuList);
            
            $aryPostList = array();
            foreach($aryPostMenuList as $intPostId => $intMenuOrder){
                $aryPostList[$intPostId] = $aryPostListTmp[$intPostId];
                $aryPostListTmp[$intPostId] = null;
            }
            $aryPostListTmp = null;
            
            self::$aryPostList = ToolsExt::arySetNode('ID', 'post_parent', $aryPostList, $aryPostList);
            $aryNav = ToolsExt::arySetTree('ID', 'post_parent', self::$aryPostList);
            self::$aryNav = array_shift($aryNav);
        }
        return array(self::$aryNav, self::$aryPostList, self::$intHomeId, self::$intSelectedPostId);
    }
    
    private static function retrieveParentId($aryPostList, $intSelectedPostId, $aryParentIdList = array()){
        array_unshift($aryParentIdList, $intSelectedPostId);
        $aryParentPost = $aryPostList[$aryPostList[$intSelectedPostId]['post_parent']];
        if(is_array($aryParentPost) && sizeof($aryParentPost) && $aryParentPost['ID']){
            $aryParentIdList = self::retrieveParentId($aryPostList, $aryParentPost['ID'], $aryParentIdList);
        }
        return $aryParentIdList;
    }
    
}