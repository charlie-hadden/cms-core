<?php

namespace CMS\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use CMS\CoreBundle\Entity\Page;
use CMS\CoreBundle\Entity\Field;

class UpdatePagesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('cms:pages:update')
            ->setDescription('Update the database to build page structure from config')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $pageLoader = $container->get('cms_core.page_loader');

        // We want to find all config files that don't start with an underscore
        $finder = (new Finder())
            ->files()
            ->in($container->getParameter('cms_core.page_config_dir'))
            ->notName('_*')
        ;

        // Iterate over the files and update the database
        foreach ($finder as $file) {
            // Strip out the file extension as we don't care about that
            $extLen = strlen($file->getExtension()) + 1;
            $path = substr($file->getRelativePathname(), 0, -$extLen);

            // Get the path to automatically route and the fields for the page
            $routePath = $this->getRoutePath($path);
            $fields = $pageLoader->getFields($path);

            $output->writeLn(sprintf('%s => %s', $path, $routePath));

            $this->update($routePath, $fields);
        }
    }

    /**
     * Return the route path for the given path.
     *
     * @param  string $path
     * @return string
     */
    protected function getRoutePath($path)
    {
        $path = str_replace('\\', '/', $path);
        $path = str_replace('_', '-', $path);

        return $path;
    }

    /**
     * Updates the page in the database.
     *
     * @param  string $routePath
     * @param  array  $fields
     */
    protected function update($routePath, array $fields)
    {
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $repo = $em->getRepository('CMSCoreBundle:Page');

        // Either find or create the page
        $page = $repo->findOneByRoutePath($routePath) ?: new Page($routePath);

        // Unpublish now irrelevant fields
        foreach ($page->getPublishedFields() as $field) {
            if ($this->isIrrelevant($field, $fields)) {
                $field->unpublish();
            }
        }

        $em->persist($page);
        $em->flush();
    }

    /**
     * Checks if a field is irrelvant, given the config.
     *
     * @param  Field   $field
     * @param  array   $fields
     * @return boolean
     */
    protected function isIrrelevant(Field $field, array $fields)
    {
        return !in_array($field->getName(), array_keys($fields))
            || $field->getType() !== $fields[$field->getName()]['type'];
    }
}
