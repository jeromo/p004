<?php
// src/Jrojo/HelloBundle/Controller/HelloController.php
namespace Jrojo\HelloBundle\Controller;

class HelloController {
	public function indexAction($name) {
		return new Response ( '<html><body>Hello ' . $name . '!</body></html>' );
	}
}