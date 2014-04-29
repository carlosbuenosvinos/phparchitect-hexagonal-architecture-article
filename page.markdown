# Hexagonal Architecture with PHP
###### by Carlos Buenosvinos

> With the raise of DDD, architectures that promote
domain centric designs are getting more popular.
This is the case of CQRS, Event Sourcing or **Hexagonal
Architecture** (aka. Ports and Adapters). Let's check
how to build testable, infrastructure agnostic apps.

It?s monday morning, another sprint is starting and you
are reviewing some user stories with your team and your
Product Owner. ?As a not logged user, I want to vote a
post and notify the author via email.?, that?s a really
cool feature, isn?t it? You?ll start your day with that one.

## First approach

Your company web is still using a ZF1 app (don't laugh)
so after some hours of work you get something like:

~~~~
...
public function voteAction()
{
    $postId = $this->request->getParam('id');
    $rating = $this->request->getParam('rating');

    $post = PostDAO::find($postId);
    if (!$post) {
        throw new Exception('Post does not exist');
    }

    $post->addVote($rating);

    $client = new Predis\Client();
    $client->set('post_'.$postId, 'bar');


}
...
~~~~

Mmmm, ok, it works. However, all your business logic is
inside a controller action.
Believe me, one day, you will have to change your framework.
What about moving all that logic, inside a class for doing that. Can I call it a Service?

## New guy is coming

"

<Complete Code for Use Case>

This service is a special one. It is the entry point for your application, not your framework, so some guys call it Application Service, Interactor or Use Case. Do you remember when you were at University?

<Use Case picture>


That?s pure business logic. A UseCase object, there is nothing related to databases, frameworks and so on. Cool.


## Add it to the API

During the day, Product Owner comes to you and says: ?by the way
we should also be able to do it through the mobile app, so we should
update the API, could it be done in this sprint??, ?No problem!?.
Business is really happy with you guys. Keep going!

"The Web is a delivery mechanism [...] Your system architecture should
be as ignorant as possible about how it is to be delivered. You should
be able to deliver it as a console app, or a web app, or even a web
service app, without undue complication or change to the fundamental
architecture".
- Robert C. Martin

Our API is a Silex <http://silex.sensiolabs.org/> application, so let?s write some code.

<CODE>

Man! I remember those 4 lines of code. They look exactly the same as the web application.

 look the same as the web application. That means reusing. The main difference here is how we create the request to





Tomorrow, what about moving from MySQL to Redis?

During the same sprint, your architect comes to you and says: ?what about moving the voting story to redis? Could you do it for this sprint??, ?Yep, no problem!?. Man, you are on fire.

<Code for the new Adapter>




New CTO asking for doing Unit Testing


## Add it to the API

<Test Code>

Michael Feathers[1] introduced a definition of legacy code as code without tests.

Bam! 100% Coverage. Maybe, next time we can do it using TDD so the test will come first.

## Arggg, so many dependencies!

Is that normal that I have so many dependencies to create by hand?

Yes, it is. Service Container


## Who I have to thank?
Alistair Cockburn invented it in 2005. It is a response to the desired
to create thoroughly testable applications. As Cockburn says: "Allow an
application to equally be driven by users, programs, automated test or
batch scripts, and to be developed and tested in isolation from its eventual run-time devices and databases."



Let?s review
We have completely separate business logic from infrastructure.

Different clients (web, api, console)
Infrastructure (Database, Framework, etc.) agnostic

Where does it come from?
When should you use it?
If 100% unit-test code coverage is important to your application. Also, if you want to be able to switch your storage mechanism, or any other type of third-party code. The architecture is especially useful for long-lasting applications that need to keep up with changing requirements.

