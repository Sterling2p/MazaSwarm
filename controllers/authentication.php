<?php

use \MartynBiz\Slim3Controller\Controller;
use \Firebase\JWT\JWT;

class C_Authentication extends Controller
{
    private $ua;
    private $user;
    private $config;
    protected $app;
    
    function __construct( $app )
    {
        global $config;
        
        parent::__construct( $app );
        
        $this->ua     = new UserAuthentication;
        $this->user   = $this->ua->getCurrent();
        $this->app    = $app;
        $this->config = $config;
    }
    
    # Login:
    function login()
    {
        $request = $this->request->getParsedBody();
        
        # Are they already logged in?
        if( $this->user )
        {
            redirect('/');
        }
        
        $tpl = [
                'meta' => [
                'title' => 'Login'
            ]
        ];
        
        if( isset($_POST['login']) )
        {
            $email    = $request['email'];
            $password = $request['password'];
            $remember = ( isset($request['remember']) && $request['remember'] );
            
            try
            {
                $this->ua->login($email, $password, $remember);
                redirect('/');
            }
            catch( \Exception $e )
            {
                $tpl['alert'] = [
                    'text'  => 'Email address or password is incorrect.',
                    'class' => 'danger'
                ];
                
                $tpl['form'] = [
                    'email' => $email
                ];
            }
        }
        
        // Get the intro text:
        $hour   = date('H');
        
        if( $hour >= 0 && $hour <= 11 )     { $tpl['welcome_text'] = 'Good Morning';  }
        elseif( $hour > 12 && $hour <= 17 ) { $tpl['welcome_text'] = 'Good Afternoon'; }
        else                                { $tpl['welcome_text'] = 'Good Evening';   }
        
        return $this->render('auth-login.html', $tpl);
    }
    
    # Logout:
    function logout()
    {
        $this->ua->logout();
        
        return redirect('/login');
    }
    
    # Register a new account:
    public function register()
    {
        $request = $this->request->getParsedBody();
        
        # Are they already logged in?
        if( $this->user )
        {
            redirect('/');
        }
        
        if( isset($_POST['register']) )
        {
            $forename  = $tpl['form']['forename'] = trim( strip_tags($request['forename']) );
            $surname   = $tpl['form']['surname']  = trim( strip_tags($request['surname']) );
            $email     = $tpl['form']['email']    = trim($request['email']);
            $password  = $request['password'];
            $password2 = $request['password2'];
            
            $tpl['form'] = [
                'forename' => $forename,
                'surname'  => $surname,
                'email'    => $email
            ];
            
            # Have they specified a name?
            if( empty($forename) || empty($surname) )
            {
                $tpl['alert'] = [
                    'text'  => 'Please enter your full name.',
                    'class' => 'danger'
                ];
            }
            
            # Is the email valid?
            elseif( !filter_var($email, FILTER_VALIDATE_EMAIL) )
            {
                $tpl['alert'] = [
                    'text'  => 'Please enter a valid email address.',
                    'class' => 'danger'
                ];
            }
            
            # Did they provide a password?
            elseif( empty($password) )
            {
                $tpl['alert'] = [
                    'text'  => 'Please choose a password.',
                    'class' => 'danger'
                ];
            }
            
            # Do the passwords match?
            elseif( $password != $password2 )
            {
                $tpl['alert'] = [
                    'text'  => 'The chosen passwords do not match',
                    'class' => 'danger'
                ];
            }
            
            # Is the email in use?
            elseif( $this->ua->emailExists($email) )
            {
                $tpl['alert'] = [
                    'text'  => 'Email address already exists. <a href="'.$this->config['site.dir'].'login/">Click here</a> to login',
                    'class' => 'danger'
                ];
            }
            
            # Everything's okay, create an account:
            else
            {
                $user = R::dispense('user');
                
                $user->forename   = $forename;
                $user->surname    = $surname;
                $user->email      = $email;
                $user->password   = $this->ua->passwordHash( $password );
                $user->registered = R::isoDateTime();
                $user->modified   = R::isoDateTime();
                $user->role       = 'user';
                
                # Stored:
                if( R::store($user) )
                {
                    $this->ua->login( $email, $password, 1 );
                    
                    # Redirect to the dashboard:
                    redirect('/');
                }
                else
                {
                    $tpl['alert'] = [
                        'text'  => 'There was a problem creating your account. Please try again.',
                        'class' => 'danger'
                    ];
                }
            }
        }
        
        // Get the intro text:
        $hour   = date('H');
        
        if( $hour >= 0 && $hour <= 11 )     { $tpl['welcome_text'] = 'Good Morning';  }
        elseif( $hour > 12 && $hour <= 17 ) { $tpl['welcome_text'] = 'Good Afternoon'; }
        else                                { $tpl['welcome_text'] = 'Good Evening';   }
        
        return $this->render('auth-register.html', $tpl);
    }
    
