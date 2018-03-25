<?php
/**
 * Coffee & Brackets software studio
 * @author Mohamed KRISTOU <krisstwo@gmail.com>.
 */

namespace HappybreakJeuConcoursAdminBundle\Admin;

use \Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class QuestionOption extends AbstractAdmin
{
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('_action', null, array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array()
                )
            ))
            ->addIdentifier('id')
            ->add('title')
            ->add('ordering')
            ->add('question.title', null, array('label' => 'Question'));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('title')
                       ->add('question', null, array(), 'entity', array(
                           'class' => 'HappybreakJeuConcoursBundle\Entity\Question',
                           'choice_label' => 'title',
                       ));
    }

    public function configureShowFields(ShowMapper $showMapper)
    {
        parent::configureShowFields($showMapper);

        $showMapper->add('id')
                   ->add('title')
                   ->add('ordering')
                   ->add('illustration')
                   ->add('question.title', null, array('label' => 'Question'));
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('title', 'text')
                   ->add('ordering', 'integer')
                   ->add('illustration', 'text', array('required' => false))
                   ->add('question', 'entity', array(
                       'class' => 'HappybreakJeuConcoursBundle\Entity\Question',
                       'choice_label' => 'title'
                   ));
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(array('list', 'export', 'show', 'edit', 'delete'));
    }

    public function toString($object)
    {
        return $object instanceof \HappybreakJeuConcoursBundle\Entity\QuestionOption
            ? 'Option #' . $object->getId()
            : 'Option'; // shown in the breadcrumb on the create view
    }
}