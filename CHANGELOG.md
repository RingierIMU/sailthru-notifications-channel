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

## 1.0.0 - 2019-01-01

- Initial release
