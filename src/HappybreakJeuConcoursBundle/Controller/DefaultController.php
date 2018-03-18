<?php
/**
 * Coffee & Brackets software studio
 * @author Mohamed KRISTOU <krisstwo@gmail.com>.
 */

namespace HappybreakJeuConcoursBundle\Controller;

use Facebook\Exceptions\FacebookSDKException;
use HappybreakJeuConcoursBundle\Exception\ExistingShare;
use HappybreakJeuConcoursBundle\Form\EmailShare;
use HappybreakJeuConcoursBundle\Form\Quizz;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        /**
         * @var $facebookService \HappybreakJeuConcoursBundle\Service\Facebook
         */
        $facebookService = $this->get('happybreak_jeu_concours.facebook');

        $questions = $this->getDoctrine()->getRepository('HappybreakJeuConcoursBundle:Question')->findBy(array(
            'isEnabled' => true
        ));

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
                    'birthday' => $userGraph->getBirthday()->format('dmY')
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

        return $this->render('HappybreakJeuConcoursBundle:Default:quizz.html.twig', array(
            'questions' => $questions,
            'facebookUserData' => $facebookUserData,
            'facebookLogoutUrl' => $facebookLogoutUrl
        ));
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
                array('questionRepository' => $this->getDoctrine()->getRepository('HappybreakJeuConcoursBundle:Question'), 'allow_extra_fields' => true))->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                $registration = $registrationService->createRegistration($data);

                return new JsonResponse(array('registration' => $hasherService->getHasher()->encode($registration->getId())));
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
}
