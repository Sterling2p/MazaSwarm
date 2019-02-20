<?php

require 'app/load.php';

# Set up Slim:
$app = new \Slim\App(['settings' => [ 'displayErrorDetails' => $config['slim.debug'] ] ]);

# Set up Twig:
$container = $app->getContainer();

$container['view'] = function ($c)
{
    global $config;
    
    $view = new \Slim\Views\Twig( $config['twig.dir'], [ 'cache' => $config['twig.cache'] ]);
    
    $router = $c->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
    
    # Add our Twig globals:
    $view->getEnvironment()->addGlobal( 'base_dir',  $config['site.dir'] );
    $view->getEnvironment()->addGlobal( 'base_url',  $config['site.scheme'].'://'.$config['site.domain'].$config['site.dir']);
    $view->getEnvironment()->addGlobal( 'site_name', $config['site.name'] );
    
    foreach( $config['twig.globals'] as $name => $value )
    {
        $view->getEnvironment()->addGlobal( $name, $value );
    }
    
    return $view;
};


# Set up our routes:
require 'app/routes.php';

# Execute the app:
$app->run();