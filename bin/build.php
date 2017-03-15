<?php

function handleError($errno, $errstr, $errfile, $errline)
{
    if ($errno == E_NOTICE || $errno == E_WARNING) {
        throw new Exception("$errstr in $errfile @ line $errline", $errno);
    }
}

set_error_handler('handleError');

try {
    if (isset($argv) && is_array($argv) && count($argv) > 1) {
        $optionArray = array_values($argv);
        array_shift($optionArray);
    } else {
        $optionArray = array();
    }
    $options = BuildOptions::fromArray($optionArray);
    echo 'Processing CLDR version: ', $options->getCLDRVersion(), "\n";
    echo 'Processing locales     : ', $options->describeLocales(), "\n";
    echo 'Output directory       : ', str_replace('/', DIRECTORY_SEPARATOR, $options->getOutputDirectory()), "\n";
    echo 'Temporary directory    : ', str_replace('/', DIRECTORY_SEPARATOR, $options->getTemporaryDirectory()), "\n";
    $fileUtils = new FileUtils();
    $sourceData = new SourceData($options, $fileUtils);
    if ($options->getResetCLDRData()) {
        if ($sourceData->isSvnPresent()) {
            echo 'Deleting the SVN repository... ';
            $sourceData->deleteSvnRepository();
            echo "done.\n";
        }
    }
    if (!$sourceData->isSvnPresent()) {
        echo 'Checking out the CLDR repository... ';
        $sourceData->ensureSvnPresent();
        echo "done.\n";
    }
    $locales = $options->finalizeLocalesList($sourceData->getAvailableLocales());
    if (empty($locales)) {
        throw new Exception('No locale will be generated');
    }
    if (!$sourceData->isJarPresent()) {
        echo 'Creating the CLDR jar file... ';
        $sourceData->ensureJarPresent();
        echo "done.\n";
    }
    if ($options->getResetCLDRData()) {
        if ($sourceData->isJsonContainerPresent()) {
            echo 'Deleting the CLDR JSON directory... ';
            $sourceData->deleteJsonContainer();
            echo "done.\n";
        }
    }
    if (!$sourceData->isJsonGenericPresent('segments')) {
        echo 'Creating the segments JSON files... ';
        $sourceData->ensureJsonGeneric('segments');
        echo "done.\n";
    }
    if (!$sourceData->isJsonGenericPresent('supplemental')) {
        echo 'Creating the supplemental JSON files... ';
        $sourceData->ensureJsonGeneric('supplemental');
        echo "done.\n";
    }
    if (is_dir($options->getOutputDirectory()) && $options->getResetPunicData()) {
        echo 'Clearing current Punic data... ';
        $fileUtils->deleteFromFilesystem($options->getOutputDirectory(), true);
        echo "done.\n";
    }
    if (!$sourceData->isJsonLocalePresent('en')) {
        echo 'Building source JSON data for English... ';
        $sourceData->ensureJsonLocale('en');
        echo "done.\n";
    }

    $jsonFileHelper = new JsonFileHelper($fileUtils, $options);
    $converter = new JSonConverter($fileUtils, $sourceData, $jsonFileHelper);
    foreach ($locales as $localeID) {
        echo "Processing $localeID:\n";
        $destDir = $options->getOutputDirectory().'/'.str_replace('_', '-', $localeID);
        if (is_dir($destDir)) {
            echo "  - destination directory exists: SKIPPED\n";
        } else {
            if (!$sourceData->isJsonLocalePresent($localeID)) {
                echo '  - building source JSON data... ';
                $sourceData->ensureJsonLocale($localeID);
                echo "done.\n";
            }
            echo '  - processing...';
            $converter->convertLocale($localeID, $destDir);
            echo "done.\n";
        }
    }
    echo 'Processing supplemental files... ';
    $converter->convertSupplemental($options->getOutputDirectory());
    echo "done.\n";
    die(0);
} catch (Exception $x) {
    echo "\n\n", $x->getMessage(), "\n";
    if (method_exists($x, 'getTraceAsString')) {
        echo "\nTRACE:\n", $x->getTraceAsString(), "\n";
    }
    die(1);
}

/**
 * Command options.
 */
class BuildOptions
{
    /**
     * The default CLDR version.
     *
     * @var string
     */
    const DEFAULT_CLDR_VERSION = '31';

    /**
     * Comma-separated list of the default locales.
     *
     * @var string
     */
    const DEFAULT_LOCALES = 'ar,ca,cs,da,de,el,en,en_AU,en_CA,en_GB,en_HK,en_IN,es,fi,fr,he,hi,hr,hu,it,ja,ko,nb,nl,nn,pl,pt,pt_PT,ro,root,ru,sk,sl,sr,sv,th,tr,uk,vi,zh,zh_Hant';

    /**
     * Placeholder for all locales.
     *
     * @var string
     */
    const ALL_LOCALES_PLACEHOLDER = '[ALL]';

    /**
     * The CLDR version.
     *
     * @var string
     */
    protected $cldrVersion;

    /**
     * Get the CLDR version.
     *
     * @return string
     */
    public function getCLDRVersion()
    {
        return $this->cldrVersion;
    }

    /**
     * The CLDR version data should be reset?
     *
     * @var bool
     */
    protected $resetCLDRData;

    /**
     * The CLDR version data should be reset?
     *
     * @return bool
     */
    public function getResetCLDRData()
    {
        return $this->resetCLDRData;
    }

    /**
     * The Punic data should be reset?
     *
     * @var bool
     */
    protected $resetPunicData;

    /**
     * The Punic data should be reset?
     *
     * @return bool
     */
    public function getResetPunicData()
    {
        return $this->resetPunicData;
    }

    /**
     * Output uncompressed?
     *
     * @var bool
     */
    protected $prettyOutput;

    /**
     * Output uncompressed?
     *
     * @return bool
     */
    public function getPrettyOutput()
    {
        return $this->prettyOutput;
    }

    /**
     * The list of the output locales (or true for all).
     *
     * @var string[]|true
     */
    protected $locales;

    /**
     * The list of the locales to exclude.
     *
     * @var string[]|true
     */
    protected $excludeLocales;

    /**
     * The output directory.
     *
     * @var string
     */
    protected $outputDirectory;

    /**
     * Get the output directory.
     *
     * @return string
     */
    public function getOutputDirectory()
    {
        return $this->outputDirectory;
    }

    /**
     * @return string
     */
    protected static function getDefaultOutputDirectory()
    {
        return rtrim(str_replace(DIRECTORY_SEPARATOR, '/', dirname(dirname(__FILE__))), '/').'/code/data';
    }

    /**
     * The temporary directory.
     *
     * @var string
     */
    protected $temporaryDirectory;

    /**
     * Get the temporary directory.
     *
     * @return string
     */
    public function getTemporaryDirectory()
    {
        return $this->temporaryDirectory;
    }

    /**
     * @return string
     */
    protected static function getDefaultTemporaryDirectory()
    {
        return rtrim(str_replace(DIRECTORY_SEPARATOR, '/', dirname(dirname(__FILE__))), '/').'/temp';
    }

    /**
     * Initializes the instance.
     */
    protected function __construct()
    {
        $this->cldrVersion = static::DEFAULT_CLDR_VERSION;
        $this->resetCLDRData = false;
        $this->resetPunicData = false;
        $this->prettyOutput = false;
        $this->locales = explode(',', static::DEFAULT_LOCALES);
        $this->excludeLocales = array();
        $this->outputDirectory = static::getDefaultOutputDirectory();
        $this->temporaryDirectory = static::getDefaultTemporaryDirectory();
    }

