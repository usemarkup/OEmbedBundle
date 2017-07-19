<?php

namespace Markup\OEmbedBundle\Client;

/**
* A superclass for client implementations.
*/
abstract class AbstractClient implements ClientInterface
{
    use ResolveOEmbedUrlTrait;
}
