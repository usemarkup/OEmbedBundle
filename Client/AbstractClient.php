<?php

namespace Markup\OEmbedBundle\Client;

use Markup\OEmbedBundle\Provider\ProviderInterface;

/**
* A superclass for client implementations.
*/
abstract class AbstractClient implements ClientInterface
{
    use ResolveOEmbedUrlTrait;
}
