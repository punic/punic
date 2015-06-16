<?php

function handleError($errno, $errstr, $errfile, $errline)
{
    if ($errno == E_NOTICE || $errno == E_WARNING) {
        throw new Exception("$errstr in $errfile @ line $errline", $errno);
    }
}

set_error_handler('handleError');

try {
    echo 'Initializing... ';
    define('ROOT_DIR', dirname(__DIR__));
    define('SOURCE_DIR', dirname(__DIR__).DIRECTORY_SEPARATOR.'code');
    define('TEMP_DIR', ROOT_DIR.DIRECTORY_SEPARATOR.'temp');
    define('WEBSITE_DIR', TEMP_DIR.DIRECTORY_SEPARATOR.'website');
    define('DEST_DIR', WEBSITE_DIR.DIRECTORY_SEPARATOR.'docs');
    if (!is_dir(TEMP_DIR)) {
        @mkdir(TEMP_DIR, 0777, true);
        if (!is_dir(TEMP_DIR)) {
            throw new Exception('Unable to create the directory '.TEMP_DIR);
        }
    }
    echo "done.\n";

    if (!is_dir(WEBSITE_DIR)) {
        echo 'Fetching repository... ';
        $output = array();
        $rc = null;
        exec('git clone git@github.com:punic/punic.github.io.git '.escapeshellarg(WEBSITE_DIR).' 2>&1', $output, $rc);
        if ($rc !== 0) {
            throw new Exception("Failed to clone website repository:\n".trim(implode("\n", $output)));
        }
        echo "done.\n";
    }

    echo 'Cleanup older docs... ';
    if (is_dir(DEST_DIR)) {
        deleteFromFilesystem(DEST_DIR);
    }
    @mkdir(DEST_DIR, 0777, true);
    if (!is_dir(DEST_DIR)) {
        throw new Exception('Unable to create the directory '.DEST_DIR);
    }
    echo "done.\n";

    echo 'Creating doc files... ';
    $output = array();
    exec(
        'php'
        .' '.escapeshellarg(ROOT_DIR.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'apigen'.DIRECTORY_SEPARATOR.'apigen'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'apigen.php')
        .' generate'
        .' --source='.escapeshellarg(SOURCE_DIR)
        .' --destination='.escapeshellarg(DEST_DIR)
        .' --access-levels=public'
        .' --extensions=php'
        .' --groups=namespaces'
        .' --charset=UTF-8'
        .' --template-theme=bootstrap'
        .' --title='.escapeshellarg('Punic APIs')
        .' --todo'
        .' --tree'
        .' --debug'
        .' 2>&1',
        $output,
        $rc
    );
    if ($rc !== 0) {
        throw new Exception("ApiGen failed:\n".trim(implode("\n", $output)));
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
            deleteFromFilesystem($path.DIRECTORY_SEPARATOR.$item);
        }
        if (rmdir($path) === false) {
            throw new Exception("Failed to delete directory $path");
        }
    }
}
