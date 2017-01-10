<?php

/*
 * This file is part of QA Tools.
 * Largely based on the Composer installer.
 */

installQaTools(is_array($argv) ? $argv : []);

/**
 * processes the installer
 */
function installQaTools($argv)
{
    // Determine ANSI output from --ansi and --no-ansi flags
    setUseAnsi($argv);

    if (in_array('--help', $argv)) {
        displayHelp();
        exit(0);
    }

    $check = in_array('--check', $argv);
    $force = in_array('--force', $argv);
    $quiet = in_array('--quiet', $argv);
    $installDir = getOptValue('--install-dir', $argv, false);
    $version = getOptValue('--version', $argv, false);
    $filename = getOptValue('--filename', $argv, 'qa-tools');
    $cafile = getOptValue('--cafile', $argv, false);

    if (!checkParams($installDir, $version, $cafile)) {
        exit(1);
    }

    $ok = checkPlatform($warnings, $quiet, true);

    if ($check) {
        // Only show warnings if we haven't output any errors
        if ($ok) {
            showWarnings($warnings);
        }
        exit($ok ? 0 : 1);
    }

    if ($ok || $force) {
        $installer = new Installer($quiet, new HttpClient($cafile), 'ibuildingsnl', 'qa-tools-v3');
        if ($installer->run($version, $installDir, $filename)) {
            showWarnings($warnings);
            exit(0);
        }
    }

    exit(1);
}

/**
 * displays the help
 */
function displayHelp()
{
    echo <<<EOF
QA Tools Installer
------------------
Options
--help               this help
--check              for checking environment only
--force              forces the installation
--ansi               force ANSI color output
--no-ansi            disable ANSI color output
--quiet              do not output unimportant messages
--install-dir="..."  accepts a target installation directory
--version="..."      accepts a specific version to install instead of the latest
--filename="..."     accepts a target filename (default: composer.phar)
--cafile="..."       accepts a path to a Certificate Authority (CA) certificate file for SSL/TLS verification
EOF;
}

/**
 * Sets the USE_ANSI define for colorizing output
 *
 * @param array $argv Command-line arguments
 */
function setUseAnsi($argv)
{
    // --no-ansi wins over --ansi
    if (in_array('--no-ansi', $argv)) {
        define('USE_ANSI', false);
    } elseif (in_array('--ansi', $argv)) {
        define('USE_ANSI', true);
    } else {
        // On Windows, default to no ANSI, except in ANSICON and ConEmu.
        // Everywhere else, default to ANSI if stdout is a terminal.
        define(
            'USE_ANSI',
            (DIRECTORY_SEPARATOR == '\\')
                ? (false !== getenv('ANSICON') || 'ON' === getenv('ConEmuANSI'))
                : (function_exists('posix_isatty') && posix_isatty(1))
        );
    }
}

/**
 * Returns the value of a command-line option
 *
 * @param string $opt The command-line option to check
 * @param array  $argv Command-line arguments
 * @param mixed  $default Default value to be returned
 *
 * @return mixed The command-line value or the default
 */
function getOptValue($opt, $argv, $default)
{
    $optLength = strlen($opt);

    foreach ($argv as $key => $value) {
        $next = $key + 1;
        if (0 === strpos($value, $opt)) {
            if ($optLength === strlen($value) && isset($argv[$next])) {
                return trim($argv[$next]);
            } else {
                return trim(substr($value, $optLength + 1));
            }
        }
    }

    return $default;
}

/**
 * Checks that user-supplied params are valid
 *
 * @param mixed $installDir The required istallation directory
 * @param mixed $version The required composer version to install
 * @param mixed $cafile Certificate Authority file
 *
 * @return bool True if the supplied params are okay
 */
function checkParams($installDir, $version, $cafile)
{
    $result = true;

    if (false !== $installDir && !is_dir($installDir)) {
        out("The defined install dir ({$installDir}) does not exist.", 'info');
        $result = false;
    }

    if (false !== $version && 1 !== preg_match('/^\d+\.\d+\.\d+(\-(alpha|beta|RC)\d*)*$/', $version)) {
        out("The defined install version ({$version}) does not match release pattern.", 'info');
        $result = false;
    }

    if (false !== $cafile && (!file_exists($cafile) || !is_readable($cafile))) {
        out("The defined Certificate Authority (CA) cert file ({$cafile}) does not exist or is not readable.", 'info');
        $result = false;
    }

    return $result;
}

