<?php

namespace Seblegall\ApiValidatorBundle\Tests\Request;

use Seblegall\ApiValidatorBundle\Request\ListApiParameterBag;
use Symfony\Component\HttpFoundation\Request;

class ListApiParameterBagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ListApiParameterBag
     */
    protected $bag;

    /**
     * @var ListApiParameterBag
     */
    protected $extendedBag;

    protected function setUp()
    {
        $this->bag = new ListApiParameterBag();
        $this->extendedBag = new TestListApiParameterBag();
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

        if (isset($expected['all'])) {
            $this->assertEquals($expected['all'], $bag->all());
        }

        if (isset($expected['sort'])) {
            $this->assertEquals($expected['sort'], $bag->getSort());
        }

        if (isset($expected['offset'])) {
            $this->assertEquals($expected['offset'], $bag->getOffset());
            $this->assertEquals($expected['offset'], $bag->getStart());
        }

        if (isset($expected['limit'])) {
            $this->assertEquals($expected['limit'], $bag->getLimit());
        }

        if (isset($expected['page'])) {
            $this->assertEquals($expected['page'], $bag->getPage());
        }

        if (isset($expected['end'])) {
            $this->assertEquals($expected['end'], $bag->getEnd());
        }
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
                    'all' => array(
                        'bar' => 'a',
                        'id' => 1,
                        'foo' => 'b',
                        'content' => 'lorem',
                        'sort' => '-id',
                        'foobar' => 'lorem',
                    ),
                    'sort' => array(
                        array(
                            'column' => 'id',
                            'order' => 'DESC',
                        ),
                    ),
                ),
                false,
            ),
            // dataset #1
            array(
                array('bar' => 'a', 'id' => 1),
                array('foo' => 'b', 'content' => 'lorem'),
                array('sort' => 'name', 'foobar' => 'lorem'),
                array(
                    'all' => array(
                        'bar' => 'a',
                        'foo' => 'b',
                        'sort' => 'name',
                    ),
                    'sort' => array(
                        array(
                            'column' => 'name',
                            'order' => 'ASC',
                        ),
                    ),
                ),
                true,
            ),
            // dataset #2
            array(
                array('bar' => 'a', 'id' => 1),
                array('foo' => 'b', 'content' => 'lorem'),
                array('sort' => 'name,-created_at', 'foobar' => 'lorem'),
                array(
                    'all' => array(
                        'bar' => 'a',
                        'foo' => 'b',
                        'sort' => 'name,-created_at',
                    ),
                    'sort' => array(
                        array(
                            'column' => 'name',
                            'order' => 'ASC',
                        ),
                        array(
                            'column' => 'created_at',
                            'order' => 'DESC',
                        ),
                    ),
                ),
                true,
            ),
            // dataset #3
            array(
                array('bar' => 'a'),
                array('foo' => 'b'),
                array('sort' => ''),
                array(
                    'all' => array(
                        'bar' => 'a',
                        'foo' => 'b',
                        'sort' => '',
                    ),
                    'sort' => array(
                    ),
                ),
                false,
            ),
            // dataset #4
            array(
                array(),
                array(),
                array('page' => '1', 'limit' => '100'),
                array(
                    'all' => array(
                        'page' => '1',
                        'limit' => '100',
                    ),
                    'page' => 1,
                    'limit' => 100,
                    'start' => 0,
                    'offset' => 0,
                    'end' => 99,
                ),
                false,
            ),
            // dataset #5
            array(
                array(),
                array(),
                array('page' => '5', 'limit' => '100'),
                array(
                    'all' => array(
                        'page' => '5',
                        'limit' => '100',
                    ),
                    'page' => 5,
                    'limit' => 100,
                    'start' => 400,
                    'offset' => 400,
                    'end' => 499,
                ),
                false,
            ),
            // dataset #6
            array(
                array(),
                array(),
                array('page' => '5', 'start' => '200', 'limit' => '100'),
                array(
                    'all' => array(
                        'page' => '5',
                        'start' => '200',
                        'limit' => '100',
                    ),
                    'page' => 3,
                    'limit' => 100,
                    'start' => 200,
                    'offset' => 200,
                    'end' => 299,
                ),
                false,
            ),
            // dataset #7
            array(
                array(),
                array(),
                array('page' => '6', 'start' => '200', 'limit' => '100', 'end' => '499'),
                array(
                    'all' => array(
                        'page' => '6',
                        'start' => '200',
                        'limit' => '100',
                        'end' => '499',
                    ),
                    'page' => 1,
                    'limit' => 300,
                    'start' => 200,
                    'offset' => 200,
                    'end' => 499,
                ),
                false,
            ),
        );
    }
}

class TestListApiParameterBag extends ListApiParameterBag
{
    public function getFilteredKeys()
    {
        return array_merge(array(
            'foo',
            'bar',
            'sort',
        ), $this->getListKeys());
    }
}
