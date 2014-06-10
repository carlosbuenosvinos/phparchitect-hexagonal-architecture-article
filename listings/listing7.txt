<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new \Silex\Application();

// ... more routes

$app->get(
    '/api/rate/idea/{ideaId}/rating/{rating}',
    function($ideaId, $rating) use ($app) {
        $ideaRepository = new RedisIdeaRepository();
        $useCase = new RateIdeaUseCase($ideaRepository);
        $response = $useCase->execute(
            new RateIdeaRequest($ideaId, $rating)
        );

        return $app->json($response->idea);
    }
);

$app->run();
