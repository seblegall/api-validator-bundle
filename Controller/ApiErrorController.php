<?php

namespace Seblegall\ApiValidatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class ApiErrorController extends Controller
{
    /**
     * Called when parameter bag isn't valid.
     * This controller returns an array of errors.
     *
     * @param Request $request
     *
     * @return JsonResponse|Response
     */
    public function validationErrorsAction(Request $request)
    {
        $format = $request->get('_format', 'json');
        $apiErrors = $request->attributes->get('_api_errors', array());

        switch ($format) {
            case 'xml':
                $encoders = array(new XmlEncoder('result'), new JsonEncoder());
                $normalizers = array(new GetSetMethodNormalizer());

                $serializer = new Serializer($normalizers, $encoders);
                $xml = $serializer->serialize(array('errors' => $apiErrors), 'xml');

                $response = new Response($xml, 400, array('Content-Type' => 'application/xml'));
                break;

            case 'json':
            default:
                $response = new JsonResponse(
                    array(
                        'errors' => $apiErrors,
                    ),
                    400
                );
        }

        return $response;
    }
}
