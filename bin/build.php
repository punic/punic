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
    define('CLDR_VERSION', 27);
    define('ROOT_DIR', dirname(__DIR__));
    define('SOURCE_DIR', ROOT_DIR.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.'source-data');
    define('DESTINATION_DIR', ROOT_DIR.DIRECTORY_SEPARATOR.'code'.DIRECTORY_SEPARATOR.'data');
    define('TESTS_DIR', ROOT_DIR.DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'dataFiles');

    if (isset($argv)) {
        foreach ($argv as $i => $arg) {
            if ($i > 0) {
                if ((strcasecmp($arg, 'debug') === 0) || (strcasecmp($arg, '--debug') === 0)) {
                    defined('DEBUG') or define('DEBUG', true);
                }
                if ((strcasecmp($arg, 'full') === 0) || (strcasecmp($arg, '--full') === 0)) {
                    defined('FULL_JSON') or define('FULL_JSON', true);
                }
                if ((strcasecmp($arg, 'post-clean') === 0) || (strcasecmp($arg, '--post-clean') === 0)) {
                    defined('POST_CLEAN') or define('POST_CLEAN', true);
                }
            }
        }
    }
    defined('DEBUG') or define('DEBUG', false);
    defined('FULL_JSON') or define('FULL_JSON', false);
    define('LOCAL_ZIP_FILE', SOURCE_DIR.DIRECTORY_SEPARATOR.(FULL_JSON ? 'cldr_full' : 'cldr').'-'.CLDR_VERSION.'.zip');
    define('LOCAL_VCS_DIR', SOURCE_DIR.DIRECTORY_SEPARATOR.'cldr-'.CLDR_VERSION.'-source');
    define('SOURCE_DIR_DATA', SOURCE_DIR.DIRECTORY_SEPARATOR.(FULL_JSON ? 'cldr_full' : 'cldr').'-'.CLDR_VERSION);
    defined('POST_CLEAN') or define('POST_CLEAN', false);

    if (!is_dir(SOURCE_DIR)) {
        if (mkdir(SOURCE_DIR, 0777, true) === false) {
            echo 'Failed to create '.SOURCE_DIR."\n";
            die(1);
        }
    }
    echo "done.\n";

    if (is_dir(DESTINATION_DIR)) {
        echo 'Cleanup old data folder... ';
        deleteFromFilesystem(DESTINATION_DIR);
        echo "done.\n";
    }
    echo 'Creating data folder... ';
    if (mkdir(DESTINATION_DIR, 0777, false) === false) {
        echo 'Failed to create '.DESTINATION_DIR."\n";
        die(1);
    }
    echo "done.\n";
    if (!is_dir(TESTS_DIR)) {
        echo 'Creating tests folder... ';
        if (mkdir(TESTS_DIR, 0777, false) === false) {
            echo 'Failed to create '.TESTS_DIR."\n";
            die(1);
        }
        echo "done.\n";
    }

    if (!is_dir(SOURCE_DIR_DATA)) {
        if (version_compare(CLDR_VERSION, '27') >= 0) {
            if (!is_dir(LOCAL_VCS_DIR)) {
                checkoutCLDR();
            }
            if (!is_file(LOCAL_VCS_DIR.'/tools/java/cldr.jar')) {
                buildCLDRJar();
            }
            buildCLDRJson();
        } else {
            if (!is_file(LOCAL_ZIP_FILE)) {
                downloadCLDR();
            }
            extractCLDR();
        }
    }
    copyData();
    if (POST_CLEAN) {
        echo 'Cleanup temporary data folder... ';
        deleteFromFilesystem(SOURCE_DIR_DATA);
        echo "done.\n";
    }
    die(0);
} catch (Exception $x) {
    echo $x->getMessage(), "\n";
    die(1);
}

function checkoutCLDR()
{
    if (file_exists(LOCAL_VCS_DIR)) {
        deleteFromFilesystem(LOCAL_VCS_DIR);
    }
    try {
        echo 'Checking out the CLDR repository (this may take a while)... ';
        $output = array();
        $rc = null;
        @exec('svn co http://www.unicode.org/repos/cldr/tags/release-'.CLDR_VERSION.'/ '.escapeshellarg(LOCAL_VCS_DIR).' 2>&1', $output, $rc);
        if ($rc === 0) {
            if (!is_dir(LOCAL_VCS_DIR)) {
                $rc = -1;
            }
        }
        if ($rc !== 0) {
            $msg = "Error!\n";
            if (stripos(PHP_OS, 'WIN') !== false) {
                $msg .= 'Please make sure that SVN is installed and in your path. You can install TortoiseSVN for instance.';
            } else {
                $msg .= "You need the svn command line tool: under Ubuntu and Debian systems you can for instance run 'sudo apt-get install subversion'";
            }
            $msg .= "\nError details:\n".implode("\n", $output);
            throw new Exception($msg);
        }
        echo "done.\n";
    } catch (Exception $x) {
        if (file_exists(LOCAL_VCS_DIR)) {
            try {
                deleteFromFilesystem(LOCAL_VCS_DIR);
            } catch (Exception $foo) {
            }
        }
        throw $x;
    }
}

function buildCLDRJar()
{
    echo 'Creating the CLDR jar file... ';
    $output = array();
    $rc = null;
    @exec('ant -f '.escapeshellarg(LOCAL_VCS_DIR.'/tools/java/build.xml').' jar 2>&1', $output, $rc);
    if ($rc === 0) {
        if (!is_file(LOCAL_VCS_DIR.'/tools/java/cldr.jar')) {
            $rc = -1;
        }
    }
    if ($rc !== 0) {
        $msg = "Error!\n";
        if (stripos(PHP_OS, 'WIN') !== false) {
            $msg .= 'Please make sure that the ant tool is installed and in your path, and that Java JDK is installed and configured correctly.';
        } else {
            $msg .= "You need the ant command line tool and JDK: under Ubuntu and Debian systems you can for instance run 'sudo apt-get install ant openjdk-7-jdk'";
        }
        $msg .= "\nError details:\n".implode("\n", $output);
        throw new Exception($msg);
    }
    echo "done.\n";
}

