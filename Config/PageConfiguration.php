<?php

namespace CMS\CoreBundle\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;

class PageConfiguration implements ConfigurationInterface
{
    /**
     * The SplFileInfo instance for the file the config should be loaded from.
     *
     * @var SplFileInfo
     */
    protected $fileInfo;

    /**
     * The config array for the page once loaded.
     *
     * @var array|null
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param SplFileInfo $fileInfo
     */
    public function __construct(SplFileInfo $fileInfo)
    {
        $this->fileInfo = $fileInfo;
    }

    /**
     * Create a new PageConfiguration instance as a parent of the given file.
     *
     * @param  string      $file    The relative pathname of the file to load.
     * @param  SplFileInfo $oldInfo The SplFileInfo instance of the child config.
     * @return PageConfiguration
     */
    protected static function create($file, SplFileInfo $oldInfo)
    {
        // Work out the root directory from the current file
        $relPathLen = strlen($oldInfo->getRelativePath());

        if ($relPathLen) {
            $rootDir = substr($oldInfo->getPath(), 0, -$relPathLen);
            $rootDir = rtrim($rootDir, DIRECTORY_SEPARATOR);
        } else {
            $rootDir = $oldInfo->getPath();
        }

        // Work out the paths we need
        $absPath = $rootDir . '/' . $file;
        $relativePath = dirname($file);
        $relativePathname = $file;

        $info = new SplFileInfo($absPath, $relativePath, $relativePathname);

        return new self($info);
    }

    /**
     * Gets the configuration array for the page.
     *
     * @return array
     */
    public function getConfigArray()
    {
        if (!$this->config) {
            $config = Yaml::parse($this->fileInfo->getContents());

            $configTree = $this->getConfigTreeBuilder()->buildTree();

            $config = $configTree->normalize($config);
            $config = $configTree->finalize($config);

            if (isset($config['extends'])) {
                $parent = self::create($config['extends'], $this->fileInfo);

                $config = array_merge($parent->getConfigArray(), $config);
            }

            $this->config = $config;
        }

        return $this->config;
    }

    /**
     * Gets the view to use for automatic rendering of the page.
     *
     * @return string|null
     */
    public function getView()
    {
        $config = $this->getConfigArray();

        return isset($config['view']) ? $config['view'] : null;
    }

    /**
     * Gets an array of field configs for the page.
     *
     * @return array
     */
    public function getFields()
    {
        return $this->getConfigArray()['fields'];
    }

    /**
     * Gets the relative pathname of the file the configuration was loaded from.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->fileInfo->getRelativePathname();
    }

    /**
     * Return the route path for the page.
     *
     * @return string
     */
    public function getRoutePath()
    {
        // Trim the extension
        $extLen = strlen($this->fileInfo->getExtension()) + 1;
        $path = substr($this->fileInfo->getRelativePathname(), 0, -$extLen);

        // Replace required characters
        $path = str_replace('\\', '/', $path);
        $path = str_replace('_', '-', $path);

        return $path;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('page');

        $rootNode
            ->children()
                ->scalarNode('extends')->end()
                ->scalarNode('view')->end()
                ->arrayNode('fields')
                    ->isRequired()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('type')->isRequired()->end()
                            ->scalarNode('group')->end()
                            ->scalarNode('label')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
