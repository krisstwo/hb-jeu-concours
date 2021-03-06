<?php
/**
 * Coffee & Brackets software studio
 * @author Mohamed KRISTOU <krisstwo@gmail.com>.
 */

namespace HappybreakJeuConcoursBundle\Service;

use Doctrine\ORM\EntityManager;
use HappybreakJeuConcoursBundle\Exception\ExistingShare;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Container;
use HappybreakJeuConcoursBundle\Entity\Share AS ShareEntity;

class Share
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
     * @var Mailer
     */
    private $mailer;

    /**
     * Registration constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em, Container $container, LoggerInterface $logger, Mailer $mailer)
    {
        $this->em        = $em;
        $this->container = $container;
        $this->logger    = $logger;
        $this->mailer    = $mailer;
    }

    /**
     * @param $data
     *
     * @throws ExistingShare
     * @throws \Exception
     */
    public function shareToEmail($data)
    {
        /**
         * @var $registration \HappybreakJeuConcoursBundle\Entity\Registration
         */
        $registration = $this->em->getRepository('HappybreakJeuConcoursBundle:Registration')->find($data['registration']);

        if ( ! $registration) {
            throw new \Exception('Registration not found');
        }

        //Send one time only
        $existingShare = $this->em->getRepository('HappybreakJeuConcoursBundle:Share')->findOneBy(array(
            'registration' => $registration->getId(),
            'type' => ShareEntity::SHARE_TYPE_EMAIL,
            'target' => $data['share-email']
        ));

        if($existingShare)
            throw new ExistingShare(sprintf('Share existing for registration %s and email %s', $registration->getId(), $data['share-email']));

        $subjectTags = array(
            '{first-name}' => $registration->getFirstName(),
            '{last-name}' => $registration->getLastName()
        );

        //Send email
        $mailConfig = array(
            'to' => $data['share-email'],
            'template' => 'HappybreakJeuConcoursBundle:Email:share.html.twig',
            'subject' => str_replace(array_keys($subjectTags), array_values($subjectTags),
                $this->container->getParameter('share_email_subject')),
            'from' => $this->container->getParameter('share_email_from_address'),
            'fromName' => $this->container->getParameter('share_email_from_name'),
            'params' => array(
                'registration' => $registration,
                'ctaLink' => $this->container->getParameter('share_email_cta_link')
            )
        );
        $this->mailer->sendMessage($mailConfig, 'text/html');

        //Log share
        $share = new ShareEntity();
        $share->setRegistration($registration);
        $share->setType(ShareEntity::SHARE_TYPE_EMAIL);
        $share->setTarget($data['share-email']);

        $this->em->persist($share);
        $this->em->flush();
    }

    /**
     * @param $data
     *
     * @throws \Exception
     */
    public function shareToFacebook($data)
    {
        /**
         * @var $registration \HappybreakJeuConcoursBundle\Entity\Registration
         */
        $registration = $this->em->getRepository('HappybreakJeuConcoursBundle:Registration')->find($data['registration']);

        if ( ! $registration) {
            throw new \Exception('Registration not found');
        }

        // Record one time only
        $existingShare = $this->em->getRepository('HappybreakJeuConcoursBundle:Share')->findOneBy(array(
            'registration' => $registration->getId(),
            'type' => ShareEntity::SHARE_TYPE_FB,
            'target' => $data['target']
        ));

        if ( ! $existingShare) {
            //Log share
            $share = new ShareEntity();
            $share->setRegistration($registration);
            $share->setType(ShareEntity::SHARE_TYPE_FB);
            $share->setTarget($data['target']);

            $this->em->persist($share);
            $this->em->flush();
        }
    }
}