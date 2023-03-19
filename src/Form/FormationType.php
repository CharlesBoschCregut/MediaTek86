<?php
namespace App\Form;

use App\Entity\Formation;
use App\Entity\Categorie;
use App\Entity\Playlist;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'label' => 'Titre :'
            ])
            ->add('description', null, [
                'label' => 'Description :',
                'required' => false
            ])
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'name',
                'multiple' => true
            ])
            ->add('playlist', EntityType::class, [
                'class' => Playlist::class,
                'choice_label' => 'name'
            ])

            ->add('publishedAt', DateTimeType::class, [
                'widget' => 'choice',
                 'years' => range(date('Y'), date('Y')-34),
                 'months' => range(1,12),
                 'days' => range(1, 31),
            ])
                
            ->add('videoId', null, [
                'label' => 'video id'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}