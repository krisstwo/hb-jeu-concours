<?php
/**
 * Coffee & Brackets software studio
 * @author Mohamed KRISTOU <krisstwo@gmail.com>.
 */

namespace HappybreakJeuConcoursBundle\Service;

use Hashids\Hashids;
use Symfony\Component\DependencyInjection\Container;

class Hasher
{

    /**
     * @var Hashids
     */
    private $hashids;

    /**
     * Hasher constructor.
     *
     */
    public function __construct(Container $container)
    {

        $this->hashids = new Hashids($container->getParameter('hashids_salt'), 6,
            'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
    }


    public function getHasher()
    {
        return $this->hashids;
    }
}