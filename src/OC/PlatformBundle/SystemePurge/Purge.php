<?php 

namespace OC\PlatformBundle\SystemePurge; 

use Doctrine\ORM\EntityManagerInterface;


class Purge{


	private $em;  

	public function __construct(EntityManagerInterface $em){
		$this->em = $em; 
	}

	public function purge($days){
		//convertir: il y a $days jours en date (création de la date précise $days plus tôt)
		$date = new \Datetime($days.' days ago'); //IMPORTANT
		
		//recuperons à présent, les annonces à supprimer
		$listAdverts = $this->em->getRepository('OCPlatformBundle:Advert')->getAdvertsBefore($date); 

		foreach($listAdverts as $a){
			//liberons $a de toutes ses associations
			$advertsSkills = $a->getAdvertSkills();   //ceci est possible grace à la bidirectionnalité

			foreach($advertsSkills as $as){
				$this->em->remove($as); 
			}

			foreach($a->getCategories() as $c){
				$this->em->remove($c); 
			}

			$this->em->remove($a);
		}

		$this->em->flush(); 
	}
}