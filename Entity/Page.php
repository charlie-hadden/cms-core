<?php

namespace CMS\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;

/**
 * @ORM\Entity
 */
class Page
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
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('published', true))
        ;

        return $this->fields->matching($criteria);
    }
}