function buildCLDRJson()
{
    if (file_exists(SOURCE_DIR_DATA)) {
        deleteFromFilesystem(SOURCE_DIR_DATA);
    }
    @mkdir(SOURCE_DIR_DATA);
    if (!is_dir(SOURCE_DIR_DATA)) {
        throw new Exception('Error creating directory '.SOURCE_DIR_DATA);
    }
    try {
        echo 'Determining the list of the available locales... ';
        $availableLocales = array();
        $contents = @scandir(LOCAL_VCS_DIR.'/common/main');
        if ($contents === false) {
            throw new Exception('Error reading contents of the directory '.LOCAL_VCS_DIR.'/common/main');
        }
        $match = null;
        foreach ($contents as $item) {
            if (preg_match('/^(.+)\.xml$/', $item, $match)) {
                $availableLocales[] = str_replace('_', '-', $match[1]);
            }
        }
        if (empty($availableLocales)) {
            throw new Exception('no locales found!');
        }
        sort($availableLocales);
        echo count($availableLocales)." locales found.\n";
        if (FULL_JSON) {
            $locales = $availableLocales;
        } else {
            echo 'Checking standard locales... ';
            // Same locales as of CLDR 26 not-full distribution
            $locales = array('ar', 'ca', 'cs', 'da', 'de', 'el', 'en', 'en-001', 'en-AU', 'en-CA', 'en-GB', 'en-HK', 'en-IN', 'es', 'fi', 'fr', 'he', 'hi', 'hr', 'hu', 'it', 'ja', 'ko', 'nb', 'nl', 'pl', 'pt', 'pt-PT', 'ro', 'root', 'ru', 'sk', 'sl', 'sr', 'sv', 'th', 'tr', 'uk', 'vi', 'zh', 'zh-Hant');
            $diff = array_diff($locales, $availableLocales);
            if (!empty($diff)) {
                throw new Exception("The following locales were not found:\n- ".implode("\n- ", $diff));
            }
            echo "done.\n";
        }
        foreach ($locales as $locale) {
            echo "Building json data for $locale... ";
            $cmd = 'java';
            $cmd .= ' -DCLDR_DIR='.escapeshellarg(LOCAL_VCS_DIR);
            $cmd .= ' -DCLDR_GEN_DIR='.escapeshellarg(SOURCE_DIR_DATA.'/main/'.$locale);
            $cmd .= ' -jar '.escapeshellarg(LOCAL_VCS_DIR.'/tools/java/cldr.jar');
            $cmd .= ' ldml2json';
            $cmd .= ' -o true'; // (true|false) Whether to write out the 'other' section, which contains any unmatched paths
            $cmd .= ' -t main'; // (main|supplemental|segments) Type of CLDR data being generated, main, supplemental, or segments.
            $cmd .= ' -r true'; // (true|false) Whether the output JSON for the main directory should be based on resolved or unresolved data
            $cmd .= ' -m '.escapeshellarg(str_replace('-', '_', $locale)); // Regular expression to define only specific locales or files to be generated
            $output = array();
            $rc = null;
            @exec($cmd.' 2>&1', $output, $rc);
            if ($rc !== 0) {
                throw new Exception("Error!\n".implode("\n", $output));
            }
            if (!is_dir(SOURCE_DIR_DATA.'/main/'.$locale)) {
                throw new Exception("No data generated!\nTool output:\n".implode("\n", $output));
            }
            echo "done.\n";
        }
        echo 'Building json supplemental data... ';
        $cmd = 'java';
        $cmd .= ' -DCLDR_DIR='.escapeshellarg(LOCAL_VCS_DIR);
        $cmd .= ' -DCLDR_GEN_DIR='.escapeshellarg(SOURCE_DIR_DATA.'/supplemental');
        $cmd .= ' -jar '.escapeshellarg(LOCAL_VCS_DIR.'/tools/java/cldr.jar');
        $cmd .= ' ldml2json';
        $cmd .= ' -o true'; // (true|false) Whether to write out the 'other' section, which contains any unmatched paths
        $cmd .= ' -t supplemental'; // (main|supplemental|segments) Type of CLDR data being generated, main, supplemental, or segments.
        $output = array();
        @exec($cmd.' 2>&1', $output, $rc);
        if ($rc !== 0) {
            throw new Exception("Error!\n".implode("\n", $output));
        }
        echo "done.\n";
        echo 'Building json segments data... ';
        $cmd = 'java';
        $cmd .= ' -DCLDR_DIR='.escapeshellarg(LOCAL_VCS_DIR);
        $cmd .= ' -DCLDR_GEN_DIR='.escapeshellarg(SOURCE_DIR_DATA.'/segments');
        $cmd .= ' -jar '.escapeshellarg(LOCAL_VCS_DIR.'/tools/java/cldr.jar');
        $cmd .= ' ldml2json';
        $cmd .= ' -o true'; // (true|false) Whether to write out the 'other' section, which contains any unmatched paths
        $cmd .= ' -t segments'; // (main|supplemental|segments) Type of CLDR data being generated, main, supplemental, or segments.
        $output = array();
        @exec($cmd.' 2>&1', $output, $rc);
        if ($rc !== 0) {
            throw new Exception("Error!\n".implode("\n", $output));
        }
        echo "done.\n";
    } catch (Exception $x) {
        try {
            deleteFromFilesystem(SOURCE_DIR_DATA);
        } catch (Exception $foo) {
        }
        throw $x;
    }
}

