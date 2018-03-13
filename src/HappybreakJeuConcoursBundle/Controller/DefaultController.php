<?php

namespace HappybreakJeuConcoursBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $questions = $this->getDoctrine()->getRepository('HappybreakJeuConcoursBundle:Question')->findAll();

        return $this->render('HappybreakJeuConcoursBundle:Default:quizz.html.twig', array(
            'questions' => $questions
        ));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function ajaxSubmitQuizz(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

        }

        return new Response("Action not allowed", 400);
    }
}
