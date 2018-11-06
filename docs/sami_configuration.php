<?php

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$rootDir = dirname(__DIR__);
$srcDir = $rootDir.DIRECTORY_SEPARATOR.'src';

$changelog = @file_get_contents($rootDir.'/CHANGELOG.md');
if ($changelog === false) {
    throw new Exception('Failed to read the CHANGELOG file.');
}
$isDev = false;
$version = null;
foreach (explode("\n", str_replace("\r", "\n", $changelog)) as $line) {
    $line = trim($line);
    if ($line === '### NEXT (YYYY-MM-DD)') {
        $isDev = true;
    } elseif (preg_match('/^### (\d+\.\d+\.\d+)/', $line, $m)) {
        $version = $m[1];
        break;
    }
}
if ($version === null) {
    throw new Exception('Failed to detect the version from the CHANGELOG file.');
}
if ($isDev) {
    list($major, $minor, $revision) = explode('.', $version);
    $revision = 1 + (int) $revision;
    $version = "{$major}.{$minor}.{$revision}-dev";
}

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in($srcDir)
    ->exclude('data')
;

return new Sami($iterator, array(
    'title' => "Punic API v.{$version}",
    'theme' => 'default',
    'build_dir' => __DIR__.'/themes/punic/static/api',
    'cache_dir' => __DIR__.'/cache/sami',
    'default_opened_level' => 2,
));
