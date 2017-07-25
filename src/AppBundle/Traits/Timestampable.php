<?php
/**
 * Created by PhpStorm.
 * User: ughostephan
 * Date: 19/07/2017
 * Time: 17:59
 */

namespace AppBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adds created at and updated at timestamps to entities. Requires that entities are
 * marked with the HasLifecycleCallbacks annotation.
 *
 * Don't forget to add annotation to your class : ORM\HasLifecycleCallbacks
 */
trait Timestampable
{

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @param \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function _timestampable_prePersist()
    {
        $this->setCreatedAt(new \DateTime());
    }

    /**
     * @ORM\PreUpdate
     */
    public function _timestampable_preUpdate()
    {
        $this->setUpdatedAt(new \DateTime());
    }
}