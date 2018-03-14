<?php
/**
 * Coffee & Brackets software studio
 * @author Mohamed KRISTOU <krisstwo@gmail.com>.
 */

namespace HappybreakJeuConcoursBundle\Controller;

use HappybreakJeuConcoursBundle\Form\Quizz;
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
        $questions = $this->getDoctrine()->getRepository('HappybreakJeuConcoursBundle:Question')->findBy(array(
            'isEnabled' => true
        ));

        return $this->render('HappybreakJeuConcoursBundle:Default:quizz.html.twig', array(
            'questions' => $questions
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
             */
            $registrationService = $this->get('happybreak_jeu_concours.registration');

            /**
             * @var Form $form
             */
            $form = $this->get('form.factory')->createNamedBuilder('', Quizz::class, array(),
                array('questionRepository' => $this->getDoctrine()->getRepository('HappybreakJeuConcoursBundle:Question'), 'allow_extra_fields' => true))->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                $registrationService->createRegistration($data);

                return new JsonResponse($data);
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
