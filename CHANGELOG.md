# Changelog

All notable changes to this project will be documented in this file.

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