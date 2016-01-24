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
                ->add('initials')
                ->add('plainPassword', 'password', array('required' => false, 'label' => "Password"))
                ->add('orgPosition', 'y_tree', array(
                    'class' => "Flower\ModelBundle\Entity\User\OrgPosition",
                    'orderFields' => array('root' => 'asc','lft' => 'asc'),
                    'prefixAttributeName' => 'data-level-prefix',
                    'treeLevelField' => 'lvl',
                    'required' => false,
                    'multiple' => false,
                    'attr' => array("class" => "tall")))
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
