<?php

namespace OC\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType; 
use OC\PlatformBundle\Repository\CategoryRepository;

//pour les evenements du formulaire
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;



class AdvertType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        //Arbitrairement, on recupere toutes les categories qui commencent par "D"
        $pattern = 'D%'; 
        $builder
            ->add('date', DateType::class)
            ->add('title', TextType::class )
            ->add('author', TextType::class)
            ->add('content', TextareaType::class)

            ->add('image', ImageType::class, array('required'=>false)) //c'est l'imbrication de image
            ->add('categories', EntityType::class, 
                array(
                    'class'=> 'OCPlatformBundle:Category', 
                    'choice_label'=>'name',
                    'multiple'=>true, 
                    'expanded'=>false, 
                    'query_builder'=>function(CategoryRepository $repository) use($pattern){
                        //le use nous permet de rajouter l'argument $pattern à la fonction car la fonction elle même ne prend que le repository en parametre
                        return $repository->getLikeQueryBuilder($pattern); 
                    }
                    ))  
            ->add('valider', SubmitType::class)
            ;

            //ecouteur d'evenement (evenement avnt hydration)
            //c'est cette méthode des évènements qui permet également la création des fameuses combobox : deux champs<select>dont le deuxième (par exempleville) dépend de la valeur du premier (par exemplepays).
            $builder->addEventListener( 
                    FormEvents::PRE_SET_DATA, //est l'evenement qui nous interresse
                    function(FormEvent $event){ //la fonction à executer lorsque l'evenement est déclenché
                        $advert = $event->getData(); 

                        if($advert === null) return ; 

                        if(!$advert->getPublished() || $advert->getId() === null){
                            $event->getForm()->add('published', CheckboxType::class, array('required'=>false)); 
                        }else{
                            $event->getForm()->remove('published'); 
                        }
                    }
                ); 
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OC\PlatformBundle\Entity\Advert'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'oc_platformbundle_advert';
    }


}
