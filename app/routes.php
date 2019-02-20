<?php

#ÊAdmin routes:
$app->group('', function () use ($app) {
    
    $controllers = [
        'auth'      => new C_Authentication( $app ),
        'dashboard' => new C_Dashboard( $app ),
        'user'      => new C_User( $app )
    ];
    
    # Authentication:
    $app->map(['GET', 'POST'], '/login[/]',                 $controllers['auth']('login'));
    $app->map(['GET'],         '/logout[/]',                $controllers['auth']('logout'));
    $app->map(['GET', 'POST'], '/register[/]',              $controllers['auth']('register'));
    
    # Users:
    $this->map(['GET'],         '/users[/]',                $controllers['user']('index'));
    $this->map(['GET', 'POST'], '/users/create[/]',         $controllers['user']('create'));
    $this->map(['GET', 'POST'], '/users/{id}[/]',           $controllers['user']('edit'));
    $this->map(['GET'],         '/users/{id}/delete[/]',    $controllers['user']('delete'));
    
    $app->map(['GET', 'POST'], '[/]',      $controllers['dashboard']('main'));     
});

# User routes:
$app->group('/', function () use ($app) {
    
});