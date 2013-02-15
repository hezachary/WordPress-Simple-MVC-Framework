<?php
class TaxonomyModel extends ModelBase{
    
    public static $aryTaxonomyList = array();
    public static function loadTaxonomy($aryTaxonomyList){
        self::$aryTaxonomyList = $aryTaxonomyList;
        add_action( 'init', array('TaxonomyModel','setTaxonomy'), 0 );
    }
    
    public static function setTaxonomy(){
        foreach(self::$aryTaxonomyList as $strLabel => $dataSlug ){
            register_taxonomy('categories_'.(is_array($dataSlug) ? $dataSlug[0] : $dataSlug), $dataSlug, array(
                'show_ui' => true,
                'show_admin_column' => true,
                'hierarchical' => true,
                'query_var' => true,
                'labels' => array(
                    'name' => _x( $strLabel, 'Taxonomy general name' ),
                    'singular_name' => _x( $strLabel, 'taxonomy singular name' ),
                    'search_items' =>  __( sprintf('Search %s', $strLabel) ),
                    'all_items' => __( sprintf('All %s', $strLabel) ),
                    'parent_item' => __( sprintf('Parent %s', $strLabel) ),
                    'parent_item_colon' => __( sprintf('Parent %s:', $strLabel) ),
                    'edit_item' => __( sprintf('Edit %s', $strLabel) ), 
                    'update_item' => __( sprintf('Update %s', $strLabel) ),
                    'add_new_item' => __( sprintf('Add New %s', $strLabel) ),
                    'new_item_name' => __( sprintf('New %s Name', $strLabel) ),
                    'menu_name' => __( $strLabel ),
                ),
            ));
        }
    }
}