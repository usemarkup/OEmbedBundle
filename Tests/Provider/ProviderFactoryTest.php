<?php

namespace Markup\OEmbedBundle\Tests\Provider;

use Markup\OEmbedBundle\Exception\ProviderNotFoundException;
use Markup\OEmbedBundle\Provider\ProviderFactory;
use Markup\OEmbedBundle\Provider\ProviderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
* A test for a factory for oEmbed providers.
*/
class ProviderFactoryTest extends TestCase
{
    public function setUp()
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->servicePrefix = 'markup_ombed.provider';
        $this->fac = new ProviderFactory($this->container, $this->servicePrefix);
    }

    public function testGetProviderWhenExists()
    {
        $providerName = 'i_exist';
        $provider = $this->createMock(ProviderInterface::class);
        $this->container
            ->expects($this->any())
            ->method('get')
            ->with($this->equalTo($this->servicePrefix . '.' . $providerName))
            ->will($this->returnValue($provider));
        $this->assertSame($provider, $this->fac->fetchProvider($providerName));
    }

    public function testGetProviderWhenDoesNotExistThrowsProviderNotFoundException()
    {
        $this->expectException(ProviderNotFoundException::class);
        $providerName = 'i_dinnae_exist';
        $service = $this->servicePrefix . '.' . $providerName;
        $this->container
            ->expects($this->any())
            ->method('get')
            ->with($this->equalTo($service))
            ->will($this->throwException(new \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException($service)));
        $this->fac->fetchProvider($providerName);
    }
}
