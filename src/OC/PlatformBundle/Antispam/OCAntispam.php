<?php

namespace OC\PlatformBundle\Antispam; 

class OCAntispam{

 
	private $locale;
	private $minLength; 

	public function __construct($locale, $minLength){
		$this->locale = $locale;
		$this->minLength = (int) $minLength; 

	}

	public function isSpam($text){
		return strlen($text) < $this->minLength; 
	}

}
