<?php

namespace CrowdLuv\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FollowerType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fbUid')
            ->add('mobile')
            ->add('locationFbId')
            ->add('locationFbname')
            ->add('firstname')
            ->add('lastname')
            ->add('email')
            ->add('gender')
            ->add('birthdate')
            ->add('fbRelationshipStatus')
            ->add('signupdate')
            ->add('allowClEmail')
            ->add('allowClSms')
            ->add('deactivated')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CrowdLuv\AdminBundle\Entity\Follower'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'crowdluv_adminbundle_follower';
    }
}
