<?php

// src/OC/PlatformBundle/Beta/BetaListener.php


namespace OC\PlatformBundle\Beta;

use Symfony\Component\HttpFoundation\Response;

#l'objet que le gestionnaire d'événements transmet
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class BetaListener{

  // Notre processeur
  protected $betaHTML;


  // La date de fin de la version bêta :
  // - Avant cette date, on affichera un compte à rebours (J-3 par exemple)
  // - Après cette date, on n'affichera plus le « bêta »

  protected $endDate;


  public function __construct(BetaHTMLAdder $betaHTML, $endDate){
    $this->betaHTML = $betaHTML;
    $this->endDate  = new \Datetime($endDate);
  }


  // L'argument de la méthode est un FilterResponseEvent
  //le gestion le transmet dans la methode déclarée
  public function processBeta(FilterResponseEvent $event){

    // On teste si la requête est bien la requête principale (et non une sous-requête)
    if (!$event->isMasterRequest()) {
        return;
    }


    //est la difference entre la endDate et la date du jour
    $remainingDays = $this->endDate->diff(new \Datetime())->days;


    if ($remainingDays <= 0) {
      // Si la date est dépassée, on ne fait rien
      return;
    }

    

    // On récupère la réponse que le gestionnaire a insérée dans l'évènement
   
    // Ici on appelera la méthode

    $response =  $this->betaHTML->addBeta($event->getResponse(), $remainingDays); 

    // Puis on insère la réponse modifiée dans l'évènement
    $event->setResponse($response);

  }

}
