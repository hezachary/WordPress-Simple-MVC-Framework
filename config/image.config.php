<?php
/**
 * Defined image upload size list
 * @return array(
 *      'basename' => array(
 *          array('sub-name', (int)width, (int)height, (boolean)crop),
 *          array('sub-name-2', (int)width, (int)height, (boolean)crop),
 *          ...
 *      ),
 *      ...
 * );
 * 
 * @frontend wp_get_attachment_image_src($intImageId, 'basename-sub-name')
 **/
return array(
        'homepage' => array(
            array('bottom-image', 308, 174, 1),
            array('carousel-image', 950, 456, 1),
        ),
        
        'genetic_product_image' => array(
            array('list-thumb', 236, 203, 1),
            array('thumb', 37, 45, 1),
            array('related-thumb', 233, 203, 1),
            array('enlarged', 474, 615, 1),
        ),
        
        'article' => array(
            array('image', 670, 9999, 0),
            array('internal-image', 610, 9999, 0),
            array('related-thumb', 233, 203, 1),
        ),
    );