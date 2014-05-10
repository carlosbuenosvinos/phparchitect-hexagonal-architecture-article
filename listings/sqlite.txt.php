<?php
class SqliteIdeaRepository implements IdeaRepository
{
    private $client;

    public function __construct()
    {
         $this->client = new SQLiteDatabase('mydb');
    }

    public function find($id)
    {
        $row = $this->client->query(
            'SELECT * FROM ideas WHERE id = '.$id
        );

        if (!$ideaRow) {
            return null;
        }

        return new Idea(
            $row['id'],
            $row['title'],
            $row['author'],
            $row['rating'],
            $row['votes']
        );
    }

    public function save($idea)
    {
        $this->client->set('idea_'.$idea->getId(), $idea);
    }
}

class IdeaController extends Zend_Controller_Action
{
    public function voteAction()
    {
        $ideaId = $this->request->getParam('id');
        $rating = $this->request->getParam('rating');

        $ideaRepository = new SqliteIdeaRepository();
        $idea = $ideaRepository->find($ideaId);
        if (!$idea) {
            throw new Exception('Idea does not exist');
        }

        $idea->addVote($rating);
        $ideaRepository->save($idea);

        $this->redirect('/idea/'.$ideaId);
    }
}
