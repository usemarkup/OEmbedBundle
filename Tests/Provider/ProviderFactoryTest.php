<?php

namespace Markup\OEmbedBundle\Tests\Provider;

use Markup\OEmbedBundle\Provider\ProviderFactory;

/**
* A test for a factory for oEmbed providers.
*/
class ProviderFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->servicePrefix = 'markup_ombed.provider';
        $this->fac = new ProviderFactory($this->container, $this->servicePrefix);
    }

    public function testGetProviderWhenExists()
    {
        $providerName = 'i_exist';
        $provider = $this->getMock('Markup\OEmbedBundle\Provider\ProviderInterface');
        $this->container
            ->expects($this->any())
            ->method('get')
            ->with($this->equalTo($this->servicePrefix . '.' . $providerName))
            ->will($this->returnValue($provider));
        $this->assertSame($provider, $this->fac->fetchProvider($providerName));
    }

    public function testGetProviderWhenDoesNotExistThrowsProviderNotFoundException()
    {
        $this->setExpectedException('Markup\OEmbedBundle\Exception\ProviderNotFoundException');
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
