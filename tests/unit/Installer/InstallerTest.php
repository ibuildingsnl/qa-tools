<?php

namespace Ibuildings\QaTools\UnitTest\Installer;

use HttpClient;
use Installer;
use Mockery;
use org\bovigo\vfs\vfsStream;
use PharValidator;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class InstallerTest extends TestCase
{
    const REPOSITORY_OWNER = 'ibuildingsnl';
    const REPOSITORY_NAME = 'qa-tools';

    const PHAR_ASSET_URL = 'https://api.github.com/assets/1';
    const PUBKEY_ASSET_URL = 'https://api.github.com/assets/2';

    const VERSION = '1.0.0';

    /** @var Mockery\Mock|PharValidator */
    private $pharValidator;

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
        $this->pharValidator = Mockery::mock(PharValidator::class);
    }

    /**
     * @test
     */
    public function correctly_parses_github_release_response()
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

        $installer = new Installer(
            false,
            $httpClient,
            $this->pharValidator,
            self::REPOSITORY_OWNER,
            self::REPOSITORY_NAME
        );

        $info = $installer->getReleaseInfo(false);

        $this->assertEquals(self::VERSION, $info['version']);
        $this->assertEquals(self::PHAR_ASSET_URL, $info['pharUrl']);
        $this->assertEquals(self::PUBKEY_ASSET_URL, $info['pubkeyUrl']);
    }

    /**
     * @test
     */
    public function supports_fetching_a_specific_version()
    {
        $version = '1.0.0-alpha3';

        /** @var Mockery\Mock|HttpClient $httpClient */
        $httpClient = Mockery::mock(HttpClient::class);
        $httpClient->shouldReceive('get')
            ->with(
                sprintf(
                    'https://api.github.com/repos/%s/%s/releases/tags/%s',
                    urlencode(self::REPOSITORY_OWNER),
                    urlencode(self::REPOSITORY_NAME),
                    urlencode($version)
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

        $installer = new Installer(
            false,
            $httpClient,
            $this->pharValidator,
            self::REPOSITORY_OWNER,
            self::REPOSITORY_NAME
        );

        $info = $installer->getReleaseInfo($version);

        $this->assertEquals(self::VERSION, $info['version']);
        $this->assertEquals(self::PHAR_ASSET_URL, $info['pharUrl']);
        $this->assertEquals(self::PUBKEY_ASSET_URL, $info['pubkeyUrl']);
    }

    /**
     * @test
     */
    public function fails_on_invalid_json_from_github()
    {
        /** @var Mockery\Mock|HttpClient $httpClient */
        $httpClient = Mockery::mock(HttpClient::class);
        $httpClient->shouldReceive('get')
            ->with(
                sprintf(
                    'https://api.github.com/repos/%s/%s/releases/1.0.0',
                    urlencode(self::REPOSITORY_OWNER),
                    urlencode(self::REPOSITORY_NAME)
                ),
                'application/vnd.github.v3+json'
            )
            ->andReturn('[');

        $installer = new Installer(
            false,
            $httpClient,
            $this->pharValidator,
            self::REPOSITORY_OWNER,
            self::REPOSITORY_NAME
        );

        $this->expectException(RuntimeException::class);
        $installer->getReleaseInfo(self::VERSION);
    }

    /**
     * @test
     */
    public function fails_when_no_version_number_available_in_release()
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

        $installer = new Installer(
            false,
            $httpClient,
            $this->pharValidator,
            self::REPOSITORY_OWNER,
            self::REPOSITORY_NAME
        );

        ob_start();
        $installer->run(false, '.', 'qa-tools');
        $output = ob_get_clean();

        $this->assertRegexp('~^Unable to determine version number from release~sm', $output);
    }

    /**
     * @test
     */
    public function fails_when_no_phar_available_in_release()
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
                        'name' => 'qa-tools.phar.pubkey',
                        'url' => self::PUBKEY_ASSET_URL,
                    ]
                ]
            ]));

        $installer = new Installer(
            false,
            $httpClient,
            $this->pharValidator,
            self::REPOSITORY_OWNER,
            self::REPOSITORY_NAME
        );

        ob_start();
        $installer->run(false, '.', 'qa-tools');
        $output = ob_get_clean();

        $this->assertRegExp(
            '~^Unable to find qa-tools\.phar in release '.preg_quote(self::VERSION, '~').'~sm',
            $output
        );
    }

    /**
     * @test
     */
    public function fails_when_no_pubkey_available_in_release()
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
                    ]
                ]
            ]));

        $installer = new Installer(
            false,
            $httpClient,
            $this->pharValidator,
            self::REPOSITORY_OWNER,
            self::REPOSITORY_NAME
        );

        ob_start();
        $installer->run(false, '.', 'qa-tools');
        $output = ob_get_clean();

        $this->assertRegExp(
            '~^Unable to find qa-tools\.phar\.pubkey in release '.preg_quote(self::VERSION, '~').'~sm',
            $output
        );
    }
}
