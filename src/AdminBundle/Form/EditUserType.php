<?php

namespace AdminBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class EditUserType extends AbstractUserType
{

    /**
     * EditUserType constructor.
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

        $builder->add('enabled', ChoiceType::class, array(
            'choices'  => array(
                "Disabled"  => 0,
                "Enabled"   => 1
            ),
            "choices_as_values" => true,
            'expanded' => true,
            'multiple' => false
        ));
    }

}
