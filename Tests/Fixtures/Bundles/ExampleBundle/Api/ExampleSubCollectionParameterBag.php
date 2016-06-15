<?php

namespace Seblegall\ApiValidatorBundle\Tests\Fixtures\Bundles\ExampleBundle\Api;

use Seblegall\ApiValidatorBundle\Request\ApiParameterBag;

class ExampleSubCollectionParameterBag extends ApiParameterBag
{
    public function getFilteredKeys()
    {
        return array('page', 'filters');
    }
}
