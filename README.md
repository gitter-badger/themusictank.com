# The Music Tank.com

[![CircleCI](https://circleci.com/gh/themusictank/themusictank.com/tree/master.svg?style=svg)](https://circleci.com/gh/themusictank/themusictank.com/tree/master) [![Code Climate](https://codeclimate.com/github/themusictank/themusictank.com.svg)](https://codeclimate.com/github/strata-mvc/strata)


### Developping

Install the project using Composer and Gulp:

~~~ 
$ composer install
$ npm install
~~~

Watch frontend code changes:

~~~
$ npm run watch
~~~

Test your code using PHPUnit, PHPCS and PHPMD:

~~~
$ vendor\bin\phpunit
$ vendor\bin\phpcs --standard=PSR2 --ignore=app/views,app/storage,app/tests,app/filters.php,app/routes.php,packages/,app/Providers/,app/Console/,app/services/,http/Middleware/,app/Exceptions/,app/Events/ -w --colors app/
$ vendor\bin\phpmd app/ text cleancode,controversial,codesize,design,naming,unusedcode --exclude=app/views,app/storage,app/tests,app/filters.php,app/routes.php,packages/,app/Providers/,app/Console/,app/services/,http/Middleware/,app/Exceptions/,app/Events/
~~~
