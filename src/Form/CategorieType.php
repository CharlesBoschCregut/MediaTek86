<?php
namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\ORM\EntityManagerInterface;

class CategorieType extends AbstractType
{
    /**
     *
     * @var type
     */
    private $entityManager;

    /**
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * * Établi les types de champs et les données
     * du formaulaire d'ajout de catégorie
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
            'label' => 'Ajouter une catégorie : ',
            'constraints' => [
                new Callback([$this, 'validateCategorieName'])
            ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
        ;
    }
    
    /**
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
            'constraints' => [
                new Callback([$this, 'validateCategorieName'])
            ]
        ]);
    }

    /**
     *
     * @param type $value
     * @param ExecutionContextInterface $context
     */
    public function validateCategorieName($value, ExecutionContextInterface $context)
    {
        $existingCategorie = $this->entityManager->getRepository(Categorie::class)->findOneBy(['name' => $value]);

        if ($existingCategorie) {
            $context->buildViolation('Cette catégorie éxiste déjà')
                ->atPath('name')
                ->addViolation();
        }
    }
}