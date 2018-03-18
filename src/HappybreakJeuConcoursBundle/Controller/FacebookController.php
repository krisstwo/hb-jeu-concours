<?php
/**
 * Coffee & Brackets software studio
 * @author Mohamed KRISTOU <krisstwo@gmail.com>.
 */

namespace HappybreakJeuConcoursBundle\Controller;


use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use HappybreakJeuConcoursBundle\Service\Facebook;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FacebookController extends Controller
{
    public function loginAction()
    {
        //Mandatory for Facebook client internals
        $session = $this->getSession();

        $fb = $this->container->get('happybreak_jeu_concours.facebook')->getFacebook();

        $helper = $fb->getRedirectLoginHelper();

        $permissions = ['email', 'user_birthday']; // Optional permissions
        $loginUrl    = $helper->getLoginUrl($this->generateUrl('happybreak_jeu_concours_facebook_callback', array(),
            UrlGeneratorInterface::ABSOLUTE_URL), $permissions);

        return new RedirectResponse($loginUrl);
    }

    public function callbackAction()
    {
        //Mandatory for Facebook client internals
        $session = $this->getSession();

        $fb = $this->container->get('happybreak_jeu_concours.facebook')->getFacebook();

        $helper = $fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
        } catch (FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();//TODO: log
            exit;
        } catch (FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();//TODO: log
            exit;
        }

        if ( ! isset($accessToken)) {
            if ($helper->getError()) {
                header('HTTP/1.0 401 Unauthorized');
                echo "Error: " . $helper->getError() . "\n";//TODO: log
                echo "Error Code: " . $helper->getErrorCode() . "\n";
                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                echo "Error Description: " . $helper->getErrorDescription() . "\n";
            } else {
                header('HTTP/1.0 400 Bad Request');//TODO: log
                echo 'Bad request';
            }
            exit;
        }

        // Logged in
        echo '<h3>Access Token</h3>';
        var_dump($accessToken->getValue());

        // The OAuth 2.0 client handler helps us manage access tokens
        $oAuth2Client = $fb->getOAuth2Client();

        // Get the access token metadata from /debug_token
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);
        echo '<h3>Metadata</h3>';
        var_dump($tokenMetadata);

        // Validation (these will throw FacebookSDKException's when they fail)
        $tokenMetadata->validateAppId((string)$this->container->getParameter('facebook_app_id'));
        // If you know the user ID this access token belongs to, you can validate it here
        //$tokenMetadata->validateUserId('123');
        $tokenMetadata->validateExpiration();

        if ( ! $accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (FacebookSDKException $e) {
                echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
                exit;
            }

            echo '<h3>Long-lived</h3>';
            var_dump($accessToken->getValue());
        }

        $session->set(Facebook::FACEBOOK_TOKEN_SESSION_KEY, (string)$accessToken);

        // User is logged in with a long-lived access token.
        // You can redirect them to a members-only page.
        return new RedirectResponse($this->generateUrl('happybreak_jeu_concours_homepage'));
    }

    public function logoutAction()
    {
        $session = $this->getSession();

        $session->remove(Facebook::FACEBOOK_TOKEN_SESSION_KEY);

        return new RedirectResponse($this->generateUrl('happybreak_jeu_concours_homepage'));
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