<?php

namespace CMS\CoreBundle\Config;

use Iterator;

class PageConfigIterator implements Iterator
{
    /**
     * The original iterator returned by the finder's getIterator() method.
     *
     * @var Iterator
     */
    protected $finderIterator;

    /**
     * Array of the already decorated files.
     *
     * @var array
     */
    protected $decorated = [];

    /**
     * Constructor.
     *
     * @param Iterator $finderIterator
     */
    public function __construct(Iterator $finderIterator)
    {
        $this->finderIterator = $finderIterator;
    }

    /**
     * Decorate the SplFileInfo instance and return the result.
     *
     * @return PageConfiguration
     */
    public function current()
    {
        if (!isset($this->decorated[$this->key()])) {
            $current = $this->finderIterator->current();

            $this->decorated[$this->key()] = new PageConfiguration($current);
        }

        return $this->decorated[$this->key()];
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return $this->finderIterator->key();
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        $this->finderIterator->next();
    }

    /**
     * {@inheritDoc}
     */
    public function rewind()
    {
        $this->finderIterator->rewind();
    }

    /**
     * {@inheritDoc}
     */
    public function valid()
    {
        return $this->finderIterator->valid();
    }
}
