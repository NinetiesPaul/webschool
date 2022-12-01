#Simple web system for small school management

Project running on:
```PHP 7.2```  
```MySQL 5.2```  

Composer packages used:  
```Phinx```  
```PHP-Cs-Fixer```  
```Simple-PHP-Router```  
```VLUCAS's PHPDOTENV```  

Configuring the project:  
Install Docker   
Run ```docker-compose build```  
Run ```docker-compose up```  
Run ```docker-compose exec php composer install```  
Copy ```.env.dist to .env locally```  
Configure ```the .env file with docker container data```  
Copy ```phinx.yml.dist to phinx.yml locally```  
Configure ```the phinx.yml file with docker container data```  
Run ```docker-compose exec php vendor/bin/phinx migrate```  
