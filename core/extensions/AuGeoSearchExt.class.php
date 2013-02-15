<?php
global $wpdb, $table_prefix;
if(!isset($wpdb->au_geodata))$wpdb->au_geodata = $table_prefix . 'extension_au_geodata';
class AuGeoSearchExt{
    
    private static $intDistance = 5;
    private static $strPostcode;
    public static function GetPostCodeListByPostCode($strPostcode, $intDistance){
        if(!$strPostcode || strlen(trim($strPostcode)) != 4) return false;
        
        global $wpdb;
        
        self::$intDistance = (int)$intDistance > 0 ? (int)$intDistance : self::$intDistance;
        self::$strPostcode = trim($strPostcode);
        
        $objArea = self::GetInfoByPostCode($strPostcode);
        
        $objArea->perimeter_area = $objArea->perimeter_area && is_string($objArea->perimeter_area) ? unserialize($objArea->perimeter_area) : $objArea->perimeter_area;
        if(!is_array($objArea->perimeter_area) || sizeof($objArea->perimeter_area) < 1){
            //retrieve data
            $dubLatitude    = $objArea->lat / 180 * M_PI;
            $dubLongitude   = $objArea->long / 180 * M_PI;
            
            $aryRelatedAreaList = $wpdb->get_results(
                $wpdb->prepare(
                    '
                    SELECT  DISTINCT postcode, 
                            (
                                6367.41 * SQRT(
                                    2 * (
                                        1 - cos(RADIANS(`lat`)) * cos(%f) * (
                                            sin(RADIANS(`long`)) * sin(%f) + cos(RADIANS(`long`)) * cos(%f)
                                        ) - sin(RADIANS(`lat`)) * sin(%f)
                                    )
                                )
                            ) AS distance
                    FROM    '.$wpdb->au_geodata.'
                    WHERE   (
                                6367.41 * SQRT(
                                    2 * (
                                        1 - cos(RADIANS(`lat`)) * cos(%f) * (
                                            sin(RADIANS(`long`)) * sin(%f) + cos(RADIANS(`long`)) * cos(%f)
                                        ) - sin(RADIANS(`lat`)) * sin(%f)
                                    )
                                )
                            ) <= %f
                    OR      postcode = %s     
                    ORDER BY distance
                    ',
                    array(
                        $dubLatitude,
                        $dubLongitude,
                        $dubLongitude,
                        $dubLatitude,
                        
                        $dubLatitude,
                        $dubLongitude,
                        $dubLongitude,
                        $dubLatitude,
                        
                        self::$intDistance,
                        self::$strPostcode
                    )
                ),
                OBJECT
            );
            $objArea->perimeter_area = $aryRelatedAreaList;
            
            //update data
            $wpdb->update(
                $wpdb->au_geodata,
                array('perimeter_area' => serialize($objArea->perimeter_area),),
                array('id' => $objArea->id,)
            );
        }
        
        return $objArea;
    }
    
    private static $_objArea = null;
    public static function GetInfoByPostCode($strPostcode){
        if(!$strPostcode || strlen(trim($strPostcode)) != 4) return false;
        
        if(!self::$_objArea || self::$_objArea->postcode != $strPostcode){
            global $wpdb;
            self::$strPostcode = trim($strPostcode);
            
            self::$_objArea = $wpdb->get_row(
                $wpdb->prepare('SELECT `id`, `lat`, `suburb`, `state`, `proper_state`, `long`, `perimeter_area` FROM '.$wpdb->au_geodata.' WHERE postcode = %s LIMIT 1', self::$strPostcode),
                OBJECT
            );
            self::$_objArea->postcode = self::$strPostcode;
        }
        
        return self::$_objArea;
        
    }
    
}
