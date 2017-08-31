<?php

namespace OC\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException; 

class CoreController extends Controller{

	public function  indexAction(){
		return new Response($this->get('templating')->render('OCCoreBundle:Core:index.html.twig')); 
	}

	public function contactAction(Request $request){
		$session = $request->getSession(); 
		$session->getFlashBag()->add("information","La page de contact n'est pas disponible pour le moment, merci de revenir plus tard");
		return $this->redirectToRoute('oc_core_home');
	}
}