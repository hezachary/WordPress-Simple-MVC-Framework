<?php
class PageModel extends ModelBase{
    
    public function __construct(){
    }
    
    public function loadPageByPath($strPath){
        return get_page_by_path($strPath);
    }
    
    public function retrieveListByField($objSourcePost, $strFieldName){
        $aryPostList = get_field($strFieldName, $objSourcePost->ID);
        if(is_array($aryPostList) && sizeof($aryPostList)){
            foreach($aryPostList as $key => $objPost){
                /*
                //the extra metas yopu need
                $aryPostList[$key]->short_description = get_post_meta($objPost->ID, 'short_description', true);
                //...
                */
            }
        }else{
            $aryPostList = array();
        }
        return $aryPostList;
    }
    
    public function retrieveList($aryCondition, $intPageNum, $intPageStep, $aryOrder = null){
        $intPageNum = abs((int)$intPageNum);
        $intPageNum = $intPageNum ? $intPageNum : 1;
        
        $aryWPCondition = array(
            'post_type'     => 'page', 
            'post_status'   => 'publish', 
            'paged'         => $intPageNum, 
            'posts_per_page'=> $intPageStep
        );
        
        if($aryCondition['post_parent']){
            if(is_array($aryCondition['post_parent'])){
                $aryWPCondition['post_parent__in'] = $aryCondition['post_parent'];
                add_filter( 'posts_where', array('PageModel', 'nacin_post_parent__in'), 10, 2 );
            }else{
                $aryWPCondition['post_parent'] = $aryCondition['post_parent'];
            }
            $aryWPCondition['orderby'] = 'menu_order';
            $aryWPCondition['order'] = 'ASC';
        }else if($aryCondition['post_name']){
            $aryWPCondition['name'] = $aryCondition['post_name'];
        }else if($aryCondition['post__in']){
            $aryWPCondition['post__in'] = $aryCondition['post__in'];
        }else if($aryCondition['search']){
            $aryWPCondition['s'] = $aryCondition['search'];
            $aryWPCondition['category_name'] = 'article';
            $aryWPCondition['orderby'] = 'date';
            $aryWPCondition['order'] = 'DESC';
        }
        
        if($aryOrder){
            $aryWPCondition['orderby'] = $aryOrder['orderby'];
            $aryWPCondition['order'] = $aryOrder['order'];
        }
        
        global $post;
        $arySubPostList = array();
        $objPostListQuery = new WP_Query($aryWPCondition);
        while ( $objPostListQuery->have_posts() ){
            $objPostListQuery->the_post();
            /*
            //the extra metas yopu need
            $post->short_description = get_post_meta($post->ID, 'short_description', true);
            //...
            */
            $arySubPostList[$post->ID] = clone $post;
        };
        // Reset Post Data
        wp_reset_postdata();
        
        if($aryCondition['search']){
            $aryWPCondition['search'] = $objPostListQuery->get('s');
        }else if($aryCondition['post__in']){
            $arySubPostListTmp = $arySubPostList;
            $arySubPostList = array();
            foreach($aryCondition['post__in'] as $post_id){
                if($arySubPostListTmp[$post_id]) {
                    $arySubPostList[$post_id] = clone $arySubPostListTmp[$post_id];
                    $arySubPostListTmp[$post_id] = null;
                }
            }
        }
        
        return array($arySubPostList, $objPostListQuery->max_num_pages > $intPageNum ? $intPageNum : $objPostListQuery->max_num_pages, $objPostListQuery->max_num_pages, $aryWPCondition);
    }
    
    public function nacin_post_parent__in( $where, $object ) {
        global $wpdb, $wp;
        if ( in_array( 'post_parent__in', $wp->private_query_vars ) )
            return $where;
            
        if ( is_numeric( $object->query_vars['post_parent'] ) )
            return $where;
            
        if ( ! empty( $object->query_vars['post_parent__in'] ) ) {
            $post_parent__in = implode(',', array_map( 'absint', $object->query_vars['post_parent__in'] ) );
            $where .= " AND $wpdb->posts.post_parent IN ($post_parent__in)";
	    } elseif ( ! empty( $object->query_vars['post_parent__not_in'] ) ) {
	       $post_parent__not_in = implode(',', array_map( 'absint', $object->query_vars['post_parent__not_in'] ) );
           $where .= " AND $wpdb->posts.post_parent NOT IN ($post_parent__not_in)";
	    }
        return $where;
	}
    
    public static function loadMeta($post){
        $aryMeta = array();
        $aryMeta['keywords'] = get_post_meta($post->ID, 'keywords',1);
        $aryMeta['description'] = get_post_meta($post->ID, 'description',1);
        $aryMeta['author'] = get_post_meta($post->ID, 'author',1);
        $aryMeta['copyright'] = get_post_meta($post->ID, 'copyright',1);
        return $aryMeta;
    }
    
}