<?php
/**
 * @return boolean, ture = mobile
 **/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'extensions'.DIRECTORY_SEPARATOR.'MobileDectectExt.class.php');
return MobileDectectExt::checkMobile() ? 'views_mobile' : 'views';
