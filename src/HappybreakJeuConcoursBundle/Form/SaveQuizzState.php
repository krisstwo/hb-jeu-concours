<?php
/**
 * Coffee & Brackets software studio
 * @author Mohamed KRISTOU <krisstwo@gmail.com>.
 */

namespace HappybreakJeuConcoursBundle\Form;

use Doctrine\Common\Persistence\ObjectRepository;
use HappybreakJeuConcoursBundle\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class SaveQuizzState extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $optionCallback = function ($questionFieldValue, ExecutionContextInterface $context, $payload) {
            //TODO
        };

        /**
         * @var ObjectRepository
         */
        $questionRepository = $options['questionRepository'];

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

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
        ));

        $resolver->setRequired('questionRepository');
    }
}