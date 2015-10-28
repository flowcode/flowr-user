<?php

namespace Flower\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Flower\ModelBundle\Service\RoleService;
class UserGroupType extends AbstractType
{   
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('name')
                ->add('roles', 'choice', array(
                'choices' => $this->roleService->getRoles(),
                'multiple' => true,
                'label' => 'Roles'
                    )
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\User\UserGroup',
            'translation_domain' => 'UserGroup',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'usergroup';
    }

}
