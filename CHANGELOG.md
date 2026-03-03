# Changelog

All notable changes to this package will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.0.0] - 2026-03-03

### Added
- PHP 8.3 and 8.4 support
- Laravel 11 and 12 support
- Pest 4 test suite with full coverage of SailthruMessage, SailthruChannel, and SailthruServiceProvider
- Orchestra Testbench for integration testing
- GitHub Actions CI with PHP 8.3/8.4 and Laravel 11/12 matrix
- Typed properties and return types throughout source code

### Changed
- Minimum PHP version raised from 8.0 to 8.3
- Minimum Laravel version raised from 8.0 to 11.0
- Test framework switched from PHPUnit to Pest 4
- Source code modernised with PHP 8.3 idioms (constructor property promotion, typed properties)
- SailthruServiceProvider uses `#[\Override]` attributes

### Removed
- PHP 8.0, 8.1, and 8.2 support
- Laravel 8, 9, and 10 support
- Travis CI configuration (.travis.yml)
- StyleCI configuration (.styleci.yml)
- Scrutinizer configuration (.scrutinizer.yml)
- PHPUnit dependency (replaced by Pest 4)

## [0.5.0] - 2026-02-27

### Added
- Laravel 12 support

## [0.4.7] - 2025-11-26

### Added
- Laravel 11 support

## [0.4.6] - 2024-03-26

### Added
- Domain whitelist check to control email sending in local/dev environments

## [0.4.5] - 2023-02-24

### Added
- Laravel 10 support

## [0.4.4] - 2022-06-13

### Added
- Laravel 9 support

## [0.4.3] - 2021-04-14

### Added
- PHP 8 support

## [0.4.2] - 2020-09-09

### Added
- Laravel 8 support

## [0.4.1] - 2020-09-07

### Fixed
- Verify message variable in catch block in SailthruChannel

## [0.4.0] - 2020-08-04

### Added
- Laravel 7 support

## [0.3.0] - 2020-02-04

### Changed
- Updated Laravel dependencies to align with current notifications channel template

## [0.2.1] - 2019-08-30

### Added
- Optional debug logging of the API payload

## [0.2.0] - 2019-06-24

### Fixed
- Correctly catch `Sailthru_Client_Exception`

## [0.1.1] - 2019-06-07

### Fixed
- Only set name if sent

## [0.1.0] - 2019-06-05

- Initial release
