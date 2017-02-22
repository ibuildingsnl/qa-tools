<?php

namespace Ibuildings\QaTools\PharUpdater\Strategy;

use GuzzleHttp\Client;
use Humbug\SelfUpdate\Exception\JsonParsingException;
use Humbug\SelfUpdate\Strategy\StrategyInterface;
use Humbug\SelfUpdate\Updater;
use Humbug\SelfUpdate\VersionParser;
use Ibuildings\QaTools\Core\Assert\Assertion;

/**
 * Largely based on {Humbug\SelfUpdate\Strategy\GithubStrategy}.
 *
 * @see \Humbug\SelfUpdate\Strategy\GithubStrategy
 */
class GitHubReleasesApiStrategy implements StrategyInterface
{
    const URL_RELEASES = 'https://api.github.com/repos/%s/%s/releases';

    const ALLOW_UNSTABLE = true;
    const DISALLOW_UNSTABLE = false;

    /** @var Client */
    private $httpClient;
    /** @var string */
    private $localVersion;
    /** @var string */
    private $releasePharName;
    /** @var string */
    private $repositoryOwner;
    /** @var string */
    private $repositoryName;
    /** @var bool */
    private $allowUnstable;

    /** @var string|null */
    private $remoteVersion;
    /** @var string|null */
    private $remoteUrl;

    /**
     * @param Client  $httpClient A Guzzle HTTP client to use for talking to the GitHub API
     * @param string  $repositoryOwner
     * @param string  $repositoryName
     * @param string  $releasePharName
     * @param string  $localVersion
     * @param boolean $allowUnstable Whether to allow installation of an unstable version.
     */
    public function __construct(
        Client $httpClient,
        $repositoryOwner,
        $repositoryName,
        $releasePharName,
        $localVersion,
        $allowUnstable
    ) {
        Assertion::nonEmptyString($repositoryOwner, 'The repository owner ought to be a string, got "%s" of type "%s"');
        Assertion::nonEmptyString($repositoryName, 'The repository name ought to be a string, got "%s" of type "%s"');
        Assertion::nonEmptyString(
            $releasePharName,
            'The release Phar name ought to be a string, got "%s" of type "%s"'
        );
        Assertion::nonEmptyString($localVersion, 'The local version ought to be a string, got "%s" of type "%s"');
        Assertion::boolean($allowUnstable, '"Allow unstable" ought to be a boolean, got "%s"');

        $this->httpClient = $httpClient;
        $this->repositoryOwner = $repositoryOwner;
        $this->repositoryName = $repositoryName;
        $this->releasePharName = $releasePharName;
        $this->localVersion = $localVersion;
        $this->allowUnstable = $allowUnstable;
    }

    /**
     * Retrieve the current version available remotely.
     *
     * @param Updater $updater
     * @return string|bool
     */
    public function getCurrentRemoteVersion(Updater $updater)
    {
        $releasesUrl = sprintf(
            self::URL_RELEASES,
            urlencode($this->repositoryOwner),
            urlencode($this->repositoryName)
        );
        $releasesJson = $this->getJsonResource($releasesUrl);
        $releases = json_decode($releasesJson, true);

        if (null === $releases || json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonParsingException(sprintf('Error parsing JSON release data: %s', json_last_error_msg()));
        }

        $indexedReleases = array_combine(array_column($releases, 'tag_name'), $releases);
        $indexedPharReleases = array_filter(
            $indexedReleases,
            function (array $release) {
                if ($release['draft']) {
                    return false;
                }

                $pharAssets = array_filter(
                    $release['assets'],
                    function (array $asset) {
                        return $asset['name'] === $this->releasePharName;
                    }
                );
                $hasPharAsset = count($pharAssets) > 0;

                return $hasPharAsset;
            }
        );

        if (count($indexedPharReleases) === 0) {
            return false;
        }

        $tagNames = array_keys($indexedPharReleases);
        $versionParser = new VersionParser($tagNames);

        if ($this->allowUnstable) {
            $this->remoteVersion = $versionParser->getMostRecentAll();
        } else {
            $this->remoteVersion = $versionParser->getMostRecentStable();
        }

        if (!$this->remoteVersion) {
            return $this->remoteVersion;
        }

        $release = $indexedPharReleases[$this->remoteVersion];
        $pharAssets = array_filter(
            $release['assets'],
            function (array $asset) {
                return $asset['name'] === $this->releasePharName;
            }
        );

        $this->remoteUrl = $pharAssets[0]['url'];

        return $this->remoteVersion;
    }

    /**
     * Download the remote Phar file.
     *
     * @param Updater $updater
     * @return void
     */
    public function download(Updater $updater)
    {
        file_put_contents($updater->getTempPharFile(), $this->getAssetContents($this->remoteUrl));
    }

    /**
     * Retrieve the current version of the local phar file.
     *
     * @param Updater $updater
     * @return string
     */
    public function getCurrentLocalVersion(Updater $updater)
    {
        return $this->localVersion;
    }

    /**
     * @param string $resourceUrl
     * @return string
     */
    private function getJsonResource($resourceUrl)
    {
        $response = $this->httpClient->get(
            $resourceUrl,
            ['http_errors' => true, 'headers' => ['Accept' => 'application/vnd.github.v3+json']]
        );

        return $response->getBody()->getContents();
    }

    /**
     * @param string $assetUrl
     * @return string
     */
    private function getAssetContents($assetUrl)
    {
        $response = $this->httpClient->get(
            $assetUrl,
            ['http_errors' => true, 'headers' => ['Accept' => 'application/octet-stream']]
        );

        return $response->getBody()->getContents();
    }
}
