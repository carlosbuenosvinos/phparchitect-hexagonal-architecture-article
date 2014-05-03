interface PostRepository
{
    public function find($id);
    public function save($post);
}

class RedisPostRepository implements PostRepository
{
    private $client;

    public function __construct()
    {
         $this->client = new Predis\Client();
    }

    public function find($id)
    {
        $post = $client->get('post_'.$postId);
        if (!$post) {
            return null;
        }

        return $post;
    }

    public function save($post)
    {
        $this->client->set('post_'.$post->getId(), $post);
    }
}

class PostController extends Zend_Controller_Action
{
    public function voteAction()
    {
        $postId = $this->request->getParam('id');
        $rating = $this->request->getParam('rating');

        $postRepository = new RedisPostRepository();
        $post = $postRepository->find($postId);
        if (!$post) {
            throw new Exception('Post does not exist');
        }

        $post->addVote($rating);
        $postRepository->save($post);

        $this->redirect('/post/'.$postId);
    }
}
