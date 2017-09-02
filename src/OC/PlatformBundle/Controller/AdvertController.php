<?php

// src/OC/PlatformBundle/Controller/AdvertController.php

namespace OC\PlatformBundle\Controller; 

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OC\PlatformBundle\Entity\Advert; 


class AdvertController extends Controller{
	
	public function indexAction($page){
		if(isset($page) && !empty($page) && $page < 1) throw new NotFoundHttpException('Page"'.$page.'"inexistant.'); 

		//l'entity manager
		$em = $this->getDoctrine()->getManager(); 
    	$listAdverts = $em->getRepository("OCPlatformBundle:Advert")->findAll();
		//templating est un service. Ce service peut être appelé dans tous les controlleurs
		return new Response($this->get('templating')->render('OCPlatformBundle:Advert:index.html.twig', array('listAdverts'=>$listAdverts )) );
	}



	public function viewAction($id){
		//recuperation de l'annonce d'id $id afin de l'afficher
		$em = $this->getDoctrine()->getManager();
		$advert = $em->getRepository("OCPlatformBundle:Advert")->find($id); 

		if($advert === null) throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas !!"); 
		
		return new Response($this->get('templating')->render('OCPlatformBundle:Advert:view.html.twig', array('advert'=>$advert)));
	}



	public function addAction(Request $request){

		//creation de l'entité advert
		$advert = new Advert(); 
		$advert->setTitle("Recherche developpeur web"); 
		$advert->setAuthor("toto"); 
		$advert->setContent("Nous recherchons un developpeur web sur Nancy...blabla...."); 
		//recuperation de l'entité manager
		$em = $this->getDoctrine()->getManager(); 

		//etape 1, on persiste l'entite
		$em->persist($advert); 

		//etape 2, tout ce qui a été persisté avant
		$em->flush();


		if($request->isMethod('POST')){
			$session = $request->getSession();
			//Vue que j ai la session je peux desormais utiliser la methode getFlashBag()
			$session->getFlashBag()->add('info','Annonce prochainement enregistrée !'); 
			$session->getFlashBag()->add('info','OUI Oui Annonce prochainement enregistrée !'); 
			return $this->redirectToRoute('oc_platform_view',array('id'=>$advert->getId())); 
		}

		//test du service antispam
		$antispam = $this->container->get('oc_platform.antispam');
		$message = " je suis je suis je suis je suis je suis je suis je suis je suis je suis";  
		if($antispam->isSpam($message)){
			throw new \Exception('votre message est un spam'); 
		}

		//test de l'envoi d'email 

		$envoiEmail =  $this->container->get("oc_platform.envoiEmail");
		$envoiEmail->envoi("Ajout d'annonce: ".$advert->getTitle(),"amara.sanoh.hawa@gmail.com","sanohawa@gmail.com", "___test___ Envoi email ___test___
			".$advert->getContent()."
			Contact: ".$advert->getAuthor());  
		return new Response($this->get('templating')->render('OCPlatformBundle:Advert:add.html.twig')); 
	}




	public function editAction($id, Request $request){
		if($request->isMethod('POST')){
			
			$request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.'); 
			return $this->redirectToRoute('oc_platform_view',array('id'=>$id)); 
		}

		//recuperation de l'annonce d'id $id afin de l'editer
    	$em = $this->getDoctrine()->getManager(); 
    	$advert = $em->getRepository("OCPlatformBundle:Advert")->find($id);
    	$advert->setTitle("Comptable"); 
    	$advert->setContent("Nous recherchons un comptable sur Nancy....Beaucoup de blabla....blabla...toujours plus de blabla...");

    	$em->flush();

		return new Response($this->get('templating')->render('OCPlatformBundle:Advert:edit.html.twig', array('advert'=>$advert))); 
	}




	public function deleteAction($id){
		//recuperation en DB de l'annonce d'id $id
		$em = $this->getDoctrine()->getManager();
		$advert = $em->getRepository("OCPlatformBundle:Advert")->find($id);
		//supprimer maintenant $advert de la base de donnees
		$em->remove($advert); 

		$em->flush();

		return $this->render('OCPlatformBundle:Advert:delete.html.twig', array('id'=>$id)); 
	}


	public function menuAction(){
		//recuperation de l'entity manager
		$em = $this->getDoctrine()->getManager(); 
		//recuperer tous les tuples de l'entité advert
		$listAdverts = $em->getRepository("OCPlatformBundle:Advert")->findAll(); 
		
		return new Response($this->get('templating')->render('OCPlatformBundle:Advert:menu.html.twig', array(
		// Tout l'intérêt est ici : le contrôleur passe
		// les variables nécessaires au template !
		'listAdverts' => $listAdverts
		)));
	}
}


