# Hexagonal Architecture with PHP
###### by Carlos Buenosvinos

> With the rise of DDD,
architectures that promote domain centric designs are
getting more popular. This is the case of **Hexagonal
Architecture**, also known as **Ports and Adapters**,
that seems to have being rediscovered just now by PHP
developers. Invented in 2005 by Alistair Cockburn,
one of the Agile Manifesto authors,
the Hexagonal Architecture allows an application to
equally be driven by users, programs, automated test or
batch scripts, and to be developed and tested in
isolation from its eventual run-time devices and
databases. That means, agnostic infrastructure web
applications that are easier to test,
to write and to maintain. Let's see how to apply it
using real PHP examples.

Your company is building a brainstorming system called
_Idy_. Users add and rate ideas so the most interesting ones
can be implemented in a company. It's monday morning,
another sprint is starting and you are reviewing some
user stories with your team and your Product Owner.
**"As a not logged user, I want to rate an
idea and the author should be notified by email"**,
that's a really important one, isn't it?

## First approach

As a good developer, you decide to divide
and conquer the user story, so you'll start with the
first part, "I want to rate an idea". After that,
you will face "the author should be notified by email".
That sounds like a plan.

In terms of business rules, rating an idea is as easy
as finding the idea by its identifier in the ideas
repository, where all the ideas live, add the rating,
recalculate the average and save the idea back. If
the idea does not exist or the repository is not
available we should throw an exception so we can
show an error message, redirect the user or do whatever
business wants.

In order to _execute_ this _use case_, we just
need the idea identifier and the rating from
the user. Two integers that would come from the user
request.

Your company web application is dealing with a
Zend Framework 1 legacy application. As most of
the companies, parts of your app are newer,
more SOLID and others just a big ball of
mud. However, you know that it's not a matter of
what framework are you using, it's about of writing
clean code than .

You're trying to apply some Agile principles
you remember from your last conference, how it was,
yeah, I remember "make it work, make it right, make it fast".
After some time working you get something like Listing 1.

[Listing 1](listings/listing1.txt)

I know what readers are thinking: "Who is going to access
data directly from the controller? This is a 90's example!",
ok, ok, you're right. If you are already using a framework,
it's probable that you are also using an ORM. Maybe done
by yourself or any of the existing ones such as Doctrine,
Eloquent, Zend\DB, etc. If it's the case, you are one step
further from those who have some Database connection object
but don't count your chickens before they're hatched.

For newbies, Listing 1 code just works. However, if you
take a closer look to the Controller, you'll see more
than business rules, you'll also see how your web framework
routes a request into your business rules, references to
the database or how to connect to it. So close, you see
references to your **infrastructure**.

Infrastructure is the **detail that makes your business rules
work**. At last, we need some way to get to them (api, web, console
apps, etc.) or we need some physical place to store our ideas
(memory, database, NoSQL, etc.). However, we should be able to
exchange any of these pieces with another that behaves in the
same way but with different implementations. What about
starting with the Database access?

