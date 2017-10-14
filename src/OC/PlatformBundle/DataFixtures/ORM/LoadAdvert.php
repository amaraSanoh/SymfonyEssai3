<?php
// src/OC/PlatformBundle/DataFixtures/ORM/LoaddAdvert.php

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;

class LoadAdvert implements FixtureInterface
{
  // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
  public function load(ObjectManager $manager)
  {
    // Liste 
    $titleAuthorsContents = array(
      array('Recherche développement web !!!','Melanie', 'Nous recherchons un developpeur web au sein de notre equipe....Blabla...', new \Datetime(date('Y') .'-'.date('m').'-10') ),
      array('Recherche Développeur sur mobile!!','Amara','Nous recherchons un developpeur sur mobile au sein de notre equipe....Blabla...Trop de Blabla', new \Datetime(date('Y') .'-'.date('m').'-09')),
      array('Recherche un Graphiste','Dadai', 'Nous recherchons un graphiste au sein de notre equipe....encore des Blabla', new \Datetime(date('Y') .'-'.date('m').'-05')),
      array('Recherche un admistrateur système!!','Marie', 'Nous recherchons un administrateur systeme au sein de notre equipe....encore des Blabla', new \Datetime(date('Y') .'-'.date('m').'-04')), 
      array('Recherche un admistrateur Réseau','Manon', 'Nous recherchons un administrateur réseau au sein de notre equipe....que des blabla...', new \Datetime(date('Y') .'-'.date('m').'-11') ), 
      array('Recherche un professeur de mathématique !!!','Garcia','Nous recherchons un professeur de maths au sein de notre equipe....que des blabla...', new \Datetime(date('Y') .'-'.date('m').'-10') ), 
      array('Recherche un expert en intelligence artificielle!!','papi', 'Nous recherchons un expert en intellignece artificielle au sein de notre equipe....encore des Blabla', new \Datetime(date('Y') .'-'.date('m').'-11')), 
      array('Recherche un sociopathe de haut niveau', 'Sherlock' , 'Nous recherchons un sociopathe de haut niveau au sein de notre equipe....Blabla...Trop de Blabla', new \Datetime(date('Y') .'-'.date('m').'-10')), 
      array('Recherche un ingenieur en logiciel!!', 'Matt', 'Nous recherchons ingenieur en logiciel au sein de notre equipe....Blabla...', new \Datetime(date('Y') .'-'.date('m').'-04')), 
      array('Recherche un ingenieur de conception et de developpement informatique !', 'Songokou' , 'Nous recherchons un ingenieur de conception et de developpement informatique ...blabla ', new \Datetime(date('Y') .'-'.date('m').'-11')), 
      array('Recherche développement web !!!','Melanie', 'Nous recherchons un developpeur web au sein de notre equipe....Blabla...', new \Datetime(date('Y') .'-'.date('m').'-10') ),
      array('Recherche Développeur sur mobile!!','Amara','Nous recherchons un developpeur sur mobile au sein de notre equipe....Blabla...Trop de Blabla', new \Datetime(date('Y') .'-'.date('m').'-09')),
      array('Recherche un Graphiste','Dadai', 'Nous recherchons un graphiste au sein de notre equipe....encore des Blabla', new \Datetime(date('Y') .'-'.date('m').'-05')),
      array('Recherche un admistrateur système!!','Marie', 'Nous recherchons un administrateur systeme au sein de notre equipe....encore des Blabla', new \Datetime(date('Y') .'-'.date('m').'-04')), 
      array('Recherche un admistrateur Réseau','Manon', 'Nous recherchons un administrateur réseau au sein de notre equipe....que des blabla...', new \Datetime(date('Y') .'-'.date('m').'-11') ), 
      array('Recherche un professeur de mathématique !!!','Garcia','Nous recherchons un professeur de maths au sein de notre equipe....que des blabla...', new \Datetime(date('Y') .'-'.date('m').'-10') ), 
      array('Recherche un expert en intelligence artificielle!!','papi', 'Nous recherchons un expert en intellignece artificielle au sein de notre equipe....encore des Blabla', new \Datetime(date('Y') .'-'.date('m').'-11')), 
      array('Recherche un sociopathe de haut niveau', 'Sherlock' , 'Nous recherchons un sociopathe de haut niveau au sein de notre equipe....Blabla...Trop de Blabla', new \Datetime(date('Y') .'-'.date('m').'-10')), 
      array('Recherche un ingenieur en logiciel!!', 'Matt', 'Nous recherchons ingenieur en logiciel au sein de notre equipe....Blabla...', new \Datetime(date('Y') .'-'.date('m').'-04')), 
      array('Recherche un ingenieur de conception et de developpement informatique !', 'Songokou' , 'Nous recherchons un ingenieur de conception et de developpement informatique ...blabla ', new \Datetime(date('Y') .'-'.date('m').'-11'))
    );

    $i=0;
      
    foreach ($titleAuthorsContents as $toc) {
      // On crée la catégorie
      $advert = new Advert();
      $advert->setTitle($toc[0]); 
      $advert->setAuthor($toc[1]); 
      $advert->setContent($toc[2]); 
      $advert->setDate($toc[3]); 

      $image = new Image(); 
      $image ->setExtension("http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg"); 
      $image->setAlt("Job de reve"); 

      $advert->setImage($image); 

      if($i++ % 2 == 0){
        $appl = new Application(); 
        $appl->setAuthor('Victor');
        $appl->setContent('Je suis qualifié tout symplement!!'); 
        $advert->addApplication($appl);
      }
      
      // On la persiste
      $manager->persist($appl);
      $manager->persist($advert);

    }

    // On déclenche l'enregistrement de toutes les catégories
    $manager->flush();
  }
}  