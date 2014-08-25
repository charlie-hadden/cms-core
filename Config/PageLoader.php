<?php

namespace CMS\CoreBundle\Config;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\NodeInterface;
use Symfony\Component\Finder\Finder;

class PageLoader implements PageLoaderInterface
{
    /**
     * The root directory for page configuration.
     *
     * @var string
     */
    protected $rootDir;

    /**
     * The FileLocator to use for loading page config files.
     *
     * @var FileLocator
     */
    protected $locator;

    /**
     * The YAML parser to use when loading config files.
     *
     * @var Parser
     */
    protected $yaml;

    /**
     * The config tree used to normalize page configuration.
     *
     * @var NodeInterface
     */
    protected $configTree;

    /**
     * Cache of loaded config arrays.
     *
     * @var array
     */
    protected $loadedConfig = [];

    /**
     * {@inheritDoc}
     */
    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;

        $this->locator = new FileLocator($rootDir);
    }

    /**
     * {@inheritDoc}
     */
    public function findFiles($subDir = '')
    {
        return (new Finder())
            ->files()
            ->in($this->rootDir . '/' . $subDir)
            ->notName('_*')
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigArray($path)
    {
        if (!isset($this->loadedConfig[$path])) {
            $this->loadConfig($path);
        }

        return $this->loadedConfig[$path];
    }

    /**
     * {@inheritDoc}
     */
    public function getView($path)
    {
        $config = $this->getConfigArray($path);

        return isset($config['view']) ? $config['view'] : null;
    }

    /**
     * {@inheritDoc}
     */
    public function getFields($path)
    {
        // Load the config for this page
        $config = $this->getConfigArray($path);
        $fields = $config['fields'];

        // Recurse if the page extends anything to get parent fields
        if (isset($config['extends'])) {
            $parentFields = $this->getFields($config['extends']);

            $fields = array_merge($parentFields, $fields);
        }

        return $fields;
    }

    /**
     * Return the config tree.
     *
     * @return NodeInterface
     */
    protected function getConfigTree()
    {
        if (!$this->configTree) {
            $config = new PageConfiguration();

            $this->configTree = $config->getConfigTreeBuilder()->buildTree();
        }

        return $this->configTree;
    }

    /**
     * Load the config for the path.
     *
     * @param  string $path
     */
    protected function loadConfig($path)
    {
        // Find the file
        $file = $this->locator->locate($path . '.yml');

        // Make sure we have a YAML parser
        if (!$this->yaml) {
            $this->yaml = new Parser();
        }

        // Load the configuration
        $config = $this->yaml->parse(file_get_contents($file));

        // Normalize the configuration
        $configTree = $this->getConfigTree();
        $config = $configTree->normalize($config);

        $this->loadedConfig[$path] = $configTree->finalize($config);
    }
}
