# Api Validator Bundle

A Symfony's parameters bag based data validation bundle.

## Installation

Using composer :

```sh
$ composer require seblegall/api-validator-bundle
```

Then, set up the bundle in your Symfony's `AppKernel.php` file :

```PHP
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        return array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            // ...
            new Seblegall\ApiValidatorBundle\ApiValidatorBundle(),
        );
    }
// ....
```
Finally, import the bundle routing by inserting those lines in the `app/config/routing.yml` file :

```yml
api_validator_bundle:
    resource: "@ApiValidatorBundle/Resources/config/routing.yml"
```

## Usage

When creating a new WebService where you have to validate datas before inserting it in the database :

### Create a dedicated Parameter Bag for your web service

1. Create a directory in the bundle structure. The directory can be named as you wish. For example `Api` or `ParameterBag`.

You now should have something like that :

```sh
src\
  MyBundle\
    Api\
    Controller\
    DependencyInjection\
    Ressource\
    MyBundle.php
```

2. Create a PHP Class that extends `Seblegall\ApiValidatorBundle\Request\ApiParameterBag` :

```PHP
<?php

namespace MyBundles\Api;

use Seblegall\ApiValidatorBundle\Request\ApiParameterBag;

class ExampleApiParameterBag extends ApiParameterBag
{

    // return the parameters you need to catch.
    // It could be  :
    //static::PARAMETERS_TYPE_HEADERS
    //static::PARAMETERS_TYPE_QUERY
    //static::PARAMETERS_TYPE_REQUEST
    //static::PARAMETERS_TYPE_COOKIES
    //static::PARAMETERS_TYPE_FILES
    //static::PARAMETERS_TYPE_ATTRIBUTES

    public function getFilteredType()
    {
        return array(
            static::PARAMETERS_TYPE_QUERY,
            static::PARAMETERS_TYPE_REQUEST,
        );
    }

    // return the parameters key you need to catch.
    public function getFilteredKeys()
    {
        return array('firstname', 'name', 'birthdate');
    }
}
```

### Enable your new parameter bag.

Using yml, in the bundle `routing.yml` file :

```yml
example_api:
    path:     /example/api/ws-route
    defaults: { _controller: MyBundle:Api:ws , _api_bag: MyBundle\Api\ExampleApiParameterBag }
```
Or, if you prefer, using annotations, directly in the controller :

```PHP
/**
 * @ApiParameters(bag="MyBundle\Api\ExampleApiParameterBag")
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
```

### Add validation rules

If not yet create, add a `validation.yml` file in the `Ressource` directory.

Add your own validation rules. Here is an example :

```yml
MyBundle\Api\ExampleApiParameterBag:
    properties:
        parameters:
            - Collection:
                fields:
                    firstname:
                        - Type:
                            type: string
                    name:
                        - Type:
                            type: string
                    birthdate:
                        - Type:
                            type: datetime # This could be a custom contraint
                allowMissingFields: true
                allowExtraFields: true

```

### Enable validation

Using the `routing.yml` file :

```yml
example3_api:
    path:     /example/api/ws-route-validation
    defaults: { _controller: MyBundle:Api:ws , _api_bag:MyBundle\Api\ExampleApiParameterBag, _api_validation: true }
```

Or, using annotations :

```PHP
/**
 * @ApiParameters(bag="MyBundle\Api\ExampleApiParameterBag", validation=true)
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
```

### You're done

Now, any request send to your web service will be catch and the request data will be validate before calling the controller in a way that you don't need to validate anything in the controller or in any service one the controller is called.

### What if datas are not valid ?

If datas are not valid, the bundle will return a status code `400` and an array containing an `errors` key in which you will find all the errors catch by the validator component.
