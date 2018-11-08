<?php

error_reporting(E_ALL);

$timezone_identifier = @date_default_timezone_get();
if (empty($timezone_identifier)) {
    $timezone_identifier = 'UTC';
}
date_default_timezone_set($timezone_identifier);
unset($timezone_identifier);

spl_autoload_register(
    function ($class) {
        if (strpos($class, 'Punic\\Test\\') !== 0) {
            return;
        }
        $file = __DIR__.DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen('Punic\\Test'))).'.php';
        if (is_file($file)) {
            require_once $file;
        }
    }
);

require_once dirname(__DIR__).'/punic.php';
$dataDir = (string) getenv('PUNIC_TEST_DATADIR');
if ($dataDir !== '') {
    if (!is_dir($dataDir)) {
        throw new Exception("The PUNIC_TEST_DATADIR environment variable points to a non existing directory ({$dataDir}).");
    }
    Punic\Data::setDataDirectory($dataDir);
}
unset($dataDir);
