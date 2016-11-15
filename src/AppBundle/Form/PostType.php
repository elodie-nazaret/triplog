<?php

namespace AppBundle\Form;

use AppBundle\Entity\Post;
use AppBundle\Form\Type\PictureType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', CKEditorType::class, [
                'label' => 'Description'
            ])
            ->add('interestPoint', CheckboxType::class, [
                'label' => 'Terminé'
            ])
            ->add('pictures', CollectionType::class, [
                'entry_type'   => PictureType::class,
                'allow_delete' => true,
                'label'        => false,
                'by_reference' => false,
                //'allow_add'    => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Post::class,
        ));
    }
}