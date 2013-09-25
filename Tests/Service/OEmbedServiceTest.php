<?php

namespace Markup\OEmbedBundle\Tests\Service;

use Markup\OEmbedBundle\Service\OEmbedService;

/**
* A test for the oEmbed service.
*/
class OEmbedServiceTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->client = $this->getMock('Markup\OEmbedBundle\Client\ClientInterface');
        $this->providerFactory = $this->getMockBuilder('Markup\OEmbedBundle\Provider\ProviderFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->cache = $this->getMock('Markup\OEmbedBundle\Cache\ObjectCacheInterface');
        $this->cacheKeyDelimiter = ':';
        $this->service = new OEmbedService($this->client, $this->providerFactory, $this->cache, $this->cacheKeyDelimiter);
    }

    public function testFetchOEmbedWhenProviderExists()
    {
        $provider = $this->getMock('Markup\OEmbedBundle\Provider\ProviderInterface');
        $providerName = 'provider';
        $mediaId = '42';
        $this->providerFactory
            ->expects($this->any())
            ->method('fetchProvider')
            ->with($this->equalTo($providerName))
            ->will($this->returnValue($provider));
        $oEmbed = $this->getMock('Markup\OEmbedBundle\OEmbed\OEmbedInterface');
        $this->client
            ->expects($this->any())
            ->method('fetchEmbed')
            ->with($this->equalTo($provider), $this->equalTo($mediaId))
            ->will($this->returnValue($oEmbed));
        $this->assertSame($oEmbed, $this->service->fetchOEmbed($providerName, $mediaId));
    }

    public function testFetchOEmbedWhenProviderDoesNotExist()
    {
        $this->setExpectedException('Markup\OEmbedBundle\Exception\OEmbedUnavailableException');
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
        $this->setExpectedException('Markup\OEmbedBundle\Exception\OEmbedUnavailableException');
        $provider = $this->getMock('Markup\OEmbedBundle\Provider\ProviderInterface');
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
        $oEmbed = $this->getMock('Markup\OEmbedBundle\OEmbed\OEmbedInterface');
        $this->cache
            ->expects($this->any())
            ->method('get')
            ->with($this->equalTo('provider:42'))
            ->will($this->returnValue($oEmbed));
        $this->assertSame($oEmbed, $this->service->fetchOEmbed($providerName, $mediaId));
    }

    public function testFetchOEmbedWithParameters()
    {
        $provider = $this->getMock('Markup\OEmbedBundle\Provider\ProviderInterface');
        $providerName = 'provider';
        $mediaId = '42';
        $parameters = array('playback' => 'no');
        $this->providerFactory
            ->expects($this->any())
            ->method('fetchProvider')
            ->with($this->equalTo($providerName))
            ->will($this->returnValue($provider));
        $oEmbed = $this->getMock('Markup\OEmbedBundle\OEmbed\OEmbedInterface');
        $this->client
            ->expects($this->any())
            ->method('fetchEmbed')
            ->with($this->equalTo($provider), $this->equalTo($mediaId), $this->equalTo($parameters))
            ->will($this->returnValue($oEmbed));
        $this->assertSame($oEmbed, $this->service->fetchOEmbed($providerName, $mediaId, $parameters));
    }
}
