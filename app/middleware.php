<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

//Auth middleware to check if the user is logged in with a valid session
$authorize = function (\Psr\Http\Message\RequestInterface $request, \Psr\Http\Message\ResponseInterface $response, $next)
{
    //TODO might also want to check if a token exists
    if (!isset($_SESSION['username']) || $_SESSION['username'] == "NULL")
    {
        return $response->withRedirect("/login");
    }
    $res = $next($request, $response);

    return $res;
};