function downloadCLDR()
{
    if (version_compare(CLDR_VERSION, 26) >= 0) {
        $remoteURL = 'http://unicode.org/Public/cldr/'.CLDR_VERSION.'/'.(FULL_JSON ? 'json-full.zip' : 'json.zip');
    } else {
        $remoteURL = 'http://unicode.org/Public/cldr/'.CLDR_VERSION.'/'.(FULL_JSON ? 'json_full.zip' : 'json.zip');
    }
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
            throw new Exception('Failed to create '.LOCAL_ZIP_FILE);
        }
        while (!feof($zipFrom)) {
            $buffer = fread($zipFrom, 4096);
            if ($buffer === false) {
                throw new Exception("Failed to fetch data from $remoteURL");
            }
            if (fwrite($zipTo, $buffer) === false) {
                throw new Exception('Failed to write data to '.LOCAL_ZIP_FILE);
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
    echo 'Extracting '.LOCAL_ZIP_FILE.'... ';
    try {
        $zip = new ZipArchive();
        $rc = $zip->open(LOCAL_ZIP_FILE);
        if ($rc !== true) {
            throw new Exception('Opening '.LOCAL_ZIP_FILE." failed with return code $rc");
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
        'ca-gregorian.json' => array('kind' => 'main', 'save-as' => 'calendar.json', 'roots' => array('dates', 'calendars', 'gregorian')),
        'timeZoneNames.json' => array('kind' => 'main', 'roots' => array('dates', 'timeZoneNames')),
        'listPatterns.json' => array('kind' => 'main', 'roots' => array('listPatterns')),
        'units.json' => array('kind' => 'main', 'roots' => array('units')),
        'dateFields.json' => array('kind' => 'main', 'roots' => array('dates', 'fields')),
        'languages.json' => array('kind' => 'main', 'roots' => array('localeDisplayNames', 'languages')),
        'territories.json' => array('kind' => 'main', 'roots' => array('localeDisplayNames', 'territories')),
        'localeDisplayNames.json' => array('kind' => 'main', 'roots' => array('localeDisplayNames')),
        'numbers.json' => array('kind' => 'main', 'roots' => array('numbers')),
        'layout.json' => array('kind' => 'main', 'roots' => array('layout', 'orientation')),
        'measurementSystemNames.json' => array('kind' => 'main', 'roots' => array('localeDisplayNames', 'measurementSystemNames')),
        'currencies.json' => array('kind' => 'main', 'roots' => array('numbers', 'currencies')),
        /*
        'characters.json' => array('kind' => 'main', 'roots' => array('characters')),
        'contextTransforms.json' => array('kind' => 'main', 'roots' => array('contextTransforms')),

        'delimiters.json' => array('kind' => 'main', 'roots' => array('delimiters')),
        'scripts.json' => array('kind' => 'main', 'roots' => array('localeDisplayNames', 'scripts')),
        'transformNames.json' => array('kind' => 'main', 'roots' => array('localeDisplayNames', 'transformNames')),
        'variants.json' => array('kind' => 'main', 'roots' => array('localeDisplayNames', 'variants')),
        */
        'telephoneCodeData.json' => array('kind' => 'supplemental', 'roots' => array('supplemental', 'telephoneCodeData')),
        'territoryInfo.json' => array('kind' => 'supplemental', 'roots' => array('supplemental', 'territoryInfo')),
        'weekData.json' => array('kind' => 'supplemental', 'roots' => array('supplemental', 'weekData')),
        'parentLocales.json' => array('kind' => 'supplemental', 'roots' => array('supplemental', 'parentLocales', 'parentLocale')),
        'likelySubtags.json' => array('kind' => 'supplemental', 'roots' => array('supplemental', 'likelySubtags')),
        'territoryContainment.json' => array('kind' => 'supplemental', 'roots' => array('supplemental', 'territoryContainment')),
        'metaZones.json' => array('kind' => 'supplemental', 'roots' => array('supplemental', 'metaZones')),
        'plurals.json' => array('kind' => 'supplemental', 'roots' => array('supplemental', 'plurals-type-cardinal')),
        'measurementData.json' => array('kind' => 'supplemental', 'roots' => array('supplemental', 'measurementData')),
        'currencyData.json' => array('kind' => 'supplemental', 'roots' => array('supplemental', 'currencyData')),
    );
    $src = SOURCE_DIR_DATA.DIRECTORY_SEPARATOR.'main';
    $locales = scandir($src);
    if ($locales === false) {
        throw new Exception("Failed to retrieve the file list of $src");
    }
    $locales = array_diff($locales, array('.', '..', 'en-001'));
    foreach ($locales as $locale) {
        if (is_dir($src.DIRECTORY_SEPARATOR.$locale)) {
            echo "Parsing locale $locale... ";
            $destFolder = DESTINATION_DIR.DIRECTORY_SEPARATOR.$locale;
            if (is_dir($destFolder)) {
                deleteFromFilesystem($destFolder);
            }
            if (mkdir($destFolder) === false) {
                throw new Exception("Failed to create $destFolder\n");
            }
            foreach ($copy as $copyFrom => $info) {
                if ($info['kind'] === 'main') {
                    $copyTo = array_key_exists('save-as', $info) ? $info['save-as'] : $copyFrom;
                    if ($copyTo === false) {
                        $copyTo = $copyFrom;
                    }
                    $dstFile = $destFolder.DIRECTORY_SEPARATOR.$copyTo;
                    $useLocale = $locale;
                    $srcFile = $src.DIRECTORY_SEPARATOR.$useLocale.DIRECTORY_SEPARATOR.$copyFrom;
                    if (!is_file($srcFile)) {
                        $useLocale = 'en';
                        $srcFile = $src.DIRECTORY_SEPARATOR.$useLocale.DIRECTORY_SEPARATOR.$copyFrom;
                        if (!is_file($srcFile)) {
                            throw new Exception("File not found: $srcFile");
                        }
                    }
                    $info['roots'] = array_merge(array('main', $useLocale), $info['roots']);
                    $info['unsetByPath'] = array_merge(
                        isset($info['unsetByPath']) ? $info['unsetByPath'] : array(),
                        array(
                            "/main/$useLocale" => array('identity'),
                        )
                    );
                    copyDataFile($srcFile, $info, $dstFile);
                }
            }
            echo "done.\n";
        }
    }
    $defaultCurrencyData = readJsonFile(DESTINATION_DIR.DIRECTORY_SEPARATOR.'en'.DIRECTORY_SEPARATOR.'currencies.json');
    foreach ($locales as $locale) {
        if ($locale !== 'en') {
            if (is_dir($src.DIRECTORY_SEPARATOR.$locale)) {
                copyMissingData_currency($defaultCurrencyData, DESTINATION_DIR.DIRECTORY_SEPARATOR.$locale.DIRECTORY_SEPARATOR.'currencies.json');
            }
        }
    }
    echo 'Parsing supplemental files... ';
    $src = SOURCE_DIR_DATA.DIRECTORY_SEPARATOR.'supplemental';
    foreach ($copy as $copyFrom => $info) {
        if ($info['kind'] === 'supplemental') {
            $copyTo = array_key_exists('save-as', $info) ? $info['save-as'] : $copyFrom;
            $dstFile = DESTINATION_DIR.DIRECTORY_SEPARATOR.$copyTo;
            $srcFile = $src.DIRECTORY_SEPARATOR.$copyFrom;
            if (!is_file($srcFile)) {
                throw new Exception("File not found: $srcFile");
            }
            $info['unsetByPath'] = array_merge(
                isset($info['unsetByPath']) ? $info['unsetByPath'] : array(),
                array(
                    '/supplemental' => array('version', 'generation'),
                )
            );
            copyDataFile($srcFile, $info, $dstFile);
        }
    }
    echo "done.\n";
}

function readJsonFile($file)
{
    $json = file_get_contents($file);
    if ($json === false) {
        throw new Exception("Failed to read from $file");
    }
    $data = json_decode($json, true);
    if ($data === null) {
        throw new Exception("Failed to decode data in $file");
    }

    return $data;
}
function saveJsonFile($data, $file)
{
    $jsonFlags = 0;
    if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
        $jsonFlags |= JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        if (DEBUG) {
            $jsonFlags |= JSON_PRETTY_PRINT;
        }
    }
    $json = json_encode($data, $jsonFlags);
    if ($json === false) {
        throw new Exception("Failed to serialize data for $file");
    }
    if (is_file($file)) {
        deleteFromFilesystem($file);
    }
    if (file_put_contents($file, $json) === false) {
        throw new Exception("Failed write to $file");
    }
}

function copyDataFile($srcFile, $info, $dstFile)
{
    $data = readJsonFile($srcFile);
    $path = '';
    foreach ($info['roots'] as $root) {
        if (!is_array($data)) {
            throw new Exception("Decoded data should be an array in $srcFile (path: $path)");
        }
        if (isset($info['unsetByPath'][$path])) {
            foreach ($info['unsetByPath'][$path] as $node) {
                if (array_key_exists($node, $data)) {
                    unset($data[$node]);
                }
            }
        }
        checkOneKey($data, $root);
        $data = $data[$root];
        $path .= "/$root";
    }
    if (!is_array($data)) {
        throw new Exception("Decoded data should be an array in $srcFile (path: $path)");
    }
    switch (basename($dstFile)) {
        case 'calendar.json':
            unset($data['dateTimeFormats']['availableFormats']);
            unset($data['dateTimeFormats']['appendItems']);
            unset($data['dateTimeFormats']['intervalFormats']);
            foreach (array_keys($data['dateTimeFormats']) as $key) {
                $data['dateTimeFormats'][$key] = toPhpSprintf($data['dateTimeFormats'][$key]);
            }
            foreach (array('eraNames' => 'wide', 'eraAbbr' => 'abbreviated', 'eraNarrow' => 'narrow') as $keyFrom => $keyTo) {
                if (array_key_exists($keyFrom, $data['eras'])) {
                    $data['eras'][$keyTo] = $data['eras'][$keyFrom];
                    unset($data['eras'][$keyFrom]);
                }
            }
            break;
        case 'telephoneCodeData.json':
            foreach (array_keys($data) as $k) {
                if (!preg_match('/^([A-Z]{2}|[0-9]{3})$/', $k)) {
                    throw new Exception("Invalid territory ID: $k");
                }
                $d = $data[$k];
                if ((!is_array($d)) || empty($d)) {
                    throw new Exception("Expecting non empty array for $k, found ".gettype($d));
                }
                $data[$k] = array();
                $n = count($d);
                for ($i = 0; $i < $n; ++$i) {
                    if (!isset($d[$i])) {
                        throw new Exception("Invalid array for $k");
                    }
                    if ((!is_array($d[$i])) || (count($d[$i]) !== 1) || (!array_key_exists('telephoneCountryCode', $d[$i])) || (!is_string($d[$i]['telephoneCountryCode'])) || (!strlen($d[$i]['telephoneCountryCode']))) {
                        throw new Exception("Invalid telephoneCountryCode for $k");
                    }
                    $data[$k][] = $d[$i]['telephoneCountryCode'];
                }
            }
            break;
        case 'territoryInfo.json': // http://www.unicode.org/reports/tr35/tr35-info.html#Supplemental_Territory_Information
            unset($data['ZZ']);
            foreach ($data as $k => $v) {
                $D = array();
                foreach ($v as $k2 => $v2) {
                    switch ($k2) {
                        case '_gdp': // Gross domestic product
                            if (!is_int($v2)) {
                                $v3 = @intval($v2);
                                if (strval($v3) !== $v2) {
                                    $v3 = @floatval($v2);
                                }
                                if (strval($v3) !== $v2) {
                                    throw new Exception("Unable to parse $v2 as an integer ($k2)");
                                }
                                $v2 = $v3;
                            }
                            $D['gdp'] = $v2;
                            break;
                        case '_literacyPercent':
                            if (!(is_int($v2) || is_float($v2))) {
                                $v3 = @floatval($v2);
                                if (strval($v3) !== $v2) {
                                    $v3 = @floatval($v2);
                                }
                                if (strval($v3) !== $v2) {
                                    throw new Exception("Unable to parse $v2 as an integer ($k2)");
                                }
                                $v2 = $v3;
                            }
                            $D['literacy'] = $v2;
                            break;
                        case '_population':
                            if (!is_int($v2)) {
                                $v3 = @intval($v2);
                                if (strval($v3) !== $v2) {
                                    $v3 = @floatval($v2);
                                }
                                if (strval($v3) !== $v2) {
                                    throw new Exception("Unable to parse $v2 as an integer ($k2)");
                                }
                                $v2 = $v3;
                            }
                            $D['population'] = $v2;
                            break;
                        case 'languagePopulation':
                            if (!is_array($v2)) {
                                throw new Exception("Invalid node: $k2 is not an array");
                            }
                            $D['languages'] = array();
                            foreach ($v2 as $k3 => $v3) {
                                if (!is_array($v3)) {
                                    throw new Exception("Invalid node: $k2/$k3 is not an array");
                                }
                                $D['languages'][$k3] = array();
                                foreach ($v3 as $k4 => $v4) {
                                    switch ($k4) {
                                        case '_officialStatus':
                                            switch ($v4) {
                                                case 'official':
                                                    $v5 = 'o';
                                                    break;
                                                case 'official_regional':
                                                    $v5 = 'r';
                                                    break;
                                                case 'de_facto_official':
                                                    $v5 = 'f';
                                                    break;
                                                case 'official_minority':
                                                     $v5 = 'm';
                                                     break;
                                                default:
                                                    throw new Exception("Unknown language status: $v4");
                                            }
                                            $D['languages'][$k3]['status'] = $v5;
                                            break;
                                        case '_populationPercent':
                                            if (!(is_int($v4) || is_float($v4))) {
                                                $v5 = @floatval($v4);
                                                if (strval($v5) !== $v4) {
                                                    $v5 = @floatval($v4);
                                                }
                                                if (strval($v5) !== $v4) {
                                                    throw new Exception("Unable to parse $v4 as an integer ($k2)");
                                                }
                                                $v4 = $v5;
                                            }
                                            $D['languages'][$k3]['population'] = $v4;
                                            break;
                                        case '_writingPercent':
                                            if (!(is_int($v4) || is_float($v4))) {
                                                $v5 = @floatval($v4);
                                                if (strval($v5) !== $v4) {
                                                    $v5 = @floatval($v4);
                                                }
                                                if (strval($v5) !== $v4) {
                                                    throw new Exception("Unable to parse $v4 as an integer ($k2)");
                                                }
                                                $v4 = $v5;
                                            }
                                            $D['languages'][$k3]['writing'] = $v4;
                                            break;
                                        default:
                                            throw new Exception("Unknown node: $k2/$k3/$k4");
                                    }
                                }
                                if (!array_key_exists('population', $D['languages'][$k3])) {
                                    throw new Exception("Missing _populationPercent node in for $k/$k2/$k3");
                                }
                            }
                            if (empty($D['languages'])) {
                                throw new Exception("No languages for $k");
                            }
                            break;
                        default:
                            throw new Exception("Unknown node: $k2");
                    }
                }
                if (!array_key_exists('gdp', $D)) {
                    throw new Exception("Missing _gdp node in for $k");
                }
                if (!array_key_exists('literacy', $D)) {
                    throw new Exception("Missing _literacyPercent node in for $k");
                }
                if (!array_key_exists('population', $D)) {
                    throw new Exception("Missing _population node in for $k");
                }
                if (!array_key_exists('languages', $D)) {
                    throw new Exception("Missing languagePopulation node in for $k");
                }
                $data[$k] = $D;
            }
            break;
        case 'weekData.json':
            foreach (array_keys($data['minDays']) as $key) {
                $value = $data['minDays'][$key];
                if (!preg_match('/^[0-9]+$/', $value)) {
                    throw new Exception("Bad number: $value");
                }
                $data['minDays'][$key] = intval($value);
            }
            $dict = array('sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat');
            foreach (array_keys($data['firstDay']) as $key) {
                $val = array_search($data['firstDay'][$key], $dict, true);
                if ($val === false) {
                    throw new Exception("Unknown weekday name: {$data['firstDay'][$key]}");
                }
                $data['firstDay'][$key] = $val;
            }
            unset($data['firstDay-alt-variant']);
            unset($data['weekendStart']);
            unset($data['weekendEnd']);
            break;
        case 'territoryContainment.json':
            foreach (array_keys($data) as $key) {
                if (array_key_exists('_grouping', $data[$key])) {
                    unset($data[$key]['_grouping']);
                }
                if (array_key_exists('_contains', $data[$key])) {
                    $data[$key]['contains'] = $data[$key]['_contains'];
                    unset($data[$key]['_contains']);
                }
                if (strpos($key, '-status-') !== false) {
                    unset($data[$key]);
                }
            }
            break;
        case 'metaZones.json':
            checkOneKey($data['metazoneInfo'], 'timezone');
            $data['metazoneInfo'] = $data['metazoneInfo']['timezone'];
            foreach ($data['metazoneInfo'] as $id0 => $info0) {
                foreach ($info0 as $id1 => $info1) {
                    if (is_int($id1)) {
                        $info1 = fixMetazoneInfo($info1);
                    } else {
                        foreach ($info1 as $id2 => $info2) {
                            if (is_int($id2)) {
                                $info2 = fixMetazoneInfo($info2);
                            } else {
                                foreach ($info2 as $id3 => $info3) {
                                    if (is_int($id3)) {
                                        $info3 = fixMetazoneInfo($info3);
                                    } else {
                                        throw new Exception('Invalid metazoneInfo node');
                                    }
                                    $info2[$id3] = $info3;
                                }
                            }
                            $info1[$id2] = $info2;
                        }
                    }
                    $info0[$id1] = $info1;
                }
                $data['metazoneInfo'][$id0] = $info0;
            }
            $metazones = array();
            if ((!array_key_exists('metazones', $data)) && is_array($data['metazones']) && (count($data['metazones']) > 0)) {
                throw new Exception('metazones node not found/invalid');
            }
            foreach ($data['metazones'] as $mz) {
                checkOneKey($mz, 'mapZone');
                $mz = $mz['mapZone'];
                foreach (array_keys($mz) as $i) {
                    switch ($i) {
                        case '_other':
                        case '_territory':
                        case '_type':
                            $mz[substr($i, 1)] = $mz[$i];
                            unset($mz[$i]);
                            break;
                        default:
                            throw new Exception('Invalid mapZone node key: '.$i);
                    }
                }
                $metazones[] = $mz;
            }
            $data['metazones'] = $metazones;
            break;
        case 'timeZoneNames.json':
            foreach (array('gmtFormat', 'gmtZeroFormat', 'regionFormat', 'regionFormat-type-standard', 'regionFormat-type-daylight', 'fallbackFormat') as $k) {
                if (array_key_exists($k, $data)) {
                    $data[$k] = toPhpSprintf($data[$k]);
                }
            }
            break;
        case 'listPatterns.json':
            $keys = array_keys($data);
            $m = null;
            foreach ($keys as $key) {
                if (!preg_match('/^listPattern-type-(.+)$/', $key, $m)) {
                    throw new Exception("Invalid node '$key' in ".$dstFile);
                }
                foreach (array_keys($data[$key]) as $k) {
                    $data[$key][$k] = toPhpSprintf($data[$key][$k]);
                }
                $data[$m[1]] = $data[$key];
                unset($data[$key]);
            }
            break;
        case 'plurals.json':
            $testData = array();
            foreach ($data as $l => $lData) {
                $testData[$l] = array();
                $keys = array_keys($lData);
                foreach ($keys as $key) {
                    if (!preg_match('/^pluralRule-count-(.+)$/', $key, $m)) {
                        throw new Exception("Invalid node '$key' in ".$dstFile);
                    }
                    $rule = $m[1];
                    $testData[$l][$rule] = array();
                    $vOriginal = $lData[$key];
                    $examples = explode('@', $vOriginal);
                    $v = trim(array_shift($examples));
                    foreach ($examples as $example) {
                        list($exampleNumberType, $exampleValues) = explode(' ', $example, 2);
                        switch ($exampleNumberType) {
                            case 'integer':
                            case 'decimal':
                                $exampleValues = preg_replace('/, …$/', '', $exampleValues);
                                $exampleValuesParsed = array();
                                foreach (explode(', ', trim($exampleValues)) as $ev) {
                                    if (preg_match('/^[+\-]?\d+$/', $ev)) {
                                        $exampleValuesParsed[] = $ev;
                                        $exampleValuesParsed[] = intval($ev);
                                    } elseif (preg_match('/^[+\-]?\d+\.\d+$/', $ev)) {
                                        $exampleValuesParsed[] = $ev;
                                    } elseif (preg_match('/^([+\-]?\d+)~([+\-]?\d+)$/', $ev, $m)) {
                                        $exampleValuesParsed[] = $m[1];
                                        $exampleValuesParsed[] = intval($m[1]);
                                        $exampleValuesParsed[] = $m[2];
                                        $exampleValuesParsed[] = intval($m[2]);
                                    } elseif (preg_match('/^([+\-]?\d+(\.\d+)?)~([+\-]?\d+(\.\d+)?)$/', $ev, $m)) {
                                        $exampleValuesParsed[] = $m[1];
                                        $exampleValuesParsed[] = $m[3];
                                    } elseif ($ev !== '…') {
                                        throw new Exception("Invalid node '$key' in $dstFile: $vOriginal");
                                    }
                                }
                                $testData[$l][$rule] = $exampleValuesParsed;
                                break;
                            default:
                                throw new Exception("Invalid node '$key' in $dstFile: $vOriginal");
                        }
                    }
                    if ($rule === 'other') {
                        if (strlen($v) > 0) {
                            throw new Exception("Invalid node '$key' in $dstFile: $vOriginal");
                        }
                    } else {
                        $v = str_replace(' = ', ' == ', $v);
                        $map = array('==' => 'true', '!=' => 'false');
                        foreach (array('^', ' and ', ' or ') as $pre) {
                            while (preg_match(
                                '/'.$pre.'(([nivfwft]( % \\d+)?) (==|!=) ((\\d+)(((\\.\\.)|,)+(\\d+))+))/',
                                $v,
                                $m
                            )) {
                                $found = $m[1];
                                $leftyPart = $m[2]; // eg 'n % 10'
                                $operator = $m[4]; // eg '=='
                                $ranges = explode(',', $m[5]);
                                foreach (array_keys($ranges) as $j) {
                                    if (preg_match('/^(\\d+)\\.\\.(\\d+)$/', $ranges[$j], $m)) {
                                        $ranges[$j] = "array({$m[1]}, {$m[2]})";
                                    }
                                }
                                $v = str_replace($found, "static::inRange($leftyPart, {$map[$operator]}, ".implode(', ', $ranges).')', $v);
                            }
                        }
                        if (strpos($v, '..') !== false) {
                            throw new Exception("Invalid node '$key' in $dstFile: $vOriginal");
                        }
                        foreach (array(
                            'n' => '%1$s', // absolute value of the source number (integer and decimals).
                            'i' => '%2$s', // integer digits of n
                            'v' => '%3$s', // number of visible fraction digits in n, with trailing zeros.
                            'w' => '%4$s', // number of visible fraction digits in n, without trailing zeros.
                            'f' => '%5$s', // visible fractional digits in n, with trailing zeros.
                            't' => '%6$s', // visible fractional digits in n, without trailing zeros.
                        ) as $from => $to) {
                            $v = preg_replace('/^'.$from.' /', "$to ", $v);
                            $v = preg_replace("/^$from /", "$to ", $v);
                            $v = str_replace(" $from ", " $to ", $v);
                            $v = str_replace("($from, ", "($to, ", $v);
                            $v = str_replace("($from ", "($to ", $v);
                            $v = str_replace(" $from,", " $to,", $v);
                        }
                        $v = str_replace(' % ', ' %% ', $v);
                        $lData[$rule] = $v;
                    }
                    unset($lData[$key]);
                }
                $data[$l] = $lData;
            }
            saveJsonFile($testData, TESTS_DIR.DIRECTORY_SEPARATOR.basename($dstFile));
            break;
        case 'units.json':
            foreach (array_keys($data) as $width) {
                switch ($width) {
                    case 'long':
                    case 'short':
                    case 'narrow':
                    case 'long':
                        foreach (array_keys($data[$width]) as $unitKey) {
                            switch ($unitKey) {
                                case 'per':
                                    if (implode('|', array_keys(($data[$width][$unitKey]))) !== 'compoundUnitPattern') {
                                        throw new Exception("Invalid node (1) '$width/$unitKey' in ".$dstFile);
                                    }
                                    $data[$width]['_compoundPattern'] = toPhpSprintf($data[$width][$unitKey]['compoundUnitPattern']);
                                    unset($data[$width][$unitKey]);
                                    break;
                                default:
                                    if (!preg_match('/^(\\w+)?-(.+)$/', $unitKey, $m)) {
                                        throw new Exception("Invalid node (2) '$width/$unitKey' in ".$dstFile);
                                    }
                                    $unitKind = $m[1];
                                    $unitName = $m[2];
                                    if (!array_key_exists($unitKind, $data[$width])) {
                                        $data[$width][$unitKind] = array();
                                    }
                                    if (!array_key_exists($unitName, $data[$width][$unitKind])) {
                                        $data[$width][$unitKind][$unitName] = array();
                                    }
                                    foreach (array_keys($data[$width][$unitKey]) as $pluralRuleSrc) {
                                        switch ($pluralRuleSrc) {
                                            case 'displayName':
                                                $data[$width][$unitKind][$unitName]['_name'] = $data[$width][$unitKey][$pluralRuleSrc];
                                                break;
                                            case 'perUnitPattern':
                                                $data[$width][$unitKind][$unitName]['_per'] = toPhpSprintf($data[$width][$unitKey][$pluralRuleSrc]);
                                                break;
                                            default:
                                                if (!preg_match('/^unitPattern-count-(.+)$/', $pluralRuleSrc, $m)) {
                                                    throw new Exception("Invalid node (4) '$width/$unitKey/$pluralRuleSrc' in ".$dstFile);
                                                }
                                                $pluralRule = $m[1];
                                                $data[$width][$unitKind][$unitName][$pluralRule] = toPhpSprintf($data[$width][$unitKey][$pluralRuleSrc]);
                                                break;
                                        }
                                    }
                                    unset($data[$width][$unitKey]);
                                    break;
                            }
                        }
                        break;
                    default:
                        if (preg_match('/^durationUnit-type-(.+)/', $width, $m)) {
                            if (implode('|', array_keys(($data[$width]))) !== 'durationUnitPattern') {
                                throw new Exception("Invalid node (5) '$width' in ".$dstFile);
                            }
                            $t = $m[1];
                            if (!array_key_exists('_durationPattern', $data)) {
                                $data['_durationPattern'] = array();
                            }
                            $data['_durationPattern'][$t] = $data[$width]['durationUnitPattern'];
                            unset($data[$width]);
                        } else {
                            throw new Exception("Invalid node (6) '$width' in ".$dstFile);
                        }
                        break;
                }
            }
            break;
        case 'localeDisplayNames.json':
            if (!array_key_exists('localeDisplayPattern', $data)) {
                throw new Exception("Missing node 'localeDisplayPattern' in ".$dstFile);
            }
            foreach (array_keys($data['localeDisplayPattern']) as $k) {
                $data['localeDisplayPattern'][$k] = toPhpSprintf($data['localeDisplayPattern'][$k]);
            }
            if (!array_key_exists('codePatterns', $data)) {
                throw new Exception("Missing node 'codePatterns' in ".$dstFile);
            }
            foreach (array_keys($data['codePatterns']) as $k) {
                $data['codePatterns'][$k] = toPhpSprintf($data['codePatterns'][$k]);
            }
            break;
        case 'numbers.json':
            $final = array();
            $numberSystems = array();
            foreach ($data as $key => $value) {
                if (preg_match('/^([a-z]+)-numberSystem-([a-z]+)$/i', $key, $m)) {
                    $keyChunk = $m[1];
                    $ns = $m[2];
                    if (!array_key_exists($ns, $numberSystems)) {
                        $numberSystems[$ns] = array();
                    }
                    if (is_array($value)) {
                        $unitPattern = null;
                        foreach ($value as $k2 => $v2) {
                            if (preg_match('/^unitPattern-(.+)$/i', $k2, $m)) {
                                if ($unitPattern === null) {
                                    $unitPattern = array();
                                }
                                $unitPattern[$m[1]] = toPhpSprintf($v2);
                                unset($value[$k2]);
                            }
                        }
                        if ($unitPattern !== null) {
                            $value['unitPattern'] = $unitPattern;
                        }
                    }
                    $numberSystems[$ns][$keyChunk] = $value;
                } else {
                    switch ($key) {
                        case 'defaultNumberingSystem':
                        case 'otherNumberingSystems':
                            break;
                        case 'minimumGroupingDigits':
                            if (is_string($value) && preg_match('/^\\d+$/', $value)) {
                                $value = @intval($value);
                            }
                            if (!is_int($value)) {
                                throw new Exception("Invalid node '$key' in ".$dstFile);
                            }
                            $final[$key] = $value;
                            break;
                        default:
                            throw new Exception("Invalid node '$key' in ".$dstFile);
                    }
                }
            }
            if (!array_key_exists('latn', $numberSystems)) {
                throw new Exception("Missing 'latn' in ".$dstFile);
            }
            foreach ($numberSystems['latn'] as $key => $value) {
                if (array_key_exists($key, $final)) {
                    throw new Exception("Duplicated node '$key' in ".$dstFile);
                }
                // $final[$key] = $value; REMOVED ADVANCED LOCALIZATION
                if ($key === 'symbols') { // REMOVED ADVANCED LOCALIZATION
                    $final[$key] = $value;
                }
            }
            $data = $final;
            $symbols = array_key_exists('symbols', $data) ? $data['symbols'] : null;
            if (empty($symbols) || (!is_array($symbols))) {
                throw new Exception('Missing symbols in '.$dstFile);
            }
            foreach (array_keys($data) as $key) {
                if (is_array($data[$key]) && preg_match('/\\w+Formats$/', $key) && array_key_exists('standard', $data[$key])) {
                    $format = $data[$key]['standard'];
                    $data[$key]['standard'] = array('format' => $format);
                    foreach (numberFormatToRegularExpressions($symbols, $format) as $rxKey => $rx) {
                        $data[$key]['standard']["rx$rxKey"] = $rx;
                    }
                }
            }
            break;
        case 'measurementData.json':
            if (!(array_key_exists('measurementSystem', $data) && is_array($data['measurementSystem']))) {
                throw new Exception('Missing/invalid key: measurementSystem');
            }
            if (!(array_key_exists('paperSize', $data) && is_array($data['paperSize']))) {
                throw new Exception('Missing/invalid key: paperSize');
            }
            break;
        case 'currencies.json':
            $final = array();
            foreach ($data as $currencyCode => $currencyInfo) {
                if (!preg_match('/^[A-Z]{3}$/', $currencyCode)) {
                    throw new Exception("Invalid currency code: $currencyCode");
                }
                if (array_key_exists('symbol', $currencyInfo) && (strcmp($currencyInfo['symbol'], $currencyCode) === 0)) {
                    unset($currencyInfo['symbol']);
                }
                foreach ($currencyInfo as $currencyInfoKey => $currencyInfoValue) {
                    switch ($currencyInfoKey) {
                        case 'displayName':
                            unset($currencyInfo[$currencyInfoKey]);
                            $currencyInfo['name'] = $currencyInfoValue;
                            break;
                        case 'symbol-alt-variant':
                            if ($currencyInfoValue !== $currencyCode) {
                                $currencyInfo['symbolAlt'] = $currencyInfoValue;
                            }
                            unset($currencyInfo[$currencyInfoKey]);
                            break;
                        case 'symbol-alt-narrow':
                            if ($currencyInfoValue !== $currencyCode) {
                                $currencyInfo['symbolNarrow'] = $currencyInfoValue;
                            }
                            unset($currencyInfo[$currencyInfoKey]);
                            break;
                        default:
                            if (preg_match('/^displayName-count-(.+)$/', $currencyInfoKey, $m)) {
                                if (!array_key_exists('pluralName', $currencyInfo)) {
                                    $currencyInfo['pluralName'] = array();
                                }
                                $currencyInfo['pluralName'][$m[1]] = $currencyInfoValue;
                                unset($currencyInfo[$currencyInfoKey]);
                            }
                            break;
                    }
                }
                if (array_key_exists('pluralName', $currencyInfo)) {
                    if (!array_key_exists('other', $currencyInfo['pluralName'])) {
                        throw new Exception("Missing 'other' plural rule for currency $currencyCode");
                    }
                    if (!array_key_exists('name', $currencyInfo)) {
                        if (array_key_exists('one', $currencyInfo['pluralName'])) {
                            $currencyInfo['name'] = $currencyInfo['pluralName']['one'];
                        } else {
                            $currencyInfo['name'] = $currencyInfo['pluralName']['other'];
                        }
                    }
                }
                if (!array_key_exists('name', $currencyInfo)) {
                    $currencyInfo['name'] = $currencyCode;
                }
                if (array_key_exists('pluralName', $currencyInfo)) {
                    if ((count($currencyInfo['pluralName']) === 1) && (strcmp($currencyInfo['pluralName']['other'], $currencyInfo['name']) === 0)) {
                        unset($currencyInfo['pluralName']);
                    }
                }
                $final[$currencyCode] = $currencyInfo;
            }
            $data = $final;
            break;
        case 'currencyData.json':
            $keys = array('fractions', 'region');
            if ((count($data) !== count($keys)) || (count(array_diff($keys, array_keys($data))) !== 0)) {
                throw new Exception('Unexpected keys in currencyData.json');
            }
            $final = array();
            if (!array_key_exists('DEFAULT', $data['fractions'])) {
                throw new Exception('Missing DEFAULT in currencyData.json');
            }
            $parseFraction = function ($info, $defaultValues) {
                $result = array();
                foreach (array('_digits' => 'digits', '_rounding' => 'rounding', '_cashDigits' => 'cashDigits', '_cashRounding' => 'cashRounding') as $keyFrom => $keyTo) {
                    if (array_key_exists($keyTo, $info)) {
                        throw new Exception("$keyTo already exist in array");
                    }
                    if (array_key_exists($keyFrom, $info)) {
                        $v = $info[$keyFrom];
                        unset($info[$keyFrom]);
                        switch (gettype($v)) {
                            case 'integer':
                                break;
                            case 'string':
                                if (!preg_match('/^[0-9]+$/', $v)) {
                                    throw new Exception("$keyFrom is invalid");
                                }
                                $v = intval($v);
                                break;
                            default:
                                throw new Exception("$keyFrom is invalid");
                        }
                        switch ($keyTo) {
                            case 'rounding':
                            case 'cashRounding':
                                if ($v === 0) {
                                    $v = 1;
                                }
                                break;
                        }
                        $result[$keyTo] = $v;
                    }
                }
                if (!empty($info)) {
                    throw new Exception('Unexpected data in currency franction');
                }
                if (array_key_exists('cashDigits', $result) && array_key_exists('digits', $result) && ($result['cashDigits'] === $result['digits'])) {
                    unset($result['cashDigits']);
                }
                if (array_key_exists('cashRounding', $result) && array_key_exists('rounding', $result) && ($result['cashRounding'] === $result['rounding'])) {
                    unset($result['cashRounding']);
                }
                if ($defaultValues === true) {
                    if (!array_key_exists('digits', $result)) {
                        throw new Exception('Missing default rounding');
                    }
                    if (!array_key_exists('digits', $result)) {
                        throw new Exception('Missing default rounding');
                    }
                } else {
                    if (array_key_exists('digits', $result) && ($result['digits'] === $defaultValues['digits'])) {
                        unset($result['digits']);
                    }
                    if (array_key_exists('rounding', $result) && ($result['rounding'] === $defaultValues['rounding'])) {
                        unset($result['rounding']);
                    }
                }

                return $result;
            };
            $final['fractionsDefault'] = $parseFraction($data['fractions']['DEFAULT'], true);
            unset($data['fractions']['DEFAULT']);
            $final['fractions'] = array();
            foreach ($data['fractions'] as $currencyCode => $currencyInfo) {
                $currencyInfo = $parseFraction($currencyInfo, $final['fractionsDefault']);
                if (!empty($currencyInfo)) {
                    $final['fractions'][$currencyCode] = $currencyInfo;
                }
            }
            $parseRegion = function ($currencyInfo) {
                $result = array();
                if (array_key_exists('_tender', $currencyInfo)) {
                    if ($currencyInfo['_tender'] !== 'false') {
                        throw new Exception('Invalid _tender value');
                    }
                    unset($currencyInfo['_tender']);
                    $result['notTender'] = true;
                }
                foreach (array('_from' => 'from', '_to' => 'to') as $keyFrom => $keyTo) {
                    if (array_key_exists($keyFrom, $currencyInfo)) {
                        $v = $currencyInfo[$keyFrom];
                        unset($currencyInfo[$keyFrom]);
                        if (!(is_string($v) && preg_match('/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/', $v))) {
                            throw new Exception("Invalid $keyFrom value");
                        }
                        $result[$keyTo] = $v;
                    }
                }
                if (!empty($currencyInfo)) {
                    throw new Exception('Unknown currency info keys found: '.implode(', ', array_keys($currencyInfo)));
                }
                if (empty($result)) {
                    throw new Exception('Empty currency info');
                }

                return $result;
            };
            $final['regions'] = array();
            foreach ($data['region'] as $territoryCode => $territoryInfos) {
                if (is_int($territoryCode)) {
                    $territoryCode = substr('00'.$territoryCode, -3);
                }
                $final['regions'][$territoryCode] = array();
                foreach ($territoryInfos as $territoryInfo) {
                    foreach ($territoryInfo as $currencyCode => $currencyInfo) {
                        $final['regions'][$territoryCode][] = array_merge(array('currency' => $currencyCode), $parseRegion($currencyInfo));
                    }
                }
                usort($final['regions'][$territoryCode], function ($a, $b) {
                    if (array_key_exists('notTender', $a) && $a['notTender']) {
                        if (!array_key_exists('notTender', $b)) {
                            return 1;
                        }
                    } elseif (array_key_exists('notTender', $b) && $b['notTender']) {
                        return -1;
                    }
                    if (array_key_exists('to', $a)) {
                        if (array_key_exists('to', $b)) {
                            if ($a['to'] !== $b['to']) {
                                return strcmp($b['to'], $a['to']);
                            }
                        } else {
                            return 1;
                        }
                    } elseif (array_key_exists('to', $b)) {
                        return -1;
                    }

                    return 0;
                });
            }
            $data = $final;
            break;
    }
    saveJsonFile($data, $dstFile);
}
function copyMissingData_currency($defaultData, $file)
{
    $someChanged = false;
    $data = readJsonFile($file);
    foreach ($defaultData as $currency => $currencyInfo) {
        if (!array_key_exists($currency, $data)) {
            $someChanged = true;
            $data[$currency] = $currencyInfo;
        }
    }
    if ($someChanged) {
        saveJsonFile($data, $file);
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
            deleteFromFilesystem($path.DIRECTORY_SEPARATOR.$item);
        }
        if (rmdir($path) === false) {
            throw new Exception("Failed to delete directory $path");
        }
    }
}

