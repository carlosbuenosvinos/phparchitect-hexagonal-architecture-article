require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

// ... more routes

$app->post(
    '/api/vote',
    function(Request $request) {
        $postId = $request->get('id');
        $rating = $request->get('rating');

        $postRepository = new RedisPostRepository();
        $useCase = new VotePostUseCase($postRepository);
        $request = new VotePostRequest($postId, $rating);
        $response = $useCase->execute($request);

        return $app->json($response->post);
    }
);

$app->run();
