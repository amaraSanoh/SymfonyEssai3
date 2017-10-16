<?php

namespace OC\PlatformBundle\Antispam; 

class OCAntispam{

 
	private $locale;
	private $minLength; 

	public function __construct($minLength){
		$this->minLength = (int) $minLength; 

	}

	public function isSpam($text){
		return strlen($text) < $this->minLength; 
	}

	//dependances optionnelles 
	public function setLocale($locale){
		$this->locale = $locale; 
	}

}
