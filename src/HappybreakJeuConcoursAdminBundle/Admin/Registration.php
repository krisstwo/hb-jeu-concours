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

class Registration extends AbstractAdmin
{
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('_action', null, array(
                'actions' => array(
                    'show' => array(),
                    'delete' => array()
                )
            ))
            ->addIdentifier('id')
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add('facebookUserId')
            ->add('totalShares')
            ->add('creationDate');
    }

    public function configureShowFields(ShowMapper $showMapper)
    {
        parent::configureShowFields($showMapper);

        $showMapper
            ->add('id')
            ->add('civility')
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add('birthday')
            ->add('phone')
            ->add('isNewsletterOptin')
            ->add('facebookUserId')
            ->add('trackingInformation')
            ->add('code')
            ->add('shares', 'sonata_type_collection', array(
                'associated_property' => function (\HappybreakJeuConcoursBundle\Entity\Share $share) {
                    return sprintf('%s: %s, %s', $share->getType(), $share->getTarget(),
                        $share->getCreationDate()->format('Y-m-d H:i'));
                }
            ))
            ->add('creationDate')
            ->add('updateDate');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('firstName')
                       ->add('lastName')
                       ->add('email');
    }

//    protected function configureFormFields(FormMapper $formMapper)
//    {
//        $formMapper->add('name', 'text')
//                   ->add('code', 'text')
//                   ->add('logo', 'text');
//    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(array('list', 'export', 'show', 'delete'));
    }

    public function toString($object)
    {
        return $object instanceof \HappybreakJeuConcoursBundle\Entity\Registration
            ? 'Registration #' . $object->getId()
            : 'Registration'; // shown in the breadcrumb on the create view
    }
}