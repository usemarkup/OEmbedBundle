<?php

namespace Markup\OEmbedBundle\Tests\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Markup\OEmbedBundle\Client\AbstractClient;
use Markup\OEmbedBundle\Client\ClientInterface;
use Markup\OEmbedBundle\Client\GuzzleClient;
use Markup\OEmbedBundle\Exception\OEmbedUnavailableException;
use Markup\OEmbedBundle\OEmbed\OEmbedInterface;
use Markup\OEmbedBundle\Provider\ProviderInterface;
use PHPUnit\Framework\TestCase;

class GuzzleClientTest extends TestCase
{
    public function testIsClient()
    {
        $this->assertInstanceOf(ClientInterface::class, new GuzzleClient());
    }

    public function testFetchEmbed()
    {
        $html = 'the html';
        $endpointUrl = 'http://domain.com/oembed';
        $urlScheme = 'http://domain.com/media/$ID$';
        $mediaId = '42';
        $responseJson = '{"version":"1.0","type":"video","html":"'.$html.'"}';
        $mockHandler = new MockHandler([
            new Response(200, [], $responseJson),
        ]);
        $guzzle = new Client(['handler' => HandlerStack::create($mockHandler)]);
        $client = new GuzzleClient($guzzle);
        $provider = $this->createMock(ProviderInterface::class);
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
        $oEmbed = $client->fetchEmbed($provider, $mediaId);
        $this->assertInstanceOf(OEmbedInterface::class, $oEmbed);
        $this->assertEquals($html, $oEmbed->getEmbedCode());
    }

    public function testFetchEmbedThrowsUnavailableExceptionWhenRequestUnsuccessful()
    {
        $this->expectException(OEmbedUnavailableException::class);
        $endpointUrl = 'http://domain.com/oembed';
        $urlScheme = 'http://domain.com/media/$ID$';
        $mediaId = '42';
        $mockHandler = new MockHandler([
            new Response(500, [], ''),
        ]);
        $guzzle = new Client(['handler' => HandlerStack::create($mockHandler)]);
        $client = new GuzzleClient($guzzle);
        $provider = $this->createMock(ProviderInterface::class);
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
        $client->fetchEmbed($provider, $mediaId);
    }

    public function testFetchEmbedThrowsUnavailableExceptionWhenGuzzleThrows()
    {
        $this->expectException(OEmbedUnavailableException::class);
        $endpointUrl = 'http://domain.com/oembed';
        $urlScheme = 'http://domain.com/media/$ID$';
        $mediaId = '42';
        $oEmbedUrl = 'http://domain.com/oembed?url=http://domain.com/media/42';
        $mockHandler = new MockHandler([
            new RequestException("Unknown error with server", new Request('GET', $oEmbedUrl)),
        ]);
        $guzzle = new Client(['handler' => HandlerStack::create($mockHandler)]);
        $client = new GuzzleClient($guzzle);
        $provider = $this->createMock(ProviderInterface::class);
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
        $client->fetchEmbed($provider, $mediaId);
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
        $historyContainer = [];
        $history = Middleware::history($historyContainer);
        $mockHandler = new MockHandler([
            new Response(200, [], $responseJson),
        ]);
        $stack = HandlerStack::create($mockHandler);
        $stack->push($history);
        $guzzle = new Client(['handler' => $stack]);
        $client = new GuzzleClient($guzzle);
        $provider = $this->createMock(ProviderInterface::class);
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
        $client->fetchEmbed($provider, $mediaId, $parameters);
        $this->assertEquals($oEmbedUrl, (string) $historyContainer[0]['request']->getUri());
    }

    public function testFetchEmbedWithQueryStringInUrlScheme()
    {
        $endpointUrl = 'http://www.youtube.com/oembed';
        $urlScheme = 'http://www.youtube.com/watch?v=$ID$';
        $mediaId = '42';
        $parameters = array('key' => 'value');
        $oEmbedUrl = 'http://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=42%26key%3Dvalue';

        $provider = $this->createMock(ProviderInterface::class);

        $provider
            ->expects($this->any())
            ->method('getApiEndpoint')
            ->will($this->returnValue($endpointUrl));

        $provider
            ->expects($this->any())
            ->method('getUrlScheme')
            ->will($this->returnValue($urlScheme));

        $resolveUrlMethod = self::getResolveOEmbedUrl();
        $client = $this->createMock(MockClient::class);

        $this->assertEquals(
            $oEmbedUrl,
            $resolveUrlMethod->invokeArgs($client, [
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

        $provider = $this->createMock(ProviderInterface::class);

        $provider
            ->expects($this->any())
            ->method('getApiEndpoint')
            ->will($this->returnValue($endpointUrl));

        $provider
            ->expects($this->any())
            ->method('getUrlScheme')
            ->will($this->returnValue($urlScheme));


        $resolveUrlMethod = self::getResolveOEmbedUrl();
        $client = $this->createMock(MockClient::class);

        $this->assertEquals(
            $oEmbedUrl,
            $resolveUrlMethod->invokeArgs($client, [
                $provider,
                $mediaId,
                $parameters
            ])
        );
    }

    protected static function getResolveOEmbedUrl() {
        $class = new \ReflectionClass(AbstractClient::class);
        $method = $class->getMethod('resolveOEmbedUrl');
        $method->setAccessible(true);
        return $method;
    }
}