    /**
     * @param array $options
     *
     * @return BuildOptions
     */
    public static function fromArray(array $options)
    {
        $result = new static();
        $localeOptions = array();
        $substractLocales = array();
        $n = count($options);
        for ($i = 0; $i < $n; ++$i) {
            if (preg_match('/^(--[^=]+)=(.*)$/', $options[$i], $matches)) {
                $currentOption = $matches[1];
                $nextOption = $matches[2];
                $advanceNext = false;
            } else {
                $currentOption = $options[$i];
                $nextOption = $i + 1 < $n ? $options[$i + 1] : '';
                $advanceNext = true;
            }

            $optionWithValue = false;
            switch (strtolower($currentOption)) {
                case '-h':
                case '--help':
                    static::showHelp();
                    exit(0);
                case '--version':
                case '-v':
                    $optionWithValue = true;
                    if ($nextOption === '') {
                        throw new Exception('Please specify the CLDR version to be processed');
                    }
                    if (!preg_match('/^[1-9]\d*(\.\d+)*(\.[dM]\d+|\.beta\.\d+)?$/', $nextOption)) {
                        throw new Exception("Invalid version specified ($nextOption)");
                    }
                    $result->cldrVersion = $nextOption;
                    break;
                case '--locale':
                case '-l':
                    $optionWithValue = true;
                    if ($nextOption === '') {
                        throw new Exception('Please specify one or more locale identifiers');
                    }
                    $localeOptions = array_merge($localeOptions, explode(',', $nextOption));
                    break;
                case '--reset-cldr-data':
                case '-c':
                    $result->resetCLDRData = true;
                    break;
                case '--reset-punic-data':
                case '-r':
                    $result->resetPunicData = true;
                    break;
                case '--pretty-output':
                case '-p':
                    if (version_compare(PHP_VERSION, '5.4.0') < 0) {
                        throw new Exception("The $currentOption option is available for PHP 5.4+ (you are running PHP ".PHP_VERSION.')');
                    }
                    $result->prettyOutput = true;
                    break;
                case '--output':
                case '-o':
                    $optionWithValue = true;
                    if ($nextOption === '') {
                        throw new Exception('Please specify the output directory');
                    }
                    $s = static::normalizeDirectoryPath($nextOption);
                    if ($s === null) {
                        throw new Exception("$currentOption is not a valid output directory path");
                    }
                    $result->outputDirectory = $s;
                    break;
                case '--temp':
                case '-t':
                    $optionWithValue = true;
                    if ($nextOption === '') {
                        throw new Exception('Please specify the temporary directory');
                    }
                    $s = static::normalizeDirectoryPath($nextOption);
                    if ($s === null) {
                        throw new Exception("$currentOption is not a valid temporary directory path");
                    }
                    $result->temporaryDirectory = $s;
                    break;
                default:
                    throw new Exception("Unknown option: $currentOption\nUse -h (or --help) to get the list of available options");
            }
            if ($optionWithValue && $advanceNext) {
                ++$i;
            }
        }
        if (!empty($localeOptions)) {
            $result->parseLocaleOptions($localeOptions);
        }

        return $result;
    }

    /**
     * @param string|mixed $path
     *
     * @return string|null
     */
    protected static function normalizeDirectoryPath($path)
    {
        $result = null;
        if (is_string($path)) {
            $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
            if (stripos(PHP_OS, 'WIN') === 0) {
                $invalidChars = implode('', array_map('chr', range(0, 31))).'*?"<>|';
            } else {
                $invalidChars = '';
            }
            $path = rtrim($path, '/');
            if ($path !== '' && $invalidChars === '' || strpbrk($path, $invalidChars) === false) {
                $result = $path;
            }
        }

        return $result;
    }

    /**
     * @param array $localeOptions
     *
     * @throws Exception
     */
    protected function parseLocaleOptions(array $localeOptions)
    {
        $allLocales = false;
        $locales = array();
        foreach ($localeOptions as $localeOption) {
            if ($localeOption === '') {
                throw new Exception('Empty locale detected');
            }
            if ($localeOption === 'root') {
                $localeOption = 'en_US';
            } elseif ($localeOption === static::ALL_LOCALES_PLACEHOLDER) {
                $allLocales = true;
            } else {
                $localeOperation = '=';
                $localeCode = $localeOption;
                if ($localeOption !== '') {
                    switch ($localeOption[0]) {
                        case '+':
                        case '-':
                            $localeOperation = $localeOption[0];
                            $localeCode = substr($localeOption, 1);
                            break;
                    }
                }
                $locale = LocaleIdentifier::fromString($localeCode);
                if ($locale === null) {
                    throw new Exception("Invalid locale identifier specified: $localeOption");
                }
                $localeCode = (string) $locale;
                if (isset($locales[$localeCode])) {
                    throw new Exception("Locale identifier specified more than once: $localeCode");
                }
                $locales[$localeCode] = $localeOperation;
            }
        }
        if ($allLocales) {
            $this->locales = true;
            if (in_array('=', $locales)) {
                throw new Exception("You specified to use all the locales, and to use specific locales.\nIf you want to specify 'all locales except some', please prepend them with a minus sign.");
            }
            $this->excludeLocales = array_keys(array_filter(
                $locales,
                function ($operation) {
                    return $operation === '-';
                }
                ));
        } else {
            if (in_array('=', $locales)) {
                $this->locales = array_keys(array_filter(
                    $locales,
                    function ($operation) {
                        return $operation !== '-';
                    }
                    ));
            } else {
                $this->locales = array_values(array_unique(array_merge(
                    $this->locales,
                    array_keys(array_filter(
                        $locales,
                        function ($operation) {
                            return $operation === '+';
                        }
                        ))
                    )));
            }
            $this->excludeLocales = array_keys(array_filter(
                $locales,
                function ($operation) {
                    return $operation === '-';
                }
                ));
        }
        if ($this->locales !== true && !empty($this->excludeLocales)) {
            $common = array_intersect($this->locales, $this->excludeLocales);
            if (!empty($common)) {
                $this->locales = array_values(array_diff($this->locales, $common));
            }
            $this->excludeLocales = array();
        }
    }

    /**
     * @return string
     */
    public function describeLocales()
    {
        if ($this->locales === true) {
            if (empty($this->excludeLocales)) {
                $result = 'all locales';
            } else {
                $result = 'all locales except '.implode(', ', $this->excludeLocales);
            }
        } else {
            $result = implode(', ', $this->locales);
        }

        return $result;
    }

    protected function showHelp()
    {
        $defaultCLDRVersion = static::DEFAULT_CLDR_VERSION;
        $allLocalesPlaceholders = static::ALL_LOCALES_PLACEHOLDER;
        $defaultLocales = static::DEFAULT_LOCALES;
        $defaultOutputDirectory = str_replace('/', DIRECTORY_SEPARATOR, static::getDefaultOutputDirectory());
        $defaultTemporaryDirectory = str_replace('/', DIRECTORY_SEPARATOR, static::getDefaultTemporaryDirectory());
        echo <<<EOT
Available options:

  --help|-h
    Show this help message

  --version=<version>|-v <version>
    Set the CLDR version to work on (default: $defaultCLDRVersion)
    Examples: 31.d02  30.0.3  30  29.beta.1  25.M1  23.1.d01

  --reset-cldr-data|-c
    Reset the source CLDR data before the execution

  --reset-punic-data|-r
    Reset the destination Punic data before the execution

  --pretty-output|-p
    Generated expanded (uncompressed) JSON (applicable from PHP 5.4+)

  --output|-o
    Set the output directory (default: $defaultOutputDirectory)

  --temp|-t
    Set the temporary directory (default: $defaultTemporaryDirectory)

  --locale=<locales>|-l <locales>
    Set the locales to work on.
    It's a comman-separated list of locale codes (you can also specify this option multiple times).
    You can use $allLocalesPlaceholders (case-sensitive) to include all available locales.
    You can prepend a minus sign to substract specific locales: so for instance
    --locale=-it,-de
    means 'the default locales except Italian and German'.
    Likewise:
    --locale=ALL,-it,-de
    means 'all locales except Italian and German'.
    You can prepend a plus to add specific locales: so for instance
    --locale=+it,+de
    means 'default locales plus Italian and German'.
    The locales included by default are:
    $defaultLocales

EOT;
    }

    /**
     * @param string[] $availableLocales
     *
     * @throws Exception
     *
     * @return string[]
     */
    public function finalizeLocalesList(array $availableLocales)
    {
        if ($this->locales === true) {
            $locales = $availableLocales;
        } else {
            foreach ($this->locales as $testLocale) {
                $bl = LocaleIdentifier::fromString($testLocale);
                if (in_array($bl->getLanguage(), $availableLocales) === false) {
                    throw new Exception("The locale $testLocale is not defined in the CLDR data");
                }
            }
            $locales = $this->locales;
        }
        if (!empty($this->excludeLocales)) {
            $locales = array_diff($locales, $this->excludeLocales);
        }
        natcasesort($locales);

        return array_values($locales);
    }
}

