<p align="center"><a href="https://roadsurfer.com/" target="_blank"><img src="https://roadsurfer.com/wp-content/uploads/roadsurfer-logo.jpg" width="400" alt="Roadsurfer"></a></p>

<div align="center">

[![Tests](https://github.com/olml89/Roadsurfer-test/actions/workflows/tests.yml/badge.svg)](https://github.com/olml89/Roadsurfer-test)
[![Coverage](https://codecov.io/gh/olml89/Roadsurfer-test/branch/main/graph/badge.svg?token=SL6ANXRH0A)](https://codecov.io/gh/olml89/Roadsurfer-test)
[![PHPStan](https://img.shields.io/badge/PHPStan-Level%209-brightgreen.svg?style=flat&logo=php)](https://phpstan.org/user-guide/rule-levels)
</div>

This is an implementation of a technical test for a senior developer role at
**[Roadsurfer](https://roadsurfer.com)**.
You can find the original repository this test is based on here:
[https://github.com/tturkowski/fruits-and-vegetables](https://github.com/tturkowski/fruits-and-vegetables).

# Implementation details

This application can store and show lists of edibles separated into two different collections: fruits and vegetables.
(Technical detail: although the edibles are only distinct because of their type, I have decided to consider them as
different entities because in the future they could evolve to have different behaviours,
I wanted to illustrate how I would make room for this from an architectural standpoint: they share a common Doctrine
superclass but they are two full-fledged entities stored in different tables).

## Installation

### Run the application

The application runs on a docker container, with the support of an additional mysql container for data persistence. 
After cloning the repository, spin up the containers:

```php
make build
make upd
```

To perform the following needed steps you can log into the php container shell:

```php
make ssh
```

Then install the needed dependencies:

```php
composer install
```

Run the needed database migrations:

```php
./bin/console doctrine:migrations:migrate --no-interaction
```

Then you can start the php integrated development server, running this from the root of the project:

```php
php -S 0.0.0.0:80 -t public
```

The application will be listening into upcoming HTTP requests. If you want to import edibles though a JSON feed, 
after being logged into the PHP container shell, run:

```php
./bin/console app:edible:import {fileLocation}
```

Alternatively, there's a more convenient way to run the application from the outside of the container once it is installed:

```php
make listen
```

### Run tests

Before running tests, the testing database and migrations have to be created. You can log into the PHP container
to run the required commands, but there's a convenient way to do it from the outside:

```php
make reset_test_db
```

Then, also from the outside, you can run:

```php
make test
```

There's also the possibility to run PHPStan checks on the code, currently the **level 9** is enforced and
successfully achieved:

```php
make phpstan
```

## Store edibles

### Add edibles through a JSON request

An endpoint has been created for each edible entity:

```php
POST /fruits
POST /vegetables
```

The endpoints expect this request payload:

```json
{
  "id": (int),
  "name": (name),
  "quantity": (int|float),
  "unit": "kg"|"g"
}
```
When a new edible is created, a 201 Created HTTP response will be returned outputting the new created entity.
<br>
<br>
The endpoints will do validation on the incoming payload: the id has to be positive, the name cannot be longer than 
255 characters, the quantity has to be positive, and the unit has to be a valid unit. No attributes can be missing,
and no extra attributes will be accepted. 
<br>
<br>
If the validation fails, a 422 Unprocessable Entity HTTP response will be returned.
<br>
<br>
As it was intended in the initial design, the id of the edibles is provided from the outside. That means that
whoever is in charge of storing the edibles must know if the edibles already exist or not. If they do, a
409 Conflict HTTP response will be returned.

### Import edibles from a JSON feed

A Symfony command has been created to import all the edibles from a specified json feed:

```php
./bin/console app:edible:import {fileLocation}
```

The importer will check than a file with a valid json feed exists in the specified location. Then it will try to
parse its items and convert them into valid edibles in order to store them. The same validation rules will be
run on the incoming input.

## List edibles

And endpoint has been created for each edible entity:

```php
GET /fruits
GET /vegetables
```

They will return a 200 OK HTTP response outputting the requested collection. The endpoints have filtering capabilites 
using the following concepts:

```
name: It will try to check if an entity matches partially with it. The comparison will not be case-sensitive.
quantity: It will try to check if an entity matches the comparison with a specified quantity.
```
Those concepts can be combined, requiring both to be valid or only one of them. They are expressed through a query string. 
An example of a query string would be:

```php
GET /fruits?name=B&op=and&quantity[amount]=1.5&quantity[op]=gte&quantity[unit]=kg
```

This request will match all the fruits that contain **B** or **b** in the name, and that have a quantity greater
or equal than 1.5 kg.
<br>
<br>
If the name is empty, the name comparison will not be taken into account.
If the quantity amount is not specified, the quantity comparison will not be taken into consideration.
<br>
<br>
Validation will be run on the incoming filters: the name can't be longer than 255 characters, and the operators to
join the filters and perform the quantity comparison have to be valid. If there is some validation error
a 400 Bad Request HTTP response will be returned.
<br>
<br>
Valid joining operators:

```php
and
or
```

Valid quantity comparison operators:

```php
eq (=)
neq (!=)
lt (<)
lte (<=)
gt (>)
gte (>=)
in (a value is contained in a list of values)
nin (a value is not contained in a list of values)
```

To provide a list of values for the comparisons that need them (**in**, **nin**), different quantity amounts can be specified
separating them by a comma:

```php
GET /fruits?quantity[amount]=1,2,3&quantity[op]=in&quantity[unit]=kg
```

## Showcasing of the results

The edible creating edibles will output a single edible, and the edible listing endpoints will output a collection
of edibles. The desired unit in which all the edibles will be returned can be specified through another query string parameter:

```php
GET /fruits?unit=kg
```

Validation will be also run on that query string parameter, so only a valid unit (**g** or **kg**) can be specified.


# 🍎🥕 Fruits and Vegetables

## 🎯 Goal
We want to build a service which will take a `request.json` and:
* Process the file and create two separate collections for `Fruits` and `Vegetables`
* Each collection has methods like `add()`, `remove()`, `list()`;
* Units have to be stored as grams;
* Store the collections in a storage engine of your choice. (e.g. Database, In-memory)
* Provide an API endpoint to query the collections. As a bonus, this endpoint can accept filters to be applied to the returning collection.
* Provide another API endpoint to add new items to the collections (i.e., your storage engine).
* As a bonus you might:
  * consider giving option to decide which units are returned (kilograms/grams);
  * how to implement `search()` method collections;
  * use latest version of Symfony's to embbed your logic 

### ✔️ How can I check if my code is working?
You have two ways of moving on:
* You call the Service from PHPUnit test like it's done in dummy test (just run `bin/phpunit` from the console)

or

* You create a Controller which will be calling the service with a json payload

## 💡 Hints before you start working on it
* Keep KISS, DRY, YAGNI, SOLID principles in mind
* Timebox your work - we expect that you would spend between 3 and 4 hours.
* Your code should be tested

## When you are finished
* Please upload your code to a public git repository (i.e. GitHub, Gitlab)

## 🐳 Docker image
Optional. Just here if you want to run it isolated.

### 📥 Pulling image
```bash
docker pull tturkowski/fruits-and-vegetables
```

### 🧱 Building image
```bash
docker build -t tturkowski/fruits-and-vegetables -f docker/Dockerfile .
```

### 🏃‍♂️ Running container
```bash
docker run -it -w/app -v$(pwd):/app tturkowski/fruits-and-vegetables sh 
```

### 🛂 Running tests
```bash
docker run -it -w/app -v$(pwd):/app tturkowski/fruits-and-vegetables bin/phpunit
```

### ⌨️ Run development server
```bash
docker run -it -w/app -v$(pwd):/app -p8080:8080 tturkowski/fruits-and-vegetables php -S 0.0.0.0:8080 -t /app/public
# Open http://127.0.0.1:8080 in your browser
```
