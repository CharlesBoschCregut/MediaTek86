<?php
namespace App\Form;

use App\Entity\Formation;
use App\Entity\Categorie;
use App\Entity\Playlist;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class FormationType extends AbstractType
{

    /**
     * Établi les types de champs et les données
     * du formaulaire d'ajout et d'édition de formation
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre :'
            ])
            ->add('description', TextareaType::class, [
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
                 'months' => range(1, 12),
                 'days' => range(1, 31),
            ])
                
            ->add('videoId', TextType::class, [
                'label' => 'video id'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
        ;
    }
}