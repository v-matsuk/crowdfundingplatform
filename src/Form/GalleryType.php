<?php

namespace App\Form;

use App\Entity\Gallery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GalleryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file1', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Image 1'
            ])
            ->add('file2', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Image 2'
            ])
            ->add('file3', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Image 3'
            ])
            ->add('file4', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Image 4'
            ])
            ->add('file5', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Image 5'
            ])
            ->add('file6', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Image 6'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Gallery::class,
        ]);
    }
}
