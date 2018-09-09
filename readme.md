## Laravel SQL Logger

[![Packagist](https://img.shields.io/packagist/dt/mnabialek/laravel-sql-logger.svg)](https://packagist.org/packages/mnabialek/laravel-sql-logger)
[![Support via Paypal](https://img.shields.io/badge/support%20via-paypal-brightgreen.svg)](https://www.paypal.me/mnabialek/5usd)
[![Build Status](https://travis-ci.org/mnabialek/laravel-sql-logger.svg?branch=master)](https://travis-ci.org/mnabialek/laravel-sql-logger)
[![Coverage Status](https://coveralls.io/repos/github/mnabialek/laravel-sql-logger/badge.svg)](https://coveralls.io/github/mnabialek/laravel-sql-logger)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mnabialek/laravel-sql-logger/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mnabialek/laravel-sql-logger/)


This module allows you to log SQL queries (and slow SQL queries) to log file in Laravel/Lumen framework. It's useful mainly
when developing your application to verify whether your queries are valid and to make sure your application doesn't run too many or too slow database queries.

### Support

Using this package is free of charge, however to keep it up-to-date and add new features small money support is appreciated. **Suggested amount is 5$ per project where you use this package but any amount will help further development of this package.**
[![Support via Paypal](https://img.shields.io/badge/support%20via-paypal-brightgreen.svg)](https://www.paypal.me/mnabialek/5usd) (you are free to change amount on Paypal page)

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
    SQL_LOGGER_DIRECTORY="logs/sql"
    SQL_LOGGER_USE_SECONDS=false
    SQL_LOGGER_CONSOLE_SUFFIX=
    SQL_LOGGER_LOG_EXTENSION=".sql"
    SQL_LOGGER_ALL_QUERIES_ENABLED=true
    SQL_LOGGER_ALL_QUERIES_OVERRIDE=false
    SQL_LOGGER_ALL_QUERIES_PATTERN="#.*#i"
    SQL_LOGGER_ALL_QUERIES_FILE_NAME="[Y-m-d]-log"
    SQL_LOGGER_SLOW_QUERIES_ENABLED=true
    SQL_LOGGER_SLOW_QUERIES_MIN_EXEC_TIME=100
    SQL_LOGGER_SLOW_QUERIES_PATTERN="#.*#i"
    SQL_LOGGER_SLOW_QUERIES_FILE_NAME="[Y-m-d]-slow-log"
    SQL_LOGGER_FORMAT_NEW_LINES_TO_SPACES=false
    SQL_LOGGER_FORMAT_ENTRY_FORMAT="/* [origin]\n   Query [query_nr] - [datetime] [[query_time]] */\n[query]\n[separator]\n"
    ```
    
    and adjust values to your needs. You can skip variables for which you want to use default values. 
    
    If you have also `.env.sample` it's also recommended to add those entries also in `.env.sample` file just to make sure everyone know about those env variables. Be aware that `SQL_LOGGER_DIRECTORY` is directory inside storage directory. If you want you can change it editing `config/sql_logger.php` file.
    
    To find out more about those setting please take a look at [Configuration file](publish/config/sql_logger.php)
    
6. Make sure directory specified in `.env` file exists in storage path and you have valid file permissions to create and modify files in this directory (If it does not exist this package will automatically create it when needed but it's recommended to create it manually with valid file permissions)

7. Make sure on live server you will set logging SQL queries to false in your `.env` file. This package is recommended to be used only for development to not impact production application performance.

### Upgrading from 1.*

When upgrading from `1.*` version you should remove current `sql_logger.php` config file and replace this with new one (see installation step). You should also use new variables in `.env` file (old won't be used).

### Authors

Author of this awesome package is **[Marcin Nabiałek](http://marcin.nabialek.org/en/)**  and [Contributors](https://github.com/mnabialek/laravel-sql-logger/graphs/contributors)

### Changes

All changes are listed in [Changelog](CHANGELOG.md)

### License

This package is licenced under the [MIT license](LICENSE) however [Support](#support) is more than welcome.