<?php

namespace OC\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Security\Core\User\UserInterface;

use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 *
 * @ORM\Table(name="oc_user")
 * @ORM\Entity(repositoryClass="OC\UserBundle\Repository\UserRepository")
 */
class User extends BaseUser /*implements UserInterface */ 
//comme ça, symfony l'accepte en tant que classe utilisateur de la couche securité
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct(){
        parent::___construct(); 
        $this->addRole('ROLE_AUTEUR'); 
    }

/* les attributs qui existent déjà dans la super classe*/

/*
    username: nom d'utilisateur avec lequel l'utilisateur va s'identifier ;

    email: l'adresse e-mail ;

    enabled:trueoufalsesuivant que l'inscription de l'utilisateur a été validée ou non (dans le cas d'une confirmation par e-mail par exemple) ;

    password: le mot de passe de l'utilisateur ;

    lastLogin: la date de la dernière connexion ;

    locked: si vous voulez désactiver des comptes ;

    expired: si vous voulez que les comptes expirent au-delà d'une certaine durée.
*/
}

