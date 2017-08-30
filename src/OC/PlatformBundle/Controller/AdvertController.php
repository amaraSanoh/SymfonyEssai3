<?php

// src/OC/PlatformBundle/Controller/AdvertController.php

namespace OC\PlatformBundle\Controller; 

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class AdvertController extends Controller{
	
	public function indexAction($page){
		if(isset($page) && !empty($page) && $page < 1) throw new NotFoundHttpException('Page"'.$page.'"inexistant.'); 

		 // Notre liste d'annonce en dur

    		$listAdverts = array(
      			array(
        			'title'   => 'Recherche développpeur Symfony',
        			'id'      => 1,
        			'author'  => 'Alexandre',
        			'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
        			'date'    => new \Datetime()),
      			array(
        			'title'   => 'Mission de webmaster',
        			'id'      => 2,
        			'author'  => 'Hugo',
        			'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
       				'date'    => new \Datetime()),
      			array(
        			'title'   => 'Offre de stage webdesigner',
        			'id'      => 3,
        			'author'  => 'Mathieu',
        			'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
        			'date'    => new \Datetime())
    		);
		//templating est un service. Ce service peut être appelé dans tous les controlleurs
		return new Response($this->get('templating')->render('OCPlatformBundle:Advert:index.html.twig', array('listAdverts'=>$listAdverts )) );
	}



	public function viewAction($id){
		 $advert = array(	
      			'title'   => 'Recherche développpeur Symfony2',
     		 	'id'      => $id,
      			'author'  => 'Alexandre',
			'content' => 'Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…',
			'date'    => new \Datetime()

    		);
		return new Response($this->get('templating')->render('OCPlatformBundle:Advert:view.html.twig', array('advert'=>$advert)));
	}



	public function addAction(Request $request){

		if($request->isMethod('POST')){
			$session = $request->getSession();
			//Vue que j ai la session je peux desormais utiliser la methode getFlashBag()
			$session->getFlashBag()->add('info','Annonce prochainement enregistrée !'); 
			$session->getFlashBag()->add('info','OUI Oui Annonce prochainement enregistrée !'); 
			return $this->redirectToRoute('oc_platform_view',array('id'=>5)); 
		}

		$antispam = $this->container->get('oc_platform.antispam');
		$message = " je suis je suis je suis je suis je suis je suis je suis je suis je suis";  
		if($antispam->isSpam($message)){
			throw new \Exception('votre message est un spam'); 
		}

		//envoi d'email essai

		$envoiEmail =  $this->container->get("oc_platform.envoiEmail");
		$envoiEmail->envoi("Ajout annonce","amara.sanoh.hawa@gmail.com","sanohawa@gmail.com", "___test___ Envoi email ___test___");  
		return new Response($this->get('templating')->render('OCPlatformBundle:Advert:add.html.twig')); 
	}




	public function editAction($id, Request $request){
		if($request->isMethod('POST')){
			
			$request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.'); 
			return $this->redirectToRoute('oc_platform_view',array('id'=>$id)); 
		}

		
    		$advert = array(
      			'title'   => 'Recherche développpeur Symfony',
      			'id'      => $id,
      			'author'  => 'Alexandre',
      			'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
      			'date'    => new \Datetime()
    		);
		return new Response($this->get('templating')->render('OCPlatformBundle:Advert:edit.html.twig', array('advert'=>$advert))); 
	}




	public function deleteAction($id){
		return $this->render('OCPlatformBundle:Advert:delete.html.twig', array('id'=>$id)); 
	}

	public function menuAction(){
		// On fixe en dur une liste ici, bien entendu par la suite
		// on la récupérera depuis la BDD !
		$listAdverts = array(
			array('id' => 2, 'title' => 'Recherche développeur Symfony'),
			array('id' => 5, 'title' => 'Mission de webmaster'),
			array('id' => 9, 'title' => 'Offre de stage webdesigner')
	);

	return new Response($this->get('templating')->render('OCPlatformBundle:Advert:menu.html.twig', array(
		// Tout l'intérêt est ici : le contrôleur passe
		// les variables nécessaires au template !
		'listAdverts' => $listAdverts
		)));
	}
}