/**
 * Checks the platform for possible issues running QA Tools
 *
 * Errors are written to the output, warnings are saved for later display.
 *
 * @param array $warnings Populated by method, to be shown later
 * @param bool  $quiet Quiet mode
 * @param bool  $install If we are installing, rather than diagnosing
 *
 * @return bool True if there are no errors
 */
function checkPlatform(&$warnings, $quiet, $install)
{
    getPlatformIssues($errors, $warnings, $install);

    if (!empty($errors)) {
        out('Some settings on your machine make it impossible for QA Tools to work properly.', 'error');
        out('Make sure that you fix the issues listed below and run this script again:', 'error');
        outputIssues($errors);

        return false;
    }

    if (empty($warnings) && !$quiet) {
        out('All settings correct for using QA Tools', 'success');
    }

    return true;
}

/**
 * Checks platform configuration for common incompatibility issues
 *
 * @param array $errors Populated by method
 * @param array $warnings Populated by method
 * @param bool  $install If we are installing, rather than diagnosing
 *
 * @return bool If any errors or warnings have been found
 */
function getPlatformIssues(&$errors, &$warnings)
{
    $errors = [];
    $warnings = [];

    if ($iniPath = php_ini_loaded_file()) {
        $iniMessage = PHP_EOL . 'The php.ini used by your command-line PHP is: ' . $iniPath;
    } else {
        $iniMessage = PHP_EOL . 'A php.ini file does not exist. You will have to create one.';
    }
    $iniMessage .= PHP_EOL . 'If you can not modify the ini file, you can also run `php -d option=value` to modify ini values on the fly. You can use -d multiple times.';

    if (extension_loaded('suhosin')) {
        $suhosin = ini_get('suhosin.executor.include.whitelist');
        $suhosinBlacklist = ini_get('suhosin.executor.include.blacklist');
        if (false === stripos($suhosin, 'phar') && (!$suhosinBlacklist || false !== stripos($suhosinBlacklist,
                    'phar'))
        ) {
            $errors['suhosin'] = [
                'The suhosin.executor.include.whitelist setting is incorrect.',
                'Add the following to the end of your `php.ini` or suhosin.ini (Example path [for Debian]: /etc/php5/cli/conf.d/suhosin.ini):',
                '    suhosin.executor.include.whitelist = phar ' . $suhosin,
                $iniMessage,
            ];
        }
    }

    if (!function_exists('json_decode')) {
        $errors['json'] = [
            'The json extension is missing.',
            'Install it or recompile php without --disable-json',
        ];
    }

    if (!extension_loaded('Phar')) {
        $errors['phar'] = [
            'The phar extension is missing.',
            'Install it or recompile php without --disable-phar',
        ];
    }

    if (!extension_loaded('hash')) {
        $errors['hash'] = [
            'The hash extension is missing.',
            'Install it or recompile php without --disable-hash',
        ];
    }

    if (!extension_loaded('pcntl')) {
        $errors['pcntl'] = [
            'The pcntl extension is missing.',
        ];
    }

    if (!ini_get('allow_url_fopen')) {
        $errors['allow_url_fopen'] = [
            'The allow_url_fopen setting is incorrect.',
            'Add the following to the end of your `php.ini`:',
            '    allow_url_fopen = On',
            $iniMessage,
        ];
    }

    if (extension_loaded('ionCube Loader') && ioncube_loader_iversion() < 40009) {
        $ioncube = ioncube_loader_version();
        $errors['ioncube'] = [
            'Your ionCube Loader extension (' . $ioncube . ') is incompatible with Phar files.',
            'Upgrade to ionCube 4.0.9 or higher or remove this line (path may be different) from your `php.ini` to disable it:',
            '    zend_extension = /usr/lib/php5/20090626+lfs/ioncube_loader_lin_5.3.so',
            $iniMessage,
        ];
    }

    if (version_compare(PHP_VERSION, '5.6.0', '<')) {
        $errors['php'] = [
            'Your PHP (' . PHP_VERSION . ') is too old, you must upgrade to PHP 5.6.0 or higher.',
        ];
    }

    if (!extension_loaded('openssl')) {
        $errors['openssl'] = [
            'The openssl extension is missing, which means that secure HTTPS transfers are impossible.',
            'If possible you should enable it or recompile php with --with-openssl',
        ];
    }

    if (extension_loaded('openssl') && OPENSSL_VERSION_NUMBER < 0x1000100f) {
        // Attempt to parse version number out, fallback to whole string value.
        $opensslVersion = trim(strstr(OPENSSL_VERSION_TEXT, ' '));
        $opensslVersion = substr($opensslVersion, 0, strpos($opensslVersion, ' '));
        $opensslVersion = $opensslVersion ? $opensslVersion : OPENSSL_VERSION_TEXT;

        $warnings['openssl_version'] = [
            'The OpenSSL library (' . $opensslVersion . ') used by PHP does not support TLSv1.2 or TLSv1.1.',
            'If possible you should upgrade OpenSSL to version 1.0.1 or above.',
        ];
    }

    if (!defined('HHVM_VERSION') && !extension_loaded('apcu') && ini_get('apc.enable_cli')) {
        $warnings['apc_cli'] = [
            'The apc.enable_cli setting is incorrect.',
            'Add the following to the end of your `php.ini`:',
            '    apc.enable_cli = Off',
            $iniMessage,
        ];
    }

    ob_start();
    phpinfo(INFO_GENERAL);
    $phpinfo = ob_get_clean();
    if (preg_match('{Configure Command(?: *</td><td class="v">| *=> *)(.*?)(?:</td>|$)}m', $phpinfo, $match)) {
        $configure = $match[1];

        if (false !== strpos($configure, '--enable-sigchild')) {
            $warnings['sigchild'] = [
                'PHP was compiled with --enable-sigchild which can cause issues on some platforms.',
                'Recompile it without this flag if possible, see also:',
                '    https://bugs.php.net/bug.php?id=22999',
            ];
        }

        if (false !== strpos($configure, '--with-curlwrappers')) {
            $warnings['curlwrappers'] = [
                'PHP was compiled with --with-curlwrappers which will cause issues with HTTP authentication and GitHub.',
                'Recompile it without this flag if possible',
            ];
        }
    }

    // Stringify the message arrays
    foreach ($errors as $key => $value) {
        $errors[$key] = PHP_EOL . implode(PHP_EOL, $value);
    }

    foreach ($warnings as $key => $value) {
        $warnings[$key] = PHP_EOL . implode(PHP_EOL, $value);
    }

    return !empty($errors) || !empty($warnings);
}

