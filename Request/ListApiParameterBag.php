<?php

namespace Seblegall\ApiValidatorBundle\Request;

use Symfony\Component\HttpFoundation\Request;

class ListApiParameterBag extends ApiParameterBag
{
    /**
     * @var array
     */
    protected $sort = array();

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @var int
     */
    protected $limit = 0;

    /**
     * get sort parameter name, default = sort.
     *
     * @return string
     */
    public function getSortParameter()
    {
        return 'sort';
    }

    /**
     * get page parameter name, default = page.
     *
     * @return string
     */
    public function getPageParameter()
    {
        return 'page';
    }

    /**
     * get limit (items per page) parameter name, default = limit.
     *
     * @return string
     */
    public function getLimitParameter()
    {
        return 'limit';
    }

    /**
     * get start (offset) parameter name, default = start.
     *
     * @return string
     */
    public function getStartParameter()
    {
        return 'start';
    }

    /**
     * get end (offset + items per page) parameter name, default = end.
     *
     * @return string
     */
    public function getEndParameter()
    {
        return 'end';
    }

    /**
     * get all list related parameters name.
     *
     * @return array
     */
    protected function getListKeys()
    {
        return array(
            $this->getSortParameter(),
            $this->getPageParameter(),
            $this->getLimitParameter(),
            $this->getStartParameter(),
            $this->getEndParameter(),
        );
    }

    /**
     * @return array
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return ($this->limit > 0 ? (int) ($this->offset / $this->limit) : 0) + 1;
    }

    /**
     * @return int
     */
    public function getStart()
    {
        return $this->getOffset();
    }

    /**
     * @return int
     */
    public function getEnd()
    {
        return $this->offset + $this->limit - 1;
    }

    /**
     * prepare all keys to filter before filtering Request.
     *
     * @return array
     */
    protected function prepareFilteredKeys()
    {
        $keys = $this->getFilteredKeys();

        if (!empty($keys)) {
            $keys = array_merge($keys, $this->getListKeys());
        }

        return $keys;
    }

    /**
     * apply some processing to Request after populating API Parameters from Request.
     *
     * @param Request $request
     */
    protected function postPopulate(Request $request)
    {
        $this->processSort();
        $this->processPagination();
    }

    protected function processSort()
    {
        $sort = $this->has($this->getSortParameter()) ? $this->get($this->getSortParameter()) : '';
        if ($sort === null || $sort === '') {
            return array();
        }

        $rawSorts = stripos($sort, ',') === false ? array($sort) : explode(',', $sort);

        $this->sort = array_map(
            function ($v) {
                if (substr($v, 0, 1) === '-') {
                    return array(
                        'column' => substr($v, 1),
                        'order' => 'DESC',
                    );
                }

                return array(
                    'column' => $v,
                    'order' => 'ASC',
                );
            },
            $rawSorts
        );
    }

    protected function processPagination()
    {
        if ($this->has($this->getLimitParameter())) {
            $this->limit = (int) $this->get($this->getLimitParameter());
        }

        if ($this->has($this->getStartParameter())) {
            $this->offset = (int) $this->get($this->getStartParameter());
            if ($this->has($this->getEndParameter())) {
                $this->limit = ((int) $this->get($this->getEndParameter())) - $this->offset + 1;
            }
        } elseif ($this->has($this->getPageParameter())) {
            $this->offset = (((int) $this->get($this->getPageParameter())) - 1) * $this->limit;
        }
    }
}
