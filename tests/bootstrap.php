<?php
error_reporting(E_ALL);

if (is_file(dirname(__DIR__) . '/vendor/autoload.php')) {
    require_once dirname(__DIR__) . '/vendor/autoload.php';
} elseif (is_file(dirname(dirname(__DIR__)) . '/common/code/Data.php')) {
    require_once dirname(dirname(__DIR__)) . '/common/code/Data.php';
    require_once dirname(__DIR__) . '/code/Calendar.php';
}

PHPUnit_Framework_Error_Notice::$enabled = true;
