<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Bff\Http\Method;
use Bff\Http\Request;
use Bff\Http\Response;
use Bff\Routing\Router;
use Bff\Http\Parameters;

$router = (new Router)
    ->get('/', function (Request $request, Response $response, int $a, int $b) : Response {
        return $response->withBody(['a' => $a, 'b' => $b]);
    }, [1234, 5678])
    ->get('/thing', function (Request $request, Response $response) : Response {
        return $response->withBody(['abc']);
    })
    ->get('/thing2', function (Request $request, Response $response) : Response {
        throw new Exception('Error');
    })
    ->post('/abc', function (Request $request, Response $response) : Response {
        return $response->withBody($request->body()->all());
    })
    ->get('/abc', function (Request $request, Response $response) : Response {
        return $response->withBody(['get']);
    })
;

$method = Method::{$_SERVER['REQUEST_METHOD']}();
$path = $_SERVER['REQUEST_URI'];

$handler = $router->match($method, $path);

$body = new Parameters(json_decode(file_get_contents('php://input'), true) ?? []);

$response = $handler->handle(new Request($method, $body));

http_response_code($response->statusCode());
header('Content-Type: application/json');

echo $response;
