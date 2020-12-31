# Changelog

All notable changes to this project will be documented in this file.

## [2.2.8] - 2020-12-31
### Fixed
- Fix case when bindings are null

## [2.2.7] - 2020-09-17
### Changed
- Add support for Laravel 8

## [2.2.6] - 2020-05-21
### Changed
- Add support for DateTimeImmutable objects

## [2.2.5] - 2020-03-22
### Changed
- Add support for Laravel 7

## [2.2.4] - 2019-09-04
### Changed
- Add support for Laravel 6

## [2.2.3] - 2019-08-29
### Changed
- Boolean values are now displayed as **1** and **0**

## [2.2.2] - 2019-07-14
### Fixed
- Fix support for Lumen

## [2.2.1] - 2019-04-27
### Changed
- Add support for Carbon 2

## [2.2.0] - 2018-09-09
### Added
- Possibility to use custom entry format

## [2.1.0] - 2018-07-17
### Added
- Option whether new lines are converted to spaces

### Changed
- Don't convert new lines to spaces by default

## [2.0.3] - 2018-06-13
### Changed
- Null values are now displayed as **null** instead of empty strings

## [2.0.2] - 2018-05-12
### Fixed
- Fix support for Lumen

## [2.0.1] - 2018-03-23
### Fixed
- Fix for named parameters when binding order is different than order of parameters
- Fixes for replacing bindings for named parameters

## [2.0] - 2018-02-11
### Added
- Log contain now origin (HTTP action or console command)
- Support for named parameters in queries
- Option to set filename and extension for logs
- Option to choose which queries will be tracked

### Changed
- Changed configuration file structure
- Changed ENV variables names
- Changed structure of log files (added origin)
- Changes in generating queries (numbers are not quoted any more, parameters in quotes won't be converted)
- Complete package code rewrite with unit tests

## [1.1.4] - 2018-01-18
### Added
- Added CHANGELOG to keep tracking of changes

## [1.1.3] - 2017-09-03
### Added
- Allow package to be auto-discovered by Laravel 5.5
### Changed
- Automatically create log directory if it does not exist
- Internal code updates

## [1.1.2] - 2017-01-24
### Changed
- Internal code update to make it work with Laravel 5.4

## [1.1.1] - 2016-06-10
### Fixed
- Use appropriate ENV variable

## [1.1] - 2016-06-09
### Added
- Allow to log to separate files when running console

## [1.0] - 2016-02-16
### Added
- Log SQL queries
- Log slow SQL queries
