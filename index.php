<?php

require __DIR__ . '/vendor/autoload.php';

use Ilex\SwoolePsr7\SwooleServerRequestConverter;
use Nyholm\Psr7\Factory\Psr17Factory;
use Slim\App;

$psr17Factory = new Psr17Factory;
$requestConverter = new SwooleServerRequestConverter(
    $psr17Factory,
    $psr17Factory,
    $psr17Factory,
    $psr17Factory
);

$app = new App($psr17Factory);
$app->addRoutingMiddleware();
$app->get('/', function ($request, $response, $args) {
    $templates = new League\Plates\Engine(__DIR__ . '/views');
    $response->getBody()->write($templates->render('view1', ['name' => 'Joao']));
    return $response;
});

$http = new Swoole\Http\Server("0.0.0.0", 9501);

$http->on('start', function ($server) {
    echo "HTTP Server ready at http://0.0.0.0:9501";
});

$http->on('request', function ($request, $response) use ($app, $requestConverter) {
    $psr7Request = $requestConverter->createFromSwoole($request);
    $psr7Response = $app->handle($psr7Request);
    $converter = new SwooleResponseConverter($response);
    $converter->send($psr7Response);
});

$server->start();