<?php

namespace AdminBundle\Form;

use AdminBundle\Enum\RolesEnum;
use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditUserType extends AbstractType
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
            ->add('roles', ChoiceType::class, array(
                'choices'  => RolesEnum::ALL_ROLES,
                "choices_as_values" => true,
                "multiple" => true,
                "expanded" => false
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
