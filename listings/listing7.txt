namespace Blog\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class VotePostCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('post:rate')
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
        $postId = $input->getArgument('id');
        $rating = $input->getArgument('rating');

        $postRepository = new RedisPostRepository();
        $useCase = new VotePostUseCase($postRepository);
        $request = new VotePostRequest($postId, $rating);
        $response = $useCase->execute($request);

        $output->writeln('Done!');
    }
}
