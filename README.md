# The Music Tank.com

[![Build Status](https://travis-ci.org/themusictank/themusictank.com.svg?branch=master)](https://travis-ci.org/themusictank/themusictank.com) [![Code Climate](https://codeclimate.com/github/themusictank/themusictank.com.svg)](https://codeclimate.com/github/strata-mvc/strata)



### Developping

Install the project using Composer and Gulp:

~~~ 
$ composer install
$ gulp
~~~

Test your code using PHPUnit, PHPCS and PHPMD:

~~~
$ vendor\bin\phpunit
$ vendor\bin\phpcs --standard=PSR2 --ignore=app/views,app/storage,app/tests,app/filters.php,app/routes.php,packages/,app/Providers/,app/Console/,app/services/,http/Middleware/,app/Exceptions/,app/Events/ -w --colors app/
$ vendor\bin\phpmd app/ text cleancode,controversial,codesize,design,naming,unusedcode --exclude=app/views,app/storage,app/tests,app/filters.php,app/routes.php,packages/,app/Providers/,app/Console/,app/services/,http/Middleware/,app/Exceptions/,app/Events/
~~~