class FileUtils
{
    /**
     * @param string $path
     * @param bool $emptyOnlyDir
     *
     * @throws Exception
     */
    public function deleteFromFilesystem($path, $emptyOnlyDir = false)
    {
        if (is_file($path) || is_link($path)) {
            if (@unlink($path) === false) {
                throw new Exception("Failed to delete the file $path");
            }
        } elseif (is_dir($path)) {
            $contents = @scandir($path);
            if ($contents === false) {
                throw new Exception("Failed to retrieve the contents of the directory $path");
            }
            foreach (array_diff($contents, array('.', '..')) as $item) {
                $this->deleteFromFilesystem($path.'/'.$item);
            }
            if (!$emptyOnlyDir) {
                if (@rmdir($path) === false) {
                    throw new Exception("Failed to delete the directory $path");
                }
            }
        }
    }

    /**
     * @param string $path
     *
     * @throws Exception
     */
    public function createDirectory($path)
    {
        if (!is_dir($path)) {
            if (@mkdir($path, 0777, true) !== true) {
                throw new Exception("Failed to create the directory $path");
            }
        }
    }

    /**
     * @param string $path
     *
     * @throws Exception
     */
    public function createFileDirectory($path)
    {
        $this->createDirectory(dirname($path));
    }
}

class SourceData
{
    /**
     * @var BuildOptions
     */
    protected $options;

    /**
     * @var FileUtils
     */
    protected $fileUtils;

    /**
     * @var string[]|null
     */
    protected $availableLocales;

    /**
     * @param BuildOptions $options
     */
    public function __construct(BuildOptions $options, FileUtils $fileUtils)
    {
        $this->options = $options;
        $this->fileUtils = $fileUtils;
        $this->availableLocales = null;
    }

    /**
     * Get the SVN directory path.
     *
     * @return string
     */
    protected function getSvnDirectory()
    {
        return $this->options->getTemporaryDirectory().'/cldr/'.$this->options->getCLDRVersion().'/svn';
    }

    /**
     * There's a local clone of the SVN repository?
     *
     * @return bool
     */
    public function isSvnPresent()
    {
        return is_dir($this->getSvnDirectory());
    }

    /**
     * Delete the local clone of the SVN repository.
     */
    public function deleteSvnRepository()
    {
        $this->fileUtils->deleteFromFilesystem($this->getSvnDirectory());
        $this->availableLocales = null;
    }

    /**
     * Be sure that there's a local clone of the SVN repository.
     *
     * @throws Exception
     */
    public function ensureSvnPresent()
    {
        if (!$this->isSvnPresent()) {
            $dir = $this->getSvnDirectory();
            $this->fileUtils->createDirectory($dir);
            $this->fileUtils->deleteFromFilesystem($dir);
            try {
                $tag = 'release-'.str_replace('.', '-', $this->options->getCLDRVersion());
                $output = array();
                $rc = null;
                @exec('svn checkout http://www.unicode.org/repos/cldr/tags/'.$tag.'/ '.escapeshellarg($dir).' 2>&1', $output, $rc);
                if ($rc === 0) {
                    if (!is_dir($dir)) {
                        $rc = -1;
                    }
                }
                if ($rc !== 0) {
                    $msg = "Error $rc!\n";
                    if (stripos(PHP_OS, 'WIN') !== false) {
                        $msg .= 'Please make sure that SVN is installed and in your path. You can install TortoiseSVN for instance.';
                    } else {
                        $msg .= "You need the svn command line tool: under Ubuntu and Debian systems you can for instance run 'sudo apt-get install subversion'";
                    }
                    $msg .= "\nError details:\n".implode("\n", $output);
                    throw new Exception($msg);
                }
            } catch (Exception $x) {
                try {
                    $this->deleteSvnRepository();
                } catch (Exception $foo) {
                }
                throw $x;
            }
        }
    }

    /**
     * Get the list of available locale IDs.
     *
     * @throws Exception
     *
     * @return string[]
     */
    public function getAvailableLocales()
    {
        if ($this->availableLocales === null) {
            $this->ensureSvnPresent();
            $dir = $this->getSvnDirectory().'/common/main';
            if (!is_dir($dir)) {
                throw new Exception("Unable to find the directory $dir");
            }
            $contents = @scandir($dir);
            if ($contents === false) {
                throw new Exception("Failed to retrieve the contents of the directory $dir");
            }
            $availableLocales = array();
            foreach ($contents as $item) {
                if (preg_match('/^(.+)\.xml$/', $item, $matches)) {
                    $availableLocales[] = $matches[1];
                }
            }
            $availableLocales = array_values($availableLocales);
            if (empty($availableLocales)) {
                throw new Exception("No locales found in $dir");
            }
            natcasesort($availableLocales);
            $this->availableLocales = array_values($availableLocales);
        }

        return $this->availableLocales;
    }

    /**
     * Get the jar file path.
     *
     * @return string
     */
    protected function getJarFile()
    {
        return $this->getSvnDirectory().'/tools/java/cldr.jar';
    }

    /**
     * Does the CLDR jar file exist?
     *
     * @return bool
     */
    public function isJarPresent()
    {
        return is_file($this->getJarFile());
    }

    /**
     * Delete the CLDR jar.
     */
    public function deleteJar()
    {
        $this->fileUtils->deleteFromFilesystem($this->getJarFile());
    }

    /**
     * Be sure that the CLDR jar file exists.
     *
     * @throws Exception
     */
    public function ensureJarPresent()
    {
        if (!$this->isJarPresent()) {
            $this->ensureSvnPresent();
            $file = $this->getJarFile();
            try {
                $output = array();
                $rc = null;
                @exec('ant -f '.escapeshellarg($this->getSvnDirectory().'/tools/java/build.xml').' jar 2>&1', $output, $rc);
                if ($rc === 0) {
                    if (!is_file($file)) {
                        $rc = -1;
                    }
                }
                if ($rc !== 0) {
                    $msg = "Error $rc!\n";
                    if (stripos(PHP_OS, 'WIN') !== false) {
                        $msg .= 'Please make sure that the ant tool is installed and in your path, and that Java JDK is installed and configured correctly.';
                    } else {
                        $msg .= "You need the ant command line tool and JDK: under Ubuntu and Debian systems you can for instance run 'sudo apt-get install ant openjdk-7-jdk'";
                    }
                    $msg .= "\nError details:\n".implode("\n", $output);
                    throw new Exception($msg);
                }
            } catch (Exception $x) {
                try {
                    $this->deleteJar();
                } catch (Exception $foo) {
                }
                throw $x;
            }
        }
    }

    /**
     * Get the JSON directory path.
     *
     * @return string
     */
    protected function getJsonDirectory()
    {
        return $this->options->getTemporaryDirectory().'/cldr/'.$this->options->getCLDRVersion().'/json';
    }

    /**
     * Does the JSON directory is present?
     *
     * @return bool
     */
    public function isJsonContainerPresent()
    {
        return is_dir($this->getJsonDirectory());
    }

    /**
     * Delete the JSON directory.
     */
    public function deleteJsonContainer()
    {
        $this->fileUtils->deleteFromFilesystem($this->getJsonDirectory());
        $this->availableLocales = null;
    }

    /**
     * Get the JSON directory path for a specific locale.
     *
     * @param string $localeID
     *
     * @return string
     */
    public function getJsonDirectoryForLocale($localeID)
    {
        return $this->getJsonDirectory().'/locales/'.$localeID;
    }

    /**
     * Does the JSON directory is present for a specific locale?
     *
     * @param string $localeID
     *
     * @return bool
     */
    public function isJsonLocalePresent($localeID)
    {
        return is_dir($this->getJsonDirectoryForLocale($localeID));
    }

