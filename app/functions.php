<?php

use \Firebase\JWT\JWT;
                      
# Generate a new token:
function generate_jwt( $token )
{
    global $config;
    
    $token['iss'] = $config['site.scheme'].'://'.$config['site.domain'];
    $token['aud'] = $config['site.scheme'].'://'.$config['site.domain'];
    $token['iat'] = time();
    $token['nbf'] = time();
    
    try
    {
        return JWT::encode($token, $config['auth.key']);
    }
    catch( Exception $e )
    {
        return false;
    }
}

# Decode a JWT:
function decode_jwt( $token )
{
    try
    {
        return JWT::decode($token, $config['auth.key'], ['HS256'] );
    }
    catch( Exception $e )
    {
        return false;
    }
}

# Handle a redirect:
function redirect( $path, $trailing = true, $type = 302 )
{
    global $config;
    
    if( $type != 302 )
    {
        http_response_code($type);
    }
    
    $url = $config['site.scheme'].'://'.$config['site.domain'].$config['site.dir'].trim($path,' /').( ($trailing) ? '/' : '');
    $url = rtrim($url, '/');
    
    header('Location: '.$url );
    
    exit(1);
}