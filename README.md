# Cake Details

Install on fresh server :

  sudo apt-get install apache2
  sudo apt-get install php5
  sudo apt-get install libapache2-mod-php5
  sudo apt-get install mcrypt php5-mcrypt
  sudo php5enmod mcrypt

  sudo apt-get install php5-intl
  sudo service apache2 restart

  curl -s https://getcomposer.org/installer | php

Create a new project :

  php composer.phar  create-project  --prefer-dist -s dev  cakephp/app tmt

Updating a project

  php composer.phar update

Running a migration

./src/Console/cake migrations

Running a server

  ./src/Console/cake server -H 0.0.0.0 -p 3000
  (no -H if ran localy)

## Update cron

./src/Console/cake tank daily
