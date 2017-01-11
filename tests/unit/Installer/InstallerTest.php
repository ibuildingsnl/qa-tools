<?php

namespace Ibuildings\QaTools\UnitTest\Installer;

use HttpClient;
use Installer;
use Mockery;
use org\bovigo\vfs\vfsStream;
use PharValidator;
use PHPUnit\Framework\TestCase;
use RuntimeException;

define('TESTING_QA_TOOLS_INSTALLER', true);
define('QA_TOOLS_INSTALLER_ANSI', false);

require __DIR__.'/../../../installer.php';

final class InstallerTest extends TestCase
{
    const REPOSITORY_OWNER = 'ibuildingsnl';
    const REPOSITORY_NAME = 'qa-tools-v3';

    const PHAR_ASSET_URL = 'https://api.github.com/assets/1';
    const PUBKEY_ASSET_URL = 'https://api.github.com/assets/2';

    const VERSION = '1.0.0';

    /** @var Mockery\Mock|PharValidator */
    private $pharValidator;

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

        $info = $installer->getLatestReleaseInfo(false);

        $this->assertEquals(self::VERSION, $info['version']);
        $this->assertEquals($this->getAssetUrl(self::PHAR_ASSET_URL), $info['pharUrl']);
        $this->assertEquals($this->getAssetUrl(self::PUBKEY_ASSET_URL), $info['pubkeyUrl']);
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
        $installer->getLatestReleaseInfo(self::VERSION);
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

    private function getAssetUrl($url)
    {
        if (getenv('GITHUB_TOKEN') !== false) {
            return sprintf('%s?access_token=%s', $url, urlencode(getenv('GITHUB_TOKEN')));
        }
        return $url;
    }
}