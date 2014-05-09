<?php
class VotePostRequest
{
    public $postId;
    public $rating;

    public function __construct($postId, $rating)
    {
        $this->postId = $postId;
        $this->rating = $rating;
    }
}

class VotePostResponse
{
    public $post;

    public function __construct($post)
    {
        $this->post = $post;
    }
}

class PostController extends Zend_Controller_Action
{
    public function voteAction()
    {
        $postId = $this->request->getParam('id');
        $rating = $this->request->getParam('rating');

        $postRepository = new RedisPostRepository();
        $useCase = new VotePostUseCase($postRepository);
        $request = new VotePostRequest($postId, $rating);
        $response = $useCase->execute($request);

        $this->redirect('/post/'.$response->post->getId());
    }
}

class VotePostUseCase
{
    private $postRepository;

    public function __construct($postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function execute($request)
    {
        $postId = $request->postId;
        $rating = $request->rating;

        $post = $this->postRepository->find($postId);
        if (!$post) {
            throw new Exception('Post does not exist');
        }

        $post->addVote($rating);
        $this->postRepository->save($post);
    }
}
