<?php
// DIC configuration

$container = $app->getContainer();


// -----------------------------------------------------------------------------
// Service providers
// -----------------------------------------------------------------------------

// Twig
$container['view'] = function ($c)
{
    $settings = $c->get('settings');
    $view     = new Slim\Views\Twig($settings['view']['template_path'], $settings['view']['twig']);

    // Add extensions
    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri()));
    $view->addExtension(new Twig_Extension_Debug());

    return $view;
};

// Flash messages
$container['flash'] = function ($c)
{
    return new Slim\Flash\Messages;
};

// -----------------------------------------------------------------------------
// Service factories
// -----------------------------------------------------------------------------

// monolog
$container['logger'] = function ($c)
{
    $settings = $c->get('settings');
    $logger   = new Monolog\Logger($settings['logger']['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['logger']['path'], Monolog\Logger::DEBUG));

    return $logger;
};

$container['api'] = function ($c)
{
    $settings  = $c->get('settings');
    $api_token = !empty($_SESSION['api_token']) ? $_SESSION['api_token'] : NULL;
    $api       = new App\REDCap\API($settings['api']['url'], $api_token);

    return $api;
};

$container['db'] = function ($c)
{
    $settings = $c->get('settings');

    return new mysqli($settings['db']['host'], $settings['db']['user'], $settings['db']['password'], $settings['db']['database']);
};

// -----------------------------------------------------------------------------
// Error Handles
// -----------------------------------------------------------------------------
$container['notFoundHandler'] = function ($c)
{
    return function ($request, $response) use ($c)
    {
        $view = $c->get('view');

        return $view->render($c['response'], 'error/404.twig');
    };
};

$container['errorHandler'] = function ($c)
{
    return function ($request, $response, $exception) use ($c)
    {
        $view = $c->get('view');

        $message = $exception->getMessage();

        return $view->render($c['response'], 'error/500.twig', [
            'message' => $message
        ]);
    };
};


// -----------------------------------------------------------------------------
// Action factories
// -----------------------------------------------------------------------------

$container[App\Action\IndexGet::class] = function ($c)
{
    return new App\Action\IndexGet($c->get('view'), $c->get('logger'), $c->get('api'));
};

$container[App\Action\LoginGet::class] = function ($c)
{
    return new App\Action\LoginGet($c->get('view'), $c->get('flash'));
};

$container[App\Action\LoginPost::class] = function ($c)
{
    $settings = $c->get('settings');

    return new App\Action\LoginPost($c->get('view'), $c->get('logger'), $c->get('db'), $c->get('flash'), $settings['api']['project_id']);
};

$container[App\Action\LogoutGet::class] = function ($c)
{
    return new App\Action\LogoutGet($c->get('flash'));
};

$container[App\Action\NewGet::class] = function ($c)
{
    $settings = $c->get('settings');

    return new App\Action\NewGet($c->get('view'), $c->get('logger'), $settings['api']['project_id']);
};

$container[App\Action\RecordGet::class] = function ($c)
{
    return new App\Action\RecordGet($c->get('view'),$c->get('logger'), $c->get('api'));
};

$container[App\Action\FieldsGet::class] = function ($c)
{
    return new App\Action\FieldsGet($c->get('view'),$c->get('logger'), $c->get('api'));
};