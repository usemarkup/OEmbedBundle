<?php

namespace Markup\OEmbedBundle\Client;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client as Guzzle;
use Markup\OEmbedBundle\Exception\OEmbedUnavailableException;
use Markup\OEmbedBundle\OEmbed\OEmbedFactory;
use Markup\OEmbedBundle\OEmbed\OEmbedInterface;
use Markup\OEmbedBundle\Provider\ProviderInterface;

class GuzzleClient extends AbstractClient
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $guzzle;

    public function __construct(\GuzzleHttp\ClientInterface $guzzle = null)
    {
        $this->guzzle = $guzzle;
    }

    /**
     * Fetches an oEmbed instance from the provider.
     *
     * @param  ProviderInterface $provider
     * @param  string            $mediaId
     * @param  array             $parameters
     * @return OEmbedInterface
     **/
    public function fetchEmbed(ProviderInterface $provider, $mediaId, array $parameters = [])
    {
        $oEmbedUrl = $this->resolveOEmbedUrl($provider, $mediaId, $parameters);
        try {
            $response = $this->getGuzzle()->request('GET', $oEmbedUrl);
        } catch (GuzzleException $e) {
            $response = null;
            $guzzleException = $e;
        } catch (\Exception $e) {
            $response = null;
        } catch (\Throwable $e) {
            $response = null;
        }

        if (!$response || $response->getStatusCode() != 200) {
            throw new OEmbedUnavailableException(
                sprintf(
                    'Trying to get the embed code for media ID %s from provider "%s" failed.',
                    $mediaId,
                    $provider->getName()
                ),
                0,
                (isset($guzzleException)) ? $guzzleException : null);
        }

        return (new OEmbedFactory())->createFromJson((string) $response->getBody(), $provider);
    }

    private function getGuzzle()
    {
        if (null === $this->guzzle) {
            $this->guzzle = new Guzzle([
                'defaults' => [
                    'connect_timeout' => 5,
                    'timeout' => 5,
                ]
            ]);
        }

        return $this->guzzle;
    }
}
