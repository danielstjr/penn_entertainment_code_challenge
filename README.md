# Coding Challenge for Penn Entertainment

## Requirements

### User Model:
```
{
  id: int primary key,
  name: varchar(255),
  email: varchar(255),
  points_balance: int
}
```

### Endpoints
```
{
  GET /users: list all users,
  POST /users: create a user defaulting point balance to 0,
  POST /users/{id}/earn: add points to the user's balance identified by the id,
  POST /users/{id}/redeem: redeem (remove) points from the user's balance identified by the id,
  DELETE /users/{id}: delete the user identifed by the id
}
```

## Setup Overview
1. Establish a database connection of your choice with a MySQL version at or above 8.0
2. Copy the .env.example file to .env inside of the directory and fill in the DB related details
3. Install dependencies through composer install (PHP Version 8.0+)
4. Run Doctrine migrations
5. Serve the application (This README uses the native PHP webserver)

### ENV Setup
Necessary .env tags (listed in .env.example) are:
```
ENVIRONMENT=development

DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=app
DB_USER=user
DB_PASSWORD=secret
```

Fill these in with your database details for a MySQL 8.0+ instance

### Dependencies
To install the php-slim framework, Doctrine ORM, Doctrine Migrations, and PHPUnit you will need to run composer install with a PHP version 8.0+
```
composer install
```

### Migrations
Because the Migrations only work through the composer dependencies, it *must be* ran after running composer install here.

To maintain a simpler cloning process I've included Doctrine Migrations, which are setup through the cli-config.php file and ran by:
```
./vendor/doctrine-migrations/bin migrate
```

### Webserver
Although there are lots of articles on setting up Slim in production, PHP ships with a native webserver that I utilized for development which is ran by:
```
php -S localhost:8080 -t public
```

This is ran from the root directory. The -S specifies the serving URL and the -t specifies the directory index.php is located in
