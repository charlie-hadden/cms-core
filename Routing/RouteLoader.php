<?php

namespace CMS\CoreBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

use CMS\CoreBundle\Config\PageLoaderInterface;
use CMS\CoreBundle\Config\PageConfiguration;

class RouteLoader extends Loader
{
    /**
     * The page loader service used to find page configs.
     *
     * @var PageLoaderInterface
     */
    protected $pageLoader;

    /**
     * Constructor.
     *
     * @param PageLoaderInterface $pageLoader
     */
    public function __construct(PageLoaderInterface $pageLoader)
    {
        $this->pageLoader = $pageLoader;
    }

    /**
     * {@inheritDoc}
     */
    public function load($resource, $type = null)
    {
        $routes = new RouteCollection();

        // Register a route for every page that specifies a view
        foreach ($this->pageLoader->findConfigs($resource) as $config) {
            if ($config->getView()) {
                $pattern = '/' . $config->getRoutePath();
                $defaults = ['_controller' => 'CMSCoreBundle:Static:static'];
                $name = $this->getRouteName($config);

                $routes->add($name, new Route($pattern, $defaults));
            }
        }

        return $routes;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($resource, $type = null)
    {
        return $type === 'cms_pages';
    }

    /**
     * Generate a name for the route based on the file's location.
     *
     * @param  PageConfiguration $config
     * @return string
     */
    protected function getRouteName(PageConfiguration $config)
    {
        $name = $config->getRoutePath();
        $name = str_replace(['/', '-'], '_', $name);

        return 'page_' . $name;
    }
}
