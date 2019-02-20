<?php

use \MartynBiz\Slim3Controller\Controller;

class C_User extends Controller
{
    private $ua;
    private $user;
    private $tpl;
    private $config;
    
    function __construct( $app )
    {
        global $config;
        
        parent::__construct( $app );
        
        $this->ua   = new UserAuthentication;
        $this->user = $this->ua->getCurrent();
        $this->config = $config;
        
        $this->tpl = [
            'user'        => $this->user,
            'nav' => [
                'active' => 'users'
            ]
        ];
    }
    
    # Main Dashboard:
    public function index()
    {
        # They're not logged in:
        if( ! $this->user ) { redirect('/login/'); }
        
        # They're not an admin, redirect:
        if( $this->user->role != 'admin' ) { redirect('/'); }
        
        # Get the request:
        $request = $this->request->getParsedBody();
        
        # Count them:
        $numUsers = \R::count('user');
        $perPage  = 50;
        $numPages = ceil( $numUsers / $perPage );
        $page     = !is_null($this->request->getParam('p')) ? $this->request->getParam('p') : 1;
        
        $offset = ($page - 1) * $perPage;
        
        # Have we created a user?
        if( isset($_GET['created']) )
        {
            $this->tpl['alert'] = [
                'class' => 'success',
                'text'  => 'The user was created successfully.'
            ];
        }
        
        # Have we deleted a user?
        if( isset($_GET['deleted']) )
        {
            $this->tpl['alert'] = [
                'class' => 'success',
                'text'  => 'The user was deleted successfully.'
            ];
        }
        
        if( isset($_GET['err-own-user']) )
        {
            $this->tpl['alert'] = [
                'class' => 'danger',
                'text'  => 'Your cannot delete your own account.'
            ];
        }
        
        if( isset($_GET['err-delete']) )
        {
            $this->tpl['alert'] = [
                'class' => 'success',
                'text'  => 'There was a problem deleting the user. Please try again.'
            ];
        }
        
        # Do we have too many pages?
        if( $page > $numPages )
        {
            redirect('users/?p='.max(1, $numPages), false);
        }
        
        # Set up template vars:
        $this->tpl['meta']        = [ 'title' => 'Users' ];
        $this->tpl['nav']         = [ 'active' => 'users' ];
        $this->tpl['breadcrumbs'] = [ ['title' => 'Users', 'text' => 'Users', 'active' => true] ];
        
        $this->tpl['pagination']  = [
            'pages'      => $numPages,
            'perPage'    => $perPage,
            'page'       => $page,
            'nextPage'   => ($page > $numPages) ? $page + 1 : false,
            'prevPage'   => ($page > 1) ? $page - 1 : false,
            'url'        => 'users/?p=%page%',
            'numRecords' => $numUsers,
            'text'       => 'Showing <b>'.number_format($offset + 1, 0, '.', ',').'</b> to <b>'.number_format(min(($offset + 1 + $perPage), $numUsers), 0, '.', ',').'</b> of <b>'.number_format($numUsers, 0, '.', ',').'</b> users'
        ];
        
        # Get the users:
        $this->tpl['users'] = \R::find( 'user', 'ORDER BY registered DESC LIMIT '.$offset.','.$perPage );
        
        return $this->render('users.html', $this->tpl);
    }
    
    # Create a new user:
    public function create()
    {
        # They're not logged in:
        if( ! $this->user ) { redirect('/login/'); }
        
        # They're not an admin, redirect:
        if( $this->user->role != 'admin' ) { redirect('/'); }
        
        # Get the request:
        $request = $this->request->getParsedBody();
        
        # Are we saving?
        if( isset($_POST['save']) )
        {
            # Get the request:
            $request = $this->request->getParsedBody();
        
            // Check the fields are valid:
            $forename  = trim( strip_tags($request['forename']) );
            $surname   = trim( strip_tags($request['surname']) );
            $email     = trim( strip_tags($request['email']) );
            $password1 = $request['password1'];
            $password2 = $request['password2'];
            $role      = $request['role'];
            
            # Transfer values to template:
            $this->tpl['form']['forename'] = $forename;
            $this->tpl['form']['surname']  = $surname;
            $this->tpl['form']['email']    = $email;
            $this->tpl['form']['role']     = $role;
            
            if( empty($forename) || empty($surname) )
            {
                $this->tpl['alert'] = [
                    'class' => 'danger',
                    'text'  => 'The user\'s full name cannot be empty.'
                ];
            }
            
            elseif( !filter_var($email, FILTER_VALIDATE_EMAIL) )
            {
                $this->tpl['alert'] = [
                    'class' => 'danger',
                    'text'  => 'The specified email address was invalid.'
                ];
            }
            
            elseif( empty($password1) )
            {
                $this->tpl['alert'] = [
                    'class' => 'danger',
                    'text'  => 'The user\'s password cannot be empty.'
                ];
            }
            
            elseif( $password1 != $password2 )
            {
                $this->tpl['alert'] = [
                    'class' => 'danger',
                    'text'  => 'The passwords you entered did not match.'
                ];
            }
            
            if( !isset($this->tpl['alert']) )
            {
                // Check the user:
                if( $this->ua->emailExists($email) )
                {
                    $tpl['alert'] =[
                        'class' => 'danger',
                        'text'  => 'A user already exists with that email address.'
                    ];
                    
                    $tpl['form']['email'] = null;
                }
                else
                {
                    try
                    {
                        $user = \R::dispense('user');
                        
                        $user->forename = $forename;
                        $user->surname  = $surname;
                        $user->email    = $email;
                        $user->password = $this->ua->passwordHash( $password1 );
                        $user->role     = $role;
                        $user->created  = R::isoDateTime();
                        $user->modified = R::isoDateTime();
                        
                        if( \R::store($user) )
                        {
                            header('Location: ../?created');
                            exit(1);
                        }
                        else
                        {
                            $tpl['alert'] = [
                                'class' => 'danger',
                                'text'  => 'The was a problem creating the user profile. Please try again.'
                            ];
                        }
                    }
                    catch( \Exception $e )
                    {
                        $tpl['alert'] = [
                            'class' => 'danger',
                            'text'  => 'The was a problem creating the user profile. Please try again.'
                        ];
                    }
                }
            }
        }
        
        # Set up template vars:
        $this->tpl['meta']        = [ 'title' => 'Create User' ];
        $this->tpl['nav']         = [ 'active' => 'users' ];
        $this->tpl['breadcrumbs'] = [
            [ 'title' => 'Users', 'text' => 'Users', 'url' => $this->config['site.dir'].'/users/' ],
            [ 'separator' => true ],
            [ 'title' => 'New User', 'text' => 'New User', 'active' => true]
        ];
        
        return $this->render('users-form.html', $this->tpl);
    }
    
