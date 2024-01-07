# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.1] - 2024-01-07
### Changed
- Added Drupal 10 support

## [2.0.0] - 2022-01-11
### Changed
- Increase minimum Drupal core version to 9.3 due to entity bundle class support
- Increase minimum PHP requirement to 7.3 due to Drupal core PHP requirement

### Removed
- Remove wieni/wmmodel dependency

## [1.1.1] - 2020-12-22
### Added
- Add PHP 8 support 
- Replace `fzaninotto/faker` dependency with `fakerphp/faker`
- Add hooks documentation
- Add PHPStan

### Changed
- Apply coding standard fixes

## [1.1.0] - 2020-06-05
### Added
- Add label properties to annotations

### Changed
- Replace EntityStatePluginManager::getNamesByEntityType with EntityStatePluginManager::getDefinitionsByEntityType

### Fixed
- Add missing type hint in docblock

## [1.0.2] - 2020-06-04
### Fixed
- Fix issue when creating entity without bundle

## [1.0.1] - 2020-05-05
### Fixed
- Fix wmmodel version requirement

## [1.0.0] - 2020-05-05
Initial release
