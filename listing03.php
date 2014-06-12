<?php

class IdeaController extends Zend_Controller_Action
{
    public function rateAction()
    {
        $ideaId = $this->request->getParam('id');
        $rating = $this->request->getParam('rating');

        $ideaRepository = new MySQLIdeaRepository();
        $idea = $ideaRepository->find($ideaId);
        if (!$idea) {
            throw new Exception('Idea does not exist');
        }

        $idea->addRating($rating);
        $ideaRepository->update($idea);

        $this->redirect('/idea/'.$ideaId);
    }
}

interface IdeaRepository
{
    /**
     * Finds an idea by id
     *
     * @param int $id
     * @return null|Idea
     */
    public function find($id);

    /**
     * Updates an idea
     *
     * @param Idea $idea
     */
    public function update(Idea $idea);
}

class MySQLIdeaRepository implements IdeaRepository
{
    // ...
}