/**
 * Outputs an array of issues
 *
 * @param array $issues
 */
function outputIssues($issues)
{
    foreach ($issues as $issue) {
        out($issue, 'info');
    }
    out('');
}

/**
 * Outputs any warnings found
 *
 * @param array $warnings
 */
function showWarnings($warnings)
{
    if (!empty($warnings)) {
        out('Some settings on your machine may cause stability issues with QA Tools.', 'error');
        out('If you encounter issues, try to change the following:', 'error');
        outputIssues($warnings);
    }
}

/**
 * colorize output
 */
function out($text, $color = null, $newLine = true)
{
    $styles = [
        'success' => "\033[0;32m%s\033[0m",
        'error' => "\033[31;31m%s\033[0m",
        'info' => "\033[33;33m%s\033[0m",
    ];

    $format = '%s';

    if (isset($styles[$color]) && USE_ANSI) {
        $format = $styles[$color];
    }

    if ($newLine) {
        $format .= PHP_EOL;
    }

    printf($format, $text);
}

function validateCaFile($contents)
{
    return (bool)openssl_x509_parse($contents);
}

class Installer
{
    /** @var bool $quiet */
    private $quiet;

    /** @var string $installPath */
    private $installPath;

    /** @var string $target */
    private $target;

    /** @var string $tmpPharPath */
    private $tmpPharPath;

    /** @var string $tmpPubkeyPath */
    private $tmpPubkeyPath;

    /** @var ErrorHandler $errHandler */
    private $errHandler;

    /** @var HttpClient $httpClient */
    private $httpClient;

    /** @var string $repositoryOwner */
    private $repositoryOwner;

    /** @var string $repositoryName */
    private $repositoryName;

