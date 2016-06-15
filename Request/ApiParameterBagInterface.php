<?php

namespace Seblegall\ApiValidatorBundle\Request;

use Symfony\Component\HttpFoundation\Request;

interface ApiParameterBagInterface
{
    const PARAMETERS_TYPE_ATTRIBUTES = 'attributes';
    const PARAMETERS_TYPE_HEADERS = 'headers';
    const PARAMETERS_TYPE_QUERY = 'query';
    const PARAMETERS_TYPE_GET = 'query';
    const PARAMETERS_TYPE_REQUEST = 'request';
    const PARAMETERS_TYPE_POST = 'request';
    const PARAMETERS_TYPE_FILES = 'files';
    const PARAMETERS_TYPE_COOKIES = 'cookies';

    /**
     * get Request parameters bag properties list to check for API Parameters.
     *
     * @return array
     */
    public function getFilteredType();

    /**
     * get only these parameter keys from Request.
     *
     * @return array
     */
    public function getFilteredKeys();

    /**
     * get API Parameters from Symfony HTTP Request.
     *
     * @param Request $request
     */
    public function populateFromRequest(Request $request);
}
