<?php
/**
 * Coffee & Brackets software studio
 * @author Mohamed KRISTOU <krisstwo@gmail.com>.
 */

namespace HappybreakJeuConcoursBundle\Service;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class Mailer {

    private $mailer;
    private $templating;
    private $template;
    private $subject;
    private $from;
    private $fromName;
    private $to;
    private $params;
    private $body;

    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating)
    {

        $this->mailer = $mailer;
        $this->templating = $templating;

    }

    /**
     * @param array $config
     * send mail
     * @param null $contentType
     */
    public function sendMessage( array $config, $contentType = null ) {

        $this->explodeConfigAndSet( $config );
        $this->setTemplateParamsTwig();

        $message = \Swift_Message::newInstance()
            ->setSubject( $this->subject )
            ->setFrom( $this->from, ! empty($this->fromName) ? $this->fromName : null )
            ->setTo( $this->to )
            ->setBody( $this->body , $contentType );

        return $this->mailer->send( $message );

    }

    /**
     * @param array $config
     */
    public function explodeConfigAndSet( array $config ) {

        foreach ( $config as $k => $v ) {

            switch( $k ) {

                case 'from':
                    $this->setFrom( $v );
                    break;

                case 'fromName':
                    $this->setFromName( $v );
                    break;

                case 'to':
                    $this->setTo( $v );
                    break;

                case 'subject':
                    $this->setSubject( $v );
                    break;

                case 'template':
                    $this->setTemplate( $v );
                    break;

                case 'params':
                    $this->setParams( $v );
                    break;

                default:
                    break;
            }

        }

    }

    /**
     * @param string $from
     */
    public function setFrom( $from ) {
        $this->from = $from;
    }

    /**
     * @param $fromName
     *
     * @internal param string $from
     */
    public function setFromName($fromName)
    {
        $this->fromName = $fromName;
    }

    /**
     * @param string $to
     */
    public function setTo( $to ) {
        $this->to = $to;
    }

    /**
     * @param string $subject
     */
    public function setSubject( $subject ) {
        $this->subject = $subject;
    }

    public function setTemplate( $template ) {
        $this->template = $template;
    }

    /**
     * @param null $params
     */
    public function setParams( $params = null ) {
        $this->params = $params;
    }

    /**
     * parameter template
     */
    public function setTemplateParamsTwig() {

        if( !is_null( $this->params ) ) {

            $this->body = $this->templating->render( $this->template, $this->params );

        } else {

            $this->body = $this->templating->render( $this->template );

        }

    }
}