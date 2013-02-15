<?php
/**
 * Define url rewrite
 * @return
 * array(
 *      array( 
 *          //Rewrite rules list
 *          'regular expression' => 'match target',
 *          'regular expression 2' => 'match target 2',
 *          ...
 *          etc
 *      ),
 *      array(
 *          //the value name list to pull from get_query_var()
 *          'value name 1',
 *          'value name 2',
 *          ...
 *          etc
 *      ),
 * );
 * @caution 
 * Please install plugin [Rewrite Rules Inspector] to validate and active url rewrite.
 **/
return array(
        array( 
            'products/([^?]+)\??' => 'index.php?pagename=products&sub=$matches[1]',
            'robots\.txt' => 'index.php?pagename=robots',
            'sitemap\.xml' => 'index.php?pagename=sitemap',
        ),
        array(
            'sub',
            'r',
        ),
    );