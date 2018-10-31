<?php

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$rootDir = dirname(__DIR__);
$srcDir = $rootDir . DIRECTORY_SEPARATOR . 'src';

$cmd = 'git -C ' . escapeshellarg($rootDir) . ' tag --list --sort=version:refname';
exec("{$cmd} 2>&1", $output, $rc);
if ($rc !== 0) {
    throw new Exception(sprintf("Failed to retrieve the list of git tags:\n%s", trim(implode("\n", $output))));
}
$latestVersion = null;
while (($line = array_pop($output)) !== null) {
    if (preg_match('/^(?:v\.?\s*)?(\d+\.\d+\.\d+)/',  $line, $m)) {
        $latestVersion = $m[1];
        break;
    }
}
if ($latestVersion === null) {
    throw new Exception('Failed to retrieve the latest version from the git tags.');
}

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in($srcDir)
;

return new Sami($iterator, array(
    'title' => "Punic API v.{$latestVersion}",
    'theme' => 'default',
    'build_dir' => __DIR__ . '/themes/punic/static/api',
    'cache_dir' => __DIR__ . '/cache/sami',
    'default_opened_level' => 2,
));
