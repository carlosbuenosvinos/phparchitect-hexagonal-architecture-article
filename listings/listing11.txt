<?php

class RateIdeaUseCaseTest extends \PHPUnit_Framework_TestCase
{
    // ...

    /**
     * @test
     */
    public function whenUpdatingInReadOnlyAnIdeaAnExceptionShouldBeThrown()
    {
        $this->setExpectedException('NotAvailableRepositoryException');
        $ideaRepository = new WriteNotAvailableRepository();
        $useCase = new RateIdeaUseCase($ideaRepository);
        $response = $useCase->execute(
            new RateIdeaRequest(1, 5)
        );
    }
}

class WriteNotAvailableRepository implements IdeaRepository
{
    public function find($id)
    {
        $idea = new Idea();
        $idea->setId(1);
        $idea->setTitle('Subscribe to php[architect]');
        $idea->setDescription('Just buy it!');
        $idea->setRating(5);
        $idea->setVotes(10);
        $idea->setAuthor('hi@carlos.io');

        return $idea;
    }

    public function update(Idea $idea)
    {
        throw NotAvailableException();
    }
}
