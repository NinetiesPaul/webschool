#Simple web system for small school management

Project running on:
* ```PHP 7.2```
* ```MySQL 5.2```

Composer packages used:
* ```Phinx```
* ```PHP-Cs-Fixer```
* ```Simple-PHP-Router```
* ```VLUCAS's PHPDOTENV```

Configuring the project:
* Install the project dependencies using Composer (https://getcomposer.org/):
* ```Instalation depends on OS```
* After that, using the command line, navigate to the main folder of the project and:
* ```Copy .env.dist to .env locally```
* ```Configure the .env file with DB connection info```
* ```Copy phinx.yml.dist to phinx.yml locally```
* ```Configure the phinx.yml file with DB connection info```
* ```Run vendor/bin/phinx migrate```
* ```Access local address depending on your hosting configuration. The project is ready to use!```
