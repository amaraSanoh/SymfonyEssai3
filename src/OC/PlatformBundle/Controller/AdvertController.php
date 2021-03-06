<?php

// src/OC/PlatformBundle/Controller/AdvertController.php

namespace OC\PlatformBundle\Controller; 

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\AdvertSkill;
use OC\PlatformBundle\Form\AdvertType;
use OC\PlatformBundle\Form\AdvertEditType; 

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
//la classe pour le controle d'acces à travers les annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

//les classe pour mes propres evenements
use OC\PlatformBundle\Event\PlatformEvents;
use OC\PlatformBundle\Event\MessagePostEvent;




class AdvertController extends Controller{
	
	
	public function indexAction($page){
		if(isset($page) && !empty($page) && $page < 1) throw new NotFoundHttpException('Page"'.$page.'"inexistant.'); 

		$nbAdvertPerPage = 3; 
		//l'entity manager
		$em = $this->getDoctrine()->getManager(); 
    	//$listAdverts = $em->getRepository("OCPlatformBundle:Advert")->findAll();
    	$listAdverts = $em->getRepository("OCPlatformBundle:Advert")->getAdverts($page,$nbAdvertPerPage); //est ma methode dans le repository. elle return un Paginateur
    	$nbAdvertInDB = $listAdverts->count(); //le nombre d'advert dans la base de données
    	$nbPage = $nbAdvertInDB/$nbAdvertPerPage; 
		//templating est un service. Ce service peut être appelé dans tous les controlleurs
		return new Response($this->get('templating')->render('OCPlatformBundle:Advert:index.html.twig', array('listAdverts'=>$listAdverts, 'nbPage'=>$nbPage, 'page'=> $page, 'user'=> $this->getUser()) ));

		//$this->getUser() donne l'utilisateur courant derriere un pare-feu
	}



	public function viewAction($id){
		//recuperation de l'annonce d'id $id afin de l'afficher
		$em = $this->getDoctrine()->getManager();
		$advert = $em->getRepository("OCPlatformBundle:Advert")->find($id);
		//$advert = $em->getRepository("OCPlatformBundle:Advert")->getAdvertWithApplications($id); 
		if($advert === null) throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas !!"); 
		

    	//$listDesCandidatures = $em->getRepository("OCPlatformBundle:Application")->findBy(array('advert' => $advert)); 
    	$listDesCandidatures = $advert->getApplications(); 
    	//le tableau en parametre de findBy() est un tableau de criteres pour recuperer les applications.

    	 // On récupère maintenant la liste des AdvertSkill
		$listAdvertSkills = $em->getRepository('OCPlatformBundle:AdvertSkill')->findBy(array('advert' => $advert));

		return new Response($this->get('templating')->render('OCPlatformBundle:Advert:view.html.twig', array('advert'=>$advert, 'listDesCandidatures'=>$listDesCandidatures, 'count'=>$advert->getNbApplications(), 'listAdvertSkills'=>$listAdvertSkills)));
	}


	//c'est l'annotation du controle d'acces 
	

	/**
	 *@Security("has_role('ROLE_AUTEUR')")
	 */
	public function addAction(Request $request){

		//on crée un objet Advert 
		$advert = new Advert(); 

		// On crée le formulaire grâce au service form.factory 
		$formulaire = $this->get('form.factory')->create(AdvertType::class, $advert); //pour dire c'est advert que tu hydrate

		// Pour l'instant, pas de candidatures, catégories, etc., on les gérera plus tard
		
		//si la requete est en POST cad que les valeurs ont été entrées en cliquant sur le bouton valider
		if($request->isMethod('POST')){


			 // On fait le lien Requête <-> Formulaire
      		// À partir de maintenant, la variable $advert contient les valeurs entrées dans le formulaire par le visiteur
      		$formulaire->handleRequest($request); 

      		// On vérifie que les valeurs entrées sont correctes
      		if($formulaire->isValid()){

      			//DECLECNCHER UN EVENEMENT

      			//cree l'evenement avec ses 2 parametres 
      			$event = new MessagePostEvent($advert->getContent(), $this->getUser());

      			//on declenche l'evenement
      			$this->get('event_dispatcher')->dispatch(PlatformEvents::POST_MESSAGE, $event); 

      			//FIN DU DECLENCHEMENT

      			// On récupère ce qui a été modifié par le ou les listeners, ici le message
				$advert->setContent($event->getMessage());


      			//on peut à présent enregistrer notre objet dans la base de données
      			//recuperation de l'entité manager
				$em = $this->getDoctrine()->getManager();
				$em->persist($advert); 
				$em->flush();  

				$session = $request->getSession();
				//Vue que j ai la session je peux desormais utiliser la methode getFlashBag()
				$session->getFlashBag()->add('infoAjoutAdvert','Annonce Enregistée avec succes!'); 
				
				return $this->redirectToRoute('oc_platform_view',array('id'=>$advert->getId())); 
      		}
		}

		
		// On passe la méthode createView() du formulaire à la vue
		// afin qu'elle puisse afficher le formulaire toute seule
		return new Response($this->get('templating')->render('OCPlatformBundle:Advert:add.html.twig', array('form'=>$formulaire->createView()))); 


		
	}


