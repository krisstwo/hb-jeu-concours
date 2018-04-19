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

class Share extends AbstractAdmin
{
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array()
                )
            ))
            ->addIdentifier('id')
            ->add('type')
            ->add('target')
            ->add('registration', null, array(
                'associated_property' => function (\HappybreakJeuConcoursBundle\Entity\Registration $registration) {

                    return sprintf('%s %s, %s', $registration->getFirstName(), $registration->getLastName(),
                        $registration->getCreationDate()->format('Y-m-d H:i'));
                }
            ))
            ->add('creationDate');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('type')
                       ->add('target')
                       ->add('creationDate');
    }

    public function getExportFields()
    {
        return ['id', 'type', 'target', 'registration.email', 'creationDate'];
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(array('list', 'export', 'delete'));
    }

    public function toString($object)
    {
        return $object instanceof \HappybreakJeuConcoursBundle\Entity\Share
            ? 'Share #' . $object->getId()
            : 'Share'; // shown in the breadcrumb on the create view
    }
}