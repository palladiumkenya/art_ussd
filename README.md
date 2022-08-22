# art_ussd service 

[![Deploying Art_USSD to test instance](https://github.com/palladiumkenya/art_ussd/actions/workflows/Art_USSD%20Service.yml/badge.svg)](https://github.com/palladiumkenya/art_ussd/actions/workflows/Art_USSD%20Service.yml)

## prerequisites

You must have the following

* PHP version 7.0 and above

* PHP default web-server [Read the Documentation](https://www.php.net/manual/en/features.commandline.webserver.php)

* docker engine (**Optional**)

### Bringing up the service on your local machine

* Clone the repository

```sh
git clone https://github.com/palladiumkenya/art_ussd.git 
```

* Run the following command

``` php
php -S localhost:8000 -t art_ussd
```

* Access this service using via Postman/Insomnia clients

```
 http://localhost:8000
```

### docker option

* Clone the repository

```sh
git clone https://github.com/palladiumkenya/art_ussd.git 
```

* Run the following command to build a docker image

``` sh
docker build -t artussd:latest .
```

* Run the following command to fire up the image

``` sh
docker run --rm -d  -p 8006:8000/tcp --name=artussd artussd:latest 
```

Enviroment variables QueryManager.php - Environment variables can be set in this file Note : You can quickly set the database information and other variables in this file and have the application fully working.
