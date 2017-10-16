<?php
// src/OC/PlatformBundle/Bigbrother/MessageNotificator.php

namespace OC\PlatformBundle\BigBrother;

use Symfony\Component\Security\Core\User\UserInterface;
use OC\PlatformBundle\Event\MessagePostEvent; 


class MessageListener{


	//est mon service d'envoi de mail
	protected $notificateur; 
	//est la liste des utilisateurs surveillés
	protected $listUsers = array(); 

	public function __construct($notificator, $listUsers){
		$this->notificator = $notificator;
		$this->listUsers = $listUsers; 
	}



  // Méthode pour notifier par e-mail un administrateur
	public function processMessage(MessagePostEvent $event){
        // On active la surveillance si l'auteur du message est dans la liste
		if (in_array($event->getUser()->getUsername(), $this->listUsers)) {

      // On envoie un e-mail à l'administrateur
			$this->notificator->envoiEmail("Message Pas correct de ".$event->getUser()->getUsername(),'amara.sanoh.hawa@gmail.com', 'sanohawa@gmail.com', $event->getMessage());

    	}
	}


}