    /**
     * Be sure that the JSON data for a locale is there.
     *
     * @param string $localeID
     *
     * @throws Exception
     */
    public function ensureJsonLocale($localeID)
    {
        if (!$this->isJsonLocalePresent($localeID)) {
            $this->ensureJarPresent();
            $dir = $this->getJsonDirectoryForLocale($localeID);
            $this->fileUtils->createDirectory($dir);
            $this->fileUtils->deleteFromFilesystem($dir);
            try {
                $cmd = 'java';
                $cmd .= ' -Duser.language=en -Duser.country=US'; // http://unicode.org/cldr/trac/ticket/10044
                $cmd .= ' -DCLDR_DIR='.escapeshellarg($this->getSvnDirectory()); //  where the CLDR data is located
                $cmd .= ' -DCLDR_GEN_DIR='.escapeshellarg($dir); // where to save the generated files
                $cmd .= ' -jar '.escapeshellarg($this->getJarFile()); // the CLDR jar file
                $cmd .= ' ldml2json';
                $cmd .= ' -o true'; // (true|false) Whether to write out the 'other' section, which contains any unmatched paths
                $cmd .= ' -t main'; // (main|supplemental|segments|rbnf) Type of CLDR data being generated, main, supplemental, or segments.
                $cmd .= ' -r true'; // (true|false) Whether the output JSON for the main directory should be based on resolved or unresolved data
                $cmd .= ' -m '.escapeshellarg(str_replace('-', '_', $localeID)); // Regular expression to define only specific locales or files to be generated
                $output = array();
                $rc = null;
                @exec($cmd.' 2>&1', $output, $rc);
                if ($rc === 0) {
                    if (!is_dir($dir)) {
                        $rc = -1;
                    }
                }
                if ($rc !== 0) {
                    $msg = "Error $rc: ".implode("\n", $output);
                    throw new Exception($msg);
                }
            } catch (Exception $x) {
                try {
                    $this->fileUtils->deleteFromFilesystem($dir);
                } catch (Exception $foo) {
                }
                throw $x;
            }
        }
    }

    /**
     * Get the JSON directory path for a generic data.
     *
     * @param string $genericID (supplemental, segments)
     *
     * @return string
     */
    public function getJsonDirectoryForGeneric($genericID)
    {
        return $this->getJsonDirectory().'/'.$genericID;
    }

    /**
     * Does the JSON directory is present for a specific generic data?
     *
     * @param string $genericID (supplemental, segments)
     *
     * @return bool
     */
    public function isJsonGenericPresent($genericID)
    {
        return is_dir($this->getJsonDirectoryForGeneric($genericID));
    }

    /**
     * Be sure that the JSON data for a generic data is there.
     *
     * @param string $genericID (supplemental, segments)
     *
     * @throws Exception
     */
    public function ensureJsonGeneric($genericID)
    {
        if (!$this->isJsonGenericPresent($genericID)) {
            $this->ensureJarPresent();
            $dir = $this->getJsonDirectoryForGeneric($genericID);
            $this->fileUtils->createDirectory($dir);
            $this->fileUtils->deleteFromFilesystem($dir);
            try {
                $cmd = 'java';
                $cmd .= ' -Duser.language=en -Duser.country=US'; // http://unicode.org/cldr/trac/ticket/10044
                $cmd .= ' -DCLDR_DIR='.escapeshellarg($this->getSvnDirectory()); //  where the CLDR data is located
                $cmd .= ' -DCLDR_GEN_DIR='.escapeshellarg($dir); // where to save the generated files
                $cmd .= ' -jar '.escapeshellarg($this->getJarFile()); // the CLDR jar file
                $cmd .= ' ldml2json';
                switch ($genericID) {
                    case 'supplemental':
                    case 'segments':
                        $cmd .= ' -o true'; // (true|false) Whether to write out the 'other' section, which contains any unmatched paths
                        $cmd .= ' -t '.$genericID; // (main|supplemental|segments|rbnf) Type of CLDR data being generated, main, supplemental, or segments.
                        break;
                    default:
                        throw new Exception("Unrecognized generic data ID: $genericID");
                }
                $output = array();
                $rc = null;
                @exec($cmd.' 2>&1', $output, $rc);
                if ($rc === 0) {
                    if (!is_dir($dir)) {
                        $rc = -1;
                    }
                }
                if ($rc !== 0) {
                    $msg = "Error $rc: ".implode("\n", $output);
                    throw new Exception($msg);
                }
            } catch (Exception $x) {
                try {
                    $this->fileUtils->deleteFromFilesystem($dir);
                } catch (Exception $foo) {
                }
                throw $x;
            }
        }
    }
}

class LocaleIdentifier
{
    /**
     * @var string
     */
    protected $language = '';

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @var string
     */
    protected $script = '';

    /**
     * @var string
     */
    protected $region = '';

    /**
     * @var string
     */
    protected $variants = array();

    protected function __construct()
    {
    }

    /**
     * @param string|mixed $localeIdentifier
     *
     * @return static|null
     */
    public static function fromString($localeIdentifier)
    {
        $result = null;
        // http://unicode.org/reports/tr35/#Unicode_language_identifier
        if (strcasecmp($localeIdentifier, 'root') === 0) {
            $result = new static();
            $result->language = 'root';
        } else {
            $rxLanguage = '(?:[a-z]{2,3})|(?:[a-z]{5,8}:)';
            $rxScript = '[a-z]{4}';
            $rxRegion = '(?:[a-z]{2})|(?:[0-9]{3})';
            $rxVariant = '(?:[a-z0-9]{5,8})|(?:[0-9][a-z0-9]{3})';
            $rxSep = '[-_]';
            if (is_string($localeIdentifier) && preg_match("/^($rxLanguage)(?:$rxSep($rxScript))?(?:$rxSep($rxRegion))?((?:$rxSep(?:$rxVariant))*)$/i", $localeIdentifier, $matches)) {
                $result = new static();
                $result->language = strtolower($matches[1]);
                if (isset($matches[2])) {
                    $result->script = ucfirst(strtolower($matches[2]));
                }
                if (isset($matches[3])) {
                    $result->region = strtoupper($matches[3]);
                }
                if ($matches[4] !== '') {
                    $result->variants = explode('_', strtoupper(str_replace('-', '_', substr($matches[4], 1))));
                }
            }
        }

        return $result;
    }

    protected static function merge($language, $script = '', $region = '', array $variants = array())
    {
        $parts = array();

        $parts[] = $language;
        if ($script !== '') {
            $parts[] = $script;
        }
        if ($region !== '') {
            $parts[] = $region;
        }
        $parts = array_merge($parts, $variants);

        return implode('_', $parts);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return static::merge($this->language, $this->script, $this->region, $this->variants);
    }

    /**
     * @return string[]
     */
    public function getParentLocaleIdentifiers()
    {
        $parents = array();
        if (!empty($this->variants)) {
            $parents[] = static::merge($this->language, $this->script, $this->region);
        }
        if ($this->script !== '' && $this->region !== '') {
            $parents[] = static::merge($this->language, $this->script, '');
            $parents[] = static::merge($this->language, '', $this->region);
        }
        if ($this->script !== '' || $this->region !== '') {
            $parents[] = static::merge($this->language);
        }

        $parents[] = 'root';

        return $parents;
    }
}

class JsonFileHelper
{
    /**
     * @var FileUtils
     */
    protected $fileUtils;

    /**
     * @var BuildOptions
     */
    protected $options;

    /**
     * @param FileUtils $fileUtils
     * @param BuildOptions $options
     */
    public function __construct(FileUtils $fileUtils, BuildOptions $options)
    {
        $this->fileUtils = $fileUtils;
        $this->options = $options;
    }

    /**
     * Read a JSON-encoded data from a file.
     *
     * @param string $file
     *
     * @throws Exception
     *
     * @return array
     */
    public function read($file)
    {
        $string = @file_get_contents($file);
        if ($string === false) {
            throw new Exception("Failed to read from file $file");
        }
        $data = @json_decode($string, true);
        if ($data === null) {
            throw new Exception("Failed to decode the JSON data of $file");
        }

        return $data;
    }

    /**
     * Save data to file in JSON format.
     *
     * @param array $data
     * @param string $file
     *
     * @throws Exception
     */
    public function save(array $data, $file)
    {
        $jsonFlags = 0;
        if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
            $jsonFlags |= JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
            if ($this->options->getPrettyOutput()) {
                $jsonFlags |= JSON_PRETTY_PRINT;
            }
        }
        $json = json_encode($data, $jsonFlags);
        if ($json === false) {
            throw new Exception("Failed to serialize data for $file");
        }
        if (@is_file($file)) {
            $this->fileUtils->deleteFromFilesystem($file);
        }
        if (@file_put_contents($file, $json) === false) {
            throw new Exception("Failed write to $file");
        }
    }
}

