<?php
/**
 * Coffee & Brackets software studio
 * @author Mohamed KRISTOU <krisstwo@gmail.com>.
 */

namespace HappybreakJeuConcoursBundle\Service;

use Doctrine\ORM\EntityManager;
use HappybreakJeuConcoursBundle\Entity\Registration as RegistrationEntity;
use HappybreakJeuConcoursBundle\Entity\RegistrationQuizzValue;

class Registration
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * Registration constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function createRegistration($data)
    {
        $registration = new RegistrationEntity();
        $registration->setSessionId(session_id());
        $registration->setCivility($data['civility']);
        $registration->setFirstName($data['first_name']);
        $registration->setLastName($data['last_name']);
        $registration->setEmail($data['email']);
        $registration->setPhone($data['phone']);
        $registration->setIsNewsletterOptin(isset($data['newsletter']) ? true : false);
        $this->em->persist($registration);

        foreach ($data as $key => $value) {
            if (strpos($key, 'question-') === 0) {
                $registrationQuizzValue = new RegistrationQuizzValue();
                $registrationQuizzValue->setRegistration($registration);

                $question = $this->em->getRepository('HappybreakJeuConcoursBundle:Question')->find(str_replace('question-',
                    '', $key));
                $registrationQuizzValue->setQuestion($question);

                $option = $this->em->getRepository('HappybreakJeuConcoursBundle:QuestionOption')->find(str_replace('option-',
                    '', $value));
                $registrationQuizzValue->setQuestionValue($option);

                $this->em->persist($registrationQuizzValue);
            }
        }

        $this->em->flush();
    }
}