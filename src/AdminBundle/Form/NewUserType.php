<?php

namespace AdminBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;

class NewUserType extends AbstractUserType
{

    /**
     * NewUserType constructor.
     * @param User $user
     */
    function __construct(User $user)
    {
        parent::__construct($user);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => array('attr' => array('class' => 'password-field')),
                'required' => true,
                'first_options'  => array(
                    'label' => 'Password',
                    'attr'  => array(
                        "placeholder" => "password"
                    )
                ),
                'second_options' => array(
                    'label' => 'Repeat Password',
                    'attr'  => array(
                        "placeholder" => "repeat password"
                    )
                ),
            ));
    }
}