All those `Zend_DB_Adapter` connection (or straight mysql commands
if it's your case) are asking for be promoted into some sort
of object that encapsulates fetching and persisting Idea objects.
They are asking for being a Repository.

## Repositories and the Persistence Edge

Whether there is a change in the business rules
or in the infrastructure, we must edit the same piece of code.
Believe me, in CS, you don't want people touching the same
piece of code for different reasons. Make your functions
do one and just one thing so it's less probable having people
messing around with the same piece of code. You should 
take a look to the Single Responsibility Principle (SRP).

Listing 1 is clearly this case. If we want to move to Redis
or add the author notification feature, you'll have to update
the `rateAction` method.

So, we must decouple our code and encapsulate the
responsibility to deal with fetching and persisting
ideas into another object. The best way, as explained
before, is using a Repository. Challenged accepted!
Let's see the results in Listing 2.

[Listing 2](listings/listing2.txt)

The result is nicer. The `rateAction` of the `IdeaController`
is more understandable. When read, it talks about business
rules. `IdeaRepository` is a **business concept**. When talking
with business guys, they understand what an `IdeaRepository` is:
A place where I put Ideas and get them.

A Repository "mediates between the domain and data mapping
layers using a collection-like interface for accessing
domain objects." as found in Martin Fowler patterns catalog.

If you are already using an ORM such as Doctrine, your current
repositories extend from an `EntityRepository`. If you need to
 get one of those repositories, you ask to Doctrine
 `EntityManager` do the job. The resulting code would be
the same, except for finding in the controller action an access
to the `EntityManager` for getting the `IdeaRepository`.

At this point, we can see in the landscape one of the edges
of our hexagon, the _persistence_ edge. However, this side is
not well drawn, there is still some relationship between what
an `IdeaRepository` is and how it's implemented.

In order to make an effective separation between our
_application boundary_ and the _infrastructure boundary_
we need an additional step. We need explicitly decouple
behaviour from implementation using some sort of interface.

## Decoupling Business and Persistence

Have you ever experienced the situation when you
start talking to your Product Owner, Business Analyst
or Project Manager about your issues with the Database?
Could you remember their faces when explaining
how to persist and fetch an object? They were not having
any idea about what you were talking about.

The truth is that they don't care, but it's ok. If
you decide to store the ideas in a MySQL server, Redis
or SQLite is your problem, not their. Remember, from a
business standpoint, **your infrastructure is a detail**.
Business rules are not going to change whether you use Symfony
or Zend Framework, MySQL or PostgreSQL, REST or SOAP, etc.

That's why it's important to decouple our IdeaRepository
from its implementation. The easiest way is to use a proper
interface. How could we that? Let's take a look to Listing 3.

[Listing 3](listings/listing3.txt)

Easy, isn't it? We have extracted the `IdeaRepository`
behaviour into an interface, rename the IdeaRepository into
`MySQLIdeaRepository` and update the `rateAction` to 
use our `MySQLIdeaRepository`. But what's the benefit?

We can now exchange the repository used in the controller
with any that implements the same interface. So, let's try
another different implementation.

## Migrating our Persistence to Redis

During the sprint and after talking to some mates, you
realize that using a NoSQL strategy could improve the
performance of your feature. Redis is one of your
best friends. Go for it and show me your Listing 4.

[Listing 4](listings/listing4.txt)

Easy again. You've created a `RedisIdeaRepository`
that implements `IdeaRepository` interface and we
have decided to use Predis as a connection manager.
Code looks smaller, easier and faster. But what about
the controller? It remains the same, we have just
change what repository to use, but it was just one line
of code.

As an exercise for the reader, try to create the
IdeaRepository for SQLite, a file or a in-memory
using arrays.

## Decouple Business and Web Framework

We have seen already how easily could be changing
from one persistence strategy to another one. However,
the persistence is not the only edge from our Hexagon.
What about how the user interacts with the application?

Your CTO has set up in the roadmap that your team is
moving to Symfony2, so when developing new features in
you current ZF1 application, we would like to make the
future but immediate migration easier. That's tricky,
show me your Listing 5.

[Listing 5](listings/listing5.txt)

Let's review the changes. Our controller is not having
any business rules at all. We have push all the logic
inside a new object called `RateIdeaUseCase` that
encapsulates it. This object is also known as
Controller, Interactor or Application Service.

The magic is done by the `execute` method. All the
dependencies such as the `RedisIdeaRepository` is passed
as an argument to the constructor. All the references
to an `IdeaRepository` inside our use case are pointing
to the interface not to any of the concrete implementation.

That's really cool. If you take a look inside `RateIdeaUseCase`,
there is nothing talking about MySQL or Zend Framework.
No references, no instances, no annotations, nothing. Is
like your infrastructure doesn't mind. It just talks about
business logic.

Additionally, we have also tune the Exceptions we throw.
Business processes has also exceptions. NotAvailableRepository
and IdeaDoesNotExist are. Based on the one thrown we can
react in different ways in the framework boundary.

Sometimes, the number of parameters that a use case
receives can be too many. In order to organize them,
it's quite common to build a _use case request_
using a Data Transfer Object (DTO) to pass them together.
Let's see how you could solve this in Listing 6.

[Listing 6](listings/listing6.txt)

The main changes here are introducing two new objects, a
Request and a Response. It's not mandatory, maybe a use
case has no request or response. Another important detail
is how you build this request. In this case, we are building
it getting the parameters from ZF request object.

Ok, but wait, what's the real benefit? It's easier to change
from one framework to other, or execute our use case from
another _delivery mechanism_. Let's see this point.

## Rating an idea using the API

During the day, your Product Owner comes to you and says:
"by the way, a user should be able to rate an idea using
our mobile app. I think we will need to update the API,
could you do it for this sprint?". Here's the PO again.
"No problem!". Business is impressed with your commitment.

As Robert C. Martin says: "The Web is a delivery
mechanism [...] Your system architecture should be as
ignorant as possible about how it is to be delivered. You
should be able to deliver it as a console app,
or a web app, or even a web service app,
without undue complication or change to the fundamental
architecture".

Your current API is built using Silex, the PHP micro-framework
based on the Symfony2 Components. Let's go for it in Listing 7.

[Listing 7](listings/listing7.txt)

Is there anything familiar to you? Can you identify 
some code that you have seen before? I'll give you a clue.

~~~~
$ideaRepository = new RedisIdeaRepository();
$useCase = new RateIdeaUseCase($ideaRepository);
$response = $useCase->execute(
    new RateIdeaRequest($ideaId, $rating)
);
~~~~

"Man! I remember those 3 lines of code. They look exactly
the same as the web application". That's right,
because the use case encapsulates the business rules
you just need to prepare the request, get the response
and act accordingly.

We are just providing our users another way for rating an
idea, what it's called another _delivery mechanism_.

The main difference is how we have created the
`RateIdeaRequest` from. On the first example,
it was from a ZF request and now from a Silex
request using `$request` object.

## Console app rating

Sometimes, some use case is going to be executed from
a cronjob o command line. As an example, batch processing
or some testing command lines for accelerating the
development.

While testing this feature using the web or the api,
you realize that it would be nice to have a command line
to do it, so you don't have to go through the browser.

If you are using shell scripts files, I suggest you
to check the Symfony Console component. How would the
code look like? 

[Listing 8](listings/listing8.txt)

Again those 3 lines of code. As before, the Use Case
and its business logic remains untouched, we are just
providing a new _delivery mechanism_. Congratulations, 
you've discovered the _user side_ hexagon edge.

There is too much to do. As you may heard, a real
craftsman does TDD. We have already started our story
so we must be ok with just testing after.

## Testing rating an idea use case

Michael Feathers introduced a definition of legacy code
as _code without tests_. You don't want your code to be
legacy just born, do you?

In order to test this UseCase object, you decide to
start with the easiest part, what happen if the
repository is not available? How can we generate such a
behaviour? Do we stop our Redis server while running
the unit tests? No. We need to have an object that
has such behaviour. Let's use a _mock_ object in Listing 9.

[Listing 9](listings/listing9.txt)

Nice. `NotAvailableRepository` has the behaviour
that we need and we can use it with `RateIdeaUseCase`
because implements `IdeaRepository` interface.

Next case to test is what happen if the idea is not
in the repository. Listing 10 shows the code.

[Listing 10](listings/listing10.txt)

Here, we use the same strategy but with an `EmptyIdeaRepository`.
It also implements the same interface but the implementation
is returning always `null` regardless what identifier the 
`find` method receives.

Why are we testing this cases?, remember Kent Beck's words:
"Test everything that could possibly break".

Let's carry on with the rest of the feature. We need to check
a special case that is related with read available but write
not repository. Solution can be found in Listing 11.

[Listing 11](listings/listing11.txt)

Ok, now it's remaining the key part of the feature. We
have different ways of testing this, we can write our
own mock or use a mocking framework such as mockery
or prophecy. Let's choose the first one. Another interesting
exercise would be write this example and the previous
ones using one of this frameworks.

[Listing 12](listings/listing12.txt)

Bam! 100% Coverage for the use case. Maybe, next time we
can do it using TDD so the test will come first. However,
testing this feature was really easy because how
decoupling is promoted in this architecture.

Maybe you are thinking what was that:

~~~~
$this->updateCalled = true;
~~~~

We need a way to guarantee that the update method
has being called during the use case execution. This
makes the trick. This _test double_ object is called a
_spy_, cousin of _mocks_.

When to use mocks? As a general rule, use mocks
when crossing boundaries. In this case, we need mocks
because we are crossing from the domain to the
persistence boundary.

What about testing the infrastructure?

## Testing Infrastructure

If you want to arrive to 100% coverage of your
whole application you will have to test also
your infrastructure. Before doing that, you have
to know that those unit tests will be more coupled
to your implementation than the business ones. That
means that the probability to be broken with implementation
details changes is higher. So it's a trade-off you
will have to consider.

So, if you want to continue, we need to do some
modifications. We need to decouple even more. Let's
see the code in Listing 13.

[Listing 13](listings/listing13.txt)

If we want to 100% unit test `RedisIdeaRepository`
we need to be able to pass the `Predis\Connection`
as a parameter to the repository without specifying
TypeHinting so we can pass a mock to force the
code flow necessary to cover all the cases.

That forces us to update the Controller to 
build the Redis connection, pass it to the repository
and the result, pass it to the use case.

Now, it's a matter about creating mocks, test cases
and having fun doing asserts.

## Arggg, so many dependencies!

Is that normal that I have so many dependencies
to create by hand? No. 

Yes, it is. Service Container


## Recommended Path structure

Is that normal that I have so many dependencies to create by hand?

Yes, it is. Service Container


## How can I apply it to existing code

"Be refactor my friend".

## Migrating to new framework



## Next steps

DDD, leaking, factory, etc.



## Let's recap


We encapsulate a user story business rules inside a
Use Case or Interactor. We build the Use Case request
from our framework request, instantiate the Use Case
and all its dependencies and then execute it.
If our framework has a Dependency Injection component
you can use it to simplify the code.



Different clients (web, api, console)
Infrastructure (Database, Framework, etc.) agnostic

When should you use it?
If 100% unit-test code coverage is important to your application. Also, if you want to be able to switch your storage mechanism, or any other type of third-party code. The architecture is especially useful for long-lasting applications that need to keep up with changing requirements.

