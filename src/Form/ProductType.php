<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\Image;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        //Vérifier si on ajoute ou mofidifie un produit (pour l'image)
        // = [Recupere les donne avec l option data (si jamais il existe il a des donness sinon null:]
        $produit = $options['data'] ?? null;
        //$modification donnera true ou false , donc si l objet existe
        $modification= $produit && $produit->getId();

        $imgConstraints = [new Image()];
        if(!$modification){
            $imgConstraints[] = new NotNull(['message' => 'Veuillez envoyer une image']);
        }
        $builder
            ->add('title', TextType::class,[
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez indiquer un titre']),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Le titre doit contenir au moins 3 caractères',
                        'max' => 100,
                        'maxMessage' => 'Le titre ne peut contenir plus de 100 caractères',
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez indiquer une description']),
                    new Length([
                        'min' => 20,
                        'minMessage' => 'Le titre doit contenir au moins 3 caractères',

                    ])
                ]
            ])
            ->add('stock', IntegerType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez indiquer un stock']),
                    new PositiveOrZero(['message' => 'Le stock ne peut être négatif'])
                    ]
            ])
            ->add('price', MoneyType::class,[
                'currency' => 'EUR',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez indiquer un prix']),
                    new Positive(['message' => 'Le prix doit être positif'])
                ]
            ])
//            L'image on ne doit pas etre obligé d'y toucher quand on modifi donc :
            ->add('imageUpload', FileType::class,[
                //= ce champs la n appartient pas a une proprete de la classe product
                'mapped' => false,
                'required' => false,
                'constraints' =>  $imgConstraints
            ])
            ->add('weigth', ChoiceType::class, [
                'choices' => [
                    '1' => '1',
                    '2' => '2' ,
                    '3' => '3',

                ]
            ])
            ->add('flavour', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez renseigner une saveur/goût']),
                    new Length([
                        'max'=> 50,
                        'maxMessage' => 'La saveur/goût ne peut pas contenir plus de 50 caractères',
                        'min' => 3,
                        'minMessage' => 'La saveur/goût doit contenir au moins 3 caractères',
                    ])
                ]
            ])
            ->add('brand', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez renseigner une saveur/goût']),
                    new Length([
                        'max'=> 30,
                        'maxMessage' => 'La marque ne peut pas contenir plus de 50 caractères (BeeOHoney)',
                        'min' => 2,
                        'minMessage' => 'La marque doit contenir au moins 3 caractères (BeeOHoney)',
                    ])
                ]
            ])
            ->add('categories', EntityType::class, [
                'multiple' => true,
                'expanded' => true,
                'class' => Category::class,
                'choice_label' => 'title',
                'constraints' => [
                    new Count([
                        'min' => 1,
                        'minMessage' => 'Veuillez sélectionner au moins 1 catégorie'
                    ])
                ]
            ])
        ;
    }
//nb : pour deseactivé a validation html5 , metre novalidate sur la balise dans le navigateur / a faire pour tester si ya un ptit malin
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
