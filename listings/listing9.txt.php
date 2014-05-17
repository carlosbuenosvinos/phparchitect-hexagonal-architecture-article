<?php
namespace Idy\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class VoteIdeaCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('idea:rate')
            ->setDescription('Greet someone')
            ->addArgument('id', InputArgument::REQUIRED)
            ->addArgument('rating', InputArgument::REQUIRED)
        ;
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    )
    {
        $ideaId = $input->getArgument('id');
        $rating = $input->getArgument('rating');

        $ideaRepository = new RedisIdeaRepository();
        $useCase = new VoteIdeaUseCase($ideaRepository);
        $request = new VoteIdeaRequest($ideaId, $rating);
        $response = $useCase->execute($request);

        $output->writeln('Done!');
    }
}