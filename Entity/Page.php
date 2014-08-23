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
     * All the fields related to this page.
     *
     * @ORM\OneToMany(targetEntity="Field", mappedBy="page", orphanRemoval=true)
     *
     * @var Collection
     */
    protected $fields;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->fields = new ArrayCollection();
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
