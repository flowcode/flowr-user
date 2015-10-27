<?php

namespace Flower\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('firstname')
                ->add('lastname')
                ->add('email')
                ->add('username')
                ->add('plainPassword', 'password', array('required' => false))
//                ->add('roles', 'choice', array(
//                    'choices' => array("ROLE_USER" => "User", "ROLE_ADMIN" => "Admin"),
//                    "multiple" => true
//                ))
                ->add('groups', 'entity', array(
                    'class' => 'FlowerModelBundle:User\UserGroup',
                    'property' => 'name',
                    'required' => false,
                    'multiple' => true,
                ))
                ->add('enabled', null, array('required' => false))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\User\User',
            'translation_domain' => 'User',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'user';
    }

}
