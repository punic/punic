<?php
function handleError($errno, $errstr, $errfile, $errline)
{
    if ($errno == E_NOTICE || $errno == E_WARNING) {
        throw new Exception("$errstr in $errfile @ line $errline", $errno);
    }
}

set_error_handler('handleError');

try {
    echo "Initializing... ";
    define('ROOT_DIR', dirname(__DIR__));
    define('SOURCE_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'code');
    define('TEMP_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'temp');
    define('CONFIG_FILE', TEMP_DIR . DIRECTORY_SEPARATOR . 'apigen.neon');
    define('WEBSITE_DIR', TEMP_DIR . DIRECTORY_SEPARATOR . 'website');
    define('DEST_PHPDOCS_DIR', WEBSITE_DIR . DIRECTORY_SEPARATOR . 'docs');
    define('DEST_COVERAGE_DIR', WEBSITE_DIR . DIRECTORY_SEPARATOR . 'coverage');
    if (!is_dir(TEMP_DIR)) {
            @mkdir(TEMP_DIR, 0777, true);
        if (!is_dir(TEMP_DIR)) {
            throw new Exception('Unable to create the directory ' . TEMP_DIR);
        }
    }
    $someDoIsSet = false;
    if (isset($argv)) {
        foreach ($argv as $i => $arg) {
            if ($i > 0) {
                if (stripos($arg, 'doc') !== false) {
                    $someDoIsSet = true;
                    defined('DO_PHPDOCS') or define('DO_PHPDOCS', true);
                }
                if (stripos($arg, 'cover') !== false) {
                    $someDoIsSet = true;
                    defined('DO_COVERAGE') or define('DO_COVERAGE', true);
                }
            }
        }
    }
    if (!defined('DO_PHPDOCS')) {
        define('DO_PHPDOCS', $someDoIsSet ? false : true);
    }
    if (!defined('DO_COVERAGE')) {
        define('DO_COVERAGE', $someDoIsSet ? false : true);
    }

    echo "done.\n";

    if (!is_dir(WEBSITE_DIR)) {
        echo "Fetching repository... ";
        $output = array();
        exec('git clone git@github.com:punic/punic.github.io.git ' . escapeshellarg(WEBSITE_DIR) . ' 2>&1', $output, $rc);
        if ($rc !== 0) {
            throw new Exception("Failed to clone website repository:\n" . trim(implode("\n", $output)));
        }
        echo "done.\n";
    }

    if (DO_PHPDOCS) {
        echo "Cleanup older docs... ";
        if (is_dir(DEST_PHPDOCS_DIR)) {
            deleteFromFilesystem(DEST_PHPDOCS_DIR);
        }
        @mkdir(DEST_PHPDOCS_DIR, 0777, true);
        if (!is_dir(DEST_PHPDOCS_DIR)) {
            throw new Exception('Unable to create the directory ' . DEST_PHPDOCS_DIR);
        }
        echo "done.\n";

        echo "Creating configuration file... ";
        $v = array(
            'from' => addslashes(SOURCE_DIR),
            'to' => addslashes(DEST_PHPDOCS_DIR),
            'template' => addslashes(ROOT_DIR . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'apigen' . DIRECTORY_SEPARATOR . 'apigen' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'config.neon'),
        );
        if(file_put_contents(CONFIG_FILE, <<<EOT
source: "{$v['from']}"
destination: "{$v['to']}"
extensions: php
charset: utf-8
title: Punic APIs
groups: namespaces
accessLevels: public
php: no
tree: yes
todo: yes
quiet: yes
progressbar: no
templateConfig: "{$v['template']}"
EOT
        ) === false) {
            throw new Exception('Failed to create temporary ApiGen configuration');
        }
        echo "done.\n";

        echo "Creating doc files... ";
        $output = array();
        exec(
            'php'
            . ' ' . escapeshellarg(ROOT_DIR . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'apigen' . DIRECTORY_SEPARATOR . 'apigen' . DIRECTORY_SEPARATOR . 'apigen.php')
            . ' --config ' . escapeshellarg(CONFIG_FILE)
            . ' 2>&1',
            $output,
            $rc
        );
        if ($rc !== 0) {
            throw new Exception("ApiGen failed:\n" . trim(implode("\n", $output)));
        }
        echo "done.\n";
    }

    if (DO_COVERAGE) {
        echo "Cleanup older coverage... ";
        if (is_dir(DEST_COVERAGE_DIR)) {
            deleteFromFilesystem(DEST_COVERAGE_DIR);
        }
        @mkdir(DEST_COVERAGE_DIR, 0777, true);
        if (!is_dir(DEST_COVERAGE_DIR)) {
            throw new Exception('Unable to create the directory ' . DEST_COVERAGE_DIR);
        }
        echo "done.\n";

        echo "Create coverage report... ";
        $output = array();
        exec(
            'phpunit'
            . ' --disallow-test-output --stop-on-error --stop-on-failure'
            . ' --configuration ' . escapeshellarg(ROOT_DIR . DIRECTORY_SEPARATOR . 'phpunit.xml')
            . ' --coverage-html ' . escapeshellarg(DEST_COVERAGE_DIR)
            . ' 2>&1',
            $output,
            $rc
        );
        if ($rc !== 0) {
            throw new Exception("ApiGen failed:\n" . trim(implode("\n", $output)));
        }
        echo "done.\n";

        echo "Fixing report paths... ";
        $rxSearch = '/\\b';
        foreach (explode('/', str_replace(DIRECTORY_SEPARATOR, '/', ROOT_DIR)) as $i => $s) {
            if ($i > 0) {
                $rxSearch .= '[\\\\\\/]';
            }
            $rxSearch .= preg_quote($s);
        }
        $rxSearch .= '[\\\\\\/]?/i';
        fixCoveragePath($rxSearch, '/punic/', DEST_COVERAGE_DIR);
        echo "done.\n";
    }
    die(0);
} catch (Exception $x) {
    echo $x->getMessage(), "\n";
    die(1);
}

function deleteFromFilesystem($path)
{
    if (is_file($path)) {
        if (unlink($path) === false) {
            throw new Exception("Failed to delete file $path");
        }
    } else {
        $contents = scandir($path);
        if ($contents === false) {
            throw new Exception("Failed to retrieve the file list of $path");
        }
        foreach (array_diff($contents, array('.', '..')) as $item) {
            deleteFromFilesystem($path . DIRECTORY_SEPARATOR . $item);
        }
        if (rmdir($path) === false) {
            throw new Exception("Failed to delete directory $path");
        }
    }
}

function fixCoveragePath($rxSearch, $rxReplace, $path)
{
    $list = scandir($path);
    if ($list === false) {
        throw new Exception("Failed to retrieve the file list of $path");
    }
    foreach (array_diff($list, array('.', '..')) as $item) {
        $full = $path . DIRECTORY_SEPARATOR . $item;
        if (is_dir($full)) {
            fixCoveragePath($rxSearch, $rxReplace, $full);
        } else {
            if (preg_match('/\\.(html|js)$/i', $item)) {
                $contents = file_get_contents($full);
                if (!is_string($contents)) {
                    throw new Exception("Failed to read from file $full");
                }
                $contents = preg_replace($rxSearch, $rxReplace, $contents);
                if (file_put_contents($full, $contents) === false) {
                    throw new Exception("Failed to write to $full");
                }
            }
        }
    }
}
