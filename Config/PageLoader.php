<?php

namespace CMS\CoreBundle\Config;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\NodeInterface;

class PageLoader implements PageLoaderInterface
{
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
     * {@inheritDoc}
     */
    public function setRootDir($rootDir)
    {
        $this->locator = new FileLocator($rootDir);
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigArray($path)
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

        return $config;
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
}
