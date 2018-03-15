<?php

namespace HappybreakJeuConcoursBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Share
 *
 * @ORM\Table(name="share")
 * @ORM\Entity(repositoryClass="HappybreakJeuConcoursBundle\Repository\ShareRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Share
{
    const SHARE_TYPE_FB = 'SHARE_TYPE_FB';
    const SHARE_TYPE_EMAIL = 'SHARE_TYPE_EMAIL';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", columnDefinition="enum('SHARE_TYPE_FB', 'SHARE_TYPE_EMAIL')")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="target", type="text")
     */
    private $target;

    /**
     * @var Registration
     *
     * @ORM\ManyToOne(targetEntity="HappybreakJeuConcoursBundle\Entity\Registration", cascade={"persist"})
     */
    private $registration;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creation_date", type="datetime")
     */
    private $creationDate;

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->creationDate = new \DateTime();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Share
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set target
     *
     * @param string $target
     *
     * @return Share
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @return Registration
     */
    public function getRegistration()
    {
        return $this->registration;
    }

    /**
     * @param Registration $registration
     */
    public function setRegistration($registration)
    {
        $this->registration = $registration;
    }

    /**
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }
}

