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
     * Gets the configuration array for the page found at the given path.
     *
     * @param  string $path
     * @return array
     */
    public function getConfigArray($path);
}
