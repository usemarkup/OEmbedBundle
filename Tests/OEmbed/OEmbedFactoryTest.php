<?php

namespace Markup\OEmbedBundle\Tests\OEmbed;

use Markup\OEmbedBundle\OEmbed\OEmbedFactory;

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
        $provider = $this->getMock('Markup\OEmbedBundle\Provider\ProviderInterface');
        $codeProperty = 'html';
        $provider
            ->expects($this->any())
            ->method('getEmbedCodeProperty')
            ->will($this->returnValue($codeProperty));
        $oEmbed = $this->fac->createFromJson($json, $provider);
        $this->assertInstanceOf('Markup\OEmbedBundle\OEmbed\OEmbedInterface', $oEmbed);
    }
}
