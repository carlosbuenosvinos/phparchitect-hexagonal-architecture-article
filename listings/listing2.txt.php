<?php
class PostRepository
{
    private $client;

    public function __construct()
    {
        $this->client = Zend_Db::factory('Pdo_Mysql', array(
            'host'             => '127.0.0.1',
            'username'         => 'webuser',
            'password'         => 'xxxxxxxx',
            'dbname'           => 'test'
        ));
    }

    public function find($id)
    {
        $ideaId = $this->client->quote($id, 'INTEGER');
        $row = $this->client->fetchRow('SELECT * FROM ideas WHERE id = '.$ideaId);
        if (!$row) {
            return null;
        }

        $idea = new Idea();
        $idea->setId($row['id']);
        $idea->setTitle($row['title']);
        $idea->setDescription($row['description']);
        $idea->setRating($row['rating']);
        $idea->setVotes($row['votes']);
        $idea->setAuthor($row['email']);

        return $idea;
    }

    public function save($idea)
    {
        $data = array(
            'title' => $idea->getTitle(),
            'description' => $idea->getDescription(),
            'rating' => $idea->getRating(),
            'votes' => $idea->getVotes(),
            'email' => $idea->getAuthor(),
        );

        if ($id = $idea->getId()) {
            $data['id'] = $id;
            $this->client->update('bugs', array('reported_by = ?' => $id));
        }

        $this->client->insert($data);
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
