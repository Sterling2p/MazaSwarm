<?php

use \MartynBiz\Slim3Controller\Controller;
use \Firebase\JWT\JWT;

class C_Dashboard extends Controller
{
    private $ua;
    private $user;
    private $config;
    private $tpl;
    
    protected $app;
    
    function __construct( $app )
    {
        global $config;
        
        parent::__construct( $app );
        
        $this->ua     = new UserAuthentication;
        $this->user   = $this->ua->getCurrent();
        $this->app    = $app;
        $this->config = $config;
        
        $this->tpl    = [
            'user' => $this->user,
            'meta' => [
                'title' => 'Dashboard'
            ],
            'nav' => [
                'active' => 'dashboard'
            ]
        ];
    }
    
    # Main dashboard function:
    public function main()
    {
        # Not logged in:
        if( ! $this->user )
        {
            return $this->response->withRedirect( $this->config['site.dir'].'login/' );
        }
        
        # Load admin dashboard:
        if( $this->user->role == 'admin' )
        {
            
        }
        
        # Load user dashboard:
        else
        {
            
        }
        
        return $this->render('dashboard.html', $this->tpl);
    }
}