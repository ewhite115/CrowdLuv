<?php

namespace CrowdLuv\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FollowerLuvsTalentType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('stillFollowing')
            ->add('allowEmail')
            ->add('allowSms')
            ->add('willTravelDistance')
            ->add('willTravelTime')
            ->add('followDate')
            ->add('crowdluvFollower')
            ->add('crowdluvTalent')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CrowdLuv\AdminBundle\Entity\FollowerLuvsTalent'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'crowdluv_adminbundle_followerluvstalent';
    }
}
