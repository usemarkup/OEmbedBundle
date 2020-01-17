<?php

namespace Markup\OEmbedBundle\Tests\Service;

use Markup\OEmbedBundle\Cache\ObjectCache;
use Markup\OEmbedBundle\Client\ClientInterface;
use Markup\OEmbedBundle\Exception\OEmbedUnavailableException;
use Markup\OEmbedBundle\OEmbed\OEmbedInterface;
use Markup\OEmbedBundle\Provider\ProviderInterface;
use Markup\OEmbedBundle\Service\OEmbedService;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
* A test for the oEmbed service.
*/
class OEmbedServiceTest extends TestCase
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var ContainerInterface
     */
    private $providerLocator;

    /**
     * @var ObjectCache
     */
    private $cache;

    /**
     * @var string
     */
    private $cacheKeyDelimiter;

    /**
     * @var OEmbedService
     */
    private $service;

    protected function setUp()
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->providerLocator = $this->createMock(ContainerInterface::class);
        $this->cache = $this->createMock(ObjectCache::class);
        $this->cacheKeyDelimiter = ':';
        $this->service = new OEmbedService($this->client, $this->providerLocator, $this->cache, $this->cacheKeyDelimiter);
    }

    public function testFetchOEmbedWhenProviderExists()
    {
        $provider = $this->createMock(ProviderInterface::class);
        $providerName = 'provider';
        $mediaId = '42';
        $this->providerLocator
            ->expects($this->any())
            ->method('get')
            ->with($this->equalTo($providerName))
            ->willReturn($provider);
        $oEmbed = $this->createMock(OEmbedInterface::class);
        $this->client
            ->expects($this->any())
            ->method('fetchEmbed')
            ->with($this->equalTo($provider), $this->equalTo($mediaId))
            ->willReturn($oEmbed);
        $this->assertSame($oEmbed, $this->service->fetchOEmbed($providerName, $mediaId));
    }

    public function testFetchOEmbedWhenProviderDoesNotExist()
    {
        $this->expectException(OEmbedUnavailableException::class);
        $providerName = 'unknown';
        $mediaId = '42';
        $this->providerLocator
            ->expects($this->any())
            ->method('get')
            ->with($this->equalTo($providerName))
            ->willThrowException(new ServiceNotFoundException($providerName));
        $this->service->fetchOEmbed($providerName, $mediaId);
    }

    public function testFetchOEmbedWhenContentInvalid()
    {
        $this->expectException(OEmbedUnavailableException::class);
        $provider = $this->createMock(ProviderInterface::class);
        $providerName = 'provider';
        $mediaId = '42';
        $this->providerLocator
            ->expects($this->any())
            ->method('get')
            ->with($this->equalTo($providerName))
            ->willReturn($provider);
        $this->client
            ->expects($this->any())
            ->method('fetchEmbed')
            ->with($this->equalTo($provider), $this->equalTo($mediaId))
            ->will($this->throwException(new \Markup\OEmbedBundle\Exception\InvalidOEmbedContentException()));
        $this->service->fetchOEmbed($providerName, $mediaId);
    }

    public function testFetchOEmbedWithHitInObjectCache()
    {
        $providerName = 'provider';
        $mediaId = '42';
        $oEmbed = $this->createMock(OEmbedInterface::class);
        $this->cache
            ->expects($this->any())
            ->method('get')
            ->with($this->equalTo('provider:42'))
            ->will($this->returnValue($oEmbed));
        $this->assertSame($oEmbed, $this->service->fetchOEmbed($providerName, $mediaId));
    }

    public function testFetchOEmbedWithParameters()
    {
        $provider = $this->createMock(ProviderInterface::class);
        $providerName = 'provider';
        $mediaId = '42';
        $parameters = array('playback' => 'no');
        $this->providerLocator
            ->expects($this->any())
            ->method('get')
            ->with($this->equalTo($providerName))
            ->willReturn($provider);
        $oEmbed = $this->createMock(OEmbedInterface::class);
        $this->client
            ->expects($this->any())
            ->method('fetchEmbed')
            ->with($this->equalTo($provider), $this->equalTo($mediaId), $this->equalTo($parameters))
            ->will($this->returnValue($oEmbed));
        $this->assertSame($oEmbed, $this->service->fetchOEmbed($providerName, $mediaId, $parameters));
    }
}
