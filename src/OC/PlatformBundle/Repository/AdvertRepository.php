<?php

namespace OC\PlatformBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder; 
use Doctrine\ORM\Tools\Pagination\Paginator;



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
		$qb->andWhere('a.id = :id')
			->setParameter('id', $id);

		return $qb->getQuery()->getResult();
	}

	//IMPORTANT
	//une methode qui recupere que toutes les annonces, les candidatures etc... et return un paginateur
	public function getAdverts($page, $nbPerPage){
		$qb = $this->createQueryBuilder('a'); 
		$qb->leftJoin('a.image', 'img');
		$qb->addSelect('img'); 
		$qb->leftJoin('a.categories','categ');  
		$qb->addSelect('categ');
		$qb->leftJoin('a.advertSkills','ask');  
		$qb->addSelect('ask');
		$qb->orderBy('a.date', 'DESC'); 
		$qb->getQuery(); 

		//cote pagination: IMPORTANT
		$qb->setFirstResult(($page-1)*$nbPerPage); 
		$qb->setMaxResults($nbPerPage); 

		return new Paginator($qb,true); 
	}


	//les methodes necessaires pour le pure IMPORTANT 
	//En paramtre, on lui donne la date jusqu'à laquelle retourner les annonces IMPORTANT
	//J’ai choisi de mettre en argument une date précise et non un nombre de jour car c’est plus pratique à utiliser ici, plus générique. La conversion “nombre de jour” =>“date précise” se fera dans le service.

	public function getAdvertsBefore($date){
		$qb = $this->createQueryBuilder('a');
		$qb->where('a.updatedAt <= :date' )
		   ->orWhere('a.updateAt IS NULL AND a.date <= :date')
		   ->andWhere('a.applications IS EMPTY')
		   ->setParameter('date', $date)
		; 

		return $qb->getQuery()->getResult();  
	}

}
