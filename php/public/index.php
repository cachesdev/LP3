<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . "/../vendor/autoload.php";

$app = AppFactory::create();
$app->setBasePath("/api");

$app->get("/ping", function (Request $request, Response $response) {
    $response->getBody()->write(
        json_encode([
            "time" => (new DateTime("now", new DateTimeZone("UTC")))->format(
                "c"
            ),
            "service" => "php-api",
        ])
    );
    return $response
        ->withHeader("Content-Type", "application/json")
        ->withStatus(200);
});

$app->run();
