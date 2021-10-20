# Gila Vehicle System Exercise

## Introduction

* Used [Laminas](https://getlaminas.org/) Framework. to use the routing and the db adapter.
* Bootstrap to quickly apply some styles.
* No javascript framework was used only vanilla javascript
* A very basic JWT kind of token is used to validate the access to API endpoints. 

## Installation using Composer

To install the Exercise:

```bash
$ git clone vehicle-system
$ composer install
```

Once installed, you can test it out immediately using PHP's built-in web server:

```bash
$ cd vehicle-system
$ php -S 0.0.0.0:8080 -t public
# OR use the composer alias:
$ composer run --timeout 0 serve
```

This will start the cli-server on port 8080, and bind it to all network
interfaces. You can then visit the site at http://localhost:8080/ or http://0.0.0.0:8080

## Files Locations
Controllers and Models: 
```bash
vehicle-system/module/Application/src
```

Views
```bash
vehicle-system/module/Application/views
```

Js
```bash
vehicle-system/public/js/all.js
```

Postman collection
```bash
vehicle-system/Gila Vehicle System.postman_collection.json
```

## Database Setup
The database config will try to connect to a localhost server under user: root and password: 12345

Database config is located at
```bash
vehicle-system/config/autoload/database.global.php
```

```bash
CREATE SCHEMA `vehicles` DEFAULT CHARACTER SET utf8 ;

CREATE TABLE `vehicles`.`vehicle` (
  `vehicle_id` INT NOT NULL AUTO_INCREMENT,
  `horsepower` INT NULL,
  `total_tires` INT NULL,
  `model` VARCHAR(45) NULL,
  `color` VARCHAR(45) NULL,
  PRIMARY KEY (`vehicle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
  
CREATE TABLE `sedan` (
  `sedan_id` int NOT NULL AUTO_INCREMENT,
  `vehicle_id` varchar(45) DEFAULT NULL,
  `sedan_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`sedan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE `motorcycle` (
  `motorcycle_id` int NOT NULL AUTO_INCREMENT,
  `vehicle_id` varchar(45) DEFAULT NULL,
  `motorcycle_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`motorcycle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;  
  
CREATE TABLE `vehicles`.`user` (
  `user_id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `access_token` VARCHAR(255) NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;  
  
INSERT INTO `vehicles`.`user` (`email`,`password`) VALUES ('test@test.com', MD5('123456'));  
```

## Test user
* Email: test@test.com
* Password: 123456

## Endpoints
All the endpoints in the collection need an access_token that is generated 
when executing the login endpoint.
