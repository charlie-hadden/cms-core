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
     * @return \Symfony\Component\Finder\Finder
     */
    public function findFiles($subDir = '');

    /**
     * Gets the configuration array for the page found at the given path.
     *
     * @param  string $path
     * @return array
     */
    public function getConfigArray($path);

    /**
     * Gets the view to use for automatic rendering of the page.
     *
     * @param  string $path
     * @return string|null
     */
    public function getView($path);

    /**
     * Gets an array of field configs for the page, with parents taken into
     * account.
     *
     * @param  string $path
     * @return array
     */
    public function getFields($path);
}
