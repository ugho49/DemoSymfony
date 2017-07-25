<?php

namespace AppBundle\Form;

use AppBundle\Entity\Comment;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array(
                "required" => false,
                "attr" => array(
                    "placeholder" => "Title"
                )
            ))
            ->add('content', TextareaType::class, array(
                "attr" => array(
                    "placeholder" => "Content"
                )
            ))
            ->add('parent', EntityType::class, array(
                "class" => Comment::class,
                "required" => false,
                "attr" => array(
                    "style" => 'display:none;'
                ),
                "label_attr" => array(
                    "style" => 'display:none;'
                )
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Comment::class
        ));
    }

}
