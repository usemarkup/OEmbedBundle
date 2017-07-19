<?php

namespace Markup\OEmbedBundle\Tests\OEmbed;

use Markup\OEmbedBundle\OEmbed\OEmbed;
use Markup\OEmbedBundle\OEmbed\OEmbedInterface;
use PHPUnit\Framework\TestCase;

/**
* A test for a simple oEmbed object implementation.
*/
class OEmbedTest extends TestCase
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
        $this->assertInstanceOf(OEmbedInterface::class, $this->oEmbed);
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
        $this->expectException(\InvalidArgumentException::class);
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
