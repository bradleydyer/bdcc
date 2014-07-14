<?php

namespace Bdcc\Data\Pagination;

/**
 * Bdcc\Data\Pagination\Pager
 *
 * Provides methods to paginate interable objects
 *
 * @author Kris Rybak <kris.rybak@bradleydyer.com>
 */
class Pager
{
    /**
     * @var array   Array of elements
     */
    public $elements;

    /**
     * @var integer Specifies how many results should be return in a given set
     */
    private $limit;

    /**
     * @var integer Specifies the offset for results
     */
    private $offset;

    public function __construct($elements = null, $offset = null, $limit = null)
    {
        $this->elements = array();
        $this->limit    = $limit;
        $this->offset   = $offset;

        if (is_array($elements)) {
            $this->elements = $elements;
        }

        if (is_object($elements)) {
            $this->setElements($elements);
        }
    }

    /**
     * Adds element to array of elements
     *
     * @param   mixed   Element to add
     * @return  Bdcc\Data\Pagination\Pager;
     */
    public function addElement($element)
    {
        $this->elements[] = $element;

        return $this;
    }

    /**
     * Sets elements
     *
     * @param   iterable    Object or array to extract elements from
     * @return  Bdcc\Data\Pagination\Pager;
     */
    public function setElements($elements){

        foreach ($elements as $element) {
            $this->addElement($element);
        }

        return $this;
    }

    /**
     * Sets Limit
     *
     * @param   integer     Number of the results to limit set to
     * @return  Bdcc\Data\Pagination\Pager;
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Gets Limit
     *
     * @return  integer|null
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Sets Offset
     *
     * @param   integer     Number of elements to offset result set
     * @return  Bdcc\Data\Pagination\Pager;
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Gets Offset
     *
     * @return  integer|null
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Returns total number of results
     *
     * @return  integer
     */
    public function count()
    {
        return count($this->elements);
    }

    /**
     * Returns current result set
     *
     * @return  integer
     */
    public function getResults($offset = null, $limit = null)
    {
        return array_slice($this->elements, $this->offset, $this->limit);
    }
}
