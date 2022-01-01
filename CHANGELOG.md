# Changelog

All notable changes to `code-distortion/swap-con` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).



## [0.3.2] - 2022-01-02

### Added
- Added support for PHP 8.0 and 8.1
- Added phpstan ^1.0 to dev dependencies
- Updated version of phpstan, testbench & fluent-dotenv dependencies
- Updated to PSR12



## [0.3.1] - 2020-08-18

### Added
- Added code-distortion/fluent-dotenv internally to read .env files instead of reading them directly



## [0.3.0] - 2020-05-02

### Changed (breaking)
- Renamed the `copyBroadcast`, `copyCache`, `copyDatabase`, `copyDB`, `copyFilesystem`, `copyLog`, `copyQueue` methods to `cloneBroadcast`, `cloneCache`, `cloneDatabase`, `cloneDB`, `cloneFilesystem`, `cloneLog`, `cloneQueue` respectively for consistency



## [0.2.4] - 2020-03-23

### Changed
- Tweaked the "missing .env" unit test so it works with all versions of Laravel



## [0.2.3] - 2020-03-23

### Added
- Allow SwapCon to fall-over gracefully if the .env file doesn't exist



## [0.2.2] - 2020-03-23

### Added
- Backwards compatibility support for PHP 7.0



## [0.2.1] - 2020-03-08

### Added
- Support for Laravel 7 and PHP dotenv 4



## [0.2.0] - 2020-01-27

### Added
- GitHub actions workflows file

### Changed (breaking)
- Changed the config file's name

### Changed
- Updated the code-of-conduct to https://www.contributor-covenant.org/version/2/0/code_of_conduct.html
- Added Treeware details
- Bumped dependencies
- Updated non-Testbench tests so they could use the non-namespaced phpunit TestCase from old versions of phpunit (because old versions of Testbench require old versions of phpunit). This allowed testing back to Laravel 5.2.



## [0.1.0] - 2019-12-02

### Added
- beta release
