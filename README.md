# Laravel Ecommerce API's and CMS
Laravel package API Result filtering, sorting &amp; searching.

## Installation

Require this package with composer.

```shell
composer require mark-villudo/laravel-ecommerce
```

Laravel 5.5 and above uses Package Auto-Discovery, so doesn't require you to manually add the ServiceProvider.

## Usage
//goes to cms of shop http://127.0.0.1:8000

```
php artisan serve

```
## Installation
//publish all helpers, controller, models, resources, views, routes, migration and assets

```

php artisan vendor:publish

```

## Migrate database tables.

//Setup ``.env`` file change database name. run this command after success to setup enviroment.
//Migrate also the permissions table under service provider of ``MarkVilludo\Permissions\``.

```
php artisan migrate

```

## Seed the initial user, modules, product contents, categories, and etc

```

  php artisan db:seed
  
```

## Include Ecommerce Routes API AND WEB
//in routes/api.php
//Add this below 
```
include_once('api_ecommerce.php');

```

//in routes/web.php
//Add this below 
```
include_once('web_ecommerce.php');

```
