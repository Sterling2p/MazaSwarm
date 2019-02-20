<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email
{
    private $to;
    private $cc;
    private $bcc;
    private $subject;
    private $from;
    private $attachments;
    
    private $template;
    private $tplData;
    
    private $app;
    private $twig;
    private $mail;
    private $tplPath;
    
    function __construct( $app )
    {
        global $config;
        
        $this->app  = $app;
        $this->twig = $this->app->getContainer()['view'];
        $this->mail = new PHPMailer();
        
        $this->from['name']  = SITE_NAME;
        $this->from['addr'] = EMAIL_SUPPORT;
        
        $this->to = [ ];
        $this->cc = [ ];
        $this->bcc= [ ];
        
        $this->attachments = [ ];
        
        $this->subject  = null;
        $this->template = null;
        $this->tplData  = [ ];
        
        #ÊSet up SMTP:
        $this->mail->isSMTP(true);
        $this->mail->isHTML(true);
        
        $this->mail->Host       = $config['smtp.host'];
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = $config['smtp.user'];
        $this->mail->Password   = $config['smtp.pass'];
        $this->mail->Port       = $config['smtp.port'];
        
        $this->mail->SMTPDebug = 2;
        
        $this->tplPath = __DIR__.'/../'.$config['twig.dir'].'/emails/';
    }
    
    # Set the sent from:
    public function setFrom( string $addr, string $name = null )
    {
        if( ! filter_var($addr, FILTER_VALIDATE_EMAIL) )
        {
            throw new \Exception('Address must be a valid email address.');
        }
        
        $this->from['name'] = $name;
        $this->from['addr'] = $addr;
    }
    
    # Set the subject:
    public function setSubject( string $subject )
    {
        $this->subject = $subject;
    }
    
    # Set the template:
    public function setTemplate( string $tpl )
    {
        $this->template = $tpl;
    }
    
    # Add a to address:
    public function addTo( string $addr )
    {
        if( ! filter_var($addr, FILTER_VALIDATE_EMAIL) )
        {
            throw new \Exception('Address must be a valid email address.');
        }
        
        $this->to[] = $addr;
    }
    
    # Add a CC address:
    public function addCC( string $addr )
    {
        if( ! filter_var($addr, FILTER_VALIDATE_EMAIL) )
        {
            throw new \Exception('Address must be a valid email address.');
        }
        
        $this->cc[] = $addr;
    }
    
    # Add a BCC address:
    public function addBCC( string $addr )
    {
        if( ! filter_var($addr, FILTER_VALIDATE_EMAIL) )
        {
            throw new \Exception('Address must be a valid email address.');
        }
        
        $this->bcc[] = $addr;
    }
    
    # Add a new attachments:
    public function addAttachment( string $path )
    {
        $this->attachments[] = $path;
    }
    
    # Add template data:
    public function addData( string $name, $value )
    {
        $this->data[$name] = $value;
    }
    
    # Send:
    public function send()
    {
        global $config;
        
        try
        {
            # Is there at least one recipient?
            if( empty($this->to) )
            {
                throw new \Exception('No recipients specified.');
            }
            
            # Have they specified a template?
            if( empty($this->template) || is_null($this->template) )
            {
                throw new \Exception('No email template specified.');
            }
            
            # Does the template exist?
            if( ! file_exists($this->tplPath.$this->template.'.html') )
            {
                throw new \Exception('Specified template does not exist.');
            }
            
            $this->mail->setFrom( $this->from['addr'], $this->from['name'] );
            
            # Add recipients:
            foreach( $this->to as $addr )
            {
                $this->mail->addAddress($addr);
            }
            
            # Add CC:
            foreach( $this->cc as $addr )
            {
                $this->mail->addCC($addr);
            }
            
            # Add BCC:
            foreach( $this->bcc as $addr )
            {
                $this->mail->addBCC($addr);
            }
            
            # Now set attachments:
            foreach( $this->attachments as $path )
            {
                $this->mail->addAttachment($path);
            }
            
            # Now set the subject and content:
            $this->mail->Subject = $this->subject;
            
            # Add the template data:
            $this->tplData['to_email']  = $this->to[0];
            $this->tplData['site_name'] = $config['site.name'];
            $this->tplData['send_date'] = date('jS F Y');
            $this->tplData['send_time'] = date('H:i:s');
            $this->tplData['base_url']  = $config['site.scheme'].'://'.$config['site.domain'].$config['site.dir'];
            
            # Now set the content:
            $this->mail->Body = $this->app->getContainer()['view']->fetchFromString( file_get_contents( $this->tplPath.$this->template.'.html'), $this->tplData );
            
            return $this->mail->Send();
            
        }
        catch( Exception $e )
        {
            return false;
        }
    }
    
}