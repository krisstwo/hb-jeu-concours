<?php
/**
 * Coffee & Brackets software studio
 * @author Mohamed KRISTOU <krisstwo@gmail.com>.
 */

namespace HappybreakJeuConcoursBundle\Service;


use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;

class Facebook
{
    const FACEBOOK_TOKEN_SESSION_KEY = 'fb_access_token';
    /**
     * @var Container
     */
    private $container;

    /**
     * Facebook constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return \Facebook\Facebook
     */
    public function getFacebook()
    {
        $session = $this->getSession();

        $fb = new \Facebook\Facebook(array(
            'app_id' => $this->container->getParameter('facebook_app_id'),
            'app_secret' => $this->container->getParameter('facebook_app_secret'),
            'default_graph_version' => 'v2.10',
        ));

        if($this->hasAccessToken())
            $fb->setDefaultAccessToken($session->get(self::FACEBOOK_TOKEN_SESSION_KEY));

        return $fb;
    }

    public function hasAccessToken()
    {
        $session = $this->getSession();

        return $session->has(self::FACEBOOK_TOKEN_SESSION_KEY);
    }

    public function getAccessToken()
    {
        $session = $this->getSession();

        return $this->hasAccessToken() ? $session->get(self::FACEBOOK_TOKEN_SESSION_KEY) : null;
    }

    public function getUserData()
    {
        $fb       = $this->getFacebook();
        $response = $fb->get('/me?fields=id,name,first_name,last_name,email,gender,birthday');

        return $response->getGraphUser();
    }

    /**
     * @return Session
     */
    private function getSession()
    {
        $session = new Session(new PhpBridgeSessionStorage());
        ! $session->isStarted() && $session->start();

        return $session;
    }
}