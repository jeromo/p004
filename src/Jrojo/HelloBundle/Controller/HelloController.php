<?php
// src/Jrojo/HelloBundle/Controller/HelloController.php
namespace Jrojo\HelloBundle\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HelloController extends Controller{
	public function indexAction($name) {
		return $this->render(
				'JrojoHelloBundle:Hello:index.html.twig',
				array('name' => $name)
		);
		//return new Response ( '<html><body>Bienvenido ' . $name . '!</body></html>' );
	}
}