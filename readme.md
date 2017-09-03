## Simple SQL Logger

This module allows you to log SQL queries (and slow SQL queries) to log file in Laravel/Lumen framework. It's useful mainly
when developing your application to verify whether your queries are valid and to make sure your application doesn't run too many or too slow database queries.

### Installation

1. Run
   ```php   
   composer require mnabialek/laravel-sql-logger --dev
   ```
   in console to install this module (Notice `--dev` flag - it's recommended to use this package only for development). 

2. If you use Laravel < 5.5 open `config/app.php` and in `providers` section add:
 
    ```php
    Mnabialek\LaravelSqlLogger\Providers\ServiceProvider::class,
    ```
    
    Laravel 5.5 uses Package Auto-Discovery and it will automatically load this service provider so you don't need to add anything into above file.
    
    If you are using Lumen open `bootstrap/app.php` and add:
    
   ```php
   $app->register(Mnabialek\LaravelSqlLogger\Providers\ServiceProvider::class);
   ```
    
3. If you use Laravel < 5.5 run:
    
    ```php
    php artisan vendor:publish --provider="Mnabialek\LaravelSqlLogger\Providers\ServiceProvider"
    ```
    
    in your console to publish default configuration files.
    
    If you are using Laravel 5.5 run:
    
    ```php
    php artisan vendor:publish
    ```
    
    and choose the number matching `"Mnabialek\LaravelSqlLogger\Providers\ServiceProvider"` provider.
    
    By default you should not edit published file because all the settings are loaded from `.env` file by default.
    
    For Lumen you should skip this step.     

5. In your .env file add the following entries:

    ```
    SQL_LOG_QUERIES=true
    SQL_LOG_SLOW_QUERIES=true
    SQL_SLOW_QUERIES_MIN_EXEC_TIME=100
    SQL_LOG_OVERRIDE=false
    SQL_LOG_DIRECTORY=logs/sql
    SQL_CONVERT_TIME_TO_SECONDS=false
    SQL_LOG_SEPARATE_ARTISAN=false
    ```
    
    and adjust values to your needs. If you have also `.env.sample` it's also recommended to add those entries also in `.env.sample` file just to make sure everyone know about those env variables. Be aware that `SQL_LOG_DIRECTORY` is directory inside storage directory. If you want you can change it editing `config/sql_logger.php` file.
    
6. Make sure directory specified in `.env` file exists in storage path and you have valid file permissions to create and modify files in this directory (If it does not exist this package will automatically create it when needed but it's recommended to create it manually with valid file permissions)

7. Make sure on live server you will set logging SQL queries to false in your `.env` file. This package is recommended to be used only for development to not impact production application performance. 
