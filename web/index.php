<?php

require('../vendor/autoload.php');

$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register view rendering
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

// Our web handlers

$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('index.twig');
});


$app->get('/aboutme', function() use($app) {
    $app['monolog']->addDebug('logging output.');
    return $app['twig']->render('aboutme.html');
});


$db = parse_url(getenv('DATABASE_URL'));
$db["path"] = ltrim($db["path"], "/");

//
//$pdo = new PDO("pgsql:" . sprintf("host=%s;port=%s;user=%s;password=%s;dbname=%s",
//        $db["host"],
//        $db["port"],
//        $db["user"],
//        $db["pass"],
//        ltrim($db["path"], "/")
//    ));

//$app->register(new Csanquer\Silex\PdoServiceProvider\Provider\PDOServiceProvider('pdo'),
//    array(
//        'pdo.server' => array(
//            'driver'   => 'pgsql',
//            'user' => $db["user"],
//            'password' => $db["pass"],
//            'host' => $db["host"],
//            'port' => $db["port"],
//            'dbname' => ltrim($db["path"],'/')
//        )
//    )
//);

$app->get('/db/', function() use($app) {
    $st = $app['pdo']->prepare('SELECT name FROM test_table');
    $st->execute();

    $names = array();
    while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
        $app['monolog']->addDebug('Row ' . $row['name']);
        $names[] = $row;
    }

    return $app['twig']->render('database.twig', array(
        'names' => $names
    ));
});

$app->run();
