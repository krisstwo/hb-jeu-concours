<?php

namespace HappybreakJeuConcoursBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Registration
 *
 * @ORM\Table(name="registration")
 * @ORM\Entity(repositoryClass="HappybreakJeuConcoursBundle\Repository\RegistrationRepository") @ORM\HasLifecycleCallbacks
 */
class Registration
{
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
     * @ORM\Column(name="civility", type="string", length=255)
     */
    private $civility;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthday", type="date")
     */
    private $birthday;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255)
     */
    private $phone;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_newsletter_optin", type="boolean")
     */
    private $isNewsletterOptin = true;

    /**
     * @var string
     *
     * @ORM\Column(name="session_id", type="string", length=255)
     */
    private $sessionId;

    /**
     * @var string
     *
     * @ORM\Column(name="facebook_user_id", type="string", length=255, nullable=true, unique=true)
     */
    private $facebookUserId;

    /**
     * @var string
     *
     * @ORM\Column(name="tracking_information", type="text", nullable=true)
     */
    private $trackingInformation;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @var RegistrationQuizzValue[]
     *
     * @ORM\OneToMany(targetEntity="HappybreakJeuConcoursBundle\Entity\RegistrationQuizzValue", mappedBy="registration")
     */
    private $quizzValues;

    /**
     * @var string
     *
     * @ORM\Column(name="quizz_values_summary", type="text")
     */
    private $quizzValuesSummary;

    /**
     * @var Share[]
     *
     * @ORM\OneToMany(targetEntity="HappybreakJeuConcoursBundle\Entity\Share", mappedBy="registration")
     */
    private $shares;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creation_date", type="datetime")
     */
    private $creationDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_date", type="datetime", nullable=true)
     */
    private $updateDate;

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->creationDate = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updateDate = new \DateTime();
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
     * Set civility
     *
     * @param string $civility
     *
     * @return Registration
     */
    public function setCivility($civility)
    {
        $this->civility = $civility;

        return $this;
    }

    /**
     * Get civility
     *
     * @return string
     */
    public function getCivility()
    {
        return $this->civility;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Registration
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Registration
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Registration
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Registration
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set isNewsletterOptin
     *
     * @param boolean $isNewsletterOptin
     *
     * @return Registration
     */
    public function setIsNewsletterOptin($isNewsletterOptin)
    {
        $this->isNewsletterOptin = $isNewsletterOptin;

        return $this;
    }

    /**
     * Get isNewsletterOptin
     *
     * @return bool
     */
    public function getIsNewsletterOptin()
    {
        return $this->isNewsletterOptin;
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @param string $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @return string
     */
    public function getFacebookUserId()
    {
        return $this->facebookUserId;
    }

    /**
     * @param string $facebookUserId
     */
    public function setFacebookUserId($facebookUserId)
    {
        $this->facebookUserId = $facebookUserId;
    }

    /**
     * @return string
     */
    public function getTrackingInformation()
    {
        return $this->trackingInformation;
    }

    /**
     * @param string $trackingInformation
     */
    public function setTrackingInformation($trackingInformation)
    {
        $this->trackingInformation = $trackingInformation;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getQuizzValues()
    {
        return $this->quizzValues;
    }

    /**
     * @return string
     */
    public function getQuizzValuesSummary()
    {
        return $this->quizzValuesSummary;
    }

    /**
     * @param string $quizzValuesSummary
     */
    public function setQuizzValuesSummary($quizzValuesSummary)
    {
        $this->quizzValuesSummary = $quizzValuesSummary;
    }

    /**
     * @return mixed
     */
    public function getShares()
    {
        return $this->shares;
    }

    /**
     * @return int
     */
    public function getTotalShares()
    {
        return count($this->shares);
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Registration
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set updateDate
     *
     * @param \DateTime $updateDate
     *
     * @return Registration
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    /**
     * Get updateDate
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }
}

