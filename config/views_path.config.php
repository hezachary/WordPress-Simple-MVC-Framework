<?php
/**
 * @return boolean, ture = mobile
 **/
require_once(dirname(__FILE__).'/../core/extensions/MobileDectectExt.class.php');
return MobileDectectExt::checkMobile() ? 'views_mobile' : 'views';
