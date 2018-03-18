<?php
/**
 * Coffee & Brackets software studio
 * @author Mohamed KRISTOU <krisstwo@gmail.com>.
 */

namespace HappybreakJeuConcoursBundle\Service;

use Doctrine\ORM\EntityManager;
use DrewM\MailChimp\MailChimp;
use HappybreakJeuConcoursBundle\Entity\Registration as RegistrationEntity;
use HappybreakJeuConcoursBundle\Entity\RegistrationQuizzValue;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Container;

class Registration
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Registration constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em, Container $container, LoggerInterface $logger)
    {
        $this->em        = $em;
        $this->container = $container;
        $this->logger    = $logger;
    }

    /**
     * @param $data
     *
     * @return RegistrationEntity
     */
    public function createRegistration($data)
    {
        $registration = new RegistrationEntity();
        $registration->setSessionId(session_id());
        $registration->setCivility($data['civility']);
        $registration->setFirstName($data['first_name']);
        $registration->setLastName($data['last_name']);
        $registration->setEmail($data['email']);
        $registration->setPhone($data['phone']);
        if(!empty($data['facebook_user_id']))
            $registration->setFacebookUserId($data['facebook_user_id']);
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

        if ($this->container->hasParameter('mailchimp_enable') && $this->container->getParameter('mailchimp_enable')) {
            $MailChimp = new MailChimp($this->container->getParameter('mailchimp_api_key'));

            $result = $MailChimp->post(sprintf('lists/%s/members', $this->container->getParameter('mailchimp_list_id')),
                array(
                    'email_address' => $registration->getEmail(),
                    'status' => 'subscribed',
                    'merge_fields' => array(
                        'FNAME' => $registration->getFirstName(),
                        'LNAME' => $registration->getLastName()
                    )
                ));

            if ($MailChimp->success()) {
                $this->logger->debug('MailChimp : ' . print_r($result, true));
            } else {
                $this->logger->error('MailChimp : ' . $MailChimp->getLastError());
            }
        }

        return $registration;
    }
}