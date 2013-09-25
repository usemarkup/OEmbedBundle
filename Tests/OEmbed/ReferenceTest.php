<?php

namespace Markup\OEmbedBundle\Tests\OEmbed;

use Markup\OEmbedBundle\OEmbed\Reference;

/**
* Test for a reference to an OEmbed instance
*/
class ReferenceTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->mediaId = 'asldkfkgjfghghf';
        $this->provider = 'youtube';
        $this->ref = new Reference($this->mediaId, $this->provider);
    }

    public function testGetMediaId()
    {
        $this->assertEquals($this->mediaId, $this->ref->getMediaId());
    }

    public function testGetProvider()
    {
        $this->assertEquals($this->provider, $this->ref->getProvider());
    }
}
