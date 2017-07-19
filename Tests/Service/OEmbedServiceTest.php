<?php

namespace Markup\OEmbedBundle\Tests\Service;

use Markup\OEmbedBundle\Cache\ObjectCacheInterface;
use Markup\OEmbedBundle\Client\ClientInterface;
use Markup\OEmbedBundle\Exception\OEmbedUnavailableException;
use Markup\OEmbedBundle\OEmbed\OEmbedInterface;
use Markup\OEmbedBundle\Provider\ProviderFactory;
use Markup\OEmbedBundle\Provider\ProviderInterface;
use Markup\OEmbedBundle\Service\OEmbedService;
use PHPUnit\Framework\TestCase;

/**
* A test for the oEmbed service.
*/
class OEmbedServiceTest extends TestCase
{
    public function setUp()
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->providerFactory = $this->createMock(ProviderFactory::class);
        $this->cache = $this->createMock(ObjectCacheInterface::class);
        $this->cacheKeyDelimiter = ':';
        $this->service = new OEmbedService($this->client, $this->providerFactory, $this->cache, $this->cacheKeyDelimiter);
    }

    public function testFetchOEmbedWhenProviderExists()
    {
        $provider = $this->createMock(ProviderInterface::class);
        $providerName = 'provider';
        $mediaId = '42';
        $this->providerFactory
            ->expects($this->any())
            ->method('fetchProvider')
            ->with($this->equalTo($providerName))
            ->will($this->returnValue($provider));
        $oEmbed = $this->createMock(OEmbedInterface::class);
        $this->client
            ->expects($this->any())
            ->method('fetchEmbed')
            ->with($this->equalTo($provider), $this->equalTo($mediaId))
            ->will($this->returnValue($oEmbed));
        $this->assertSame($oEmbed, $this->service->fetchOEmbed($providerName, $mediaId));
    }

    public function testFetchOEmbedWhenProviderDoesNotExist()
    {
        $this->expectException(OEmbedUnavailableException::class);
        $providerName = 'unknown';
        $mediaId = '42';
        $this->providerFactory
            ->expects($this->any())
            ->method('fetchProvider')
            ->with($this->equalTo($providerName))
            ->will($this->throwException(new \Markup\OEmbedBundle\Exception\ProviderNotFoundException($providerName)));
        $this->service->fetchOEmbed($providerName, $mediaId);
    }

    public function testFetchOEmbedWhenContentInvalid()
    {
        $this->expectException(OEmbedUnavailableException::class);
        $provider = $this->createMock(ProviderInterface::class);
        $providerName = 'provider';
        $mediaId = '42';
        $this->providerFactory
            ->expects($this->any())
            ->method('fetchProvider')
            ->with($this->equalTo($providerName))
            ->will($this->returnValue($provider));
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
        $this->providerFactory
            ->expects($this->any())
            ->method('fetchProvider')
            ->with($this->equalTo($providerName))
            ->will($this->returnValue($provider));
        $oEmbed = $this->createMock(OEmbedInterface::class);
        $this->client
            ->expects($this->any())
            ->method('fetchEmbed')
            ->with($this->equalTo($provider), $this->equalTo($mediaId), $this->equalTo($parameters))
            ->will($this->returnValue($oEmbed));
        $this->assertSame($oEmbed, $this->service->fetchOEmbed($providerName, $mediaId, $parameters));
    }
}
