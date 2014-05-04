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

It's monday morning, another sprint is starting and you
are reviewing some user stories with your team and your
Product Owner. "As a not logged user, I want to rate a
post and the author should be notified by email.",
that's a really cool feature to add into your blog
system, isn't it?

## Let's start

Let's start with "I want to rate a post".

In terms of business rules, rating a post is as easy
as finding the post by id in the post repository,
where all the posts live, add the rating,
recalculate the average and save the post.

If the post does not exist or the repository is not
available we should throw an exception and our _delivery
method_[1] should behave accordingly.

Your company web application is using Zend Framework 1
and MySQL. After some time working you get something
like Listing 1.

[Listing 1](listings/listing1.txt)

I know what you are thinking: "Who does not use an". If
you are already using repositories (handmade, doctrine,
zend db, etc.) nice. Just in case, for newbies,

Mmmm, ok, it works. However, all your business
 logic is
inside a controller action mixed with infrastructure. You
 shouldn't touch this code if
 you want to change _notification_ library. _Persistence_
  neither.


Maybe, your code is not connecting

This is not going to change. It's not
related with what database we are using

Believe it or not, one day you will have to change your
framework or the libraries you are using for a specific
task.

[Listing 2](listings/listing2.txt)

What about moving all that logic, inside a class for doing
that. Can I call it a Service?


This service is a special one. It is the entry point for
your application, not your framework, so some guys call it
Application Service, Interactor or Use Case.
Do you remember when you were at University?

That's pure business logic. A UseCase object,
there is nothing related to databases, frameworks and so on. Cool.

## Rating a post using the API

During the day, your Product Owner comes to you and says:
 "by the way, a user should be able to rate a post using
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
post, what it's called another _delivery method_.

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

## Testing your code

Tomorrow, what about moving from MySQL to Redis?

During the same sprint, your architect comes to you and says:
?what about moving the voting story to redis? Could you do it for this sprint??, ?Yep, no problem!?. Man, you are on fire.

<Code for the new Adapter>

## Testing your code

Michael Feathers introduced a definition of legacy code
as _code without tests_. You don't want your code to be
legacy just born, do you?

[VotePostUseCaseTestCase](listings/usecase-test.txt)

Bam! 100% Coverage. Maybe, next time we can do it using
TDD so the test will come first. However,
testing this feature was really easy.

## Arggg, so many dependencies!

Is that normal that I have so many dependencies to create by hand?

Yes, it is. Service Container

## Migrating to new framework







Let?s review
We have completely separate business logic from infrastructure.

Different clients (web, api, console)
Infrastructure (Database, Framework, etc.) agnostic

When should you use it?
If 100% unit-test code coverage is important to your application. Also, if you want to be able to switch your storage mechanism, or any other type of third-party code. The architecture is especially useful for long-lasting applications that need to keep up with changing requirements.

