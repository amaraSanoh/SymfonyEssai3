<?php

namespace OC\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class OCUserBundle extends Bundle
{

	//cette methode indique à symfony que ce bundle doit heriter de FOSUserBundle

	public function getParent(){
		return 'FOSUserBundle'; 
	}
}
