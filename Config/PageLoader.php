<?php

namespace CMS\CoreBundle\Config;

class PageLoader implements PageLoaderInterface
{
    /**
     * The root directory for page configuration.
     *
     * @var string
     */
    protected $rootDir;

    /**
     * {@inheritDoc}
     */
    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * {@inheritDoc}
     */
    public function findConfigs($subDir = '')
    {
        return (new PageFinder())
            ->files()
            ->in($this->rootDir . '/' . $subDir)
            ->notName('_*')
        ;
    }
}
