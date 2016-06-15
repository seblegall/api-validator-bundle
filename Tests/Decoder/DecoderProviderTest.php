<?php

namespace Seblegall\ApiValidatorBundle\Tests\Decoder;

use Seblegall\ApiValidatorBundle\Decoder\DecoderProvider;
use Seblegall\ApiValidatorBundle\Decoder\JsonDecoder;
use Symfony\Component\DependencyInjection\Container;

class DecoderProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DecoderProvider
     */
    protected $decoderProvider;

    public function setUp()
    {
        $container = new Container();
        $container->set('api_validator.decoder.json', new JsonDecoder());
        $this->decoderProvider = new DecoderProvider(array('json' => 'api_validator.decoder.json'));
        $this->decoderProvider->setContainer($container);
    }

    /**
     * @dataProvider providerSupports
     */
    public function testSupports($format, $expected)
    {
        $this->assertEquals($expected, $this->decoderProvider->supports($format));
    }

    public function providerSupports()
    {
        return array(
            array('json', true),
            array('html', false),
        );
    }

    public function testGetDecoder()
    {
        $this->assertInstanceOf('\Seblegall\ApiValidatorBundle\Decoder\JsonDecoder', $this->decoderProvider->getDecoder('json'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Format 'markdown' is not supported by Seblegall\ApiValidatorBundle\Decoder\DecoderProvider.
     */
    public function testGetDecoderInexistentFormat()
    {
        $this->decoderProvider->getDecoder('markdown');
    }
}
