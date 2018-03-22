<?php

namespace HappybreakJeuConcoursBundle\Repository;

/**
 * CodeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CodeRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * @return null|object
     */
    public function findOneUnused()
    {
        return $this->findOneBy(array(
            'isUsed' => 0
        ), array(
            'clear' => 'ASC'
        ));
    }
}
