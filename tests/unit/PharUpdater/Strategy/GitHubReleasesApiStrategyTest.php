<?php

namespace Ibuildings\QaTools\UnitTest\PharUpdater\Strategy;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Humbug\SelfUpdate\Updater;
use Ibuildings\QaTools\PharUpdater\Strategy\GitHubReleasesApiStrategy;
use Ibuildings\QaTools\Test\MockeryTestCase;
use Mockery as m;

class GitHubReleasesApiStrategyTest extends MockeryTestCase
{
    /** @test */
    public function can_determine_the_current_stable_remote_version_from_two_stable_versions()
    {
        $mock = new MockHandler(
            [
                new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    file_get_contents(__DIR__ . '/two-stable-releases.json')
                ),
            ]
        );

        $stack = HandlerStack::create($mock);

        $transactions = [];
        $history = Middleware::history($transactions);
        $stack->push($history);

        $httpClient = new Client(['handler' => $stack]);
        $strategy = new GitHubReleasesApiStrategy(
            $httpClient,
            'ibuildingsnl',
            'qa-tools',
            'qa-tools.phar',
            '1.0.0',
            GitHubReleasesApiStrategy::DISALLOW_UNSTABLE
        );

        $this->assertEquals('3.1.0', $strategy->getCurrentRemoteVersion(m::mock(Updater::class)));

        $this->assertCount(1, $transactions);
        /** @var Request $request */
        $request = $transactions[0]['request'];
        $this->assertEquals('/repos/ibuildingsnl/qa-tools/releases', $request->getRequestTarget());
        $this->assertEquals('application/vnd.github.v3+json', $request->getHeaderLine('Accept'));
    }

    /** @test */
    public function can_determine_the_current_unstable_remote_version_from_two_versions()
    {
        $mock = new MockHandler(
            [
                new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    file_get_contents(__DIR__ . '/two-releases.json')
                ),
            ]
        );

        $stack = HandlerStack::create($mock);

        $transactions = [];
        $history = Middleware::history($transactions);
        $stack->push($history);

        $httpClient = new Client(['handler' => $stack]);
        $strategy = new GitHubReleasesApiStrategy(
            $httpClient,
            'ibuildingsnl',
            'qa-tools',
            'qa-tools.phar',
            '1.0.0',
            GitHubReleasesApiStrategy::ALLOW_UNSTABLE
        );

        $this->assertEquals('3.1.0-beta', $strategy->getCurrentRemoteVersion(m::mock(Updater::class)));

        $this->assertCount(1, $transactions);
        /** @var Request $request */
        $request = $transactions[0]['request'];
        $this->assertEquals('/repos/ibuildingsnl/qa-tools/releases', $request->getRequestTarget());
        $this->assertEquals('application/vnd.github.v3+json', $request->getHeaderLine('Accept'));
    }

    /** @test */
    public function can_determine_the_current_stable_remote_version_from_two_versions()
    {
        $mock = new MockHandler(
            [
                new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    file_get_contents(__DIR__ . '/two-releases.json')
                ),
            ]
        );

        $stack = HandlerStack::create($mock);

        $transactions = [];
        $history = Middleware::history($transactions);
        $stack->push($history);

        $httpClient = new Client(['handler' => $stack]);
        $strategy = new GitHubReleasesApiStrategy(
            $httpClient,
            'ibuildingsnl',
            'qa-tools',
            'qa-tools.phar',
            '1.0.0',
            GitHubReleasesApiStrategy::DISALLOW_UNSTABLE
        );

        $this->assertEquals('3.0.0', $strategy->getCurrentRemoteVersion(m::mock(Updater::class)));

        $this->assertCount(1, $transactions);
        /** @var Request $request */
        $request = $transactions[0]['request'];
        $this->assertEquals('/repos/ibuildingsnl/qa-tools/releases', $request->getRequestTarget());
        $this->assertEquals('application/vnd.github.v3+json', $request->getHeaderLine('Accept'));
    }

    /** @test */
    public function can_determine_no_remote_version_from_no_versions()
    {
        $mock = new MockHandler(
            [
                new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    file_get_contents(__DIR__ . '/no-releases.json')
                ),
            ]
        );
        $stack = HandlerStack::create($mock);

        $transactions = [];
        $history = Middleware::history($transactions);
        $stack->push($history);

        $httpClient = new Client(['handler' => $stack]);
        $strategy = new GitHubReleasesApiStrategy(
            $httpClient,
            'ibuildingsnl',
            'qa-tools',
            'qa-tools.phar',
            '1.0.0',
            GitHubReleasesApiStrategy::DISALLOW_UNSTABLE
        );

        $this->assertFalse($strategy->getCurrentRemoteVersion(m::mock(Updater::class)));

        $this->assertCount(1, $transactions);
        /** @var Request $request */
        $request = $transactions[0]['request'];
        $this->assertEquals('/repos/ibuildingsnl/qa-tools/releases', $request->getRequestTarget());
        $this->assertEquals('application/vnd.github.v3+json', $request->getHeaderLine('Accept'));
    }

    /** @test */
    public function ignores_releases_without_the_required_phar_file()
    {
        $mock = new MockHandler(
            [
                new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    file_get_contents(__DIR__ . '/two-stable-releases-one-phar.json')
                ),
            ]
        );
        $stack = HandlerStack::create($mock);

        $transactions = [];
        $history = Middleware::history($transactions);
        $stack->push($history);

        $httpClient = new Client(['handler' => $stack]);
        $strategy = new GitHubReleasesApiStrategy(
            $httpClient,
            'ibuildingsnl',
            'qa-tools',
            'qa-tools.phar',
            '2.1.0',
            GitHubReleasesApiStrategy::DISALLOW_UNSTABLE
        );

        $this->assertEquals('3.0.0', $strategy->getCurrentRemoteVersion(m::mock(Updater::class)));

        $this->assertCount(1, $transactions);
        /** @var Request $request */
        $request = $transactions[0]['request'];
        $this->assertEquals('/repos/ibuildingsnl/qa-tools/releases', $request->getRequestTarget());
        $this->assertEquals('application/vnd.github.v3+json', $request->getHeaderLine('Accept'));
    }
}