class JSonConverter
{
    /**
     * @var FileUtils
     */
    protected $fileUtils;

    /**
     * @var SourceData
     */
    protected $sourceData;

    /**
     * @var JsonFileHelper
     */
    protected $jsonFileHelper;

    /**
     * @param SourceData $sourceData
     * @param JsonFileHelper $jsonFileHelper
     */
    public function __construct(FileUtils $fileUtils, SourceData $sourceData, JsonFileHelper $jsonFileHelper)
    {
        $this->fileUtils = $fileUtils;
        $this->sourceData = $sourceData;
        $this->jsonFileHelper = $jsonFileHelper;
    }

    /**
     * Convert the CLDR JSON data into Punic data for a specific locale.
     *
     * @param string $localeID
     * @param string $destinationDirectory
     *
     * @throws Exception
     */
    public function convertLocale($localeID, $destinationDirectory)
    {
        $this->fileUtils->createDirectory($destinationDirectory);
        try {
            $converters = array(
                'ca-gregorian.json' => CalendarLocalePunicConversion::create(),
                'timeZoneNames.json' => TimeZoneNameslocalePunicConversion::create(),
                'listPatterns.json' => ListPatternsLocalePunicConversion::create(),
                'units.json' => UnitsLocalePunicConversion::create(),
                'dateFields.json' => NoopLocalePunicConversion::create(array('dates', 'fields')),
                'languages.json' => NoopLocalePunicConversion::create(array('localeDisplayNames', 'languages')),
                'territories.json' => NoopLocalePunicConversion::create(array('localeDisplayNames', 'territories')),
                'localeDisplayNames.json' => LocaleDisplayNamesLocalePunicConversion::create(),
                'numbers.json' => NumbersLocalePunicConversion::create(),
                'layout.json' => NoopLocalePunicConversion::create(array('layout', 'orientation')),
                'measurementSystemNames.json' => NoopLocalePunicConversion::create(array('localeDisplayNames', 'measurementSystemNames')),
                'currencies.json' => CurrenciesLocalePunicConversion::create(),
            );
            foreach ($converters as $copyFrom => $converter) {
                $destinationFile = $destinationDirectory.'/'.$converter->getSaveAs($copyFrom);
                $useLocale = $localeID;
                $sourceFile = $this->sourceData->getJsonDirectoryForLocale($useLocale).'/'.$copyFrom;
                if (!is_file($sourceFile)) {
                    list($useLocale) = preg_split('/[^a-zA-Z0-9]+/', $sourceFile);
                    $sourceFile = $this->sourceData->getJsonDirectoryForLocale($useLocale).'/'.$copyFrom;
                    if (!is_file($sourceFile)) {
                        throw new Exception("File not found: $sourceFile");
                    }
                }
                $data = $this->jsonFileHelper->read($sourceFile);
                $data = $converter->process($data, $useLocale);
                $this->jsonFileHelper->save($data, $destinationFile);
            }
            if ($localeID !== 'en') {
                $this->copyMissingData_currency(
                    $destinationDirectory.'/currencies.json'
                );
            }
        } catch (Exception $x) {
            $this->fileUtils->deleteFromFilesystem($destinationDirectory);
            throw $x;
        }
    }

    /**
     * Convert the supplemental CLDR JSON data into Punic data.
     *
     * @param string $destinationDirectory
     *
     * @throws Exception
     */
    public function convertSupplemental($destinationDirectory)
    {
        $converters = array(
            'telephoneCodeData.json' => TelephoneCodeDataSupplementalPunicConversion::create(),
            'territoryInfo.json' => TerritoryInfoSupplementalPunicConversion::create(),
            'weekData.json' => WeekDataSupplementalPunicConversion::create(),
            'parentLocales.json' => NoopSupplementalPunicConversion::create(array('supplemental', 'parentLocales', 'parentLocale')),
            'likelySubtags.json' => NoopSupplementalPunicConversion::create(array('supplemental', 'likelySubtags')),
            'territoryContainment.json' => TerritoryContainmentSupplementalPunicConversion::create(),
            'metaZones.json' => MetaZonesSupplementalPunicConversion::create(),
            'plurals.json' => PluralsSupplementalPunicConversion::create(),
            'measurementData.json' => MeasurementDataSupplementalPunicConversion::create(),
            'currencyData.json' => CurrencyDataSupplementalPunicConversion::create(),
        );
        foreach ($converters as $copyFrom => $converter) {
            $destinationFile = $destinationDirectory.'/'.$converter->getSaveAs($copyFrom);
            if (!is_file($destinationFile)) {
                $sourceFile = $this->sourceData->getJsonDirectoryForGeneric('supplemental').'/'.$copyFrom;
                if (!is_file($sourceFile)) {
                    throw new Exception("File not found: $sourceFile");
                }
                $data = $this->jsonFileHelper->read($sourceFile);
                $data = $converter->process($data);
                $this->jsonFileHelper->save($data, $destinationFile);
            }
            if ($converter instanceof PluralsSupplementalPunicConversion) {
                $testDir = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', dirname(dirname(__FILE__))), '/').'/tests/dataFiles';
                if (is_dir($testDir)) {
                    $sourceFile = $this->sourceData->getJsonDirectoryForGeneric('supplemental').'/'.$copyFrom;
                    $data = $this->jsonFileHelper->read($sourceFile);
                    $testData = $converter->getTestData($data);
                    $this->jsonFileHelper->save($testData, $testDir.'/plurals.json');
                }
            }
        }
    }

    /**
     * @param array|mixed $node
     * @param string[] $expectedKeys
     *
     * @throws Exception
     */
    private function checkExactKeys($node, array $expectedKeys)
    {
        if (!is_array($node)) {
            throw new Exception("$node is not an array");
        }
        $nodeKeys = array_keys($node);
        $missingKeys = array_diff($expectedKeys, $nodeKeys);
        if (count($missingKeys) > 0) {
            throw new Exception('Missing these node keys: '.implode(', ', $missingKeys));
        }
        $extraKeys = array_diff($nodeKeys, $expectedKeys);
        if (count($extraKeys) > 0) {
            throw new Exception('Unexpected node keys: '.implode(', ', $extraKeys));
        }
    }

    /**
     * @param array $symbols
     * @param string $isoPattern
     *
     * @throws Exception
     *
     * @return string[]
     */
    private function numberFormatToRegularExpressions(array $symbols, $isoPattern)
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
                        throw new Exception("Invalid chunk ('$intPattern') in pattern '$pattern'");
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
                                throw new Exception("Invalid chunk #$chunkIndex ('$chunk') in pattern '$pattern'");
                            }
                            break;
                        case '##':
                            if ($nextChunk === '##0') {
                                $rx .= '(('.preg_quote($symbols['group']).')?[0-9]{2})*';
                            } else {
                                throw new Exception("Invalid chunk #$chunkIndex ('$chunk') in pattern '$pattern'");
                            }
                            break;
                        case '##0':
                            if ($prevChunk === '##') {
                                $rx .= '[0-9]';
                            } elseif (($prevChunk === '#') || ($prevChunk === '-#')) {
                                $rx .= '(('.preg_quote($symbols['group']).')?[0-9]{3})*';
                            } else {
                                throw new Exception("Invalid chunk #$chunkIndex ('$chunk') in pattern '$pattern'");
                            }
                            break;
                        case '#0':
                            if ($chunkIndex === 0) {
                                $rx .= '[0-9]*';
                            } else {
                                throw new Exception("Invalid chunk #$chunkIndex ('$chunk') in pattern '$pattern'");
                            }
                            break;
                    }
                    $prevChunk = $chunk;
                }
            } else {
                throw new Exception("Invalid chunk ('$intPattern') in pattern '$pattern'");
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
                            throw new Exception("Invalid chunk ('$decimalPattern') in pattern '$pattern'");
                        }
                }
            }

            $result[$patternKey] = '/^'.$rxPre.$rx.$rxPost.'$/u';
        }

        return $result;
    }

    /**
     * @param string $destinationFile
     *
     * @throws Exception
     */
    private function copyMissingData_currency($destinationFile)
    {
        $sourceFile = $this->sourceData->getJsonDirectoryForLocale('en').'/currencies.json';
        $sourceData = $this->jsonFileHelper->read($sourceFile);
        $sourceData = CurrenciesLocalePunicConversion::create()->process($sourceData, 'en');
        $destinationData = $this->jsonFileHelper->read($destinationFile);
        $someChanged = false;
        foreach ($sourceData as $currency => $currencyInfo) {
            if (!array_key_exists($currency, $destinationData)) {
                $someChanged = true;
                $destinationData[$currency] = $currencyInfo;
            }
        }
        if ($someChanged) {
            $this->jsonFileHelper->save($destinationData, $destinationFile);
        }
    }
}

