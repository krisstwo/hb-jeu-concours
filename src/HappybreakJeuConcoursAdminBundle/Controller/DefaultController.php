<?php

namespace HappybreakJeuConcoursAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('HappybreakJeuConcoursAdminBundle:Default:index.html.twig');
    }
}