    /**
     * @param bool   $quiet Quiet mode
     * @param mixed  $caFile Path to CA bundle, or false
     * @param string $repositoryOwner Owner of repository to download from
     * @param string $repositoryName Name of repository to download release from
     */
    public function __construct($quiet, HttpClient $httpClient, $repositoryOwner, $repositoryName)
    {
        if (($this->quiet = $quiet)) {
            ob_start();
        }
        $this->httpClient = $httpClient;
        $this->errHandler = new ErrorHandler();
        $this->repositoryOwner = $repositoryOwner;
        $this->repositoryName = $repositoryName;
    }

    /**
     * Runs the installer
     *
     * @param mixed  $version Specific version to install, or false
     * @param mixed  $installDir Specific installation directory, or false
     * @param string $filename Specific filename to save to, or composer.phar
     * @throws Exception If anything other than a RuntimeException is caught
     *
     * @return bool If the installation succeeded
     */
    public function run($version, $installDir, $filename)
    {
        try {
            $this->initTargets($installDir, $filename);
            $result = $this->install($version);
        } catch (Exception $e) {
            $result = false;
            if (!$e instanceof RuntimeException) {
                throw $e;
            }
            out($e->getMessage(), 'error');
        } finally {
            $this->cleanUp($result);
        }

        return $result;
    }

    /**
     * Initialization methods to set the required filenames and base url
     *
     * @param mixed  $installDir Specific installation directory, or false
     * @param string $filename Specific filename to save to
     * @throws RuntimeException If the installation directory is not writable
     */
    protected function initTargets($installDir, $filename)
    {
        $this->installPath = (is_dir($installDir) ? rtrim($installDir, '/') . '/' : '') . $filename;
        $installDir = realpath($installDir) ? realpath($installDir) : getcwd();

        if (!is_writable($installDir)) {
            throw new RuntimeException('The installation directory "' . $installDir . '" is not writable');
        }

        $this->target = $installDir . DIRECTORY_SEPARATOR . $filename;
        $this->tmpPharPath = $installDir . DIRECTORY_SEPARATOR . basename($this->target, '.phar') . '-temp.phar';
        $this->tmpPubkeyPath = $this->tmpPharPath . '.pubkey';
    }

    /**
     * The main install function
     *
     * @param mixed  $version Specific version to install, or false
     * @param string $channel Version channel to use
     *
     * @return bool If the installation succeeded
     */
    protected function install($version)
    {
        $retries = 3;
        $infoMsg = 'Downloading...';
        $infoType = 'info';
        $getDownloadUrl = function ($url) {
            if (getenv('GITHUB_TOKEN') !== false) {
                $url .= '?access_token='.getenv('GITHUB_TOKEN');
            }
            return $url;
        };

        $success = false;
        while ($retries--) {
            try {
                if (!$this->quiet) {
                    out($infoMsg, $infoType);
                    $infoMsg = 'Retrying...';
                    $infoType = 'error';
                }

                $releaseInfo = $this->getLatestReleaseInfo($version);
                $version = $releaseInfo['tag_name'];

                if (count($releaseInfo['assets']) < 2) {
                    throw new RuntimeException(
                        sprintf('Expected at least 2 assets, found %d', count($releaseInfo['assets']))
                    );
                }

                $pharUrl = $pubkeyUrl = false;
                foreach ($releaseInfo['assets'] as $asset) {
                    if ($asset['name'] === 'qa-tools.phar') {
                        $pharUrl = $getDownloadUrl($asset['url']);
                    }
                    if ($asset['name'] === 'qa-tools.phar.pubkey') {
                        $pubkeyUrl = $getDownloadUrl($asset['url']);
                    }
                }

                if ($pharUrl === false) {
                    throw new RuntimeException('Unable to find qa-tools.phar in release');
                }
                if ($pubkeyUrl === false) {
                    throw new RuntimeException('Unable to find qa-tools.phar.pubkey in release');
                }

                $this->downloadTemporaryFile($pharUrl, $this->tmpPharPath);
                $this->downloadTemporaryFile($pubkeyUrl, $this->tmpPubkeyPath);

                $this->verifyAndSave();

                $success = true;
                break;
            } catch (RuntimeException $e) {
                out($e->getMessage(), 'error');
            }
        }

        if (!$this->quiet) {
            if ($success) {
                out(PHP_EOL . "QA Tools (version {$version}) successfully installed to: {$this->target}", 'success');
                out("Use it: php {$this->installPath}", 'info');
                out('');
            } else {
                out('The download failed repeatedly, aborting.', 'error');
            }
        }

        return true;
    }

