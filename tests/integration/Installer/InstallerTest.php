<?php

namespace Ibuildings\QaTools\IntegrationTest\Installer;

use HttpClient;
use Ibuildings\QaTools\Test\MockeryTestCase;
use Installer;
use Mockery;
use PharValidator;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;

final class InstallerTest extends MockeryTestCase
{
    const REPOSITORY_OWNER = 'ibuildingsnl';
    const REPOSITORY_NAME = 'qa-tools';

    const PHAR_ASSET_URL = 'https://api.github.com/assets/1';
    const PHAR_ASSET_CONTENTS = 'PHAR';

    const PUBKEY_ASSET_URL = 'https://api.github.com/assets/2';
    const PUBKEY_ASSET_CONTENTS = 'PUBKEY';

    const VERSION = '1.0.0';

    /** @var string $tempDirectory */
    private $tempDirectory;

    /** @var Filesystem $filesystem */
    private $filesystem;

    public static function setUpBeforeClass()
    {
        if (!defined('TESTING_QA_TOOLS_INSTALLER')) {
            define('TESTING_QA_TOOLS_INSTALLER', true);
        } elseif (!TESTING_QA_TOOLS_INSTALLER) {
            self::fail(
                'Cannot execute Installer unit tests; ' .
                "TESTING_QA_TOOLS_INSTALLER constant already defined and is false, rather than true"
            );
        }

        if (!defined('QA_TOOLS_INSTALLER_ANSI')) {
            define('QA_TOOLS_INSTALLER_ANSI', false);
        } elseif (QA_TOOLS_INSTALLER_ANSI) {
            self::fail(
                'Cannot execute Installer unit tests; ' .
                "QA_TOOLS_INSTALLER_ANSI constant already defined and is true, rather than false"
            );
        }

        require_once __DIR__.'/../../../installer.php';
    }

    public function setUp()
    {
        $this->tempDirectory = sys_get_temp_dir().'/qa-tools-download-test';
        $this->filesystem = new Filesystem();

        if (file_exists($this->tempDirectory) && !is_dir($this->tempDirectory)) {
            $this->markTestSkipped(
                sprintf(
                    'Unable to create temp directory (%s) because there already exists a file with that name.',
                    $this->tempDirectory
                )
            );
        }

        if (is_dir($this->tempDirectory)) {
            $this->filesystem->remove($this->tempDirectory);
        }

        try {
            $this->filesystem->mkdir($this->tempDirectory);
        } catch (\Exception $e) {
            $this->markTestSkipped(
                sprintf(
                    'Unable to create qa-tools temp download folder (%s): %s'.
                    $this->tempDirectory,
                    $e->getMessage()
                )
            );
        }
    }

    public function tearDown()
    {
        $this->filesystem->remove($this->tempDirectory);
    }

    /** @test */
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
            ->with(self::PHAR_ASSET_URL, 'application/octet-stream')
            ->andReturn(self::PHAR_ASSET_CONTENTS);

        $httpClient->shouldReceive('get')
            ->with(self::PUBKEY_ASSET_URL, 'application/octet-stream')
            ->andReturn(self::PUBKEY_ASSET_CONTENTS);

        /** @var Mockery\Mock|PharValidator $pharValidator */
        $pharValidator = Mockery::mock(PharValidator::class);
        $pharValidator->shouldReceive('assertPharValid');

        $installer = new Installer(false, $httpClient, $pharValidator, self::REPOSITORY_OWNER, self::REPOSITORY_NAME);

        ob_start();
        $success = $installer->run(false, $this->tempDirectory, 'qa-tools');
        $output = ob_get_clean();

        $this->assertTrue($success);
        $this->assertRegExp(
            '~^QA Tools \(version '.preg_quote(self::VERSION, '~').'\) successfully installed~sm',
            $output
        );
        $this->assertFileExists($this->tempDirectory.'/qa-tools');
        $this->assertEquals(self::PHAR_ASSET_CONTENTS, file_get_contents($this->tempDirectory.'/qa-tools'));
        $this->assertFileExists($this->tempDirectory.'/qa-tools.pubkey');
        $this->assertEquals(self::PUBKEY_ASSET_CONTENTS, file_get_contents($this->tempDirectory.'/qa-tools.pubkey'));
    }

    /** @test */
    public function fails_when_files_cannot_be_downloaded()
    {
        /** @var Mockery\Mock|HttpClient $httpClient */
        $httpClient = Mockery::mock(HttpClient::class);
        $httpClient->shouldReceive('get')->andThrow(new RuntimeException());

        /** @var Mockery\Mock|PharValidator $pharValidator */
        $pharValidator = Mockery::mock(PharValidator::class);
        $pharValidator->shouldReceive('assertPharValid');

        $installer = new Installer(false, $httpClient, $pharValidator, self::REPOSITORY_OWNER, self::REPOSITORY_NAME);

        ob_start();
        $success = $installer->run(false, $this->tempDirectory, 'qa-tools');
        $output = ob_get_clean();

        $this->assertFalse($success);
        $this->assertContains('Unable to download version information from https://', $output);
        $this->assertContains('The download failed repeatedly, aborting.', $output);

        $this->assertFileNotExists($this->tempDirectory.'/qa-tools');
        $this->assertFileNotExists($this->tempDirectory.'/qa-tools.pubkey');
    }
}
