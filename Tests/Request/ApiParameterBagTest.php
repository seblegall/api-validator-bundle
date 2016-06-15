<?php

namespace Seblegall\ApiValidatorBundle\Tests\Request;

use Seblegall\ApiValidatorBundle\Request\ApiParameterBag;
use Symfony\Component\HttpFoundation\Request;

class ApiParameterBagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ApiParameterBag
     */
    protected $bag;

    /**
     * @var ApiParameterBag
     */
    protected $extendedBag;

    protected function setUp()
    {
        $this->bag = new ApiParameterBag();
        $this->extendedBag = new TestApiParameterBag();
    }

    /**
     * @dataProvider providerPopulateFromRequest
     */
    public function testPopulateFromRequest($get, $post, $headers, $expected, $extended = false)
    {
        $request = new Request();
        $request->query->add($get);
        $request->request->add($post);
        $request->headers->add($headers);

        $bag = $extended ? $this->extendedBag : $this->bag;
        $bag->populateFromRequest($request);

        $this->assertEquals($expected, $bag->all());
    }

    /**
     * @dataProvider
     */
    public function providerPopulateFromRequest()
    {
        return array(
            // dataset #0
            array(
                array('bar' => 'a', 'id' => 1),
                array('foo' => 'b', 'content' => 'lorem'),
                array('sort' => '-id', 'foobar' => 'lorem'),
                array(
                    'bar' => 'a',
                    'id' => 1,
                    'foo' => 'b',
                    'content' => 'lorem',
                    'sort' => '-id',
                    'foobar' => 'lorem',
                ),
                false,
            ),
            // dataset #1
            array(
                array('bar' => 'a', 'id' => 1),
                array('foo' => 'b', 'content' => 'lorem'),
                array('sort' => '-id', 'foobar' => 'lorem'),
                array(
                    'bar' => 'a',
                    'foo' => 'b',
                    'sort' => '-id',
                ),
                true,
            ),
        );
    }
}

class TestApiParameterBag extends ApiParameterBag
{
    public function getFilteredKeys()
    {
        return array(
            'foo',
            'bar',
            'sort',
        );
    }
}
