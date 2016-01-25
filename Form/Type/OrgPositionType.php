<?php

namespace Flower\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrgPositionType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('name')
                ->add('parent', 'y_tree', array(
                    'class' => 'Flower\ModelBundle\Entity\User\OrgPosition',
                    'orderFields' => array('root' => 'asc','lft' => 'asc'),
                    'prefixAttributeName' => 'data-level-prefix',
                    'treeLevelField' => 'lvl',
                    'required' => false,
                    'multiple' => false,
                    'attr' => array("class" => "tall")))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\User\OrgPosition',
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
