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

![Figure 1: Use case](figures/figure1.png)

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
(memory, database, nosql, etc.). However, we should be able to
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
take a look to the Single Responsability Principle (SRP).

Listing 1 is clearly this case. If we want to move to Redis
or add the author notification feature, you'll have to update
 the `rateAction` method.

So, we must decouple our code and encapsulate the
responsability to deal with fetching and persisting
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
_application boundary_ and the __infrastructure boundary__
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
IdeaRepository for SQLite, a file or a memory
using arrays.

## Decouple framework and business

We have seen already how easily could be changing
from one persistence strategy to another one. However,
the persistence is not the only edge from our hexagon.
What about how the user interacts with the application?

[Listing 6](listings/listing6.txt)

Sometimes, the number of parameters that a use case
recieves can be long. In order to organize them, it's
quite common to use a Data Transfer Object (DTO) to
pass the message from the framework to the use case.

[Listing 7](listings/listing7.txt)

Maybe, your code is not connecting

This is not going to change. It's not
related with what database we are using

Believe it or not, one day you will have to change your
framework or the libraries you are using for a specific
task.

What about moving all that logic, inside a class for doing
that. Can I call it a Service?

Our _delivery mechanism_ should behave accordingly.


This service is a special one. It is the entry point for
your application, not your framework, so some guys call it
Application Service, Interactor or Use Case.
Do you remember when you were at University?

That's pure business logic. A UseCase object,
there is nothing related to databases, frameworks and so on. Cool.

## Rating a idea using the API

During the day, your Product Owner comes to you and says:
 "by the way, a user should be able to rate a idea using
 our mobile app. I think we will need to update the API,
 could you do it for this sprint?". Here's the PO again.
 This user story is
 "No problem!".
 Business is really happy with you. Keep going!

As Robert C. Martin says: "The Web is a delivery
mechanism [...] Your system architecture should be as
ignorant as possible about how it is to be delivered. You
 should be able to deliver it as a console app,
 or a web app, or even a web service app,
 without undue complication or change to the fundamental
 architecture".

Your current API is built using Silex <http://silex
.sensiolabs.org/>

[Voting using the API](listings/silex-api.txt)

"Man! I remember those 4 lines of code. They look exactly
the same as the web application". That's right,
because the use case and the business rules remains the
same, the code to run the business logic should be the same.
We are just providing our users another way for rating a
idea, what it's called another _delivery method_.

The main difference is how we have created the
`VotePostRequest` from. On the first
example, it was from a ZF request and now from a Silex
request.

## Console app rating

While you are testing this feature using the web or the api,
 you realize that it would be nice to have a command line
 to do it, so you'll be faster. Maybe you can thing about
  a cronjob.

Your current API is built using Silex <http://silex
.sensiolabs.org/>

[Rating console command](listings/symfony-console.txt)

Man! I remember those 4 lines of code. They look exactly
the same as the web application. That's right,
because the use case and the business rules are the same,
the code should be the same. We only have provided
another delivery method. The main difference is how we
have created the `VotePostRequest` from. On the first
example, it was from a ZF request and now from a Silex
request.

## Add author email notification



## Testing your code

Tomorrow, what about moving from MySQL to Redis?

During the same sprint, your architect comes to you and says:
?what about moving the voting story to redis? Could you do it for this sprint??, ?Yep, no problem!?. Man, you are on fire.

<Code for the new Adapter>

## Testing your code

Michael Feathers introduced a definition of legacy code
as _code without tests_. You don't want your code to be
legacy just born, do you?



[](listings/usecase-test.txt)

Bam! 100% Coverage. Maybe, next time we can do it using
TDD so the test will come first. However,
testing this feature was really easy.

## Arggg, so many dependencies!

Is that normal that I have so many dependencies to create by hand?

Yes, it is. Service Container


## How can I apply it to existing code

"Be refactor my friend".

## Migrating to new framework




## Let's recap

We encapsulate a user story business rules inside a
Use Case or Interactor. We build
the Use Case request from our framework request,
 instantiate the Use Case and all its dependencies
 and then execute it. If our framework has a Dependency Injection
 component you can use it to simplify the code

nothing special, however it could be using an
adhoc framework, an old open-source or the most
brand new one. It would be exactly the same.


Let?s review
We have completely separate business logic from infrastructure.

Different clients (web, api, console)
Infrastructure (Database, Framework, etc.) agnostic

When should you use it?
If 100% unit-test code coverage is important to your application. Also, if you want to be able to switch your storage mechanism, or any other type of third-party code. The architecture is especially useful for long-lasting applications that need to keep up with changing requirements.

