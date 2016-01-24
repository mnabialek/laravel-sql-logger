## Simple SQL Logger

This module allows you to log SQL queries (and slow SQL queries) to log file in Laravel/Lumen framework. It's useful mainly
when developing your application to verify your queries, they amount and number.

### Installation

1. Run
   ```php   
       composer require mnabialek/laravel-sql-logger
   ```     
   in console to install this module

2. Open `config/app.php` and in `providers` section add:
 
    ```php
     Mnabialek\LaravelSqlLogger\Providers\ServiceProvider::class,
    ```
    
3. Run:
    
    ```php
    php artisan vendor:publish --provider="Mnabialek\LaravelSqlLogger\Providers\ServiceProvider"
    ```
    
    in your console to publish default configuration files
    
4. Open `config/sql_logger.php` file and adjust settings to your need.
    
5. Make sure directory specified in `config/sql_logger.php` configuration file exists and you have valid file permissions to create and modify files in this directory

