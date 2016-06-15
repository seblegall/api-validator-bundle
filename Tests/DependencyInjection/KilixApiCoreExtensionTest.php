<?php

namespace Seblegall\ApiValidatorBundle\Tests\DependencyInjection;

use Seblegall\ApiValidatorBundle\DependencyInjection\ApiValidatorExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class KilixApiCoreExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testDefault()
    {
        $container = new ContainerBuilder();
        $loader = new ApiValidatorExtension();
        $loader->load(array(array()), $container);
    }
}
