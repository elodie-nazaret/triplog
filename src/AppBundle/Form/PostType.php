<?php

namespace AppBundle\Form;

use AppBundle\Entity\Post;
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
                'label' => 'Description',
                'required' => false,
            ])
            ->add('interestPoint', CheckboxType::class, [
                'label' => 'Terminé',
                'required' => false,
            ])
            ->add('pictures', CollectionType::class, [
                'entry_type'   => PictureType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'label'        => false,
                'by_reference' => false,
                'entry_options' => ['label' => false],
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