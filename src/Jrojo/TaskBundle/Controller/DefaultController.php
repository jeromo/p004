<?php

namespace Jrojo\TaskBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Jrojo\TaskBundle\Entity\Task;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('JrojoTaskBundle:Default:index.html.twig', array('name' => $name));
    }
	public function newAction(Request $request) {
		// create a task and give it some dummy data for this example
		$task = new Task();
		$task->setTask ( 'Write a blog post' );
		$task->setDueDate ( new \DateTime( 'tomorrow' ) );
		$form = $this->createFormBuilder($task )->add('task', 'text')
													->add('dueDate', 'date')
													->add('save', 'submit')
													->add('saveAndAdd', 'submit')
													->getForm ();
		if ($form->isValid()) {
			// perform some action, such as saving the task to the database
			$nextAction = $form->get('saveAndAdd')->isClicked()
				? 'task_new'
				: 'task_success';
			return $this->redirect($this->generateUrl($nextAction));
		}
		
		return $this->render ( 'JrojoTaskBundle:Default:new.html.twig', array (
				'form' => $form->createView () 
		));
	}
}