    # Edit the user:
    public function edit( $id )
    {
        # They're not logged in:
        if( ! $this->user ) { redirect('/login/'); }
        
        # They're not an admin, redirect:
        if( $this->user->role != 'admin' && $this->user->id != $id ) { redirect('/'); }
        
         # If ID isn't numeric:
        if( !is_numeric($id) )
        {
            redirect('/users/');
        }
        
        # Dispense the user:
        $user = \R::load('user', $id);
        
        # User doesn't exist:
        if( !$user->id )
        {
            redirect('/users/');
        }
        
        # Get the request:
        $request = $this->request->getParsedBody();
        
        # Are we saving?
        if( isset($_POST['save']) )
        {
            // Check the fields are valid:
            $forename  = trim( strip_tags($request['forename']) );
            $surname   = trim( strip_tags($request['surname']) );
            $email     = trim( strip_tags($request['email']) );
            $password1 = $request['password1'];
            $password2 = $request['password2'];
            $role      = trim( strip_tags($request['role']) );
            
            if( empty($forename) || empty($surname) )
            {
                $this->tpl['alert'] = [
                    'class' => 'danger',
                    'text'  => 'The user\'s full name cannot be empty.'
                ];
            }
            
            elseif( !filter_var($email, FILTER_VALIDATE_EMAIL) )
            {
                $this->tpl['alert'] = [
                    'class' => 'danger',
                    'text'  => 'The specified email address was invalid.'
                ];
            }
            
            if( !empty($password1) )
            {
                if( $password1 != $password2 )
                {
                    $this->tpl['alert'] = [
                        'class' => 'danger',
                        'text'  => 'The passwords you entered did not match.'
                    ];
                }
            }
            
            if( !isset($this->tpl['alert']) )
            {
                try
                {
                    $user->forename = $forename;
                    $user->surname  = $surname;
                    $user->email    = $email;
                    
                    if( !empty($password1) )
                    {
                        $user->password = $this->ua->passwordHash( $password1 );
                    }
                    
                    $user->role     = $role;
                    $user->modified = R::isoDateTime();
                    
                    if( \R::store($user) )
                    {
                        $this->tpl['alert'] = [
                            'class' => 'success',
                            'text'  => 'The user\'s profile has been successfully updated.'
                        ];
                    }
                    else
                    {
                        $this->tpl['alert'] = [
                            'class' => 'danger',
                            'text'  => 'The was a problem updating the user\'s profile. Please try again.'
                        ];
                    }
                }
                catch( \Exception $e )
                {
                    $this->tpl['alert'] = [
                        'class' => 'danger',
                        'text'  => 'The was a problem updating the user\'s profile. Please try again.'
                    ];
                }
            }
        }
        
        # Set up the form:
        $this->tpl['form'] = [
            'forename' => $user->forename,
            'surname'  => $user->surname,
            'email'    => $user->email,
            'role'     => $user->role
        ];
        
        # Set up template vars:
        $this->tpl['meta']        = [ 'title' => 'Edit User: '.$this->tpl['form']['forename'].' '.$this->tpl['form']['surname'] ];
        $this->tpl['nav']         = [ 'active' => 'users' ];
        $this->tpl['breadcrumbs'] = [
            [ 'title' => 'Users', 'text' => 'Users', 'url' => $this->config['site.dir'].'/users/' ],
            [ 'separator' => true ],
            [ 'title' => 'Edit: '.$this->tpl['form']['forename'].' '.$this->tpl['form']['surname'], 'text' => 'Edit: '.$this->tpl['form']['forename'].' '.$this->tpl['form']['surname'], 'active' => true]
        ];
        
        return $this->render('users-form.html', $this->tpl);
    }
    
    # Delete a user:
    public function delete( $id )
    {
        # They're not logged in:
        if( ! $this->user ) { redirect('/login/'); }
        
        # They're not an admin, redirect:
        if( $this->user->role != 'admin' ) { redirect('/'); }
        
         # If ID isn't numeric:
        if( !is_numeric($id) )
        {
            redirect('/users/');
        }
        
        # Dispense the user:
        $user = \R::load('user', $id);
        
        # User doesn't exist:
        if( !$user->id )
        {
            redirect('/users/');
        }
        
        # Is it their own account?
        if( $user->id == $this->user->id )
        {
            redirect('/users/?err-own-user', false);
        }
        else
        {
            try
            {
                \R::trash( $user );
                redirect('/users/?deleted', false);
            }
            catch( \Exception $e )
            {
                redirect('/users/?err-delete', false);
            }
        }
    }
}