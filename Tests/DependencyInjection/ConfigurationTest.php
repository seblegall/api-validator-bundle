<?php

namespace Seblegall\ApiValidatorBundle\Tests\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Seblegall\ApiValidatorBundle\DependencyInjection\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataForProcessedConfiguration
     */
    public function testProcessedConfiguration($configs, $expectedConfig)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $this->assertEquals($expectedConfig, $config);
    }

    public function dataForProcessedConfiguration()
    {
        return array(
            // dataset #0
            array(
                array(),
                array(
                    'content_type_listener' => array(
                        'decoders' => array(
                            'json' => 'api_validator.decoder.json',
                        ),
                    ),
                ),
            ),
            // dataset #1
            array(
                array(
                    'api_validator' => array(
                        'content_type_listener' => array(
                            'decoders' => array(
                                'json' => 'api_validator.decoder.json',
                            ),
                        ),
                    ),
                ),
                array(
                     'content_type_listener' => array(
                        'decoders' => array(
                            'json' => 'api_validator.decoder.json',
                        ),
                    ),
                ),
            ),
        );
    }
}
