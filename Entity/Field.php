<?php

namespace CMS\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Field
{
    /**
     * The ID of the field.
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer|null
     */
    protected $id;

    /**
     * The page the field belongs to.
     *
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="fields")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id")
     *
     * @var Page
     */
    protected $page;

    /**
     * The name of the field.
     *
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $name;

    /**
     * The type of the field.
     *
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $type;

    /**
     * The value of the field.
     *
     * @ORM\Column(type="object", nullable=true)
     *
     * @var mixed
     */
    protected $value;

    /**
     * Whether the field is published.
     *
     * @ORM\Column(type="boolean")
     *
     * @var boolean
     */
    protected $published;

    /**
     * The date the field was created.
     *
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    protected $date;

    /**
     * Constructor.
     *
     * @param Page   $page
     * @param string $name
     * @param string $type
     * @param mixed  $value
     */
    public function __construct(Page $page, $name, $type, $value)
    {
        $this->page = $page;
        $this->name = $name;
        $this->type = $type;
        $this->value = $value;
        $this->date = new \DateTime();

        $this->page->getFields()->add($this);
    }

    /**
     * Returns the ID of the field.
     *
     * @return integer|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the name of the field.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the type of the field.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return the value of the field.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns whether the field is published.
     *
     * @return boolean
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * Publishes the field and unpublished old versions.
     *
     * @return Field
     */
    public function publish()
    {
        // Unpublish the old version of this field, if any
        foreach ($this->page->getPublishedFields() as $field) {
            if ($field->getName() === $this->getName()) {
                $field->unpublish();
                break;
            }
        }

        $this->published = true;

        return $this;
    }

    /**
     * Unpublishes the field.
     *
     * @return Field
     */
    public function unpublish()
    {
        $this->published = false;

        return $this;
    }

    /**
     * Returns the date the field was created.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}
