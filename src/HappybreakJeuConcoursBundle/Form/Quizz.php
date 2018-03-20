<?php
/**
 * Coffee & Brackets software studio
 * @author Mohamed KRISTOU <krisstwo@gmail.com>.
 */

namespace HappybreakJeuConcoursBundle\Form;

use Doctrine\Common\Persistence\ObjectRepository;
use HappybreakJeuConcoursBundle\Entity\Question;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Unirest\Request;

class Quizz extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**
         * @var $container Container
         * @var $questionRepository ObjectRepository
         * @var $registrationRepository ObjectRepository
         */
        $container              = $options['container'];
        $questionRepository     = $container->get('doctrine')->getRepository('HappybreakJeuConcoursBundle:Question');
        $registrationRepository = $container->get('doctrine')->getRepository('HappybreakJeuConcoursBundle:Registration');

        // No duplicate emails validation
        $emailCallback = function ($email, ExecutionContextInterface $context, $payload) use ($registrationRepository) {

            $existingRegistration = $registrationRepository->findOneBy(array('email' => $email));

            if ($existingRegistration) {
                $context->buildViolation('Cette adresse email est déjà inscrite, merci d\'utiliser une autre.')
                        ->atPath('email')
                        ->addViolation();
            }
        };



        // Questions validation

        $optionCallback = function ($questionFieldValue, ExecutionContextInterface $context, $payload) {
            //TODO
        };

        $questions = $questionRepository->findBy(array(
            'isEnabled' => true
        ));

        //Validate all questions present
        foreach ($questions as $question) {
            /**
             * @var $question Question
             */

            $builder->add('question-' . $question->getId(), null, array('required' => true, 'constraints' => array(new Callback($optionCallback))));
        }

        // Recaptcha validation

        $recaptchaCallback = function ($recaptchaValue, ExecutionContextInterface $context, $payload) use ($container) {

            if (empty($recaptchaValue)) {
                $context->buildViolation('Merci de cocher la case "Je ne suis pas un robot".')
                        ->atPath('g-recaptcha-response')
                        ->addViolation();
            }

            $response = Request::post($container->getParameter('recaptcha_validation_endpoint'), array(),
                array(
                    'secret' => $container->getParameter('recaptcha_site_secret'),
                    'response' => $recaptchaValue,
                    'remoteip' => $_SERVER['REMOTE_ADDR']
                ));

            if ($response->body->success !== true) {
                foreach ($response->body->{'error-codes'} as $errorCode) {
                    $context->buildViolation($errorCode)
                            ->atPath('g-recaptcha-response')
                            ->addViolation();
                }
            }
        };

        $builder
            ->add('civility', null, array('required' => true))
            ->add(
                $builder->create('first_name', null, array('required' => true))
                        ->addModelTransformer(new CallbackTransformer(
                            function ($valueOut) {
                                return ucfirst($valueOut);
                            },
                            function ($valueIn) {
                                return ucfirst($valueIn);
                            }
                        ))
            )
            ->add(
                $builder->create('last_name', null, array('required' => true))
                        ->addModelTransformer(new CallbackTransformer(
                            function ($valueOut) {
                                return ucfirst($valueOut);
                            },
                            function ($valueIn) {
                                return ucfirst($valueIn);
                            }
                        ))
            )
            ->add('email', EmailType::class,
                array('required' => true, 'constraints' => array(new Callback($emailCallback))))
            ->add('birthday', null,
                array('required' => true, 'constraints' => array(new DateTime(array('format' => 'Y-m-d')))))
            ->add('phone_country_code', null,
                array(
                    'required' => true,
                    'constraints' => array(
                        new Length(array('min' => 3, 'max' => 4)),
                        new Regex(array('pattern' => '/^\+?[0-9]{1,3}$/'))
                    )
                ))
            ->add('phone', null,
                array(
                    'required' => true,
                    'constraints' => array(
                        new Length(array('min' => 14, 'max' => 14)),
                        new Regex(array('pattern' => '/^\d{2}(?:[\s.-]*\d{2}){4}$/'))
                    )
                ))
            ->add('facebook_user_id', null)
            ->add('g-recaptcha-response', null,
                array('constraints' => array(new Callback($recaptchaCallback))))
            ->add('cgv', null, array('required' => true));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
        ));

        $resolver->setRequired('container');
    }
}