function toPhpSprintf($fmt)
{
    $result = $fmt;
    if (is_string($fmt)) {
        $result = str_replace('%', '%%', $result);
        $result = preg_replace_callback(
            '/\\{(\\d+)\\}/',
            function ($matches) {
                return '%'.(1 + intval($matches[1])).'$s';
            },
            $fmt
        );
    }

    return $result;
}

function fixMetazoneInfo($a)
{
    checkOneKey($a, 'usesMetazone');
    $a = $a['usesMetazone'];
    foreach (array_keys($a) as $key) {
        switch ($key) {
            case '_mzone':
            case '_from':
            case '_to':
                $a[substr($key, 1)] = $a[$key];
                unset($a[$key]);
                break;
            default:
                throw new Exception('Invalid metazoneInfo node');
        }
    }

    return $a;
}

function checkOneKey($node, $key)
{
    if (!is_array($node)) {
        throw new Exception("$node is not an array");
    }
    if (count($node) !== 1) {
        throw new Exception("Expected just one node '$key', found these keys: ".implode(', ', array_keys($node)));
    }
    if (!array_key_exists($key, $node)) {
        throw new Exception("Expected just one node '$key', found this key: ".implode(', ', array_keys($node)));
    }
}

function numberFormatToRegularExpressions($symbols, $isoPattern)
{
    $p = explode(';', $isoPattern);
    $patterns = array(
        '+' => $p[0],
        '-' => (count($p) == 1) ? "-{$p[0]}" : $p[1],
    );
    $result = array();
    $m = null;
    foreach ($patterns as $patternKey => $pattern) {
        $rxPost = $rxPre = '';
        if (preg_match('/(-)?([^0#E,\\.\\-+]*)(.+?)([^0#E,\\.\\-+]*)(-)?$/', $pattern, $m)) {
            for ($i = 1; $i < 6; ++$i) {
                if (!isset($m[$i])) {
                    $m[$i] = '';
                }
            }
            if (strlen($m[2]) > 0) {
                $rxPre = preg_quote($m[2]);
            }
            $pattern = $m[1].$m[3].$m[5];
            if (strlen($m[4]) > 0) {
                $rxPost = preg_quote($m[4]);
            }
        }
        $rx = '';
        if (strpos($pattern, '.') !== false) {
            list($intPattern, $decimalPattern) = explode('.', $pattern, 2);
        } else {
            $intPattern = $pattern;
            $decimalPattern = '';
        }
        if (strpos($intPattern, 'E') !== false) {
            switch ($intPattern) {
                case '#E0':
                case '#E00':
                    $rx .= '('.preg_quote($symbols['plusSign']).')?[0-9]+(('.preg_quote($symbols['decimal']).')[0-9]+)*[eE](('.preg_quote($symbols['minusSign']).')|('.preg_quote($symbols['plusSign']).'))?[0-9]+';
                    break;
                case '-#E0':
                case '-#E00':
                    $rx .= '('.preg_quote($symbols['minusSign']).')?[0-9]+(('.preg_quote($symbols['decimal']).')[0-9]+)*[eE](('.preg_quote($symbols['minusSign']).')|('.preg_quote($symbols['plusSign']).'))?[0-9]+';
                    break;
                default:
                    throw new \Exception("Invalid chunk ('$intPattern') in pattern '$pattern'");
            }
        } elseif (strpos($intPattern, ',') !== false) {
            $chunks = explode(',', $intPattern);
            $maxChunkIndex = count($chunks) - 1;
            $prevChunk = null;
            for ($chunkIndex = 0; $chunkIndex <= $maxChunkIndex; ++$chunkIndex) {
                $chunk = $chunks[$chunkIndex];
                $nextChunk = ($chunkIndex == $maxChunkIndex) ? null : $chunks[$chunkIndex + 1];
                switch ($chunk) {
                    case '#':
                    case '-#':
                        if ($chunk === '-#') {
                            $rx .= '('.preg_quote($symbols['minusSign']).')?';
                        } else {
                            $rx .= '('.preg_quote($symbols['plusSign']).')?';
                        }
                        if ($nextChunk === '##0') {
                            $rx .= '[0-9]{1,3}';
                        } elseif ($nextChunk === '##') {
                            $rx .= '[0-9]{1,2}';
                        } else {
                            throw new \Exception("Invalid chunk #$chunkIndex ('$chunk') in pattern '$pattern'");
                        }
                        break;
                    case '##':
                        if ($nextChunk === '##0') {
                            $rx .= '(('.preg_quote($symbols['group']).')?[0-9]{2})*';
                        } else {
                            throw new \Exception("Invalid chunk #$chunkIndex ('$chunk') in pattern '$pattern'");
                        }
                        break;
                    case '##0':
                        if ($prevChunk === '##') {
                            $rx .= '[0-9]';
                        } elseif (($prevChunk === '#') || ($prevChunk === '-#')) {
                            $rx .= '(('.preg_quote($symbols['group']).')?[0-9]{3})*';
                        } else {
                            throw new \Exception("Invalid chunk #$chunkIndex ('$chunk') in pattern '$pattern'");
                        }
                        break;
                    case '#0':
                        if ($chunkIndex === 0) {
                            $rx .= '[0-9]*';
                        } else {
                            throw new \Exception("Invalid chunk #$chunkIndex ('$chunk') in pattern '$pattern'");
                        }
                        break;
                }
                $prevChunk = $chunk;
            }
        } else {
            throw new \Exception("Invalid chunk ('$intPattern') in pattern '$pattern'");
        }

        if (strlen($decimalPattern) > 0) {
            switch ($decimalPattern) {
                case '###':
                    $rx .= '(('.preg_quote($symbols['decimal']).')[0-9]+)?';
                    break;
                case '###-':
                    $rx .= '(('.preg_quote($symbols['decimal']).')[0-9]+)?('.preg_quote($symbols['minusSign']).')';
                    break;
                default:
                    $m = null;
                    if (preg_match('/^(0+)(-?)$/', $decimalPattern, $m)) {
                        $rx .= '('.preg_quote($symbols['decimal']).')[0-9]{'.strlen($m[1]).'}';
                        if (substr($decimalPattern, -1) === '-') {
                            $rx .= '('.preg_quote($symbols['minusSign']).')';
                        }
                    } else {
                        throw new \Exception("Invalid chunk ('$decimalPattern') in pattern '$pattern'");
                    }
            }
        }

        $result[$patternKey] = '/^'.$rxPre.$rx.$rxPost.'$/u';
    }

    return $result;
}