    /**
     * @return string The version of the latest QA tools release
     * @throws \RuntimeException
     */
    public function getLatestReleaseInfo($version)
    {
        if (!$version) {
            $version = 'latest';
        }

        $url = sprintf(
            'https://api.github.com/repos/%s/%s/releases/%s',
            urlencode($this->repositoryOwner),
            urlencode($this->repositoryName),
            $version
        );

        try {
            $response = $this->httpClient->get($url, 'application/vnd.github.v3+json');
        } catch (Exception $e) {
            throw new RuntimeException('Unable to download version information from '.$url.': '.$e->getMessage());
        }

        $releaseInfo = json_decode($response, true);
        if (json_last_error()) {
            throw new RuntimeException(
                'Invalid response from GitHub when requesting version information (' . json_last_error_msg() . ')'
            );
        }

        return $releaseInfo;
    }

    /**
     * A wrapper around the methods needed to download and save the phar
     *
     * @param string      $url The versioned download url
     * @param string      $target The target location to download to
     *
     * @return bool If the operation succeeded
     * @throws \RuntimeException
     */
    protected function downloadTemporaryFile($url, $target)
    {
        $this->errHandler->start();

        try {
            if (!$fh = fopen($target, 'w')) {
                throw new RuntimeException(
                    sprintf(
                        'Could not create file "%s": %s',
                        $target,
                        $this->errHandler->message
                    )
                );
            }

            if (!fwrite($fh, $this->httpClient->get($url, 'application/octet-stream'))) {
                throw new RuntimeException(
                    sprintf(
                        'The "%s" file could not be downloaded: %s',
                        $url,
                        $this->errHandler->message
                    )
                );
            }
        } finally {
            $this->errHandler->stop();
            if (is_resource($fh)) {
                fclose($fh);
            }
        }
    }

    /**
     * Verifies the downloaded file and saves it to the target location
     *
     * @return bool If the operation succeeded
     * @throws \RuntimeException
     */
    protected function verifyAndSave()
    {
        $this->assertPharValid();

        $this->errHandler->start();

        try {
            if (!rename($this->tmpPharPath, $this->target)) {
                throw new RuntimeException(
                    sprintf(
                        'Could not write to file "%s": %s',
                        $this->target,
                        $this->errHandler->message
                    )
                );
            }
            if (!rename($this->tmpPubkeyPath, $this->target . '.pubkey')) {
                throw new RuntimeException(
                    sprintf(
                        'Could not write to file "%s": %s',
                        $this->target,
                        $this->errHandler->message
                    )
                );
            }

            chmod($this->target, 0755);
        } finally {
            $this->errHandler->stop();
        }
    }

    /**
     * Validates the downloaded phar file
     *
     * @return bool If the operation succeeded
     * @throws \RuntimeException
     */
    protected function assertPharValid()
    {
        try {
            // Test the phar validity - simply opening the PHAR will actually
            // check the against the <pharname>.pubkey file
            $phar = new Phar($this->tmpPharPath);
            $signature = $phar->getSignature();
            if (strtolower($signature['hash_type']) !== 'openssl') {
                throw new RuntimeException('Downloaded PHAR was not signed!');
            }
            // Free the variable to unlock the file
            unset($phar);
        } catch (Exception $e) {
            throw new RuntimeException(
                sprintf(
                    'Unable to open PHAR file: %s',
                    $e->getMessage()
                ),
                0,
                $e
            );
        }
    }

    /**
     * Cleans up resources at the end of the installation
     *
     * @param bool $result If the installation succeeded
     */
    protected function cleanUp($result)
    {
        if (!$result) {
            // Output buffered errors
            if ($this->quiet) {
                $this->outputErrors();
            }
            // Clean up stuff we created
            $this->uninstall();
        }
    }

