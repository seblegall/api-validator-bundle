<?php

namespace Seblegall\ApiValidatorBundle\Tests\Fixtures\Bundles\ExampleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Seblegall\ApiValidatorBundle\Annotations\ApiParameters;

class ApiController extends Controller
{
    public function wsAction(Request $request)
    {
        $apiParameters = $request->attributes->get('api_parameters');

        return new JsonResponse($apiParameters->all());
    }

    /**
     * @ApiParameters(bag="Seblegall\ApiValidatorBundle\Tests\Fixtures\Bundles\ExampleBundle\Api\ExampleApiParameterBag")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function ws2Action(Request $request)
    {
        $apiParameters = $request->attributes->get('api_parameters');

        return new JsonResponse($apiParameters->all());
    }

    /**
     * @ApiParameters(bag="Seblegall\ApiValidatorBundle\Tests\Fixtures\Bundles\ExampleBundle\Api\ExampleApiParameterBag", validation=true)
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function ws3Action(Request $request)
    {
        $apiParameters = $request->attributes->get('api_parameters');

        return new JsonResponse($apiParameters->all());
    }

    /**
     * @ApiParameters(bag="Seblegall\ApiValidatorBundle\Tests\Fixtures\Bundles\ExampleBundle\Api\ExampleApiParameterBag", as="apiParam")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function ws4Action(Request $request, $apiParam)
    {
        return new JsonResponse($apiParam->all());
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function ws5Action(Request $request, $apiParam)
    {
        return new JsonResponse($apiParam->all());
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function subCollectionAction(Request $request, $apiParam)
    {
        return new JsonResponse($apiParam->all());
    }
}
