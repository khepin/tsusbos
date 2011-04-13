<?php

require_once __DIR__.'/../vendor/Silex/silex.phar';

/**  Bootstraping */
$app = new Silex\Application();
$app['key'] = 'my_key';

$app['autoloader']->registerNamespaces(array('Khepin' => __DIR__,));

$app->register(new Khepin\ShortenerExtension(), array('url_file_name'  =>  __DIR__.'/../resources/urls.ini'));
$app->register(new Silex\Extension\TwigExtension(), array(
    'twig.path' => __DIR__.'/templates',
    'twig.class_path' => __DIR__.'/../vendor/Twig/lib',
    'twig.options' => array('cache' => __DIR__.'/../cache'),
));
$app->register(new \Silex\Extension\MonologExtension(), array(
    'monolog.logfile' => __DIR__.'/../log/tsusbos.log',
    'monolog.class_path' => __DIR__.'/../vendor/Monolog/src',
));

/** App definition */
$app->error(function(Exception $e) use ($app){
    if (!in_array($app['request']->server->get('REMOTE_ADDR'), array('127.0.0.1', '::1'))) {
        return $app->redirect('/');
    }
});

$app->get('/', function() use ($app){
    return $app['twig']->render('index.html.twig');
});

$app->get('/{url_slug}', function($url_slug) use ($app) {
    return $app->redirect($app['shortener']->get($url_slug));
});

$app->get('/view/list', function() use($app){
    return $app['twig']->render('list.html.twig', array('list'  =>  $app['shortener']->getAll()));
});

$app->get('/add/{key}/{url_slug}', function($url_slug, $key) use ($app){
    if($app['key'] != $key){
        throw new Exception('Invalid key');
    }
    $app['shortener']->add($url_slug, $app['request']->get('url'));
    return $app['twig']->render('add.html.twig', array(
        'url_slug'  =>  $url_slug,
        'url'  =>  $app['request']->get('url')));
});

return $app;