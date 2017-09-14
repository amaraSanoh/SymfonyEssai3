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


//pour le formulaire
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;



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
		return new Response($this->get('templating')->render('OCPlatformBundle:Advert:index.html.twig', array('listAdverts'=>$listAdverts, 'nbPage'=>$nbPage, 'page'=> $page)) );
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





	public function addAction(Request $request){

		//creation de l'entité advert
		$advert = new Advert(); 


		
		//on crée un objet Advert 
		$advert = new Advert(); 

		// On crée le FormBuilder grâce au service form.factory
		$formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $advert); //pour dire c'est advert que tu hydrate

		// On ajoute les champs de l'entité que l'on veut à notre formulaire
		//c'est les champs à hydrater
		//class est une constante php --> elle retourne le chemin complet de la classe
		$formBuilder
			->add('title', TextType::class) 
			->add('author', TextType::class)
			->add('date', DateType::class)
			->add('content', TextareaType::class)
			->add('published', CheckboxType::class, array('required' => false))
			->add('valider', SubmitType::class)
		; 

		// Pour l'instant, pas de candidatures, catégories, etc., on les gérera plus tard

		//Avec le formBuilder on génere le formulaire
		$formulaire = $formBuilder->getForm(); 

		
		//si la requete est en POST cad que les valeurs ont été entrées en cliquant sur le bouton valider
		if($request->isMethod('POST')){
			 // On fait le lien Requête <-> Formulaire

      		// À partir de maintenant, la variable $advert contient les valeurs entrées dans le formulaire par le visiteur

      		$formulaire->handleRequest($request); 


      		// On vérifie que les valeurs entrées sont correctes
      		if($formulaire->isValid()){
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

		//test du service antispam
		$antispam = $this->container->get('oc_platform.antispam');
		$message = " je suis je suis je suis je suis je suis je suis je suis je suis je suis";  
		if($antispam->isSpam($message)){
			throw new \Exception('votre message est un spam'); 
		}


		// On passe la méthode createView() du formulaire à la vue
		// afin qu'elle puisse afficher le formulaire toute seule
		return new Response($this->get('templating')->render('OCPlatformBundle:Advert:add.html.twig', array('form'=>$formulaire->createView()))); 


		
	}




	public function editAction($id, Request $request){
		
		//recuperation de l'annonce d'id $id afin de l'editer
		//le recuperer avant la création du formulaire est important comme ça, ce dernier sera pré-remplit 
    	$em = $this->getDoctrine()->getManager();
    	$advert = $em->getRepository("OCPlatformBundle:Advert")->find($id);

    	if($advert === null ){
    		throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    	}

    	$formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $advert); 

    	$formBuilder
			->add('title', TextType::class) 
			->add('author', TextType::class)
			->add('date', DateType::class)
			->add('content', TextareaType::class)
			->add('published', CheckboxType::class, array('required' => false))
			->add('valider', SubmitType::class)
		; 

		$formulaire = $formBuilder->getForm(); 


		if($request->isMethod('POST')){//en cliquant sur envoyer 

			$formulaire->handleRequest($request);

			if($formulaire->isValid()){
 
				$em->persist($advert); 
				$em->flush(); 

				$request->getSession()->getFlashBag()->add('infoModifAnnonce', 'Annonce bien modifiée.'); 
				return $this->redirectToRoute('oc_platform_view',array('id'=>$id)); 
			}
		
		}


		return new Response($this->get('templating')->render('OCPlatformBundle:Advert:edit.html.twig', array('form'=>$formulaire->createView(), 'advert'=>$advert))); 
	}




	public function deleteAction($id){
		//recuperation en DB de l'annonce d'id $id
		$em = $this->getDoctrine()->getManager();
		$advert = $em->getRepository("OCPlatformBundle:Advert")->find($id);
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

		return $this->render('OCPlatformBundle:Advert:delete.html.twig', array('id'=>$id)); 
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


	public function purgeAction(Request $request, $days){
		$this->get('oc_platform.advert.purge')->purge($days); 

		//message flashBag 
		$request->getSession()->getFlashBag()->add('infoPurge','Les annonces	plus vieilles que '.$days.'	jours ont été	purgées.'); 
		
		return  $this->redirectToRoute('oc_platform_home');
	}
}


