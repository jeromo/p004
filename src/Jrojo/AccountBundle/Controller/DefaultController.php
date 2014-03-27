<?php

namespace Jrojo\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('JrojoAccountBundle:Default:index.html.twig', array('name' => $name));
    }
}
