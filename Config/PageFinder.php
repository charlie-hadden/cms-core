<?php

namespace CMS\CoreBundle\Config;

use Symfony\Component\Finder\Finder;

class PageFinder extends Finder
{
    /**
     * Return our custom iterator so we can decorate the files.
     *
     * @return Iterator
     */
    public function getIterator()
    {
        return new PageConfigIterator(parent::getIterator());
    }
}