    /**
     * Outputs unique errors
     */
    protected function outputErrors()
    {
        $errors = explode(PHP_EOL, ob_get_clean());
        $shown = [];

        foreach ($errors as $error) {
            if ($error && !in_array($error, $shown)) {
                out($error, 'error');
                $shown[] = $error;
            }
        }
    }

    /**
     * Uninstalls newly-created files and directories on failure
     */
    protected function uninstall()
    {
        if (file_exists($this->tmpPharPath)) {
            @unlink($this->tmpPharPath);
        }
        if (file_exists($this->tmpPubkeyPath)) {
            @unlink($this->tmpPharPath);
        }
    }
}

class ErrorHandler
{
    public $message;
    protected $active;

    /**
     * Handle php errors
     *
     * @param mixed $code The error code
     * @param mixed $msg The error message
     */
    public function handleError($code, $msg)
    {
        if ($this->message) {
            $this->message .= PHP_EOL;
        }
        $this->message .= preg_replace('{^file_get_contents\(.*?\): }', '', $msg);
    }

    /**
     * Starts error-handling if not already active
     *
     * Any message is cleared
     */
    public function start()
    {
        if (!$this->active) {
            set_error_handler([$this, 'handleError']);
            $this->active = true;
        }
        $this->message = '';
    }

    /**
     * Stops error-handling if active
     *
     * Any message is preserved until the next call to start()
     */
    public function stop()
    {
        if ($this->active) {
            restore_error_handler();
            $this->active = false;
        }
    }
}

class HttpClient
{
    /** @var array $options */
    private $options = ['http' => []];

    public function __construct($cafile = false)
    {
        if (!empty($cafile) && !is_dir($cafile)) {
            if (!is_readable($cafile) || !validateCaFile(file_get_contents($cafile))) {
                throw new RuntimeException('The configured cafile (' . $cafile . ') was not valid or could not be read.');
            }
        }
        $options = $this->getTlsStreamContextDefaults($cafile);
        $this->options = array_replace_recursive($this->options, $options);
    }

    public function get($url, $acceptMimeType = 'application/octet-stream')
    {
        $context = $this->getMergedStreamContext($url, $acceptMimeType);
        $result = @file_get_contents($url, false, $context);

        if ($result === false) {
            throw new RuntimeException('Unable to read '.$url.': '.@error_get_last()['message']);
        }

        if ($result && extension_loaded('zlib')) {
            $decode = false;
            foreach ($http_response_header as $header) {
                if (preg_match('{^content-encoding: *gzip *$}i', $header)) {
                    $decode = true;
                    continue;
                } elseif (preg_match('{^HTTP/}i', $header)) {
                    $decode = false;
                }
            }

            if ($decode) {
                $result = zlib_decode($result);

                if ($result === false) {
                    throw new RuntimeException('Failed to decode zlib stream');
                }
            }
        }

        return $result;
    }

    protected function getTlsStreamContextDefaults($cafile)
    {
        $ciphers = implode(':', [
            'ECDHE-RSA-AES128-GCM-SHA256',
            'ECDHE-ECDSA-AES128-GCM-SHA256',
            'ECDHE-RSA-AES256-GCM-SHA384',
            'ECDHE-ECDSA-AES256-GCM-SHA384',
            'DHE-RSA-AES128-GCM-SHA256',
            'DHE-DSS-AES128-GCM-SHA256',
            'kEDH+AESGCM',
            'ECDHE-RSA-AES128-SHA256',
            'ECDHE-ECDSA-AES128-SHA256',
            'ECDHE-RSA-AES128-SHA',
            'ECDHE-ECDSA-AES128-SHA',
            'ECDHE-RSA-AES256-SHA384',
            'ECDHE-ECDSA-AES256-SHA384',
            'ECDHE-RSA-AES256-SHA',
            'ECDHE-ECDSA-AES256-SHA',
            'DHE-RSA-AES128-SHA256',
            'DHE-RSA-AES128-SHA',
            'DHE-DSS-AES128-SHA256',
            'DHE-RSA-AES256-SHA256',
            'DHE-DSS-AES256-SHA',
            'DHE-RSA-AES256-SHA',
            'AES128-GCM-SHA256',
            'AES256-GCM-SHA384',
            'ECDHE-RSA-RC4-SHA',
            'ECDHE-ECDSA-RC4-SHA',
            'AES128',
            'AES256',
            'RC4-SHA',
            'HIGH',
            '!aNULL',
            '!eNULL',
            '!EXPORT',
            '!DES',
            '!3DES',
            '!MD5',
            '!PSK',
        ]);

        /**
         * CN_match and SNI_server_name are only known once a URL is passed.
         * They will be set in the getOptionsForUrl() method which receives a URL.
         *
         * cafile or capath can be overridden by passing in those options to constructor.
         */
        $options = [
            'ssl' => [
                'ciphers' => $ciphers,
                'verify_peer' => true,
                'verify_depth' => 7,
                'SNI_enabled' => true,
            ],
        ];

        /**
         * Attempt to find a local cafile or throw an exception.
         * The user may go download one if this occurs.
         */
        if (!$cafile) {
            $cafile = self::getSystemCaRootBundlePath();
        }
        if (is_dir($cafile)) {
            $options['ssl']['capath'] = $cafile;
        } elseif ($cafile) {
            $options['ssl']['cafile'] = $cafile;
        } else {
            throw new RuntimeException('A valid cafile could not be located automatically.');
        }

        return $options;
    }

