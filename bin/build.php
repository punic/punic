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
    define('SOURCE_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'source-data');
    define('SOURCE_DIR_DATA', SOURCE_DIR . DIRECTORY_SEPARATOR . 'data');
    define('DESTINATION_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'code' . DIRECTORY_SEPARATOR . 'data');

    define('LOCAL_ZIP_FILE', SOURCE_DIR . DIRECTORY_SEPARATOR . 'data.zip');
    
    if(isset($argv)) {
        foreach($argv as $i => $arg) {
            if($i > 0) {
                if((strcasecmp($arg, 'debug') === 0) || (strcasecmp($arg, '--debug') === 0)) {
                    define('DEBUG', true);
                    break;
                }
            }
        }
    }
    defined('DEBUG') or define('DEBUG', false);

    if (!is_dir(SOURCE_DIR)) {
        if (mkdir(SOURCE_DIR, 0777, true) === false) {
            echo "Failed to create " . SOURCE_DIR . "\n";
            die(1);
        }
    }
    if (!is_dir(DESTINATION_DIR)) {
        if (mkdir(DESTINATION_DIR, 0777, false) === false) {
            echo "Failed to create " . DESTINATION_DIR . "\n";
            die(1);
        }
    }
    echo "done.\n";
    if (!is_dir(SOURCE_DIR_DATA)) {
        if (!is_file(LOCAL_ZIP_FILE)) {
            downloadCLDR();
        }
        ExtractCLDR();
    }
    copyData();
    die(0);
} catch (Exception $x) {
    echo $x->getMessage(), "\n";
    die(1);
}

function downloadCLDR()
{
    $remoteURL = 'http://www.unicode.org/Public/cldr/25/json.zip';
    $zipFrom = null;
    $zipTo = null;
    echo "Downloading $remoteURL... ";
    try {
        $zipFrom = fopen($remoteURL, 'rb');
        if ($zipFrom === false) {
            throw new Exception("Failed to read $remoteURL");
        }
        $zipTo = fopen(LOCAL_ZIP_FILE, 'wb');
        if ($zipTo === false) {
            throw new Exception("Failed to create " . LOCAL_ZIP_FILE);
        }
        while (!feof($zipFrom)) {
            $buffer = fread($zipFrom, 4096);
            if ($buffer === false) {
                throw new Exception("Failed to fetch data from $remoteURL");
            }
            if (fwrite($zipTo, $buffer) === false) {
                throw new Exception("Failed to write data to " . LOCAL_ZIP_FILE);
            }
        }
        fclose($zipTo);
        $zipTo = null;
        fclose($zipFrom);
        $zipFrom = null;
        echo "done.\n";
    } catch (Exception $x) {
        if ($zipTo) {
            fclose($zipTo);
            $zipTo = null;
        }
        if ($zipFrom) {
            fclose($zipFrom);
            $zipFrom = null;
        }
        if (is_file(LOCAL_ZIP_FILE)) {
            unlink(LOCAL_ZIP_FILE);
        }
        throw $x;
    }
}

function extractCLDR()
{
    $zip = null;
    echo "Extracting " . LOCAL_ZIP_FILE . "... ";
    try {
        $zip = new ZipArchive();
        $rc = $zip->open(LOCAL_ZIP_FILE);
        if ($rc !== true) {
            throw new Exception("Opening " . LOCAL_ZIP_FILE . " failed with return code $rc");
        }
        $zip->extractTo(SOURCE_DIR_DATA);
        $zip->close();
        $zip = null;
        echo "done.\n";
    } catch (Exception $x) {
        if ($zip) {
            @$zip->close();
            $zip = null;
        }
        if (is_dir(SOURCE_DIR_DATA)) {
            try {
                deleteFromFilesystem(SOURCE_DIR_DATA);
            } catch (Exception $foo) {
            }
        }
        throw $x;
    }
}

function copyData()
{
    $copy = array(
        'ca-gregorian.json' => array('save-as' => 'calendar.json', 'roots' => array('dates', 'calendars', 'gregorian')),
        // 'characters.json' => array('roots' => array('characters')),
        // 'contextTransforms.json' => array('roots' => array('contextTransforms')),
        // 'currencies.json' => array('roots' => array('numbers', 'currencies')),
        // 'dateFields.json' => array('roots' => array('dates', 'fields')),
        // 'delimiters.json' => array('roots' => array('delimiters')),
        // 'languages.json' => array('roots' => array('localeDisplayNames', 'languages')),
        // 'layout.json' => array('roots' => array('layout', 'orientation')),
        // 'listPatterns.json' => array('roots' => array('listPatterns')),
        // 'localeDisplayNames.json' => array('roots' => array('localeDisplayNames')),
        // 'measurementSystemNames.json' => array('roots' => array('localeDisplayNames', 'measurementSystemNames')),
        // 'numbers.json' => array('roots' => array('numbers')),
        // 'scripts.json' => array('roots' => array('localeDisplayNames', 'scripts')),
        // 'territories.json' => array('roots' => array('localeDisplayNames', 'territories')),
        // 'timeZoneNames.json' => array('roots' => array('dates', 'timeZoneNames')),
        // 'transformNames.json' => array('roots' => array('localeDisplayNames', 'transformNames')),
        // 'units.json' => array('roots' => array('units')),
        // 'variants.json' => array('roots' => array('localeDisplayNames', 'variants')),
    );
    $src = SOURCE_DIR_DATA . DIRECTORY_SEPARATOR . 'main';
    $languages = scandir($src);
    if ($languages === false) {
        throw new Exception("Failed to retrieve the file list of $src");
    }
    $languages = array_diff($languages, array('.', '..'));
    foreach ($languages as $language) {
        if (is_dir($src . DIRECTORY_SEPARATOR . $language)) {
            echo "Parsing language $language... ";
            $destFolder = DESTINATION_DIR . DIRECTORY_SEPARATOR . str_replace('-', '_', $language);
            if (is_dir($destFolder)) {
                deleteFromFilesystem($destFolder);
            }
            if (mkdir($destFolder) === false) {
                throw new Exception("Failed to create $destFolder\n");
            }
            foreach ($copy as $copyFrom => $info) {
                $copyTo = array_key_exists('save-as', $info) ? $info['save-as'] : $copyFrom;
                if ($copyTo === false) {
                    $copyTo = $copyFrom;
                }
                $dstFile = $destFolder . DIRECTORY_SEPARATOR . $copyTo;
                $useLanguage = $language;
                $srcFile = $src . DIRECTORY_SEPARATOR . $useLanguage . DIRECTORY_SEPARATOR . $copyFrom;
                if (!is_file($srcFile)) {
                    $useLanguage = 'en';
                    $srcFile = $src . DIRECTORY_SEPARATOR . $useLanguage . DIRECTORY_SEPARATOR . $copyFrom;
                    if (!is_file($srcFile)) {
                        throw new Exception("File not found: $srcFile");
                    }
                }
                $json = file_get_contents($srcFile);
                if ($json === false) {
                    throw new Exception("Failed to read from $srcFile");
                }
                $data = json_decode($json, true);
                if (is_null($data)) {
                    throw new Exception("Failed to decode data in $srcFile");
                }
                $path = '';
                foreach (array_merge(array('main', $useLanguage), $info['roots']) as $root) {
                    if (!is_array($data)) {
                        throw new Exception("Decoded data should be an array in $srcFile (path: $path)");
                    }
                    if (($path == "/main/$useLanguage") && array_key_exists('identity', $data)) {
                        unset($data['identity']);
                    }
                    if ((count($data) !== 1) || (!array_key_exists($root, $data))) {
                        throw new Exception("Invalid data in $srcFile:\nExpected one array with the sole key '$root' (path: $path), keys found: " . implode(', ', array_keys($data)));
                    }
                    $data = $data[$root];
                    $path .= "/$root";
                }
                if (!is_array($data)) {
                    throw new Exception("Decoded data should be an array in $srcFile (path: $path)");
                }
                switch($copyTo) {
                    case 'calendar.json':
                        unset($data['dateTimeFormats']['availableFormats']);
                        unset($data['dateTimeFormats']['appendItems']);
                        unset($data['dateTimeFormats']['intervalFormats']);
                        foreach(array_keys($data['dateTimeFormats']) as $key) {
                            $data['dateTimeFormats'][$key] = toPhpSprintf($data['dateTimeFormats'][$key]);
                        }
                        foreach(array('eraNames' => 'wide', 'eraAbbr' => 'abbreviated', 'eraNarrow' => 'narrow') as $keyFrom => $keyTo) {
                            if(array_key_exists($keyFrom, $data['eras'])) {
                                $data['eras'][$keyTo] = $data['eras'][$keyFrom];
                                unset($data['eras'][$keyFrom]);
                            }
                        }
                        break;
                }
                $flags = JSON_FORCE_OBJECT;
                if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
                    $flags |= JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
                    if(DEBUG) {
                        $flags |= JSON_PRETTY_PRINT;
                    }
                }
                $json = json_encode($data, $flags);
                if ($json === false) {
                    throw new Exception("Failed to serialise data of $srcFile");
                }
                if (is_file($dstFile)) {
                    deleteFromFilesystem($dstFile);
                }
                if (file_put_contents($dstFile, $json) === false) {
                    throw new Exception("Failed write to to $dstFile");
                }
            }
            echo "done.\n";
        }
    }
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

function toPhpSprintf($fmt) {
    $result = $fmt;
    if(is_string($fmt)) {
        $result = str_replace('%', '%%', $result);
        $result = preg_replace_callback(
            '/\\{(\\d+)\\}/',
            function($matches) {
                return '%' . (1 + intval($matches[1])) . '$s';
            },
            $fmt
        );
    }
    return $result;
}
