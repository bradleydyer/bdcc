<?php

namespace Bdcc\Data\Pagination;

/**
 * Bdcc\Data\Pagination\ResultBag
 *
 * Provides a wrapper that represents a returned subset from a query
 *
 * @author Kris Rybak <kris.rybak@bradleydyer.com>
 */
class ResultBag
{
    /**
     * @var array   Result subset. Subset of elements returned by a query
     *              performed on a set with given offset and limit
     */
    public $subset;

    /**
     * @var integer Total number of results in set as a result of query
     *              without taking into account limit and offset (total number
     *              of matching results)
     */
    public $setCount;

    /**
     * @var integer Offset used in last query
     */
    private $offset;

    /**
     * @var integer Limit used in last query
     */
    private $limit;

    public function __construct($subset = array())
    {
        $this->setSubset($subset);
    }

    /**
     * Sets subset
     *
     * @param   array       $subset
     * @return  ResultBag
     */
    public function setSubset(array $subset = array())
    {
        foreach ($subset as $result) {
            $this->addSubsetElement($result);
        }

        return $this;
    }

    /**
     * Add element to subset
     *
     * @param   object      $result
     * @return  ResultBag
     */
    public function addSubsetElement($result)
    {
        $this->subset[] = $result;

        return $this;
    }

    /**
     * Gets subset
     *
     * @return  array
     */
    public function getSubset()
    {
        return $this->subset;
    }

    /**
     * Sets setCount
     *
     * @param   integer     $numberOfResults
     * @return  ResultBag
     */
    public function setSetCount($numberOfResults)
    {
        $this->setCount = $numberOfResults;

        return $this;
    }

    /**
     * Gets setCount
     *
     * @return  integer
     */
    public function getSetCount()
    {
        return $this->setCount;
    }

    /**
     * Sets offset
     *
     * @param   integer     $offset
     * @return  ResultBag
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Gets offset
     *
     * @return  integer
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Sets limit
     *
     * @param   integer     $limit
     * @return  ResultBag
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Gets limit
     *
     * @return  integer
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Returns total number of subsets needed to return all results in paginated
     * form for a given setCount using limit and offset.
     *
     * @return  integer
     */
    public function getNumberOfSubsets()
    {
        if (!is_null($this->getLimit()) && ($this->getLimit() !== 0)) {
            return (int) ceil($this->getSetCount() / $this->getLimit());
        }

        return 1;
    }
}
