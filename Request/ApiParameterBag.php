<?php

namespace Seblegall\ApiValidatorBundle\Request;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class ApiParameterBag extends ParameterBag implements ApiParameterBagInterface
{
    /**
     * override this method to customize which request parameters bags will be used.
     *
     * @return array
     */
    public function getFilteredType()
    {
        return array(
            static::PARAMETERS_TYPE_HEADERS,
            static::PARAMETERS_TYPE_QUERY,
            static::PARAMETERS_TYPE_REQUEST,
        );
    }

    /**
     * override this methods to select only some parameters key.
     *
     * @return array
     */
    public function getFilteredKeys()
    {
        return array();
    }

    /**
     * prepare all keys to filter before filtering Request.
     *
     * @return array
     */
    protected function prepareFilteredKeys()
    {
        return $this->getFilteredKeys();
    }

    /**
     * apply some processing to Request before reading API Parameters from Request.
     *
     * @param Request $request
     */
    protected function prePopulate(Request $request)
    {
    }

    /**
     * apply some processing to Request after populating API Parameters from Request.
     *
     * @param Request $request
     */
    protected function postPopulate(Request $request)
    {
    }

    /**
     * @param Request $request
     */
    public function populateFromRequest(Request $request)
    {
        $filteredKeys = $this->prepareFilteredKeys();
        $allowedTypes = array(
            static::PARAMETERS_TYPE_HEADERS,
            static::PARAMETERS_TYPE_QUERY,
            static::PARAMETERS_TYPE_REQUEST,
            static::PARAMETERS_TYPE_COOKIES,
            static::PARAMETERS_TYPE_FILES,
            static::PARAMETERS_TYPE_ATTRIBUTES,
        );

        $this->prePopulate($request);

        foreach ($this->getFilteredType() as $type) {
            if (in_array($type, $allowedTypes)) {
                $params = empty($filteredKeys) ?
                    $request->$type->all() :
                    array_intersect_key($request->$type->all(), array_flip($filteredKeys));

                if ($type == static::PARAMETERS_TYPE_HEADERS) {
                    $params = array_map(function ($v) {
                        return is_array($v) ? implode(';', $v) : $v;
                    }, $params);
                }

                $this->add($params);
            }
        }

        $this->postPopulate($request);
    }
}
