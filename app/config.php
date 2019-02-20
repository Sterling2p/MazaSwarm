<?php

$config = [
    
    # Database:
    'db.ngin'     => 'mysql',
    'db.name'     => 'sterlingpeyton',
    'db.user'     => 'root',
    'db.pass'     => 'root',
    'db.host'     => 'localhost',
    
    # Site settings:
    'site.scheme'    => 'https',
    'site.domain'    => 'local',
    'site.dir'       => '/fiverr/sterlingpeyton/',
    'site.name'      => 'Maza Swarm',
    'site.admin.dir' => 'admin/',
    
    # Slim settings:
    'slim.debug'  => true,
    
    # Twig settings:
    'twig.dir'    => 'views',
    'twig.cache'  => false,
    
    # Twig globals:
    'twig.globals' => [
        'css_dir' => 'views/assets/css/',
        'js_dir'  => 'views/assets/js/'
    ],
    
    # Auth settings:
    'auth.key'             => 'U7N52hb83XKK61V4VDJ278wBFXlX9I3h',
    'auth.session.uid'     => 'uid',
    'auth.cookie.remember' => 'SESS_TOKEN',
    
    # SMTP settings:
    'smtp.host'   => 'smtp.mailgun.org',
    'smtp.user'   => 'postmaster@sandbox54727bce61764a63892983d28460cf79.mailgun.org',
    'smtp.pass'   => 'e2f1ea7650f74cfaea3ccf770fb0a15b',
    'smtp.port'   => '465'
];