<?php

namespace Seblegall\ApiValidatorBundle\EventListener;

use Seblegall\ApiValidatorBundle\Controller\ApiErrorController;
use Seblegall\ApiValidatorBundle\Request\ApiParameterBag;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Seblegall\ApiValidatorBundle\Annotations\ApiParameters;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiParametersListener
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    public function __construct(Reader $reader, ValidatorInterface $validator)
    {
        $this->reader = $reader;
        $this->validator = $validator;
    }

    /**
     * @param FilterControllerEvent $event
     *
     * @throws \Throwable
     * @throws \TypeError
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        $apiValidation = false;
        if ($request->attributes->has('_api_bag')) {
            $apiBagClass = $request->attributes->get('_api_bag');
            $request->attributes->remove('_api_bag');
        }

        if ($request->attributes->has('_api_validation')) {
            $apiValidation = (bool) $request->attributes->get('_api_validation');
            $request->attributes->remove('_api_validation');
        }

        if ($request->attributes->has('_api_as')) {
            $apiBagName = $request->attributes->get('_api_as');
            $request->attributes->remove('_api_as');
        }

        if (is_array($controller = $event->getController())) {
            $object = new \ReflectionObject($controller[0]);
            $method = $object->getMethod($controller[1]);

            foreach ($this->reader->getMethodAnnotations($method) as $configuration) {
                if ($configuration instanceof ApiParameters) {
                    if (isset($configuration->bag)) {
                        $apiBagClass = $configuration->bag;
                    }

                    if (isset($configuration->as)) {
                        $apiBagName = $configuration->as;
                    }

                    if (isset($configuration->validation)) {
                        $apiValidation = (bool) $configuration->validation;
                    }
                }
            }
        }

        if (!empty($apiBagClass)) {
            $apiParameterBag = class_exists($apiBagClass) ? new $apiBagClass() : new ApiParameterBag();
            $apiParameterBag->populateFromRequest($request);
            $request->attributes->set(isset($apiBagName) ? $apiBagName : 'api_parameters', $apiParameterBag);

            if ($apiValidation) {
                $errors = $this->validator->validate($apiParameterBag);
                if (count($errors) > 0) {
                    $errorsList = array();
                    $accessor = PropertyAccess::createPropertyAccessor();

                    foreach ($errors as $error) {
                        $key = preg_replace('/parameters(\[(.+)\])+/', '$1', $error->getPropertyPath());
                        $accessor->setValue($errorsList, $key, $error->getMessage());
                    }

                    $request->attributes->set('_api_errors', $errorsList);
                    $controller = new ApiErrorController();
                    $event->setController(array($controller, 'validationErrorsAction'));
                }
            }
        }
    }
}
