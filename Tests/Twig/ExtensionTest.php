<?php

namespace Markup\OEmbedBundle\Tests\Twig;

use Markup\OEmbedBundle\Exception\UnrenderableOEmbedException;
use Markup\OEmbedBundle\OEmbed\OEmbedInterface;
use Markup\OEmbedBundle\OEmbed\Reference;
use Markup\OEmbedBundle\Service\OEmbedService;
use Markup\OEmbedBundle\Twig\Extension;

/**
* A test for a Twig extension for rendering oEmbed snippets.
*/
class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->service = $this->createMock(OEmbedService::class);
        $this->shouldSquashRenderingErrors = true;
        $this->extension = new Extension($this->service, $this->shouldSquashRenderingErrors);
    }

    public function testIsTwigExtension()
    {
        $this->assertInstanceOf(\Twig_ExtensionInterface::class, $this->extension);
    }

    public function testRenderOEmbedSnippetWhenEmbedWorks()
    {
        $provider = 'youtube';
        $mediaId = 'gangnam_style';
        $reference = new Reference($mediaId, $provider);
        $oEmbed = $this->createMock(OEmbedInterface::class);
        $content = 'heeeeeeeey';
        $oEmbed
            ->expects($this->any())
            ->method('getEmbedCode')
            ->will($this->returnValue($content));
        $this->service
            ->expects($this->any())
            ->method('fetchOEmbed')
            ->with($this->equalTo($provider), $this->equalTo($mediaId))
            ->will($this->returnValue($oEmbed));
        $this->assertEquals($content, $this->extension->renderOEmbed($reference));
    }

    public function testRenderOEmbedReturnsEmptyStringOnFailIfShouldSquash()
    {
        $provider = 'youtube';
        $mediaId = 'gangnam_style';
        $reference = new Reference($mediaId, $provider);
        $this->service
            ->expects($this->any())
            ->method('fetchOEmbed')
            ->with($this->equalTo($provider), $this->equalTo($mediaId))
            ->will($this->throwException(new \Markup\OEmbedBundle\Exception\OEmbedUnavailableException()));
        $this->assertEquals('', $this->extension->renderOEmbed($reference));
    }

    public function testRenderOEmbedBlowsUpOnFailIfNoSquash()
    {
        $shouldSquashRenderingErrors = false;
        $extension = new Extension($this->service, $shouldSquashRenderingErrors);
        $provider = 'youtube';
        $mediaId = 'gangnam_style';
        $reference = new Reference($mediaId, $provider);
        $this->service
            ->expects($this->any())
            ->method('fetchOEmbed')
            ->with($this->equalTo($provider), $this->equalTo($mediaId))
            ->will($this->throwException(new \Markup\OEmbedBundle\Exception\OEmbedUnavailableException()));
        $this->expectException(UnrenderableOEmbedException::class);
        $extension->renderOEmbed($reference);
    }

    public function testCreateReference()
    {
        $mediaId = 'asldkjfdgh';
        $provider = 'vimeo';
        $reference = $this->extension->createReference($mediaId, $provider);
        $this->assertInstanceOf(Reference::class, $reference);
        $this->assertEquals($mediaId, $reference->getMediaId());
        $this->assertEquals($provider, $reference->getProvider());
    }

    public function testRenderInlineOEmbed()
    {
        $mediaId = 'asldkjfdgh';
        $provider = 'vimeo';
        $oEmbed = $this->createMock(OEmbedInterface::class);
        $content = 'heeeeeeeey';
        $oEmbed
            ->expects($this->any())
            ->method('getEmbedCode')
            ->will($this->returnValue($content));
        $this->service
            ->expects($this->any())
            ->method('fetchOEmbed')
            ->with($this->equalTo($provider), $this->equalTo($mediaId))
            ->will($this->returnValue($oEmbed));
        $this->assertEquals($content, $this->extension->renderInlineOEmbed($mediaId, $provider));
    }
}
