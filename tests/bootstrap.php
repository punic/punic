<?php
error_reporting(E_ALL);

$timezone_identifier = @date_default_timezone_get();
if(empty($timezone_identifier)) {
    $timezone_identifier = 'UTC';
}
date_default_timezone_set($timezone_identifier);
unset($timezone_identifier);

require_once dirname(__DIR__) . '/punic.php';

PHPUnit_Framework_Error_Notice::$enabled = true;
