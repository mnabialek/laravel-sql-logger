## Simple SQL Logger

This module allows you to log SQL queries (and slow SQL queries) to log file in Laravel/Lumen framework. It's useful mainly
when developing your application to verify whether your queries are valid and to make sure your application doesn't run too many or too slow database queries.

### Installation

#### Laravel 5.*

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
    
4. Open `config/sql_logger.php` file and adjust settings to your need (by default it uses `.env` file so you can skip this step if you want).

5. In your .env file add the following entries:

    ```
    SQL_LOG_QUERIES=true
    SQL_LOG_SLOW_QUERIES=true
    SQL_SLOW_QUERIES_MIN_EXEC_TIME=100
    SQL_LOG_OVERRIDE=false
    SQL_LOG_DIRECTORY=logs/sql
    SQL_CONVERT_TIME_TO_SECONDS=false
    ```
    
    and adjust values to your needs. If you have also `.env.sample` it's also recommended to add those entries also in `.env.sample` file just to make sure everyone know about those env variables. Be aware that `SQL_LOG_DIRECTORY` is directory inside storage directory. If you want you can change it editing `config/sql_logger.php` file  
    
6. Make sure directory specified in `.env` file exists in storage path and you have valid file permissions to create and modify files in this directory

#### Lumen 5.*

1. Run
   ```php   
       composer require mnabialek/laravel-sql-logger
   ```     
   in console to install this module
   
2. Open `bootstrap/app.php` and add:
   ```php
   $app->register(Mnabialek\LaravelSqlLogger\Providers\ServiceProvider::class);
   ```
3. In your .env file add the following entries:
   
    ```
    SQL_LOG_QUERIES=true
    SQL_LOG_SLOW_QUERIES=true
    SQL_SLOW_QUERIES_MIN_EXEC_TIME=100
    SQL_LOG_OVERRIDE=false
    SQL_LOG_DIRECTORY=logs/sql
    SQL_CONVERT_TIME_TO_SECONDS=false
    ```
       
    and adjust values to your needs. If you have also `.env.sample` it's also recommended to add those entries also in `.env.sample` file just to make sure everyone know about those env variables. Be aware that `SQL_LOG_DIRECTORY` is directory inside storage directory. 
       
4. Make sure directory specified in `.env` file exists in storage path and you have valid file permissions to create and modify files in this directory
    
