# Api Validator Bundle

A Symfony's parameters bag based data validation bundle.

## Requirements
* PHP > 5.3
* Symfony > 2.4
* Composer

## How it works

REST is great. But, most of the time, it is quite hard to think your WebServices architecture in a way that each WS is stateless. Here is an example.

If you have to design a personal data form and wish to ask the user to complete some fields such as the `name`, the `firstname`, the `birthdate`, and two addresses with, for each, an `addresse`, a `city`, a `zipcode`, and a `country`.

In that case, your database model will probably looks like that :

| user  | user_address | address |
|---|---|---|
|  id_user |  id_user |  id_adress |
|  name |  id_address |  address |
|  firstname |  |  city |
|  birthdate |  |  zipcode |
|   |   |  country |

Now, if you would like to strictly use REST standards, you will probably expose 2 WS :
 * One that POST a user
 * One that POST an address

 And then, for each, hydrate your objects, check some rules such as the name is only composed of  letters using the Validator Symfony Component. Here is an example :

```PHP
$user = new User();
$user->setName($request->get('name'))

$errors = $this->validator->validate($user);
if($errors) {
  //...
}
```

However, most of the time, this is probably not what you'll do. There are some reasons why :
* Making at least 3 calls to post a simple form could be a bad idea if you wish to improve performances
* That way, it will be quite difficult to check, for example, that the 2 required addresses are different. (You will probably have to post the first one and then check the DB to compare with the second one)
* Some of your WS will be stateless but won't make any sens as a domain.

A common way to avoid such problems is using the Symfony Form Builder in the API. Then, you need to hydrate your form with the request data and test the Symfony validator method `isValid()` on the form.

This is not a good way to deal with REST data validation.
* First of all, Symfony form are design to actually generate HTML form.
* Why would you create a form object (using a fractory) when you already have a form on the front app?
* Form validation is great when you need to actually show (html way) errors. If your goal is to send an error message to your frontend app... well... that's not the purpose.

This bundle is the solution you need.

Instead of exposing 2 WS, you will only expose one. You will be able to send all your datas as a Json object and validate the Json object instead of validate each domain object apart.

Here is an example of what you will be able to do :

Send your data :

```JSON
{
   "name":"Doe",
   "firstname":"John",
   "birthdate":"11/24/1988",
   "addresses":[
      {
         "address":"36, Disney Road",
         "city":"Paradize",
         "zipcode":"764576",
         "country":"France"
      },
      {
         "address":"37, Mickey Road",
         "city":"Paradize",
         "zipcode":"764576",
         "country":"France"
      }
   ]
}
```
And then, the bundle validate all the object for you using the Symfony Validator Component. All you need to do is creating a ParameterBag Class in which you'll define all the expected parameters (query, post, get, etc.) and created an associated validator file where you will define all the rules you need.

You could, for example, apply some unique constraints on the addresses.

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

See [documentation](./Resources/doc/index.md).

## License

`ApiValidatorBundle` is licensed under the MIT License - see the LICENSE file for details
