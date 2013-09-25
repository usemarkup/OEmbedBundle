<?php

namespace Markup\OEmbedBundle\Exception;

/**
* An exception representing when the content returned from an oEmbed service does not contain valid content.
*/
class InvalidOEmbedContentException extends \RuntimeException implements ExceptionInterface
{}
