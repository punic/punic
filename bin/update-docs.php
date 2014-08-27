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
    define('CONFIG_FILE', TEMP_DIR . DIRECTORY_SEPARATOR . 'phpdoc.xml');
    define('TEMP_DIR_PHPDOC', TEMP_DIR . DIRECTORY_SEPARATOR . 'phpdoc');
    define('WEBSITE_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'website');
    define('DEST_DIR', WEBSITE_DIR . DIRECTORY_SEPARATOR . 'docs');
    checkGraphviz();
    if (!is_dir(TEMP_DIR)) {
            @mkdir(TEMP_DIR, 0777, true);
        if (!is_dir(TEMP_DIR)) {
            throw new Exception('Unable to create the directory ' . TEMP_DIR);
        }
    }
    if (!is_dir(TEMP_DIR_PHPDOC)) {
        @mkdir(TEMP_DIR_PHPDOC, 0777, true);
        if (!is_dir(TEMP_DIR_PHPDOC)) {
            throw new Exception('Unable to create the directory ' . TEMP_DIR_PHPDOC);
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
        'from' => htmlspecialchars(SOURCE_DIR),
        'to' => htmlspecialchars(DEST_DIR),
        'temp' => htmlspecialchars(TEMP_DIR_PHPDOC),
    );
    if(file_put_contents(CONFIG_FILE, <<<EOT
<?xml version="1.0" encoding="UTF-8" ?>
<phpdoc>
    <title>Punic API</title>
    <files>
        <directory>{$v['from']}</directory>
    </files>
    <extensions>
        <extension>php</extension>
    </extensions>
    <parser>
        <encoding>UTF-8</encoding>
        <target>{$v['temp']}</target>
        <visibility>public</visibility>
    </parser>
    <transformer>
        <target>{$v['to']}</target>
    </transformer>
    <transformations>
        <template name="responsive" />
    </transformations>
</phpdoc>
EOT
    ) === false) {
        throw new Exception('Failed to create temporary phpdoc configuration');
    }
    echo "done.\n";

    echo "Creating doc files... ";
    $output = array();
    exec(
        'php'
        . ' ' . escapeshellarg(ROOT_DIR . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpdocumentor' . DIRECTORY_SEPARATOR . 'phpdocumentor' . DIRECTORY_SEPARATOR . 'bin/phpdoc')
        . ' -c ' . escapeshellarg(CONFIG_FILE)
        . ' 2>&1',
        $output,
        $rc
    );
    if ($rc !== 0) {
        throw new Exception("phpDoc failed:\n" . trim(implode("\n", $output)));
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

function checkGraphviz()
{
    $isWin = (stripos(PHP_OS, 'win') !== false) ? true : false;
    $output = array();
    exec('dot -V 2>&1', $output, $rc);
    if (($rc !== 0) && $isWin) {
        $graphWizPath = '';
        foreach (array('ProgramFiles', 'ProgramFiles(x86)', 'ProgramW6432') as $envName) {
            $envValue = getenv($envName);
            if (is_string($envValue)) {
                $envValue = rtrim($envValue, '\\');
                if (is_dir($envValue)) {
                    foreach (@scandir($envValue) as $pf) {
                        if (stripos($pf, 'graphviz') !== false) {
                            $pf2 = "$envValue\\$pf\\bin";
                            if (is_file("$pf2\\dot.exe")) {
                                $graphWizPath = $pf2;
                                break;
                            }
                        }
                    }
                }
            }
            if (strlen($graphWizPath)) {
                break;
            }
        }
        if (strlen($graphWizPath)) {
            $path = @getenv('PATH');
            if (is_string($path)) {
                $path = rtrim($path, ';') . ';' . $graphWizPath;
                @putenv("PATH=$path");
            }
        }
        $output = array();
        exec('dot -V 2>&1', $output, $rc);
    }
    if ($rc !== 0) {
        if ($isWin) {
            throw new Exception('graphviz has not been found. Please install it from http://www.graphviz.org/Download_windows.php');
        } else {
            throw new Exception("graphviz has not been found. Please install it with\nsudo apt-get install graphviz");
        }
    }
}
