<?php

namespace Markup\OEmbedBundle\Tests\OEmbed;

use Markup\OEmbedBundle\OEmbed\OEmbed;

/**
* A test for a simple oEmbed object implementation.
*/
class OEmbedTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->type = 'video';
        $this->title = 'This is a video!';
        $this->code = '<p>Hi! I am a video!</p>';
        $this->properties = array(
            'type' => 'video',
            'title' => $this->title,
            'code' => $this->code,
        );
        $this->embedProperty = 'code';
        $this->oEmbed = new OEmbed($this->type, $this->properties, $this->embedProperty);
    }

    public function testIsOEmbed()
    {
        $this->assertInstanceOf('Markup\OEmbedBundle\OEmbed\OEmbedInterface', $this->oEmbed);
    }

    public function testGetType()
    {
        $this->assertEquals($this->type, $this->oEmbed->getType());
    }

    public function testGetAndHasProperty()
    {
        $this->assertFalse($this->oEmbed->has('unknown'));
        $this->assertTrue($this->oEmbed->has('title'));
        $this->assertNull($this->oEmbed->get('unknown'));
        $this->assertEquals($this->title, $this->oEmbed->get('title'));
    }

    public function testAll()
    {
        $this->assertEquals($this->properties, $this->oEmbed->all());
    }

    public function testGetEmbedCode()
    {
        $this->assertEquals($this->code, $this->oEmbed->getEmbedCode());
    }

    public function testConstructWithUnknownTypeThrowsInvalidArgumentException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $type = 'unknown';
        new OEmbed($type, $this->properties, $this->embedProperty);
    }

    public function testJsonSerialize()
    {
        $expected = array(
            'type' => $this->type,
            'properties' => $this->properties,
            'embed_property' => $this->embedProperty,
        );
        $this->assertEquals($expected, $this->oEmbed->jsonSerialize());
    }
}
