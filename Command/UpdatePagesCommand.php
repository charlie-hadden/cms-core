<?php

namespace CMS\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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

        // Iterate over the files and update the database
        foreach ($pageLoader->findConfigs() as $file) {
            $routePath = $file->getRoutePath();

            $output->writeLn(sprintf('%s => %s', $file->getPath(), $routePath));

            $this->update($routePath, $file->getFields());
        }
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
