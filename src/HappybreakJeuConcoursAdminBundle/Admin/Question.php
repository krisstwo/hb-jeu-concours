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

class Question extends AbstractAdmin
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
            ->add('isEnabled');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('title')
                       ->add('isEnabled');
    }

    public function configureShowFields(ShowMapper $showMapper)
    {
        parent::configureShowFields($showMapper);

        $showMapper->add('id')
                   ->add('title')
                   ->add('hint')
                   ->add('ordering')
                   ->add('isEnabled')
                   ->add('options', 'sonata_type_collection', array(
                       'associated_property' => function (
                           \HappybreakJeuConcoursBundle\Entity\QuestionOption $questionOption
                       ) {
                           return $questionOption->getTitle();
                       }
                   ));
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('title', 'text')
                   ->add('hint', 'text', array('required' => false))
                   ->add('ordering', 'integer')
                   ->add('isEnabled', 'checkbox', array('required' => false));
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(array('list', 'export', 'show', 'edit', 'delete'));
    }

    public function toString($object)
    {
        return $object instanceof \HappybreakJeuConcoursBundle\Entity\Question
            ? 'Question #' . $object->getId()
            : 'Question'; // shown in the breadcrumb on the create view
    }
}