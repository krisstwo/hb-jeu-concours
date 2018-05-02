<?php
/**
 * Coffee & Brackets software studio
 * @author Mohamed KRISTOU <krisstwo@gmail.com>.
 */

namespace HappybreakJeuConcoursBundle\Controller;

use Facebook\Exceptions\FacebookSDKException;
use HappybreakJeuConcoursBundle\Exception\ExistingShare;
use HappybreakJeuConcoursBundle\Form\EmailShare;
use HappybreakJeuConcoursBundle\Form\FacebookShare;
use HappybreakJeuConcoursBundle\Form\Quizz;
use HappybreakJeuConcoursBundle\Form\SaveQuizzState;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $this->getSession();

        $questions = $this->getDoctrine()->getRepository('HappybreakJeuConcoursBundle:Question')->findBy(array(
            'isEnabled' => true
        ));

        // Fetch Facebook user data if logged in

        /**
         * @var $facebookService \HappybreakJeuConcoursBundle\Service\Facebook
         */
        $facebookService = $this->get('happybreak_jeu_concours.facebook');

        $facebookUserData = null;
        $facebookLogoutUrl = null;
        try {
            if ($facebookService->hasAccessToken()) {
                $userGraph        = $facebookService->getUserData();
                $facebookUserData = array(
                    'gender' => $userGraph->getGender(),
                    'name' => $userGraph->getName(),
                    'first_name' => $userGraph->getFirstName(),
                    'last_name' => $userGraph->getLastName(),
                    'email' => $userGraph->getEmail(),
                    'birthday' => $userGraph->getBirthday()->format('Y-m-d'),
                    'id' => $userGraph->getId()
                );

                $facebookLogoutUrl = $this->generateUrl('happybreak_jeu_concours_facebook_logout');
            }
        } catch (FacebookSDKException $e) {
            /**
             * @var $logger LoggerInterface
             */
            $logger = $this->container->get('logger');

            $logger->error($e->getMessage());
        }

        // Fetch current quizz state if any
        /**
         * @var $registrationService \HappybreakJeuConcoursBundle\Service\Registration
         */
        $registrationService = $this->get('happybreak_jeu_concours.registration');

        // Assign tracking information
        $trackingInformation = '';
        $queryParams = $request->query->all();
        foreach ($queryParams as $key => $value) {
            if (strpos($key, 'utm_') === 0) {
                $trackingInformation .= $key . '=' . $value . "\n";
            }
        }

        return $this->render('HappybreakJeuConcoursBundle:Default:quizz.html.twig', array(
            'questions' => $questions,
            'quizzState' => $registrationService->getQuizzCurrentState(),
            'termsAndConditionsURL' => $this->container->getParameter('terms_and_conditions_url'),
            'isFacebookEnabled' => $this->container->getParameter('facebook_enable'),
            'facebookUserData' => $facebookUserData,
            'facebookLogoutUrl' => $facebookLogoutUrl,
            'recaptchaSiteKey' => $this->container->getParameter('recaptcha_site_key'),
            'facebookAppId' => $this->container->getParameter('facebook_app_id'),
            'facebookShareUrl' => ! empty($this->container->getParameter('facebook_share_url')) ? $this->container->getParameter('facebook_share_url') : $this->generateUrl('happybreak_jeu_concours_homepage', array(), UrlGeneratorInterface::ABSOLUTE_URL),
            'trackingInformation' => $trackingInformation,
            'isGameEnabled' => $this->container->getParameter('game_enable'),
        ));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function ajaxSaveCurrentQuizzStateAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            /**
             * @var $registrationService \HappybreakJeuConcoursBundle\Service\Registration
             * @var $hasherService \HappybreakJeuConcoursBundle\Service\Hasher
             * @var Form $form
             */
            $registrationService = $this->get('happybreak_jeu_concours.registration');
            $hasherService       = $this->get('happybreak_jeu_concours.hasher');
            $form                = $this->get('form.factory')->createNamedBuilder('', SaveQuizzState::class, array(),
                array('questionRepository' => $this->getDoctrine()->getRepository('HappybreakJeuConcoursBundle:Question'), 'allow_extra_fields' => true))->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                $registrationService->saveQuizzState($data);

                return new JsonResponse(array());
            } else {
                return new JsonResponse(array(
                    'error' => 'Données invalides',
                    'details' => (string)$form->getErrors(true)
                ));
            }
        }

        return new Response("Action not allowed", 400);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function ajaxSubmitQuizzAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            /**
             * @var $registrationService \HappybreakJeuConcoursBundle\Service\Registration
             * @var $hasherService \HappybreakJeuConcoursBundle\Service\Hasher
             * @var Form $form
             */
            $registrationService = $this->get('happybreak_jeu_concours.registration');
            $hasherService       = $this->get('happybreak_jeu_concours.hasher');
            $form                = $this->get('form.factory')->createNamedBuilder('', Quizz::class, array(),
                array(
                    'container' => $this->container,
                    'allow_extra_fields' => true
                ))->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                $registration = $registrationService->createRegistration($data);

                return new JsonResponse(array('registration' => $hasherService->getHasher()->encode($registration->getId())));
            } else {
                return new JsonResponse(array(
                    'error' => 'Données invalides',
                    'details' => str_replace('ERROR: ', '', (string)$form->getErrors(true))
                ));
            }
        }

        return new Response("Action not allowed", 400);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function ajaxEmailShareAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            /**
             * @var $shareService \HappybreakJeuConcoursBundle\Service\Share
             */
            $shareService = $this->get('happybreak_jeu_concours.share');

            /**
             * @var Form $form
             */
            $form = $this->get('form.factory')->createNamedBuilder('', EmailShare::class, array(),
                array('container' => $this->container, 'allow_extra_fields' => true))->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                try {
                    $shareService->shareToEmail($data);
                } catch (ExistingShare $e) {
                    return new JsonResponse(array(
                        'error' => 'Invitation déjà envoyée, merci de choisir une adresse email.'
                    ));
                }

                return new JsonResponse(array());
            } else {
                return new JsonResponse(array(
                    'error' => 'Données invalides',
                    'details' => (string)$form->getErrors(true)
                ));
            }
        }

        return new Response("Action not allowed", 400);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function ajaxFacebookShareAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            /**
             * @var $shareService \HappybreakJeuConcoursBundle\Service\Share
             */
            $shareService = $this->get('happybreak_jeu_concours.share');

            /**
             * @var Form $form
             */
            $form = $this->get('form.factory')->createNamedBuilder('', FacebookShare::class, array(),
                array('container' => $this->container, 'allow_extra_fields' => true))->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                $shareService->shareToFacebook($data);

                return new JsonResponse(array());
            } else {
                return new JsonResponse(array(
                    'error' => 'Données invalides',
                    'details' => (string)$form->getErrors(true)
                ));
            }
        }

        return new Response("Action not allowed", 400);
    }

    /**
     * @return Session
     */
    private function getSession()
    {
        $session = new Session(! empty(session_id()) ? new PhpBridgeSessionStorage() : null);
        ! $session->isStarted() && $session->start();

        return $session;
    }
}
