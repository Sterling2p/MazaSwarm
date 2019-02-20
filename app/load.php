<?php

require 'vendor/autoload.php';
require 'app/config.php';
require 'app/functions.php';

session_start();

class_alias('\RedBeanPHP\R', 'R');

# Set up ORM:
R::setup( $config['db.ngin'].':dbname='.$config['db.name'].';host='.$config['db.host'], $config['db.user'], $config['db.pass'] );

# Include our Controllers and Models:
foreach( glob('controllers/*.php') as $controller ) { require $controller; }
foreach( glob('models/*.php') as $model )           { require $model;      }