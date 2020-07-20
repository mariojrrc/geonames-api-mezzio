GeoNames API Mezzio/MongoDB Example
=================================================
[![Build Status](https://semaphoreci.com/api/v1/mariojrrc/geonames-api-mezzio/branches/master/badge.svg)](https://semaphoreci.com/mariojrrc/geonames-api-mezzio)

This project is an example of REST API written in PHP 7.4 that makes use of [Mezzio](https://docs.mezzio.dev/) framework, [MongoDB](https://mongodb.com/) and [Redis](https://redis.io/) for cache.

It basically has two endpoints that allow us to perform some CRUD operations:

- /v1/state
- /v1/city

The endppoints are protected by authorization header tokens in the following format `X-Api-Key: uuid`. It has two types of tokens defined in the file `.env` on project's root folder. One token is to perform some "Admin level" operations, such as create, update and delete. And the other one is to perform only read operations.

Note: Tokens have rate-limit params setted up. You can configure them in `config/autoload/api-credentials.global.php` file. By default, it allow us to perform 100 requests per second.

## DOCS
The endpoint's documentation is located in `public/doc` folder. It was written on top of OpenAPI v3 notation.

## Running the project

**OBS: PHP 7.4, MongoDB, Redis extensions are requeried**

1. Rename the file `.env.dist` to `.env` and fill out the required info
2. Run `composer install`
3. Run `composer serve`
4. Make calls to the endpoints via [Postman](https://www.getpostman.com/) or similar in the following address `0.0.0.0:8080/v1/state`

**Using it with Docker**
1. `docker-compose up`
2. `docker exec -t geoname-mezzio-php bash -c "cd /var/www/html && composer install"`
Obs: If you have problem with mongodb extension when composer installing, run `docker exec -t geoname-mezzio-php bash -c "pecl install mongodb"`

**BONUS**
There's a console command to create brazilian states and cities available through `php bin/console populate:geodb` command;

## Live Demo
You can check the api and its documentation live on Heroku by clicking  [here](http://geonames-api.herokuapp.com/doc/). A simple front-end to list cities and states can be found [here](https://github.com/mariojrrc/geonames-vue).

## CI/CD
There is a configured pipeline in [SemaphoreCI](http://semaphoreci.com/) to run some code style validations ([PHPCS](https://github.com/squizlabs/PHP_CodeSniffer) and [PHPStan](https://github.com/phpstan/phpstan)) and Unit tests. After a successful build, it deploys the code to [Heroku](https://heroku.com) servers.
For monitoring, the heroku app makes use of the [Newrelic](https://newrelic.com/) add-on.

## Questions and Suggestions?
Drop me an [e-mail](mailto:mariojr.rcosta@gmail.com)

## TODO
- Fetch api tokens from database (cached) in order to keep it more easly to mantain
- Create more unit tests to have a 100% coverage score.
