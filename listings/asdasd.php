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
