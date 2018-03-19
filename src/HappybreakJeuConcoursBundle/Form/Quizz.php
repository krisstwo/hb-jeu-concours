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
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Unirest\Request;

class Quizz extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $optionCallback = function ($questionFieldValue, ExecutionContextInterface $context, $payload) {
            //TODO
        };

        /**
         * @var $container Container
         * @var $questionRepository ObjectRepository
         */
        $container = $options['container'];
        $questionRepository = $container->get('doctrine')->getRepository('HappybreakJeuConcoursBundle:Question');

        // Questions validation

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
            ->add('last_name', null, array('required' => true))
            ->add('first_name', null, array('required' => true))
            ->add('email', EmailType::class, array('required' => true))
            ->add('birthday', null,
                array('required' => true, 'constraints' => array(new DateTime(array('format' => 'd/m/Y')))))
            ->add('phone', null, array('required' => true))
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