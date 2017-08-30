<?php

namespace OC\PlatformBundle\EnvoiEmail; 

class OCEnvoiEmail{

	private $mailer; 

	public function __construct(\Swift_Mailer $mailer){
		$this->mailer = $mailer;  
	}

	public function envoi($objet, $from, $to, $body){
		$message = \Swift_Message::newInstance(); 
		$message->setSubject($objet);
		$message->setFrom($from);
		$message->setTo($to);   
		$message->setBody($body);

		$this->mailer->send($message); 
	}
}