abstract class PunicConversion
{
    /**
     * @var string[]
     */
    protected $roots;

    /**
     * @var string|null
     */
    protected $saveAs;

    /**
     * @param string[] $roots
     * @param string|null $saveAs
     */
    public function __construct(array $roots, $saveAs = null)
    {
        $this->roots = $roots;
        $this->saveAs = $saveAs;
    }

    /**
     * @param string $fallbackTo
     *
     * @return string
     */
    public function getSaveAs($fallbackTo)
    {
        return $this->saveAs === null ? $fallbackTo : $this->saveAs;
    }

    /**
     * @param array $data
     *
     * @return $data
     */
    protected function simplify(array $data, array $roots, array $unsetByPath)
    {
        $path = '';
        foreach ($roots as $root) {
            if (!is_array($data)) {
                throw new Exception("Decoded data should be an array (path: $path)");
            }
            if (isset($unsetByPath[$path])) {
                foreach ($unsetByPath[$path] as $node) {
                    if (array_key_exists($node, $data)) {
                        unset($data[$node]);
                    }
                }
            }
            $this->checkExactKeys($data, array($root));
            $data = $data[$root];
            $path .= "/$root";
        }
        if (!is_array($data)) {
            throw new Exception("Decoded data should be an array (path: $path)");
        }

        return $data;
    }

    /**
     * @param array|mixed $node
     * @param string[] $expectedKeys
     *
     * @throws Exception
     */
    protected function checkExactKeys($node, array $expectedKeys)
    {
        if (!is_array($node)) {
            throw new Exception("$node is not an array");
        }
        $nodeKeys = array_keys($node);
        $missingKeys = array_diff($expectedKeys, $nodeKeys);
        if (count($missingKeys) > 0) {
            throw new Exception('Missing these node keys: '.implode(', ', $missingKeys));
        }
        $extraKeys = array_diff($nodeKeys, $expectedKeys);
        if (count($extraKeys) > 0) {
            throw new Exception('Unexpected node keys: '.implode(', ', $extraKeys));
        }
    }

