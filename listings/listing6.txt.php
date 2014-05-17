<?php
class IdeaController extends Zend_Controller_Action
{
    public function voteAction()
    {
        $ideaId = $this->request->getParam('id');
        $rating = $this->request->getParam('rating');

        $ideaRepository = new MySQLIdeaRepository();
        $useCase = new VoteIdeaUseCase($ideaRepository);
        $useCase->execute($ideaId, $rating);

        $this->redirect('/idea/'.$ideaId);
    }
}

class VoteIdeaUseCase
{
    /**
     * @var IdeaRepository
     */
    private $ideaRepository;

    /**
     * @param IdeaRepository $ideaRepository
     */
    public function __construct($ideaRepository)
    {
        $this->ideaRepository = $ideaRepository;
    }

    /**
     * Executes this use case
     *
     * @param int $ideaId
     * @param int $rating
     * @throws Exception
     */
    public function execute($ideaId, $rating)
    {
        $idea = $this->ideaRepository->find($ideaId);
        if (!$idea) {
            throw new Exception('Idea does not exist');
        }

        $idea->addRating($rating);
        $this->ideaRepository->update($idea);
    }
}
