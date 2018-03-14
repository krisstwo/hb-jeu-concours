<?php

namespace HappybreakJeuConcoursBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RegistrationQuizzValue
 *
 * @ORM\Table(name="registration_quizz_value")
 * @ORM\Entity(repositoryClass="HappybreakJeuConcoursBundle\Repository\RegistrationQuizzValueRepository")
 */
class RegistrationQuizzValue
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
     * @var Registration
     *
     * @ORM\ManyToOne(targetEntity="HappybreakJeuConcoursBundle\Entity\Registration", cascade={"persist"})
     */
    private $registration;

    /**
     * @var Question
     *
     * @ORM\ManyToOne(targetEntity="HappybreakJeuConcoursBundle\Entity\Question", cascade={"persist"})
     */
    private $question;

    /**
     * @var QuestionOption
     *
     * @ORM\ManyToOne(targetEntity="HappybreakJeuConcoursBundle\Entity\QuestionOption", cascade={"persist"})
     */
    private $questionValue;


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
     * @return Question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param Question $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * @return QuestionOption
     */
    public function getQuestionValue()
    {
        return $this->questionValue;
    }

    /**
     * @param QuestionOption $questionValue
     */
    public function setQuestionValue($questionValue)
    {
        $this->questionValue = $questionValue;
    }
}

