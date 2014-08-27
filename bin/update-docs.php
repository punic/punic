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
    define('WEBSITE_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'website');
    define('DEST_DIR', WEBSITE_DIR . DIRECTORY_SEPARATOR . 'docs');
    if (!is_dir(TEMP_DIR)) {
            @mkdir(TEMP_DIR, 0777, true);
        if (!is_dir(TEMP_DIR)) {
            throw new Exception('Unable to create the directory ' . TEMP_DIR);
        }
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

    echo "Cleanup older docs... ";
    if (is_dir(DEST_DIR)) {
        deleteFromFilesystem(DEST_DIR);
    }
    @mkdir(DEST_DIR, 0777, true);
    if (!is_dir(DEST_DIR)) {
        throw new Exception('Unable to create the directory ' . DEST_DIR);
    }
    echo "done.\n";

    echo "Creating configuration file... ";
    $v = array(
        'from' => addslashes(SOURCE_DIR),
        'to' => addslashes(DEST_DIR),
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