	//cette annotation est une speciale qui veut juste savoir que l'utilisateur est authentifié
	//@Security("has_role('IS_AUTHENTICATED_REMEMBERED')")

	/**
	 *@Security("has_role('ROLE_AUTEUR')")
	 */
	public function editAction($id, Request $request){
		
		//recuperation de l'annonce d'id $id afin de l'editer
		//le recuperer avant la création du formulaire est important comme ça, ce dernier sera pré-remplit 
    	$em = $this->getDoctrine()->getManager();
    	$advert = $em->getRepository("OCPlatformBundle:Advert")->find($id);

    	if($advert === null ){
    		throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    	}

    	$formulaire = $this->get('form.factory')->create(AdvertEditType::class, $advert); 


		if($request->isMethod('POST')){//en cliquant sur envoyer 

			$formulaire->handleRequest($request); //remplir les champs de advert 

			if($formulaire->isValid()){
 
				//$em->persist($advert); inutile de persister car doctrine connait déjà notre annonce
				$em->flush(); 

				$request->getSession()->getFlashBag()->add('infoModifAnnonce', 'Annonce bien modifiée.'); 
				return $this->redirectToRoute('oc_platform_view',array('id'=>$id)); 
			}
		
		}


		return new Response($this->get('templating')->render('OCPlatformBundle:Advert:edit.html.twig', array('form'=>$formulaire->createView(), 'advert'=>$advert))); 
	}



	/**
	 *@Security("has_role('ROLE_AUTEUR')")
	 */
	public function deleteAction($id, Request $request){
		//recuperation en DB de l'annonce d'id $id
		$em = $this->getDoctrine()->getManager();
		$advert = $em->getRepository("OCPlatformBundle:Advert")->find($id);


		//créons un formulaire vide pour nous proteger contre les failles
		$formulaire = $this->get('form.factory')->create(); 
		if($request->isMethod('POST') && $formulaire->handleRequest($request)->isValid()){
			//supprimer maintenant $advert de la base de donnees
			foreach($advert->getCategories() as $category){
				$advert->removeCategory($category); 
			}
		
			//$advertSkill = $em->getRepository('OCPlatformBundle:AdvertSkill')->findBy(array('advert' => $advert)); 
			$advertSkills = $advert->getAdvertSkills(); 

			foreach( $advertSkills as $ad)	$em->remove($ad); 
			foreach( $advert->getApplications() as $app)	$em->remove($app); 

			$em->remove($advert); 
			//fin de la suppression

			$em->flush();

			$request->getSession()->getFlashBag()->add('infoSuppression', 'Annonce Supprimée avec Succès! ');

			return $this->redirectToRoute('oc_platform_home');  	
		}


		

		return $this->render('OCPlatformBundle:Advert:delete.html.twig', array('advert'=>$advert, 'form'=>$formulaire->createView())); 
	}


	public function menuAction(){
		//recuperation de l'entity manager
		$em = $this->getDoctrine()->getManager(); 
		//recuperer les trois dernieres annonces (advertissements)
		$listAdverts = $em->getRepository("OCPlatformBundle:Advert")->findBy(array(), array('date'=>'desc'), 3 , 0); 
		
		return new Response($this->get('templating')->render('OCPlatformBundle:Advert:menu.html.twig', array(
		// Tout l'intérêt est ici : le contrôleur passe
		// les variables nécessaires au template !
		'listAdverts' => $listAdverts
		)));
	}


	/**
	 *@Security("has_role('ROLE_ADMIN')")
	 */
	public function purgeAction(Request $request, $days){
		$this->get('oc_platform.advert.purge')->purge($days); 

		//message flashBag 
		$request->getSession()->getFlashBag()->add('infoPurge','Les annonces	plus vieilles que '.$days.'	jours ont été	purgées.'); 
		
		return  $this->redirectToRoute('oc_platform_home');
	}
}