    /**
     * function copied from Composer\Util\StreamContextFactory::getContext
     *
     * Any changes should be applied there as well, or backported here.
     *
     * @param string $url URL the context is to be used for
     * @return resource Default context
     * @throws \RuntimeException if https proxy required and OpenSSL uninstalled
     */
    protected function getMergedStreamContext($url, $acceptMimeType)
    {
        $options = $this->options;

        // Handle system proxy
        if (!empty($_SERVER['HTTP_PROXY']) || !empty($_SERVER['http_proxy'])) {
            // Some systems seem to rely on a lowercased version instead...
            $proxy = parse_url(!empty($_SERVER['http_proxy']) ? $_SERVER['http_proxy'] : $_SERVER['HTTP_PROXY']);
        }

        if (!empty($proxy)) {
            $proxyURL = isset($proxy['scheme']) ? $proxy['scheme'] . '://' : '';
            $proxyURL .= isset($proxy['host']) ? $proxy['host'] : '';

            if (isset($proxy['port'])) {
                $proxyURL .= ":" . $proxy['port'];
            } elseif ('http://' == substr($proxyURL, 0, 7)) {
                $proxyURL .= ":80";
            } elseif ('https://' == substr($proxyURL, 0, 8)) {
                $proxyURL .= ":443";
            }

            // http(s):// is not supported in proxy
            $proxyURL = str_replace(['http://', 'https://'], ['tcp://', 'ssl://'], $proxyURL);

            if (0 === strpos($proxyURL, 'ssl:') && !extension_loaded('openssl')) {
                throw new RuntimeException('You must enable the openssl extension to use a proxy over https');
            }

            $options['http'] = [
                'proxy' => $proxyURL,
            ];

            // enabled request_fulluri unless it is explicitly disabled
            switch (parse_url($url, PHP_URL_SCHEME)) {
                case 'http': // default request_fulluri to true
                    $reqFullUriEnv = getenv('HTTP_PROXY_REQUEST_FULLURI');
                    if ($reqFullUriEnv === false || $reqFullUriEnv === '' || (strtolower($reqFullUriEnv) !== 'false' && (bool)$reqFullUriEnv)) {
                        $options['http']['request_fulluri'] = true;
                    }
                    break;
                case 'https': // default request_fulluri to true
                    $reqFullUriEnv = getenv('HTTPS_PROXY_REQUEST_FULLURI');
                    if ($reqFullUriEnv === false || $reqFullUriEnv === '' || (strtolower($reqFullUriEnv) !== 'false' && (bool)$reqFullUriEnv)) {
                        $options['http']['request_fulluri'] = true;
                    }
                    break;
            }


            if (isset($proxy['user'])) {
                $auth = urldecode($proxy['user']);
                if (isset($proxy['pass'])) {
                    $auth .= ':' . urldecode($proxy['pass']);
                }
                $auth = base64_encode($auth);

                $options['http']['header'] = "Proxy-Authorization: Basic {$auth}\r\n";
            }
        }

        if (isset($options['http']['header'])) {
            $options['http']['header'] .= "Connection: close\r\n";
        } else {
            $options['http']['header'] = "Connection: close\r\n";
        }

        if (getenv('GITHUB_TOKEN') !== false && strpos($url, '?access_token=') === false) {
            $options['http']['header'] .= 'Authorization: token ' . getenv('GITHUB_TOKEN') . "\r\n";
        }

        if (extension_loaded('zlib')) {
            $options['http']['header'] .= "Accept-Encoding: gzip\r\n";
        }

        $options['http']['header'] .= "User-Agent: Ibuildingsnl QA Tools Installer\r\n";
        $options['http']['header'] .= 'Accept: '.$acceptMimeType."\r\n";
        $options['http']['protocol_version'] = 1.1;

        return stream_context_create($options);
    }

