<?php

namespace OC\PlatformBundle\Twig;

use OC\PlatformBundle\Antispam\OCAntispam;

//On fait etendre cette classe de la classe  abstraite  \Twig_Extension pour que twig puisse se servir 
//de ce service

//on peut appeler {{ checkIfSpam("pas un spam") }} dans une vue 

class AntispamExtension extends \Twig_Extension{

	private $ocAntispam; 

	public function __construct(OCAntispam $ocAntispam){
			$this->ocAntispam = $ocAntispam; 
	}

	public function checkIfArgumentIsSpam($text){
		return $this->ocAntispam->isSpam($text); 
	}

//Redefinition de deux fonctions de notre classe abstraite \Twig_Extension

  // Twig va exécuter cette méthode pour savoir quelle(s) fonction(s) ajoute notre service

  public function getFunctions()

  {

    return array(

      new \Twig_SimpleFunction('checkIfSpam', array($this, 'checkIfArgumentIsSpam')),

    );

  }


  // La méthode getName() identifie votre extension Twig, elle est obligatoire

  public function getName()

  {

    return 'OCAntispam';

  }

}