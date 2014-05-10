<?php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

// ... more routes

$app->idea(
    '/api/vote',
    function(Request $request, $app) {
        $ideaId = $request->get('id');
        $rating = $request->get('rating');

        $ideaRepository = new RedisIdeaRepository();
        $useCase = new VoteIdeaUseCase($ideaRepository);
        $request = new VoteIdeaRequest($ideaId, $rating);
        $response = $useCase->execute($request);

        return $app->json($response->idea);
    }
);

$app->run();