    /**
     * This method was adapted from Sslurp.
     * https://github.com/EvanDotPro/Sslurp
     *
     * (c) Evan Coury <me@evancoury.com>
     *
     * For the full copyright and license information, please see below:
     *
     * Copyright (c) 2013, Evan Coury
     * All rights reserved.
     *
     * Redistribution and use in source and binary forms, with or without modification,
     * are permitted provided that the following conditions are met:
     *
     *     * Redistributions of source code must retain the above copyright notice,
     *       this list of conditions and the following disclaimer.
     *
     *     * Redistributions in binary form must reproduce the above copyright notice,
     *       this list of conditions and the following disclaimer in the documentation
     *       and/or other materials provided with the distribution.
     *
     * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
     * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
     * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
     * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
     * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
     * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
     * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
     * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
     * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
     * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
     */
    public static function getSystemCaRootBundlePath()
    {
        static $caPath = null;

        if ($caPath !== null) {
            return $caPath;
        }

        // If SSL_CERT_FILE env variable points to a valid certificate/bundle, use that.
        // This mimics how OpenSSL uses the SSL_CERT_FILE env variable.
        $envCertFile = getenv('SSL_CERT_FILE');
        if ($envCertFile && is_readable($envCertFile) && validateCaFile(file_get_contents($envCertFile))) {
            return $caPath = $envCertFile;
        }

        // If SSL_CERT_DIR env variable points to a valid certificate/bundle, use that.
        // This mimics how OpenSSL uses the SSL_CERT_FILE env variable.
        $envCertDir = getenv('SSL_CERT_DIR');
        if ($envCertDir && is_dir($envCertDir) && is_readable($envCertDir)) {
            return $caPath = $envCertDir;
        }

        $configured = ini_get('openssl.cafile');
        if ($configured && strlen($configured) > 0 && is_readable($configured) && validateCaFile(file_get_contents($configured))) {
            return $caPath = $configured;
        }

        $configured = ini_get('openssl.capath');
        if ($configured && is_dir($configured) && is_readable($configured)) {
            return $caPath = $configured;
        }

        $caBundlePaths = [
            '/etc/pki/tls/certs/ca-bundle.crt', // Fedora, RHEL, CentOS (ca-certificates package)
            '/etc/ssl/certs/ca-certificates.crt', // Debian, Ubuntu, Gentoo, Arch Linux (ca-certificates package)
            '/etc/ssl/ca-bundle.pem', // SUSE, openSUSE (ca-certificates package)
            '/usr/local/share/certs/ca-root-nss.crt', // FreeBSD (ca_root_nss_package)
            '/usr/ssl/certs/ca-bundle.crt', // Cygwin
            '/opt/local/share/curl/curl-ca-bundle.crt', // OS X macports, curl-ca-bundle package
            '/usr/local/share/curl/curl-ca-bundle.crt', // Default cURL CA bunde path (without --with-ca-bundle option)
            '/usr/share/ssl/certs/ca-bundle.crt', // Really old RedHat?
            '/etc/ssl/cert.pem', // OpenBSD
            '/usr/local/etc/ssl/cert.pem', // FreeBSD 10.x
        ];

        foreach ($caBundlePaths as $caBundle) {
            if (@is_readable($caBundle) && validateCaFile(file_get_contents($caBundle))) {
                return $caPath = $caBundle;
            }
        }

        foreach ($caBundlePaths as $caBundle) {
            $caBundle = dirname($caBundle);
            if (is_dir($caBundle) && glob($caBundle . '/*')) {
                return $caPath = $caBundle;
            }
        }

        return $caPath = false;
    }
}
