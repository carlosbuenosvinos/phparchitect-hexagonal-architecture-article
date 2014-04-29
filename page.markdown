# Hexagonal Architecture with PHP
###### by Carlos Buenosvinos

> With the raise of DDD, architectures that promote
domain centric designs are getting more popular.
This is the case of CQRS, Event Sourcing or **Hexagonal
Architecture** (aka. Ports and Adapters). Let's check
how to build testable, infrastructure agnostic apps.

It's monday morning, another sprint is starting and you
are reviewing some user stories with your team and your
Product Owner. "As a not logged user, I want to vote a
post and notify the author via email.", that's a really
cool feature, isn't it? You'll start your day with that one.

## First approach

Let's start with "I want to vote a post". Your company
 web is still using ZF1. After some time you get
 something like Listing 1.

[First approach](listings/listing1.txt)

Mmmm, ok, it works. However, all your business logic is
inside a controller action mixed with infrastructure
(Redis and SwiftMailer). You shouldn't touch this code if
 you want to change _notification_ library. _Persistence_
  neither.

Believe it or not, one day you will have to change your
framework or the libraries you are using for a specific
task.

[Defining persistence](listings/listing2.txt)

What about moving all that logic, inside a class for doing
that. Can I call it a Service?


This service is a special one. It is the entry point for your application, not your framework, so some guys call it Application Service, Interactor or Use Case. Do you remember when you were at University?


That?s pure business logic. A UseCase object, there is nothing related to databases, frameworks and so on. Cool.


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

Your current API is a Silex <http://silex.sensiolabs.org/>
application.

[Voting using the API](listings/listing6.txt)

Man! I remember those 4 lines of code. They look exactly
the same as the web application. That's right,
because the use case and the business rules are the same,
the code should be the same. We only have provided
another delivery method. The main difference is how we
have created the `VotePostRequest` from. On the first
example, it was from a ZF request and now from a Silex
request.





Tomorrow, what about moving from MySQL to Redis?

During the same sprint, your architect comes to you and says: ?what about moving the voting story to redis? Could you do it for this sprint??, ?Yep, no problem!?. Man, you are on fire.

<Code for the new Adapter>

## Testing your code

Michael Feathers introduced a definition of legacy code
as _code without tests_. You don't want your code to be
legacy just born.


Bam! 100% Coverage. Maybe, next time we can do it using TDD so the test will come first.

## Arggg, so many dependencies!

Is that normal that I have so many dependencies to create by hand?

Yes, it is. Service Container

## Migrating to new framework


## Who I have to thank?

Alistair Cockburn invented it in 2005. It is a response
to the desired to create thoroughly testable applications.
As Cockburn says: "Allow an application to equally be
driven by users, programs, automated test or batch scripts,
and to be developed and tested in isolation from its
eventual run-time devices and databases."



Let?s review
We have completely separate business logic from infrastructure.

Different clients (web, api, console)
Infrastructure (Database, Framework, etc.) agnostic

When should you use it?
If 100% unit-test code coverage is important to your application. Also, if you want to be able to switch your storage mechanism, or any other type of third-party code. The architecture is especially useful for long-lasting applications that need to keep up with changing requirements.

