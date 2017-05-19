<?php

namespace Markup\OEmbedBundle\Tests\OEmbed;

use Markup\OEmbedBundle\OEmbed\OEmbedFactory;
use Markup\OEmbedBundle\OEmbed\OEmbedInterface;
use Markup\OEmbedBundle\Provider\ProviderInterface;

/**
* A test for a factory for oEmbed objects.
*/
class OEmbedFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->fac = new OEmbedFactory();
    }

    public function testCreateFromJson()
    {
        $json = '{"version":"1.0","type":"video","html":"I am the HTML!"}';
        $provider = $this->createMock(ProviderInterface::class);
        $codeProperty = 'html';
        $provider
            ->expects($this->any())
            ->method('getEmbedCodeProperty')
            ->will($this->returnValue($codeProperty));
        $oEmbed = $this->fac->createFromJson($json, $provider);
        $this->assertInstanceOf(OEmbedInterface::class, $oEmbed);
    }
}
