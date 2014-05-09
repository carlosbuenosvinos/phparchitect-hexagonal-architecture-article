<?php

/**
 * Class IdeaController
 */
class IdeaController extends Zend_Controller_Action
{
    public function voteAction()
    {
        $postId = $this->request->getParam('id');
        $rating = $this->request->getParam('rating');

        $client = new Predis\Client();
        $post = $client->get('post_'.$postId);
        if (!$post) {
            throw new Exception('Post does not exist');
        }

        $post->addVote($rating);
        $client->set('post_'.$postId, $post);

        $this->redirect('/post/'.$postId);
    }

    private function pp()
    {
        $db = new mysqli('localhost', 'root', '', 'blog');
        $stmt = $db->prepare('SELECT * FROM posts WHERE id = ?');
        $stmt->bind_param('i', $postId);
        $stmt->execute();
        $db->query('');
    }
}
