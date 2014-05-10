<?php
class VoteIdeaRequest
{
    public $ideaId;
    public $rating;

    public function __construct($ideaId, $rating)
    {
        $this->ideaId = $ideaId;
        $this->rating = $rating;
    }
}

class VoteIdeaResponse
{
    public $idea;

    public function __construct($idea)
    {
        $this->idea = $idea;
    }
}

class IdeaController extends Zend_Controller_Action
{
    public function voteAction()
    {
        $ideaId = $this->request->getParam('id');
        $rating = $this->request->getParam('rating');

        $ideaRepository = new RedisIdeaRepository();
        $useCase = new VoteIdeaUseCase($ideaRepository);
        $request = new VoteIdeaRequest($ideaId, $rating);
        $response = $useCase->execute($request);

        $this->redirect('/idea/'.$response->idea->getId());
    }
}

class VoteIdeaUseCase
{
    private $ideaRepository;

    public function __construct($ideaRepository)
    {
        $this->ideaRepository = $ideaRepository;
    }

    public function execute($request)
    {
        $ideaId = $request->ideaId;
        $rating = $request->rating;

        $idea = $this->ideaRepository->find($ideaId);
        if (!$idea) {
            throw new Exception('Idea does not exist');
        }

        $idea->addVote($rating);
        $this->ideaRepository->save($idea);
    }
}
