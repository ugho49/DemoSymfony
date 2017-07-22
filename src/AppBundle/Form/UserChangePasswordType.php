<?php
/**
 * Created by PhpStorm.
 * User: ustephan2016
 * Date: 19/07/2017
 * Time: 09:50
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class UserChangePasswordType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('old_password', PasswordType::class, array(
                'required' => true,
                'attr'  => array(
                    "placeholder" => "old password"
                )
            ))
            ->add('new_password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'constraints' => array(
                    new Length(array('min' => 8))
                ),
                'invalid_message' => 'The New and Repeat password fields must match.',
                'options' => array('attr' => array('class' => 'password-field')),
                'required' => true,
                'first_options'  => array(
                    'label' => 'New Password',
                    'attr'  => array(
                        "placeholder" => "new password"
                    )
                ),
                'second_options' => array(
                    'label' => 'Repeat New Password',
                    'attr'  => array(
                        "placeholder" => "repeat new password"
                    )
                ),
            ));
    }
}