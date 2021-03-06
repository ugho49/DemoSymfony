<?php
/**
 * Created by PhpStorm.
 * User: ustephan2016
 * Date: 19/07/2017
 * Time: 09:50
 */

namespace AdminBundle\Form;

use AdminBundle\Enum\RolesEnum;
use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminUserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var User $currentUser */
        $currentUser = $options['current_user'];
        $roles = RolesEnum::ALL_ROLES;

        if ($currentUser->hasRole(RolesEnum::ROLE_SUPERADMIN)) {
            $roles[RolesEnum::ROLE_SUPERADMIN] = RolesEnum::ROLE_SUPERADMIN;
        }

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
                'choices'  => $roles,
                "choices_as_values" => true,
                "multiple" => true,
                "expanded" => false,
                "attr" => array(
                    "class" => "js-select-advanced"
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
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
            'current_user' => null
        ));
    }

}