Hello!


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
