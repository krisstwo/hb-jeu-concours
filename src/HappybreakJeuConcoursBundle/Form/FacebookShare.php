<?php
/**
 * Coffee & Brackets software studio
 * @author Mohamed KRISTOU <krisstwo@gmail.com>.
 */

namespace HappybreakJeuConcoursBundle\Form;

use HappybreakJeuConcoursBundle\Service\Hasher;
use Hashids\Hashids;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FacebookShare extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('target', null, array('required' => true))
            ->add('registration', null, array('required' => true));

        /**
         * @var $container Container
         * @var $hasher Hasher
         */
        $container = $options['container'];
        $hasher   = $container->get('happybreak_jeu_concours.hasher');

        $builder->get('registration')
                ->addModelTransformer(new CallbackTransformer(
                    function ($registrationId) use ($hasher) {

                        return $hasher->getHasher()->encode($registrationId);
                    },
                    function ($registrationIdHash) use ($hasher) {

                        return $hasher->getHasher()->decode($registrationIdHash)[0];
                    }
                ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
        ));

        $resolver->setRequired('container');
    }
}