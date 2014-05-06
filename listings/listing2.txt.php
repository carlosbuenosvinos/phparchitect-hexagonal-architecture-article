<?php
class PostRepository
{
    private $client;

    public function __construct()
    {
         $this->client = new Predis\Client();
    }

    public function find($id)
    {
        $idea = $this->client->get('idea_'.$id);
        if (!$idea) {
            return null;
        }

        return $idea;
    }

    public function save($idea)
    {
        $this->client->set('idea_'.$idea->getId(), $idea);
    }
}

class PostController extends Zend_Controller_Action
{
    public function voteAction()
    {
        $postId = $this->request->getParam('id');
        $rating = $this->request->getParam('rating');

        $postRepository = new PostRepository();
        $post = $postRepository->find($postId);
        if (!$post) {
            throw new Exception('Post does not exist');
        }

        $post->addVote($rating);
        $postRepository->save($post);

        $this->redirect('/post/'.$postId);
    }
}
