<?php
declare(strict_types = 1);

namespace integration\Installer;

use DirectoryIterator;
use HttpClient;
use Installer;
use Mockery;
use PharValidator;
use PHPUnit\Framework\TestCase;

define('TESTING_QA_TOOLS_INSTALLER', true);
define('QA_TOOLS_INSTALLER_ANSI', false);

require __DIR__.'/../../../installer.php';

final class InstallerTest extends TestCase
{
    const REPOSITORY_OWNER = 'ibuildingsnl';
    const REPOSITORY_NAME = 'qa-tools-v3';

    const PHAR_ASSET_URL = 'https://api.github.com/assets/1';
    const PHAR_ASSET_CONTENTS = 'PHAR';

    const PUBKEY_ASSET_URL = 'https://api.github.com/assets/2';
    const PUBKEY_ASSET_CONTENTS = 'PUBKEY';

    const VERSION = '1.0.0';

    /** @var string $tempDirectory */
    private $tempDirectory;

    public function setUp()
    {
        $cwd = realpath(getcwd());
        $this->tempDirectory = $cwd.'/qa-tools-download-test';

        if (file_exists($this->tempDirectory) && !is_dir($this->tempDirectory)) {
            $this->markTestSkipped(
                'Unable to create temp directory '.$this->tempDirectory.' because there already exists a file with that name.'
            );
        }

        if (is_dir($this->tempDirectory)) {
            $this->rmdirRecursive($this->tempDirectory);
        }

        if (!@mkdir($this->tempDirectory)) {
            $this->markTestSkipped('Unable to create qa-tools temp download folder '.$this->tempDirectory);
        }
    }

    public function tearDown()
    {
        $this->rmdirRecursive($this->tempDirectory);
    }

    /**
     * @test
     */
    public function download_correct_files()
    {
        /** @var Mockery\Mock|HttpClient $httpClient */
        $httpClient = Mockery::mock(HttpClient::class);
        $httpClient->shouldReceive('get')
            ->with(
                sprintf(
                    'https://api.github.com/repos/%s/%s/releases/latest',
                    urlencode(self::REPOSITORY_OWNER),
                    urlencode(self::REPOSITORY_NAME)
                ),
                'application/vnd.github.v3+json'
            )
            ->andReturn(json_encode([
                'tag_name' => self::VERSION,
                'assets' => [
                    [
                        'name' => 'qa-tools.phar',
                        'url' => self::PHAR_ASSET_URL,
                    ],
                    [
                        'name' => 'qa-tools.phar.pubkey',
                        'url' => self::PUBKEY_ASSET_URL,
                    ]
                ]
            ]));

        $httpClient->shouldReceive('get')
            ->with($this->getAssetUrl(self::PHAR_ASSET_URL), 'application/octet-stream')
            ->andReturn(self::PHAR_ASSET_CONTENTS);

        $httpClient->shouldReceive('get')
            ->with($this->getAssetUrl(self::PUBKEY_ASSET_URL), 'application/octet-stream')
            ->andReturn(self::PUBKEY_ASSET_CONTENTS);

        /** @var Mockery\Mock|PharValidator $pharValidator */
        $pharValidator = Mockery::mock(PharValidator::class);
        $pharValidator->shouldReceive('assertPharValid');

        $installer = new Installer(false, $httpClient, $pharValidator, self::REPOSITORY_OWNER, self::REPOSITORY_NAME);

        ob_start();
        $installer->run(false, $this->tempDirectory, 'qa-tools');
        $output = ob_get_clean();

        $this->assertRegExp(
            '~^QA Tools \(version '.preg_quote(self::VERSION, '~').'\) successfully installed~sm',
            $output
        );

        $this->assertFileExists($this->tempDirectory.'/qa-tools');
        $this->assertEquals(self::PHAR_ASSET_CONTENTS, file_get_contents($this->tempDirectory.'/qa-tools'));

        $this->assertFileExists($this->tempDirectory.'/qa-tools.pubkey');
        $this->assertEquals(self::PUBKEY_ASSET_CONTENTS, file_get_contents($this->tempDirectory.'/qa-tools.pubkey'));
    }

    private function getAssetUrl($url)
    {
        if (getenv('GITHUB_TOKEN') !== false) {
            return sprintf('%s?access_token=%s', $url, urlencode(getenv('GITHUB_TOKEN')));
        }
        return $url;
    }

    private function rmdirRecursive($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $directory = new DirectoryIterator($dir);
        foreach ($directory as $file) {
            if ($file->isDot()) {
                continue;
            }
            if ($file->isDir()) {
                $this->rmdirRecursive($file->getPathname());
            }
            @unlink($file->getPathname());
        }
        rmdir($dir);
    }
}
