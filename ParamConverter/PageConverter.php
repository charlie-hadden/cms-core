<?php

namespace CMS\CoreBundle\ParamConverter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Doctrine\ORM\EntityRepository;

class PageConverter implements ParamConverterInterface
{
    /**
     * The entity repository to get the page from.
     *
     * @var EntityRepository
     */
    protected $repo;

    /**
     * Constructor.
     *
     * @param EntityRepository $repo
     */
    public function __construct(EntityRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * {@inheritDoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $path = $this->getRoutePath($request->attributes->get('_controller'));

        if ($path) {
            $page = $this->repo->findOneByRoutePath($path);

            if ($page) {
                $request->attributes->set($configuration->getName(), $page);

                return true;
            }

            throw new NotFoundHttpException(sprintf(
                'Page with route path "%s" not found.',
                $path
            ));
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() === 'CMS\\CoreBundle\\Entity\\Page';
    }

    /**
     * Convert a controller name into a route path.
     *
     * @param  string $controller
     * @return string|false
     */
    protected function getRoutePath($controller)
    {
        $parts = $this->getControllerParts($controller);

        if (!$parts) {
            return false;
        }

        return $this->formatRoutePath(implode('/', $parts));
    }

    /**
     * Get the parts (bundle, controller, action) of the controller name.
     *
     * @param  string $controller
     * @return array|false
     */
    protected function getControllerParts($controller)
    {
        $match = preg_match(
            '/.*\\\\([a-zA-Z0-9_]+)Bundle\\\\.*\\\\([a-zA-Z0-9_]+)Controller::([a-zA-Z0-9_]+)Action/',
            $controller,
            $parts
        );

        if ($match !== 1) {
            return false;
        }

        return array_slice($parts, 1);
    }

    /**
     * Fix the casing of the path and insert hyphens where needed.
     *
     * @param  string $path
     * @return string
     */
    protected function formatRoutePath($path)
    {
        return preg_replace_callback(
            '/(.?)([A-Z])/',
            function ($matches) {
                // Convert the matching character to lowercase
                $char = strtolower($matches[2]);

                // If the preceding character is not a slash then we should
                // insert a hyphen
                if ($matches[1] && $matches[1] !== '/') {
                    return $matches[1] . '-' . $char;
                }

                return $matches[1] . $char;
            },
            $path
        );
    }
}
