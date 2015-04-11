<?php

namespace Markup\OEmbedBundle\Twig;

use Markup\OEmbedBundle\Exception\OEmbedUnavailableException;
use Markup\OEmbedBundle\Exception\UnrenderableOEmbedException;
use Markup\OEmbedBundle\OEmbed\Reference;
use Markup\OEmbedBundle\Service\OEmbedService;

/**
 * A Twig extension that can render oEmbed snippets.
 */
class Extension extends \Twig_Extension
{
    /**
     * @var OEmbedService
     **/
    private $oEmbedService;

    /**
     * @var bool
     **/
    private $shouldSquashRenderingErrors;

    /**
     * @param oEmbedService $oEmbedService
     * @param bool          $shouldSquashRenderingErrors
     **/
    public function __construct(OEmbedService $oEmbedService, $shouldSquashRenderingErrors)
    {
        $this->oEmbedService = $oEmbedService;
        $this->shouldSquashRenderingErrors = $shouldSquashRenderingErrors;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('markup_oembed', array($this, 'renderInlineOEmbed'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('markup_oembed_data', array($this, 'rawInlineOEmbed'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('markup_oembed_render', array($this, 'renderOEmbed'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('markup_oembed_reference', array($this, 'createReference')),
        );
    }

    /**
     * Renders an oEmbed snippet given a provider name and a media ID.
     *
     * @param  Reference $reference  A reference to an OEmbed media instance.
     * @param  array     $parameters Some optional parameters.
     * @return string
     **/
    public function renderOEmbed(Reference $reference, array $parameters = array())
    {
        $oEmbed = $this->fetchOEmbed($reference, $parameters);
        if (!$oEmbed) {
            return '';
        }

        return $oEmbed->getEmbedCode();
    }

    /**
     * Returns a serialized oEmbed response - useful for thumbnails etc.
     *
     * @param string $mediaId
     * @param string $provider
     * @param  array $parameters Some optional parameters.
     * @return array
     **/
    public function rawInlineOEmbed($mediaId, $provider, array $parameters = array())
    {
        $oEmbed = $this->fetchOEmbed($this->createReference($mediaId, $provider), $parameters);
        if (!$oEmbed) {
            return '';
        }

        return $oEmbed->jsonSerialize();
    }

    /**
     * @param Reference $reference
     * @param array     $parameters
     * @return \Markup\OEmbedBundle\OEmbed\OEmbed|null
     */
    private function fetchOEmbed(Reference $reference, array $parameters = array())
    {
        try {
            $oEmbed = $this->oEmbedService->fetchOEmbed($reference->getProvider(), $reference->getMediaId(), $parameters);
        } catch (OEmbedUnavailableException $e) {
            //TODO: log this, because lookups shouldn't be failing
            if ($this->shouldSquashRenderingErrors) {
                return null;
            }
            throw new UnrenderableOEmbedException(sprintf('Could not render an oEmbed snippet. Message: %s', $e->getMessage()), 0, $e);
        }

        return $oEmbed;
    }

    /**
     * Creates an OEmbed reference from a media ID and a provider.
     *
     * @param  string    $mediaId
     * @param  string    $provider
     * @return Reference
     **/
    public function createReference($mediaId, $provider)
    {
        return new Reference($mediaId, $provider);
    }

    /**
     * Renders an oEmbed snippet using the direct media ID and provider parameters.
     *
     * @param string $mediaId
     * @param string $provider
     * @param array $parameters
     * @return string
     */
    public function renderInlineOEmbed($mediaId, $provider, array $parameters = array())
    {
        return $this->renderOEmbed($this->createReference($mediaId, $provider), $parameters);
    }

    public function getName()
    {
        return 'markup_oembed';
    }
}
