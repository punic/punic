<?php

error_reporting(-1);

$timezone_identifier = @date_default_timezone_get();
date_default_timezone_set($timezone_identifier ? $timezone_identifier : 'UTC');
unset($timezone_identifier);

$dataDir = (string) getenv('PUNIC_TEST_DATADIR');
if ($dataDir !== '') {
    if (!is_dir($dataDir)) {
        throw new Exception("The PUNIC_TEST_DATADIR environment variable points to a non existing directory ({$dataDir}).");
    }
    Punic\Data::setDataDirectory($dataDir);
}
unset($dataDir);

if (class_exists('PHPUnit\\Runner\\Version') && version_compare(PHPUnit\Runner\Version::id(), '9') >= 0) {
    class_alias('Punic\\Test\\TestCase9', 'Punic\\Test\\TestCase');
} elseif (class_exists('PHPUnit\\Runner\\Version') && version_compare(PHPUnit\Runner\Version::id(), '7') >= 0) {
    class_alias('Punic\\Test\\TestCase7', 'Punic\\Test\\TestCase');
} else {
    class_alias('Punic\\Test\\TestCase4', 'Punic\\Test\\TestCase');
}
