<?php

namespace App\Form;

use App\Entity\Annonce;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class   AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Name')
            ->add('Images')
            ->add('Description')
            ->add('Date')
            ->add('State')
            ->add('User', EntityType::class, [
                'class' => 'App\Entity\User',
                'choice_label' => 'id',
            ])
            ->add('Category', EntityType::class, [
                'class' => 'App\Entity\Category',
                'choice_label' => 'id',

            ]);

        $builder
            ->add('imageFile', FileType::class, [
                'label' => 'Image',
                'required' => false, // Change this based on your requirements
                'mapped' => false,   // This is important to handle file upload manually
            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }

    // Add a FileType field for image uploads

}
