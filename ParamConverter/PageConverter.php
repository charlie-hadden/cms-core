<?php

namespace CMS\CoreBundle\ParamConverter;

use Symfony\Component\HttpFoundation\Request;
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
        $path = $this->convertPath($request->getPathInfo());
        $page = $this->repo->findOneByRoutePath($path);

        if ($page) {
            $request->attributes->set($configuration->getName(), $page);

            return true;
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
     * Convert the path info to the same format used for identifying pages.
     *
     * @param  string $pathInfo
     * @return string
     */
    protected function convertPath($pathInfo)
    {
        // Append 'index' if the path ends with a slash
        if ($pathInfo[strlen($pathInfo) - 1] === '/') {
            $pathInfo .= 'index';
        }

        $pathInfo = str_replace('_', '-', $pathInfo);

        return trim($pathInfo, '/');
    }
}
