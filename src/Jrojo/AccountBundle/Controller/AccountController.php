<?php
// src/Jrojo/AccountBundle/Controller/AccountController.php
namespace Jrojo\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Jrojo\AccountBundle\Form\Type\RegistrationType;
use Jrojo\AccountBundle\Form\Model\Registration;
use Jrojo\AccountBundle\Form\Type\UserLoginType;
use Jrojo\AccountBundle\Form\Model\Login;
use Jrojo\AccountBundle\Entity\User;

class AccountController extends Controller
{

  	public function registerAction()
	{
		$registration = new Registration();
		$form = $this->createForm(new RegistrationType(), $registration, array(
				'action' => $this->generateUrl('account_create'),
		));
		return  $this->render(
				'JrojoAccountBundle:Account:register.html.twig',
				array('form' => $form->createView(),
					'title'	=>	'Regístrese en CLub Tenis',	
					'header_body' =>'Rellene los siguientes datos'
					)
		);
	}
	public function loginAction()
	{
		$login = new User();
		$form = $this->createForm(new UserLoginType(), $login, array(
				'action' => $this->generateUrl('account_check'),
		));
		return  $this->render(
				'JrojoAccountBundle:Account:login.html.twig',
				array('form' => $form->createView(),
					'title'	=>	'Identifíquese en Club Tenis',	
					'header_body' =>'Rellene los siguientes datos'
					)
		);
	}
	public function createAction(Request $request)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$form = $this->createForm(new RegistrationType(), new Registration());
		$form->handleRequest($request);
		if ($form->isValid()) {
			$registration = $form->getData();
			
			$em->persist($registration->getUser());
			$em->flush();
			return $this->redirect("Dame un error");
		}
		return  $this->render(
				'JrojoAccountBundle:Account:register.html.twig',
				array('form' => $form->createView(),
					'title'	=>	'Regístrese en Club Tenis',	
					'header_body' =>'Rellene los siguientes datos'
					)
		);
	}
	public function checkAction(Request $request)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$form = $this->createForm(new UserLoginType(), new User());
		$form->handleRequest($request);
		if ($form->isValid()) {
			$usuario = $form->getData();
			//Buscar el usuario, y comprobar su password
//			$em->persist($registration->getUser());
//			$em->flush();
			$repository = $this->getDoctrine()->getRepository('JrojoAccountBundle:User');
			$encontrado = $repository->findOneByemail($usuario->getEmail());
			
			var_dump($encontrado);exit();
				
			return $this->redirect("Dame un error");
		}
		return  $this->render(
				'JrojoAccountBundle:Account:login.html.twig',
				array('form' => $form->createView(),
					'title'	=>	'Regístrese en Club Tenis',	
					'header_body' =>'Rellene los siguientes datos'
					)
		);
	}
}