    # Forgotten password:
    function password_reset( $token = null )
    {
        global $config;
        
        $request = $this->request->getParsedBody();
        
        $tpl = [
            'show_form' => true,
            
            'meta' => [
                'title' => 'Reset Password'
            ]
        ];
        
        # Are we resetting?
        if( ! is_null($token) )
        {
            try
            {
                $token = decode_jwt($token);
                $user  = \R::load('user', $token->uid);
                
                if( isset($request['reset']) )
                {
                    $pass1 = $request['password'];
                    $pass2 = $request['password2'];
                    
                    if( !$user->id )
                    {
                        $tpl['alert'] = [
                            'text'  => 'Specified user does not exist.',
                            'class' => 'danger'
                        ];
                    }
                    elseif( empty($pass1) || empty($pass2) )
                    {
                        $tpl['alert'] = [
                            'text'  => 'Please enter and confirm your password.',
                            'class' => 'danger'
                        ];
                    }
                    elseif( $pass1 != $pass2 )
                    {
                        $tpl['alert'] = [
                            'text'  => 'The passwords you entered do not match.',
                            'class' => 'danger'
                        ];
                    }
                    else
                    {
                        $user->password = $this->ua->passwordHash($pass1);
                        
                        $tpl['alert'] = [
                            'text'  => 'Your password has been successfully changed.',
                            'class' => 'success'
                        ];
                        
                        $tpl['show_form'] = false;
                    }
                }
                
                return $this->render('auth-password-reset-form.html', $tpl);
            }
            catch( \Exception $e )
            {
                $tpl['alert'] = [
                    'text'  => 'The link you have clicked has expired. Please try again.',
                    'class' => 'danger'
                ];
            }
            
            return $this->render('auth-password-reset.html', $tpl);
        }
        
        # Handle the reset:
        if( isset($request['reset']) )
        {
            if( ! isset($request['email']) || ! filter_var($request['email'], FILTER_VALIDATE_EMAIL) )
            {
                $tpl['alert'] = [
                    'text'  => 'Please specify a valid email address.',
                    'class' => 'danger'
                ];
            }
            else
            {
                # See if we can find a user:
                $user = \R::findOne('user', 'email = ?', [ $request['email'] ]);
                
                if( !is_null($user) )
                {
                    $token = generate_jwt([
                        'uid' => $user->id,
                        'exp' => time() + (20 * 60)
                    ]);
                    
                    $email = new Email($this->app);
                    
                    $email->addTo($user->email);
                    $email->setSubject('Reset your '.$config['site.name'].' password');
                    $email->setTemplate('password-reset');
                    
                    $email->addData('name', $user->forename);
                    $email->addData('token', $token);
                    
                    $email->send();
                }
                
                $tpl['alert'] = [
                    'text'  => sprintf('If %s is associated with an account, an email with instructions has been sent there.', $request['email']),
                    'class' => 'success'
                ];
                
                $tpl['show_form'] = false;
            }
        }
        
        return $this->render('auth-password-reset.html', $tpl);
    }
}