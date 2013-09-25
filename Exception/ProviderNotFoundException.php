<?php

namespace Markup\OEmbedBundle\Exception;

/**
* An exception representing when provider could not be found in a fetch.
*/
class ProviderNotFoundException extends \RuntimeException implements ExceptionInterface
{
    public function __construct($providerName)
    {
        parent::__construct(sprintf('The oEmbed provider "%s" could not be found.', $providerName));
    }
}
