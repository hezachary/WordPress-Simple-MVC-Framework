<?php
/**
 * Defined the config files you would like to load
 **/
$path = dirname(__FILE__) . self::DIR_SEP;

$this->view_path = require_once( $path . 'views_path.config.php');
$this->aryPathList = require_once( $path . 'path.config.php');
$this->aryClassDefine = require_once( $path . 'class_path.config.php');
$this->aryImageSizeList = require_once( $path . 'image.config.php');
$this->aryRouterList = require_once( $path . 'router.config.php');
$this->aryTaxonomyList = require_once( $path . 'taxonomy.config.php');
$this->aryCDNSettings = require_once( $path . 'cdn.config.php');
        