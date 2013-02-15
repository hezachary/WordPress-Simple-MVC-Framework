<?php
$strDir = dirname(__FILE__);
$strDir = substr($strDir, 0, strpos($strDir, '/wp-content/'));
require($strDir.'/wp-load.php');

$objReminder = new stdClass();
$objReminder->router = 'Reminder';
mvc::app()->resetRouter('send_remind_email');
echo mvc::app()->run('admin', $objReminder);