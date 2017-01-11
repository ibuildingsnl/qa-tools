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
    const PHAR_ASSET_CONTENTS = 'PHAR';

    const PUBKEY_ASSET_URL = 'https://api.github.com/assets/2';
    const PUBKEY_ASSET_CONTENTS = 'PUBKEY';

    /** @var Mockery\Mock|PharValidator */
    private $pharValidator;

    public function setUp()
    {
        $this->pharValidator = Mockery::mock(PharValidator::class);
    }

    /**
     * @test
     */
    public function calls_correct_url_for_latest()
    {
        /** @var Mockery\Mock|HttpClient $httpClient */
        $httpClient = Mockery::mock(HttpClient::class);
        $httpClient->shouldReceive('get')
            ->once()
            ->with(
                sprintf(
                    'https://api.github.com/repos/%s/%s/releases/latest',
                    self::REPOSITORY_OWNER,
                    self::REPOSITORY_NAME
                ),
                'application/vnd.github.v3+json'
            )
            ->andReturn('[]');

        $installer = new Installer(
            false,
            $httpClient,
            $this->pharValidator,
            self::REPOSITORY_OWNER,
            self::REPOSITORY_NAME
        );
        $installer->getLatestReleaseInfo(false);
    }

    /**
     * @test
     */
    public function calls_correct_url_for_specific_version()
    {
        /** @var Mockery\Mock|HttpClient $httpClient */
        $httpClient = Mockery::mock(HttpClient::class);
        $httpClient->shouldReceive('get')
            ->once()
            ->with(
                sprintf(
                    'https://api.github.com/repos/%s/%s/releases/1.0.0',
                    self::REPOSITORY_OWNER,
                    self::REPOSITORY_NAME
                ),
                'application/vnd.github.v3+json'
            )
            ->andReturn('[]');

        $installer = new Installer(
            false,
            $httpClient,
            $this->pharValidator,
            self::REPOSITORY_OWNER,
            self::REPOSITORY_NAME
        );
        $installer->getLatestReleaseInfo('1.0.0');
    }

    /**
     * @test
     */
    public function fails_on_invalid_json_from_github()
    {
        /** @var Mockery\Mock|HttpClient $httpClient */
        $httpClient = Mockery::mock(HttpClient::class);
        $httpClient->shouldReceive('get')
            ->once()
            ->with(
                sprintf(
                    'https://api.github.com/repos/%s/%s/releases/1.0.0',
                    self::REPOSITORY_OWNER,
                    self::REPOSITORY_NAME
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
        $installer->getLatestReleaseInfo('1.0.0');
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
                    self::REPOSITORY_OWNER,
                    self::REPOSITORY_NAME
                ),
                'application/vnd.github.v3+json'
            )
            ->andReturn(json_encode([
                'tag_name' => '1.0.0',
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

        $this->assertRegexp('~^Unable to find qa-tools\.phar in release 1\.0\.0~sm', $output);
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
                    self::REPOSITORY_OWNER,
                    self::REPOSITORY_NAME
                ),
                'application/vnd.github.v3+json'
            )
            ->andReturn(json_encode([
                'tag_name' => '1.0.0',
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

        $this->assertRegexp('~^Unable to find qa-tools\.phar\.pubkey in release 1\.0\.0~sm', $output);
    }

    /**
     * @test
     */
    public function download_correct_files()
    {
        /** @var Mockery\Mock|HttpClient $httpClient */
        $httpClient = Mockery::mock(HttpClient::class);
        $httpClient->shouldReceive('get')
            ->once()
            ->ordered()->globally()
            ->with(
                sprintf(
                    'https://api.github.com/repos/%s/%s/releases/latest',
                    self::REPOSITORY_OWNER,
                    self::REPOSITORY_NAME
                ),
                'application/vnd.github.v3+json'
            )
            ->andReturn(json_encode([
                'tag_name' => '1.0.0',
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

        $getAssetUrl = function ($url) {
            if (getenv('GITHUB_TOKEN') !== false) {
                $url .= '?access_token='.getenv('GITHUB_TOKEN');
            }
            return $url;
        };

        $httpClient->shouldReceive('get')
            ->once()
            ->ordered()
            ->globally()
            ->with($getAssetUrl(self::PHAR_ASSET_URL), 'application/octet-stream')
            ->andReturn(self::PHAR_ASSET_CONTENTS);

        $httpClient->shouldReceive('get')
            ->once()
            ->ordered()
            ->globally()
            ->with($getAssetUrl(self::PUBKEY_ASSET_URL), 'application/octet-stream')
            ->andReturn(self::PUBKEY_ASSET_CONTENTS);

        /** @var Mockery\Mock|PharValidator $pharValidator */
        $pharValidator = Mockery::mock(PharValidator::class);
        $pharValidator->shouldReceive('assertPharValid')->once();

        $installer = new Installer(false, $httpClient, $pharValidator, self::REPOSITORY_OWNER, self::REPOSITORY_NAME);

        $root = vfsStream::setup('temp');
        ob_start();
        $installer->run(false, vfsStream::url('temp'), 'qa-tools');
        $output = ob_get_clean();

        $this->assertRegexp('~^QA Tools \(version 1\.0\.0\) successfully installed~sm', $output);

        $this->assertTrue($root->hasChild('temp/qa-tools'));
        $this->assertEquals(self::PHAR_ASSET_CONTENTS, $root->getChild('temp/qa-tools')->getContent());

        $this->assertTrue($root->hasChild('temp/qa-tools.pubkey'));
        $this->assertEquals(self::PUBKEY_ASSET_CONTENTS, $root->getChild('temp/qa-tools.pubkey')->getContent());
    }
}
