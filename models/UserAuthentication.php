<?php

# Main user class:
class UserAuthentication
{
    # Retrieve the currently logged in user (returns: Model on success, false on failure)
    public function getCurrent( $updateLastLogin = true, $flatObject = false )
    {
        global $config;
        
        # We don't have either a session or a cookie:
        if( !isset($_SESSION[$config['auth.session.uid']]) && !isset($_COOKIE[$config['auth.cookie.remember']]) )
        {
            return false;
        }
        
        # We have a cookie:
        if( isset($_COOKIE[$config['auth.cookie.remember']]) && !empty($_COOKIE[$config['auth.cookie.remember']]) )
        {
            # Decode the JWT:
            try
            {
                $decoded = \Firebase\JWT\JWT::decode($_COOKIE[$config['auth.cookie.remember']], KEY_JWT, ['HS256'] );
            }
            catch( Exception $e )
            {
                return false;
            }
            
            # Load the user:
            $user = \R::load('user', $decoded->uid);
            
            if( $user->id === 0 )
            {
                return false;
            }
            
            # Set the session ID:
            $_SESSION[ $config['auth.session.uid'] ] = $decoded->uid;
            
            if( $updateLastLogin )
            {
                # Renew the cookie:
                $this->_setLoginCookie( $decoded->uid );
                
                # Set their last activity:
                $user->setLastActivity();
            }
            
            return ($flatObject) ? $user->get() : $user;
        }
        
        elseif( isset($_SESSION[$config['auth.session.uid']]) )
        {
            # Load the User model:
            $user = \R::load('user', $_SESSION[$config['auth.session.uid']]);
            
            # Logged in user no longer exists:
            if( $user->id === 0 )
            {
                return false;
            }
            
            if( $updateLastLogin )
            {
                # Set their last activity:
                $user->setLastActivity();
            }
            
            return ($flatObject) ? $user->get() : $user;
        }
        
        # No session or cookie, return false:
        return false;
    }
    
    # Lookup a user from their email address:
    public function lookupEmail( $emailAddress )
    {
        $user = \R::findOne('user', 'email = ?', [ $emailAddress ]);
        
        if( is_null($user) )
        {
            return false;
        }
        
        return $user;
    }
    
    # Check a user's password:
    public function checkPassword( $pass, $hash )
    {
        return password_verify( $pass, $hash );
    }
    
    # Hash a password:
    public function passwordHash( $password )
    {
        return password_hash( $password, PASSWORD_DEFAULT );
    }
    
    # Check if a user is already with the email address:
    public function emailExists( $email )
    {
        $user = \R::findOne('user', 'email = ?', [ $email ]);
        
        return ( !is_null($user) );
    }
    
    # Log a user in:
    public function login( $email, $password, $remember = false )
    {
        global $config;
        
        # Check the email address is set:
        if( empty($email) )
        {
            throw new \Exception( 'E_LOGIN_EMPTY_EMAIL' );
        }
        
        # Check the password is set:
        if( empty($password) )
        {
            throw new \Exception( 'E_LOGIN_EMPTY_PASSWORD' );
        }
        
        # Make sure the email address is valid:
        if( !filter_var($email, FILTER_VALIDATE_EMAIL) )
        {
            throw new \Exception( 'E_INVALID_EMAIL' );
        }
        
        $user = $this->lookupEmail($email);
        
        # No user with that email:
        if( $user == false )
        {
            throw new \Exception( 'E_LOGIN_EMAIL' );
        }
        
        # Password is invalid:
        if( $this->checkPassword($password, $user->password) == false )
        {
            throw new \Exception( 'E_LOGIN_PASSWORD' );
        }
        
        # Email is okay and password matches:
        $_SESSION[ $config['auth.session.uid'] ] = $user->id;
        
        # Update their last login:
        $user->setLastLogin();
        
        # Are we remembering them?
        if( $remember )
        {
            $this->_setLoginCookie( $user->id );
        }
        
        return $user;
    }
    
    # Log a user out:
    public function logout()
    {
        global $config;
        
        # Destroy the session:
        session_destroy();
        
        unset( $_SESSION[$config['auth.session.uid']] );
        
        if( isset($_COOKIE[$config['auth.cookie.remember']]) )
        {
            return setcookie( $config['auth.cookie.remember'], '', time() - 100000 );
        }
        
        return true;
    }
    
    // Set the login cookie:
    private function _setLoginCookie( $uid, $expireDays = 30 )
    {
        global $config;
        
        $expire = time() + (60 * 60 * 24 * $expireDays);
        
        $token = [
            'uid' => $uid,
            'iss' => SITE_DOMAIN,
            'aud' => SITE_DOMAIN,
            'iat' => time(),
            'exp' => $expire
        ];
        
        try
        {
            $jwt = \Firebase\JWT\JWT::encode($token, $config['auth.key']);
            
            # Set the cookie:
            setcookie( $config['auth.cookie.remember'], $jwt, $expire, $config['site.dir'], $config['site.scheme'].'://'.$config['site.domain'], false, true);
            
            return true;
        }
        catch( Exception $e )
        {
            return false;
        }
        
        return false;
    }
}