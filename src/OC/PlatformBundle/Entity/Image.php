<?php

namespace OC\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile; 

/**
 * Image
 *
 * @ORM\Table(name="oc_image")
 * @ORM\Entity(repositoryClass="OC\PlatformBundle\Repository\ImageRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Image
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=255, nullable=true)
     */
    private $extension;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255)
     */
    private $alt;

    private $file; 

    private $tmpFilename; 


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set url
     *
     * @param string $extension
     *
     * @return Image
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set alt
     *
     * @param string $alt
     *
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }


    //on modifie le setter de file pour prendre en compte l'upload d'un fichier lorsqu'il en existe déjà un autre
    public function setFile(UploadedFile $file ){
        $this->file = $file; 

        //on verifie si on avait déjà un fichier dans cette entite
        if($this->extension !== null){
            //on sauvegarde l'extension du fichier pour le supprimer plus tard
            $this->tmpFilename = $this->extension; //ancienne extension

            //on reinitialise les attributs extension et alt
            $this->extension = null; 
            $this->alt = null;
        }
    }


    /**
     *@ORM\PrePersist
     *@ORM\PreUpdate
     */
    public function preUpload(){
        if($this->file === null ){ return; }

        //Le nom du fichier est son id et son extension
        $this->extension = $this->file->guessExtension(); //pour qu'il puisse etre persisté en base de donnees

        $this->alt = $this->file->getClientOriginalName(); //pour qu'il puisse etre persisté en base de donnees

    }

    /**
     *@ORM\PostPersist
     *@ORM\PostUpdate
     */
    public function upload(){
        if($this->file === null){ return; }

        //si on avait un ancien fichier, on le supprime
        if($this->tmpFilename !== null){
            $oldFile = $this->getUploadRootDir().'/'.$this->id.'.'.$this->tmpFilename;
            if(file_exists($oldFile)){
                unlink($oldFile); 
            } 
        }

        //on déplace le fichier envoyé dans le repertoire de notre choix
        $this->file->move(
            $this->getUploadRootDir(), //repertoire de destination
            $this->id.'.'.$this->extension //nom du fichier à créer, ici id.extension
            ); 

    }


    /**
     *@ORM\PreRemove
     */
    public function preRemoveUpload(){
        $this->tmpFilename = $this->getUploadRootDir().'/'.$this->id.'.'.$this->extension; 
    }

    /**
     *@ORM\PostRemove
     */
    public function removeUpload(){
        //ici on utilise le nom de fichier sauvegardé avan la suppression puisqu'on a plus acces à id
        if(file_exists($this->tmpFilename)){
            //on supprime le fichier 
            unlink($this->tmpFilename); 
        }
    }

    public function getFile(){
        return $this->file; 
    }


    public function getUploadDir(){
         // On retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)
        return 'uploads/img';
    }

    public function getUploadRootDir(){
            // On retourne le chemin relatif vers l'image pour notre code PHP
            return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    public function getWebPath(){
        return $this->getUploadDir().'/'.$this->getId().'.'.$this->getExtension(); 
    }
}
