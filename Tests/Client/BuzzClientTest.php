<?php

namespace Markup\OEmbedBundle\Tests\Client;

use Markup\OEmbedBundle\Client\BuzzClient;
use Markup\OEmbedBundle\OEmbed\OEmbedFactory;

/**
* A test for an oEmbed client using Buzz.
*/
class BuzzClientTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!class_exists('Buzz\Browser')) {
            $this->markTestSkipped('You must install the buzz/buzz package to run this test.');
        }
        $this->buzz = $this->getMockBuilder('Buzz\Browser')
            ->disableOriginalConstructor()
            ->getMock();
        $this->oEmbedFactory = new OEmbedFactory();
        $this->client = new BuzzClient($this->buzz, $this->oEmbedFactory);
    }

    public function testIsClient()
    {
        $this->assertInstanceOf('Markup\OEmbedBundle\Client\ClientInterface', $this->client);
    }

    public function testFetchEmbed()
    {
        $html = 'the html';
        $endpointUrl = 'http://domain.com/oembed';
        $urlScheme = 'http://domain.com/media/$ID$';
        $mediaId = '42';
        $oEmbedUrl = 'http://domain.com/oembed?url=http://domain.com/media/42';
        $responseJson = '{"version":"1.0","type":"video","html":"'.$html.'"}';
        $response = $this->getMockBuilder('Buzz\Message\Response')
            ->disableOriginalConstructor()
            ->getMock();
        $response
            ->expects($this->any())
            ->method('getContent')
            ->will($this->returnValue($responseJson));
        $response
            ->expects($this->any())
            ->method('isOk')
            ->will($this->returnValue(true));
        $this->buzz
            ->expects($this->any())
            ->method('get')
            ->with($this->equalTo($oEmbedUrl))
            ->will($this->returnValue($response));
        $provider = $this->getMock('Markup\OEmbedBundle\Provider\ProviderInterface');
        $provider
            ->expects($this->any())
            ->method('getApiEndpoint')
            ->will($this->returnValue($endpointUrl));
        $provider
            ->expects($this->any())
            ->method('getUrlScheme')
            ->will($this->returnValue($urlScheme));
        $provider
            ->expects($this->any())
            ->method('getEmbedCodeProperty')
            ->will($this->returnValue('html'));
        $oEmbed = $this->client->fetchEmbed($provider, $mediaId);
        $this->assertInstanceOf('Markup\OEmbedBundle\OEmbed\OEmbedInterface', $oEmbed);
        $this->assertEquals($html, $oEmbed->getEmbedCode());
    }

    public function testFetchEmbedThrowsUnavailableExceptionWhenRequestUnsuccessful()
    {
        $this->setExpectedException('Markup\OEmbedBundle\Exception\OEmbedUnavailableException');
        $html = 'the html';
        $endpointUrl = 'http://domain.com/oembed';
        $urlScheme = 'http://domain.com/media/$ID$';
        $mediaId = '42';
        $oEmbedUrl = 'http://domain.com/oembed?url=http://domain.com/media/42';
        $response = $this->getMockBuilder('Buzz\Message\Response')
            ->disableOriginalConstructor()
            ->getMock();
        $response
            ->expects($this->any())
            ->method('getContent')
            ->will($this->returnValue(''));
        $response
            ->expects($this->any())
            ->method('isOk')
            ->will($this->returnValue(false));
        $this->buzz
            ->expects($this->any())
            ->method('get')
            ->with($this->equalTo($oEmbedUrl))
            ->will($this->returnValue($response));
        $provider = $this->getMock('Markup\OEmbedBundle\Provider\ProviderInterface');
        $provider
            ->expects($this->any())
            ->method('getApiEndpoint')
            ->will($this->returnValue($endpointUrl));
        $provider
            ->expects($this->any())
            ->method('getUrlScheme')
            ->will($this->returnValue($urlScheme));
        $provider
            ->expects($this->any())
            ->method('getEmbedCodeProperty')
            ->will($this->returnValue('html'));
        $this->client->fetchEmbed($provider, $mediaId);
    }

    public function testFetchEmbedThrowsUnavailableExceptionWhenBuzzThrows()
    {
        $this->setExpectedException('Markup\OEmbedBundle\Exception\OEmbedUnavailableException');
        $html = 'the html';
        $endpointUrl = 'http://domain.com/oembed';
        $urlScheme = 'http://domain.com/media/$ID$';
        $mediaId = '42';
        $oEmbedUrl = 'http://domain.com/oembed?url=http://domain.com/media/42';
        $this->buzz
            ->expects($this->any())
            ->method('get')
            ->with($this->equalTo($oEmbedUrl))
            ->will($this->throwException(new \Buzz\Exception\ClientException()));
        $provider = $this->getMock('Markup\OEmbedBundle\Provider\ProviderInterface');
        $provider
            ->expects($this->any())
            ->method('getApiEndpoint')
            ->will($this->returnValue($endpointUrl));
        $provider
            ->expects($this->any())
            ->method('getUrlScheme')
            ->will($this->returnValue($urlScheme));
        $provider
            ->expects($this->any())
            ->method('getEmbedCodeProperty')
            ->will($this->returnValue('html'));
        $this->client->fetchEmbed($provider, $mediaId);
    }

    public function testFetchEmbedWithParameters()
    {
        $html = 'the html';
        $endpointUrl = 'http://domain.com/oembed';
        $urlScheme = 'http://domain.com/media/$ID$';
        $mediaId = '42';
        $parameters = array('key' => 'value');
        $oEmbedUrl = 'http://domain.com/oembed?url=http://domain.com/media/42%3Fkey%3Dvalue';
        $responseJson = '{"version":"1.0","type":"video","html":"'.$html.'"}';
        $response = $this->getMockBuilder('Buzz\Message\Response')
            ->disableOriginalConstructor()
            ->getMock();
        $response
            ->expects($this->any())
            ->method('getContent')
            ->will($this->returnValue($responseJson));
        $response
            ->expects($this->any())
            ->method('isOk')
            ->will($this->returnValue(true));
        $this->buzz
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo($oEmbedUrl))
            ->will($this->returnValue($response));
        $provider = $this->getMock('Markup\OEmbedBundle\Provider\ProviderInterface');
        $provider
            ->expects($this->any())
            ->method('getApiEndpoint')
            ->will($this->returnValue($endpointUrl));
        $provider
            ->expects($this->any())
            ->method('getUrlScheme')
            ->will($this->returnValue($urlScheme));
        $provider
            ->expects($this->any())
            ->method('getEmbedCodeProperty')
            ->will($this->returnValue('html'));
        $this->client->fetchEmbed($provider, $mediaId, $parameters);
    }

    public function testFetchEmbedWithQueryStringInUrlScheme()
    {
        $endpointUrl = 'http://www.youtube.com/oembed';
        $urlScheme = 'http://www.youtube.com/watch?v=$ID$';
        $mediaId = '42';
        $parameters = array('key' => 'value');
        $oEmbedUrl = 'http://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=42%26key%3Dvalue';

        $provider = $this->getMock('Markup\OEmbedBundle\Provider\ProviderInterface');

        $provider
            ->expects($this->any())
            ->method('getApiEndpoint')
            ->will($this->returnValue($endpointUrl));

        $provider
            ->expects($this->any())
            ->method('getUrlScheme')
            ->will($this->returnValue($urlScheme));


        $resolveUrlMethod = self::getResolveOEmbedUrl();
        $abstractClient = $this->getMockForAbstractClass('Markup\OEmbedBundle\Client\AbstractClient');

        $this->assertEquals(
            $oEmbedUrl,
            $resolveUrlMethod->invokeArgs($abstractClient, [
                $provider,
                $mediaId,
                $parameters
            ])
        );
    }

    public function testFetchEmbedWithNoQueryStringInUrlScheme()
    {
        $endpointUrl = 'http://www.youtube.com/oembed';
        $urlScheme = 'http://www.youtube.com/watch/$ID$';
        $mediaId = '42';
        $parameters = array('key' => 'value');
        $oEmbedUrl = 'http://www.youtube.com/oembed?url=http://www.youtube.com/watch/42%3Fkey%3Dvalue';

        $provider = $this->getMock('Markup\OEmbedBundle\Provider\ProviderInterface');

        $provider
            ->expects($this->any())
            ->method('getApiEndpoint')
            ->will($this->returnValue($endpointUrl));

        $provider
            ->expects($this->any())
            ->method('getUrlScheme')
            ->will($this->returnValue($urlScheme));


        $resolveUrlMethod = self::getResolveOEmbedUrl();
        $abstractClient = $this->getMockForAbstractClass('Markup\OEmbedBundle\Client\AbstractClient');

        $this->assertEquals(
            $oEmbedUrl,
            $resolveUrlMethod->invokeArgs($abstractClient, [
                $provider,
                $mediaId,
                $parameters
            ])
        );
    }

    protected static function getResolveOEmbedUrl() {
        $class = new \ReflectionClass('Markup\OEmbedBundle\Client\AbstractClient');
        $method = $class->getMethod('resolveOEmbedUrl');
        $method->setAccessible(true);
        return $method;
    }
}
