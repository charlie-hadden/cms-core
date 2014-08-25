<?php

namespace CMS\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;

use ArrayAccess;
use BadMethodCallException;

/**
 * @ORM\Entity
 */
class Page implements ArrayAccess
{
    /**
     * The id of the page.
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer|null
     */
    protected $id;

    /**
     * The route path used for automatic routing.
     *
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $routePath;

    /**
     * All the fields related to this page.
     *
     * @ORM\OneToMany(targetEntity="Field", mappedBy="page", orphanRemoval=true)
     *
     * @var Collection
     */
    protected $fields;

    /**
     * The published fields related to this page.
     *
     * @var Collection|null
     */
    protected $publishedFields;

    /**
     * Constructor.
     *
     * @param string $routePath
     */
    public function __construct($routePath)
    {
        $this->fields = new ArrayCollection();

        $this->setRoutePath($routePath);
    }

    /**
     * Returns the ID of the page.
     *
     * @return integer|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the route path of the page.
     *
     * @return string
     */
    public function getRoutePath()
    {
        return $this->routePath;
    }

    /**
     * Sets the route path of the page.
     *
     * @param  string $routePath
     * @return Page
     */
    public function setRoutePath($routePath)
    {
        $this->routePath = $routePath;

        return $this;
    }

    /**
     * Returns the fields belonging to the page.
     *
     * @return Collection
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Returns the currently published fields belonging to the page.
     *
     * @return Collection
     */
    public function getPublishedFields()
    {
        if (!$this->publishedFields) {
            $criteria = Criteria::create()
                ->where(Criteria::expr()->eq('published', true))
            ;

            $this->publishedFields = $this->fields->matching($criteria);
        }

        return $this->publishedFields;
    }

    /**
     * Flushes the cached published fields so they are reloaded on next access.
     */
    public function flushPublishedFields()
    {
        $this->publishedFields = null;
    }

    /**
     * Checks if a published field with the given name exists for this page.
     *
     * @param string $name
     */
    public function offsetExists($name)
    {
        foreach ($this->getPublishedFields() as $field) {
            if ($field->getName() === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the value of the published field with this name, or null if no
     * such field exists.
     *
     * @param  string $name
     * @return mixed
     */
    public function offsetGet($name)
    {
        foreach ($this->getPublishedFields() as $field) {
            if ($field->getName() === $name) {
                return $field->getValue();
            }
        }

        return null;
    }

    /**
     * Don't allow setting by offset.
     *
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        throw new BadMethodCallException(sprintf(
            'Setting by offset is not allowed for %s.',
            get_class($this)
        ));
    }

    /**
     * Don't allow unsetting by offset.
     *
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        throw new BadMethodCallException(sprintf(
            'Unsetting by offset is not allowed for %s.',
            get_class($this)
        ));
    }
}
