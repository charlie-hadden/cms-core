<?php

namespace CMS\CoreBundle\Config;

interface PageLoaderInterface
{
    /**
     * Sets the root directory for page configuration files.
     *
     * @param string $rootDir
     */
    public function setRootDir($rootDir);

    /**
     * Return an iterator of the config files in the given sub-directory.
     *
     * @param  string $subDir
     * @return PageFinder
     */
    public function findConfigs($subDir = '');
}
