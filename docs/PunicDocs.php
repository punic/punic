<?php

namespace PunicDocs;

use ApiGen\FileSystem\FileSystem;
use Exception;

class PunicDocs
{
    /**
     * @var self|null
     */
    private static $instance;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var string
     */
    private $srcDir;

    /**
     * @var string
     */
    private $apiDir;

    /**
     * @var string
     */
    private $apigenBin;

    /**
     * @var string|null
     */
    private $punicVersion;

    private function __construct()
    {
        $this->rootDir = str_replace(DIRECTORY_SEPARATOR, '/', dirname(__DIR__));
        $this->srcDir = "{$this->rootDir}/src";
        $this->apiDir = str_replace(DIRECTORY_SEPARATOR, '/', __DIR__).'/themes/punic/static/api';
        $this->apigenBin = str_replace(DIRECTORY_SEPARATOR, '/', __DIR__).'/vendor/bin/apigen';
    }

    /**
     * @return string
     */
    public function getRootDir()
    {
        return $this->rootDir;
    }

    /**
     * @return string
     */
    public function getSrcDir()
    {
        return $this->srcDir;
    }

    /**
     * @return string
     */
    public function getApiDir()
    {
        return $this->apiDir;
    }

    /**
     * @return string
     */
    public function getPunicVersion()
    {
        if ($this->punicVersion === null) {
            $changelog = @file_get_contents($this->getRootDir().'/CHANGELOG.md');
            if ($changelog === false) {
                throw new Exception('Failed to read the CHANGELOG file.');
            }
            $isDev = false;
            $m = null;
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
            $this->punicVersion = $version;
        }

        return $this->punicVersion;
    }

    /**
     * @return string
     */
    public function getApigenBin()
    {
        return $this->apigenBin;
    }

    /**
     * @return self
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function updateDocs()
    {
        $instance = self::getInstance();
        FileSystem::purgeDir($instance->getApiDir());
        $cmd = implode(' ', array(
            escapeshellarg($instance->getApigenBin()),
            'generate',
            escapeshellarg('--source='.$instance->getSrcDir()),
            escapeshellarg('--destination='.$instance->getApiDir()),
            '--access-levels=public',
            '--annotation-groups=todo,deprecated',
            escapeshellarg('--exclude=*/data/*'),
            '--charset=utf-8',
            '--main=Punic',
            '--tree',
            '--template-theme=bootstrap',
            escapeshellarg('--title=Punic v'.$instance->getPunicVersion()),
        ));
        $rc = -1;
        passthru($cmd, $rc);
        if ($rc !== 0) {
            throw new Exception('apigen failed!');
        }
    }
}
