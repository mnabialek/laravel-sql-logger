# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Laravel SQL Logger is a development-only package that logs SQL queries (including slow queries) to log files for Laravel/Lumen applications. It supports Laravel 5.x through 11.x.

## Commands

### Testing
```bash
# Run all tests
vendor/bin/phpunit

# Run tests with coverage report
vendor/bin/phpunit --coverage-clover build/logs/clover.xml

# Run a single test file
vendor/bin/phpunit tests/SqlLoggerTest.php

# Run a specific test method
vendor/bin/phpunit --filter testMethodName
```

### Code Quality
```bash
# Run PHP CS Fixer to check code style
vendor/bin/php-cs-fixer fix --dry-run --diff

# Fix code style issues automatically
vendor/bin/php-cs-fixer fix
```

### Development Setup
```bash
# Install dependencies
composer install

# Update dependencies
composer update
```

## Architecture

### Query Logging Flow

The package hooks into Laravel's database event system through a Service Provider:

1. **ServiceProvider** (`src/Providers/ServiceProvider.php`) - Registers the logger and attaches to Laravel's `DB::listen()` event
2. **SqlLogger** (`src/SqlLogger.php`) - Main coordinator that receives query events, increments query counter, and delegates to Query and Writer
3. **Query** (`src/Query.php`) - Adapts query data between Laravel versions (5.2+ uses QueryExecuted event object, earlier versions use separate parameters)
4. **SqlQuery** (`src/Objects/SqlQuery.php`) - Value object holding query number, SQL, bindings, and execution time
5. **Writer** (`src/Writer.php`) - Handles file system operations, determines which log files to write to (all queries vs slow queries), and manages directory creation
6. **Formatter** (`src/Formatter.php`) - Formats query output including origin (console/request), timestamp, execution time, and formatted SQL
7. **FileName** (`src/FileName.php`) - Generates log file names based on date patterns and configuration

### Binding Replacement System

The `ReplacesBindings` trait (`src/Objects/Concerns/ReplacesBindings.php`) is shared by both `SqlQuery` and `Formatter`. It handles the complex task of:
- Replacing `?` placeholders with actual values
- Replacing named parameters (`:param`) with values
- Using regex patterns to avoid replacing placeholders inside quoted strings
- Formatting values (null, booleans, dates, strings with escaping)

**Known Issue**: Line 23 has a deprecated warning comment about passing null to preg_replace parameter #3 in PHP 8.1+. This needs investigation.

### Configuration System

The `Config` class wraps access to the `config/sql_logger.php` configuration file, which loads all settings from environment variables. Key settings:
- All queries logging (enabled/disabled, file pattern, override mode)
- Slow queries logging (enabled/disabled, minimum execution time threshold)
- Formatting options (entry format template, newline handling)
- Log file settings (directory, extension, console suffix)

### Version Compatibility

The package uses `mnabialek/laravel-version` to detect Laravel version at runtime. This enables:
- Supporting both old event signature (Laravel < 5.2) and QueryExecuted objects (5.2+)
- Maintaining compatibility across Laravel 5.x through 11.x

## Code Style

This project follows PSR-12 with additional rules enforced by PHP CS Fixer:
- Short array syntax
- Single quotes for strings
- Specific spacing around operators and concatenation
- Ordered class elements (constants, properties, methods)
- Multiline trailing commas for arrays only

The `.php-cs-fixer.php` file applies rules to both `src/` and `tests/` directories.

## Testing Patterns

Tests use PHPUnit 11 with Mockery for mocking. Each main class has a corresponding test file in `tests/`. The test suite covers:
- Unit tests for individual classes
- Integration behavior between Query, Writer, and Formatter
- Configuration loading and validation
- Service Provider registration and event listening