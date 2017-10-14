<?php

namespace OC\PlatformBundle\DoctrineListener; 

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use OC\PlatformBundle\EnvoiEmail\OCEnvoiEmail; 
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\Advert; 

class CreationListener{

	private $serviceEmail; 

	public function __construct(OCEnvoiEmail $serviceEmail){
		$this->serviceEmail = $serviceEmail; 
	}

	//IMPORTANT: le nom de la methode, doit toujours etre celui d'un evenement doctrine
	public function PostPersist(LifecycleEventArgs $args){
		$entity = $args->getObject(); 

		if(!$entity instanceof Application && !$entity instanceof Advert) return ; 

		if($entity instanceof Application) $this->serviceEmail->envoiEmail("Une nouvelle candidature", "amara.sanoh.hawa@gmail.com", "sanohawa@gmail.com", "Bonjour, \nJe m'appelle ".$entity->getAuthor()." \n".$entity->getContent()); 

		if($entity instanceof Advert) $this->serviceEmail->envoiEmail("Ajout de candidature", "amara.sanoh.hawa@gmail.com", "sanohawa@gmail.com", "Bonjour monsieur ".$entity->getAuthor()."\nNous vous informons que vous venez à l'instant de rajouter une nouvelle annonce sur notre site.\nTitre de l'annonce: ".$entity->getTitle()." \ncontenu de l'annonce: ".$entity->getContent()); 

	}
}

