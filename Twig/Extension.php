<?php
declare(strict_types=1);

namespace Markup\OEmbedBundle\Twig;

use Markup\OEmbedBundle\Exception\OEmbedUnavailableException;
use Markup\OEmbedBundle\Exception\UnrenderableOEmbedException;
use Markup\OEmbedBundle\OEmbed\OEmbedInterface;
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
            new \Twig_SimpleFunction('markup_oembed', [$this, 'renderInlineOEmbed'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('markup_oembed_data', [$this, 'rawInlineOEmbed'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('markup_oembed_render', [$this, 'renderOEmbed'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('markup_oembed_reference', [$this, 'createReference']),
        );
    }

    /**
     * Renders an oEmbed snippet given a provider name and a media ID.
     *
     * @param  Reference $reference  A reference to an OEmbed media instance.
     * @param  array     $parameters Some optional parameters.
     * @return string
     **/
    public function renderOEmbed(Reference $reference, array $parameters = []): string
    {
        $oEmbed = $this->fetchOEmbed($reference, $parameters);
        if (!$oEmbed) {
            return '';
        }

        return $oEmbed->getEmbedCode() ?? '';
    }

    /**
     * Returns a serialized oEmbed response - useful for thumbnails etc.
     *
     * @param string $mediaId
     * @param string $provider
     * @param  array $parameters Some optional parameters.
     * @return mixed
     **/
    public function rawInlineOEmbed($mediaId, $provider, array $parameters = [])
    {
        $oEmbed = $this->fetchOEmbed($this->createReference($mediaId, $provider), $parameters);
        if (!$oEmbed) {
            return '';
        }

        return $oEmbed->jsonSerialize();
    }

    /**
     * @param Reference $reference
     * @param array $parameters
     * @return OEmbedInterface|null
     */
    private function fetchOEmbed(Reference $reference, array $parameters = [])
    {
        try {
            $oEmbed = $this->oEmbedService->fetchOEmbed($reference->getProvider(), $reference->getMediaId(), $parameters);
        } catch (OEmbedUnavailableException $e) {
            //TODO: log this, because lookups shouldn't be failing
            if ($this->shouldSquashRenderingErrors) {
                return null;
            }
            throw new UnrenderableOEmbedException(
                sprintf(
                    'Could not render an oEmbed snippet. Message: %s',
                    $e->getMessage()
                ),
                0,
                $e
            );
        }

        return $oEmbed;
    }

    /**
     * Creates an OEmbed reference from a media ID and a provider.
     **/
    public function createReference(string $mediaId, string $provider): Reference
    {
        return new Reference($mediaId, $provider);
    }

    /**
     * Renders an oEmbed snippet using the direct media ID and provider parameters.
     */
    public function renderInlineOEmbed(string $mediaId = null, string $provider, array $parameters = array()): string
    {
        if (null === $mediaId) {
            return '';
        }

        return $this->renderOEmbed($this->createReference($mediaId, $provider), $parameters);
    }

    public function getName(): string
    {
        return 'markup_oembed';
    }
}
