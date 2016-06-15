<?php

namespace Seblegall\ApiValidatorBundle\Tests\Fixtures\Bundles\ExampleBundle\Api;

use Seblegall\ApiValidatorBundle\Request\ApiParameterBag;

class ExampleApiParameterBag extends ApiParameterBag
{
    public function getFilteredType()
    {
        return array(
            static::PARAMETERS_TYPE_QUERY,
            static::PARAMETERS_TYPE_REQUEST,
        );
    }

    public function getFilteredKeys()
    {
        return array('page', 'max', 'sort', 'created_at');
    }
}
