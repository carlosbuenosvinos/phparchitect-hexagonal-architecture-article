class PostController extends Zend_Controller_Action
{
    public function voteAction()
    {
        $postId = $this->request->getParam('id');
        $rating = $this->request->getParam('rating');

        $postRepository = new RedisPostRepository();
        $useCase = new VotePostUseCase($postRepository);
        $useCase->execute($postId, $rating);

        $this->redirect('/post/'.$postId);
    }
}

class VotePostUseCase
{
    private $postRepository;

    public function __construct($postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function execute($postId, $rating)
    {
        $post = $this->postRepository->find($postId);
        if (!$post) {
            throw new Exception('Post does not exist');
        }

        $post->addVote($rating);
        $this->postRepository->save($post);
    }
}