    /**
     * @param string|mixed $fmt
     *
     * @return string
     */
    protected function toPhpSprintf($fmt)
    {
        $result = $fmt;
        if (is_string($fmt)) {
            $result = str_replace('%', '%%', $result);
            $result = preg_replace_callback(
                '/\\{(\\d+)\\}/',
                function ($matches) {
                    return '%'.(1 + (int) $matches[1]).'$s';
                },
                $fmt
            );
        }

        return $result;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    protected function asInt(&$value)
    {
        if (is_int($value)) {
            $result = true;
        } else {
            $result = false;
            if (is_string($value) || is_float($value)) {
                $v = @(int) $value;
                if ((string) $value === (string) $v) {
                    $value = $v;
                    $result = true;
                }
            }
        }

        return $result;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    protected function asNumber(&$value)
    {
        if (is_int($value) || is_float($value)) {
            $result = true;
        } else {
            $result = false;
            if (is_string($value)) {
                $v = @(int) $value;
                if ((string) $value !== (string) $v) {
                    $v = @(float) $value;
                }
                if ((string) $value === (string) $v) {
                    $value = $v;
                    $result = true;
                }
            }
        }

        return $result;
    }
}

abstract class LocalePunicConversion extends PunicConversion
{
    /**
     * @param string $localeID
     *
     * @return string[]
     */
    protected function getRoots($localeID)
    {
        return array_merge(
            array('main', str_replace('_', '-', $localeID)),
            $this->roots
        );
    }

    /**
     * @param string $localeID
     *
     * @return array
     */
    protected function getUnsetByPath($localeID)
    {
        return array(
            '/main/'.str_replace('_', '-', $localeID) => array('identity'),
        );

        return $result;
    }

    /**
     * @param array $data
     * @param string $localeID
     *
     * @return array
     */
    protected function process(array $data, $localeID)
    {
        $data = $this->simplify($data, $this->getRoots($localeID), $this->getUnsetByPath($localeID));

        return $data;
    }
}

class NoopLocalePunicConversion extends LocalePunicConversion
{
    /**
     * @return static
     */
    public static function create(array $roots, $saveAs = null)
    {
        return new static($roots, $saveAs);
    }

    /**
     * @param array $data
     * @param string $localeID
     *
     * @return array
     */
    public function process(array $data, $localeID)
    {
        return parent::process($data, $localeID);
    }
}

class CalendarLocalePunicConversion extends LocalePunicConversion
{
    /**
     * @return static
     */
    public static function create()
    {
        return new static(array('dates', 'calendars', 'gregorian'), 'calendar.json');
    }

    /**
     * @param array $data
     * @param string $localeID
     *
     * @return array
     */
    public function process(array $data, $localeID)
    {
        $data = parent::process($data, $localeID);
        unset($data['dateTimeFormats']['availableFormats']);
        unset($data['dateTimeFormats']['appendItems']);
        unset($data['dateTimeFormats']['intervalFormats']);
        foreach (array_keys($data['dateTimeFormats']) as $width) {
            $data['dateTimeFormats'][$width] = $this->toPhpSprintf($data['dateTimeFormats'][$width]);
        }
        foreach (array('eraNames' => 'wide', 'eraAbbr' => 'abbreviated', 'eraNarrow' => 'narrow') as $keyFrom => $keyTo) {
            if (array_key_exists($keyFrom, $data['eras'])) {
                $data['eras'][$keyTo] = $data['eras'][$keyFrom];
                unset($data['eras'][$keyFrom]);
            }
        }

        return $data;
    }
}
class TimeZoneNamesLocalePunicConversion extends LocalePunicConversion
{
    /**
     * @return static
     */
    public static function create()
    {
        return new static(array('dates', 'timeZoneNames'));
    }

    /**
     * @param array $data
     * @param string $localeID
     *
     * @return array
     */
    public function process(array $data, $localeID)
    {
        $data = parent::process($data, $localeID);
        foreach (array_keys($data) as $dataKey) {
            switch ($dataKey) {
                case 'gmtFormat':
                case 'gmtZeroFormat':
                case 'regionFormat':
                case 'regionFormat-type-standard':
                case 'regionFormat-type-daylight':
                case 'fallbackFormat':
                    $data[$dataKey] = $this->toPhpSprintf($data[$dataKey]);
                    break;
                case 'hourFormat':
                case 'zone':
                case 'metazone':
                    break;
                default:
                    throw new Exception("Unknown data key for time zone names: $dataKey");
            }
        }

        return $data;
    }
}

class ListPatternsLocalePunicConversion extends LocalePunicConversion
{
    /**
     * @return static
     */
    public static function create()
    {
        return new static(array('listPatterns'));
    }

    /**
     * @param array $data
     * @param string $localeID
     *
     * @return array
     */
    public function process(array $data, $localeID)
    {
        $data = parent::process($data, $localeID);
        $result = array();
        foreach ($data as $patternType => $patternData) {
            if (!preg_match('/^listPattern-type-(.+)$/', $patternType, $m)) {
                throw new Exception("Invalid list patterns node '$patternType'");
            }
            $patternName = $m[1];
            $result[$patternName] = array();
            foreach ($data[$patternType] as $when => $pattern) {
                $result[$patternName][$when] = $this->toPhpSprintf($pattern);
            }
        }

        return $result;
    }
}

class UnitsLocalePunicConversion extends LocalePunicConversion
{
    /**
     * @return static
     */
    public static function create()
    {
        return new static(array('units'));
    }

    /**
     * @param array $data
     * @param string $localeID
     *
     * @return array
     */
    public function process(array $data, $localeID)
    {
        $data = parent::process($data, $localeID);
        foreach (array_keys($data) as $width) {
            switch ($width) {
                case 'long':
                case 'short':
                case 'narrow':
                case 'long':
                    foreach (array_keys($data[$width]) as $unitKey) {
                        switch ($unitKey) {
                            case 'per':
                                $this->checkExactKeys($data[$width][$unitKey], array('compoundUnitPattern'));
                                $data[$width]['_compoundPattern'] = $this->toPhpSprintf($data[$width][$unitKey]['compoundUnitPattern']);
                                unset($data[$width][$unitKey]);
                                break;
                            case 'coordinateUnit':
                                $this->checkExactKeys($data[$width][$unitKey], array('east', 'north', 'south', 'west'));
                                $data[$width]['_coordinateUnit'] = array();
                                foreach (array_keys($data[$width][$unitKey]) as $direction) {
                                    $data[$width]['_coordinateUnit'][$direction] = $this->toPhpSprintf($data[$width][$unitKey][$direction]);
                                }
                                unset($data[$width][$unitKey]);
                                break;
                            default:
                                if (!preg_match('/^(\\w+)?-(.+)$/', $unitKey, $m)) {
                                    throw new Exception("Invalid node (2) '$width/$unitKey'");
                                }
                                $unitKind = $m[1];
                                $unitName = $m[2];
                                if (!array_key_exists($unitKind, $data[$width])) {
                                    $data[$width][$unitKind] = array();
                                }
                                if (!array_key_exists($unitName, $data[$width][$unitKind])) {
                                    $data[$width][$unitKind][$unitName] = array();
                                }
                                if (!isset($data[$width][$unitKey]['displayName'])) {
                                    throw new Exception("Missing unit name in '$width/$unitKey'");
                                }
                                if (!isset($data[$width][$unitKey]['unitPattern-count-other'])) {
                                    throw new Exception("Missing 'other' rule in '$width/$unitKey'");
                                }
                                foreach (array_keys($data[$width][$unitKey]) as $pluralRuleSrc) {
                                    switch ($pluralRuleSrc) {
                                        case 'displayName':
                                            $data[$width][$unitKind][$unitName]['_name'] = $data[$width][$unitKey][$pluralRuleSrc];
                                            break;
                                        case 'perUnitPattern':
                                            $data[$width][$unitKind][$unitName]['_per'] = $this->toPhpSprintf($data[$width][$unitKey][$pluralRuleSrc]);
                                            break;
                                        default:
                                            if (!preg_match('/^unitPattern-count-(.+)$/', $pluralRuleSrc, $m)) {
                                                throw new Exception("Invalid node (4) '$width/$unitKey/$pluralRuleSrc'");
                                            }
                                            $pluralRule = $m[1];
                                            $data[$width][$unitKind][$unitName][$pluralRule] = $this->toPhpSprintf($data[$width][$unitKey][$pluralRuleSrc]);
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
                        unset($data[$width]['durationUnitPattern-alt-variant']);
                        $this->checkExactKeys($data[$width], array('durationUnitPattern'));
                        $t = $m[1];
                        if (!array_key_exists('_durationPattern', $data)) {
                            $data['_durationPattern'] = array();
                        }
                        $data['_durationPattern'][$t] = $data[$width]['durationUnitPattern'];
                        unset($data[$width]);
                    } else {
                        throw new Exception("Invalid node (6) '$width'");
                    }
                    break;
            }
        }

        return $data;
    }
}

class LocaleDisplayNamesLocalePunicConversion extends LocalePunicConversion
{
    /**
     * @return static
     */
    public static function create()
    {
        return new static(array('localeDisplayNames'));
    }

    /**
     * @param array $data
     * @param string $localeID
     *
     * @return array
     */
    public function process(array $data, $localeID)
    {
        $data = parent::process($data, $localeID);
        if (!array_key_exists('localeDisplayPattern', $data)) {
            throw new Exception("Missing node 'localeDisplayPattern'");
        }
        foreach (array_keys($data['localeDisplayPattern']) as $k) {
            $data['localeDisplayPattern'][$k] = $this->toPhpSprintf($data['localeDisplayPattern'][$k]);
        }
        if (!array_key_exists('codePatterns', $data)) {
            throw new Exception("Missing node 'codePatterns'");
        }
        foreach (array_keys($data['codePatterns']) as $k) {
            $data['codePatterns'][$k] = $this->toPhpSprintf($data['codePatterns'][$k]);
        }

        return $data;
    }
}

class NumbersLocalePunicConversion extends LocalePunicConversion
{
    /**
     * @return static
     */
    public static function create()
    {
        return new static(array('numbers'));
    }

    /**
     * @param array $data
     * @param string $localeID
     *
     * @return array
     */
    public function process(array $data, $localeID)
    {
        $data = parent::process($data, $localeID);
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
                            $unitPattern[$m[1]] = $this->toPhpSprintf($v2);
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
                    case 'minimalPairs':
                        break;
                    case 'minimumGroupingDigits':
                        if (!$this->asInt($value)) {
                            throw new Exception("Invalid node '$key'");
                        }
                        $final[$key] = $value;
                        break;
                    default:
                        throw new Exception("Invalid node '$key'");
                }
            }
        }
        if (!array_key_exists('latn', $numberSystems)) {
            throw new Exception("Missing 'latn'");
        }
        foreach ($numberSystems['latn'] as $key => $value) {
            if (array_key_exists($key, $final)) {
                throw new Exception("Duplicated node '$key'");
            }
            // $final[$key] = $value; REMOVED ADVANCED LOCALIZATION
            if ($key === 'symbols') { // REMOVED ADVANCED LOCALIZATION
                $final[$key] = $value;
            }
        }
        $data = $final;
        $symbols = array_key_exists('symbols', $data) ? $data['symbols'] : null;
        if (empty($symbols) || (!is_array($symbols))) {
            throw new Exception('Missing symbols');
        }
        foreach (array_keys($data) as $key) {
            if (is_array($data[$key]) && preg_match('/\\w+Formats$/', $key) && array_key_exists('standard', $data[$key])) {
                $format = $data[$key]['standard'];
                $data[$key]['standard'] = array('format' => $format);
                foreach ($this->numberFormatToRegularExpressions($symbols, $format) as $rxKey => $rx) {
                    $data[$key]['standard']["rx$rxKey"] = $rx;
                }
            }
        }

        return $data;
    }
}

class CurrenciesLocalePunicConversion extends LocalePunicConversion
{
    /**
     * @return static
     */
    public static function create()
    {
        return new static(array('numbers', 'currencies'));
    }

    /**
     * @param array $data
     * @param string $localeID
     *
     * @return array
     */
    public function process(array $data, $localeID)
    {
        $data = parent::process($data, $localeID);
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

        return $data;
    }
}

abstract class SupplementalPunicConversion extends PunicConversion
{
    /**
     * @return string[]
     */
    protected function getRoots()
    {
        return $this->roots;
    }

    /**
     * @return array
     */
    protected function getUnsetByPath()
    {
        return array(
            '/supplemental' => array('version', 'generation'),
        );
    }

    /**
     * @param array $data
     */
    protected function process(array $data)
    {
        $data = $this->simplify($data, $this->getRoots(), $this->getUnsetByPath());

        return $data;
    }
}

class NoopSupplementalPunicConversion extends SupplementalPunicConversion
{
    /**
     * @return static
     */
    public static function create(array $roots, $saveAs = null)
    {
        return new static($roots, $saveAs);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function process(array $data)
    {
        return parent::process($data);
    }
}

class TelephoneCodeDataSupplementalPunicConversion extends SupplementalPunicConversion
{
    /**
     * @return static
     */
    public static function create()
    {
        return new static(array('supplemental', 'telephoneCodeData'));
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function process(array $data)
    {
        $data = parent::process($data);
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

        return $data;
    }
}

class TerritoryInfoSupplementalPunicConversion extends SupplementalPunicConversion
{
    /**
     * @return static
     */
    public static function create()
    {
        return new static(array('supplemental', 'territoryInfo'));
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function process(array $data)
    {
        $data = parent::process($data);
        //http://www.unicode.org/reports/tr35/tr35-info.html#Supplemental_Territory_Information
        unset($data['ZZ']);
        foreach ($data as $territoryID => $territoryInfoList) {
            $finalTerritoryData = array();
            foreach ($territoryInfoList as $territoryInfoID => $territoryInfoData) {
                switch ($territoryInfoID) {
                    case '_gdp': // Gross domestic product
                        if (!$this->asNumber($territoryInfoData)) {
                            throw new Exception("Unable to parse $territoryInfoData as a number ($territoryInfoID)");
                        }
                        $finalTerritoryData['gdp'] = $territoryInfoData;
                        break;
                    case '_literacyPercent':
                        if (!$this->asNumber($territoryInfoData)) {
                            throw new Exception("Unable to parse $territoryInfoData as a number ($territoryInfoID)");
                        }
                        $finalTerritoryData['literacy'] = $territoryInfoData;
                        break;
                    case '_population':
                        if (!$this->asNumber($territoryInfoData)) {
                            throw new Exception("Unable to parse $territoryInfoData as a number ($territoryInfoID)");
                        }
                        $finalTerritoryData['population'] = $territoryInfoData;
                        break;
                    case 'languagePopulation':
                        if (!is_array($territoryInfoData)) {
                            throw new Exception("Invalid node: $infoIDis not an array");
                        }
                        $finalTerritoryData['languages'] = array();
                        foreach ($territoryInfoData as $languageID => $languageInfoList) {
                            if (!is_array($languageInfoList)) {
                                throw new Exception("Invalid node: $territoryInfoID/$languageID is not an array");
                            }
                            $finalTerritoryData['languages'][$languageID] = array();
                            foreach ($languageInfoList as $languageInfoID => $languageInfoData) {
                                switch ($languageInfoID) {
                                    case '_officialStatus':
                                        switch ($languageInfoData) {
                                            case 'official':
                                                $v = 'o';
                                                break;
                                            case 'official_regional':
                                                $v = 'r';
                                                break;
                                            case 'de_facto_official':
                                                $v = 'f';
                                                break;
                                            case 'official_minority':
                                                $v = 'm';
                                                break;
                                            default:
                                                throw new Exception("Unknown language status: $languageInfoData");
                                        }
                                        $finalTerritoryData['languages'][$languageID]['status'] = $v;
                                        break;
                                    case '_populationPercent':
                                        if (!$this->asNumber($languageInfoData)) {
                                            throw new Exception("Unable to parse $languageInfoData as a number ($territoryInfoID)");
                                        }
                                        $finalTerritoryData['languages'][$languageID]['population'] = $languageInfoData;
                                        break;
                                    case '_writingPercent':
                                        if (!$this->asNumber($languageInfoData)) {
                                            throw new Exception("Unable to parse $languageInfoData as a number ($territoryInfoID)");
                                        }
                                        $finalTerritoryData['languages'][$languageID]['writing'] = $languageInfoData;
                                        break;
                                    case '_literacyPercent':
                                        if (!$this->asNumber($languageInfoData)) {
                                            throw new Exception("Unable to parse $languageInfoData as a number ($territoryInfoID)");
                                        }
                                        $finalTerritoryData['languages'][$languageID]['literacy'] = $languageInfoData;
                                        break;
                                    default:
                                        throw new Exception("Unknown node: $territoryInfoID/$languageID/$languageInfoID");
                                }
                            }
                            if (!array_key_exists('population', $finalTerritoryData['languages'][$languageID])) {
                                throw new Exception("Missing _populationPercent node in for $territoryID/$territoryInfoID/$languageID");
                            }
                        }
                        if (empty($finalTerritoryData['languages'])) {
                            throw new Exception("No languages for $territoryID");
                        }
                        break;
                    default:
                        throw new Exception("Unknown node: $territoryInfoID");
                }
            }
            if (!array_key_exists('gdp', $finalTerritoryData)) {
                throw new Exception("Missing _gdp node in for $territoryID");
            }
            if (!array_key_exists('literacy', $finalTerritoryData)) {
                throw new Exception("Missing _literacyPercent node in for $territoryID");
            }
            if (!array_key_exists('population', $finalTerritoryData)) {
                throw new Exception("Missing _population node in for $territoryID");
            }
            if (!array_key_exists('languages', $finalTerritoryData)) {
                throw new Exception("Missing languagePopulation node in for $territoryID");
            }
            $data[$territoryID] = $finalTerritoryData;
        }

        return $data;
    }
}

class WeekDataSupplementalPunicConversion extends SupplementalPunicConversion
{
    /**
     * @return static
     */
    public static function create()
    {
        return new static(array('supplemental', 'weekData'));
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function process(array $data)
    {
        $data = parent::process($data);
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

        return $data;
    }
}

class TerritoryContainmentSupplementalPunicConversion extends SupplementalPunicConversion
{
    /**
     * @return static
     */
    public static function create()
    {
        return new static(array('supplemental', 'territoryContainment'));
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function process(array $data)
    {
        $data = parent::process($data);
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

        return $data;
    }
}

class MetaZonesSupplementalPunicConversion extends SupplementalPunicConversion
{
    /**
     * @return static
     */
    public static function create()
    {
        return new static(array('supplemental', 'metaZones'));
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function process(array $data)
    {
        $data = parent::process($data);
        $this->checkExactKeys($data['metazoneInfo'], array('timezone'));
        $data['metazoneInfo'] = $data['metazoneInfo']['timezone'];
        foreach ($data['metazoneInfo'] as $id0 => $info0) {
            foreach ($info0 as $id1 => $info1) {
                if (is_int($id1)) {
                    $info1 = $this->fixMetazoneInfo($info1);
                } else {
                    foreach ($info1 as $id2 => $info2) {
                        if (is_int($id2)) {
                            $info2 = $this->fixMetazoneInfo($info2);
                        } else {
                            foreach ($info2 as $id3 => $info3) {
                                if (is_int($id3)) {
                                    $info3 = $this->fixMetazoneInfo($info3);
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
            $this->checkExactKeys($mz, array('mapZone'));
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

        return $data;
    }

    /**
     * @param array|mixed $a
     *
     * @throws Exception
     *
     * @return array
     */
    private function fixMetazoneInfo($a)
    {
        $this->checkExactKeys($a, array('usesMetazone'));
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
}

class PluralsSupplementalPunicConversion extends SupplementalPunicConversion
{
    /**
     * @return static
     */
    public static function create()
    {
        return new static(array('supplemental', 'plurals-type-cardinal'));
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function realProcess(array $data)
    {
        $data = parent::process($data);
        $testData = array();
        foreach ($data as $l => $lData) {
            $testData[$l] = array();
            $keys = array_keys($lData);
            foreach ($keys as $key) {
                if (!preg_match('/^pluralRule-count-(.+)$/', $key, $m)) {
                    throw new Exception("Invalid node '$key'");
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
                                    throw new Exception("Invalid node '$key': $vOriginal");
                                }
                            }
                            $testData[$l][$rule] = $exampleValuesParsed;
                            break;
                        default:
                            throw new Exception("Invalid node '$key': $vOriginal");
                    }
                }
                if ($rule === 'other') {
                    if (strlen($v) > 0) {
                        throw new Exception("Invalid node '$key': $vOriginal");
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
                        throw new Exception("Invalid node '$key': $vOriginal");
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

        return array($data, $testData);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function process(array $data)
    {
        $dt = $this->realProcess($data);

        return $dt[0];
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function getTestData(array $data)
    {
        $dt = $this->realProcess($data);

        return $dt[1];
    }
}

class MeasurementDataSupplementalPunicConversion extends SupplementalPunicConversion
{
    /**
     * @return static
     */
    public static function create()
    {
        return new static(array('supplemental', 'measurementData'));
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function process(array $data)
    {
        $data = parent::process($data);
        if (!(array_key_exists('measurementSystem', $data) && is_array($data['measurementSystem']))) {
            throw new Exception('Missing/invalid key: measurementSystem');
        }
        if (!(array_key_exists('paperSize', $data) && is_array($data['paperSize']))) {
            throw new Exception('Missing/invalid key: paperSize');
        }

        return $data;
    }
}

class CurrencyDataSupplementalPunicConversion extends SupplementalPunicConversion
{
    /**
     * @return static
     */
    public static function create()
    {
        return new static(array('supplemental', 'currencyData'));
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function process(array $data)
    {
        $data = parent::process($data);
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

        return $data;
    }
}
