<?php
/**
 * Created by PhpStorm.
 * User: ustephan2016
 * Date: 19/07/2017
 * Time: 09:50
 */

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstname', TextType::class, array(
                "attr" => array(
                    "placeholder" => "firstname"
                )
            ))
            ->add('lastname', TextType::class, array(
                "attr" => array(
                    "placeholder" => "lastname"
                )
            ))
            ->add('email', EmailType::class, array(
                "attr" => array(
                    "placeholder" => "email"
                )
            ))
            ->add('birthday', DateType::class, array(
                'required' => false,
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'yyyy-MM-dd',
                'attr' => array(
                    "class" => "date-advanced"
                )
            ))->add('uploadedFile', FileType::class, array(
                'required' => false,
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class
        ));
    }
}