<?php

// src/OC/PlatformBundle/Event/MessagePostEvent.php


namespace OC\PlatformBundle\Event;


use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;


class MessagePostEvent extends Event{

	protected $message; 
	protected $user; 


	public function __construct($message, UserInterface $user){
		$this->message = $message; 
		$this->user = $user; 
	}

	//le listener doit avoir acces au message 
	public function getMessage(){
		return $this->message; 
	}

	public function setMessage($message){
		$this->message = $message; 
	}


	//le listener doit avoir acces à l'utilisateur courant
	public function getUser(){
		return $this->user;
	}

}
