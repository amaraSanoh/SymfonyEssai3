<?php

namespace OC\PlatformBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder; 
//use \Datetime; 


/**
 * AdvertRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AdvertRepository extends \Doctrine\ORM\EntityRepository
{

	public function getAdvertWithCategories(Array $categoryNames){
		$qb = $this->createQueryBuilder('a');
		$qb->InnerJoin('a.categories', 'categ'); 
		$qb->addSelect('categ');
		// Puis on filtre sur le nom des catégories à l'aide d'un IN

		$qb->where($qb->expr()->in('categ.name', $categoryNames)); 
		
		//Enfin, on retourne le resultat
		return $qb->getQuery()->getResult();
	}


	//condition que l'on peut appliquer sur toutes nos requetes en construction
	public function whereCurrentYear(QueryBuilder $qb){
		$qb
      		->andWhere('a.date BETWEEN :start AND :end')
      		->setParameter('start', new \Datetime(date('Y').'-01-01'))  // Date entre le 1er janvier de cette année
      		->setParameter('end',   new \Datetime(date('Y').'-12-31'))  // Et le 31 décembre de cette année
      	;   
		//pas de return, c'est une fonction accessoire
	}

	//cette methode return les advert et les candidatures
	public function getAdvertWithApplicationsCurrentYear(){
		$qb = $this->createQueryBuilder('a'); 
		$qb->InnerJoin('a.applications', 'app');
		$qb->addSelect('app'); 
		$this->whereCurrentYear($qb); 
		$qb->orderBy('a.date','DESC');  //ordre decroissant

		$qb->getQuery()->getResult(); 
	}


	public function myFind($id){
		$qb = $this->createQueryBuilder('a'); 
		$qb->where('a.id = :id')
			->setParameter('id', $id);

		return $qb->getQuery()->getResult();
	}


	//Methode à revoir
	/*public function getAdvertWithApplications($id){
		$qb = $this->createQueryBuilder('a'); 
		$qb->InnerJoin('a.applications', 'app'); 
		$qb->addSelect('app'); 
		$qb->where('a.id = :id'); 
		$qb->setParameter('id',$id); 

		return $qb->getQuery()->getResult(); 
	}
	*/
}
