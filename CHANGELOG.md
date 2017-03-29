# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

This version introduces *backward incompatible* changes

### Added
* Use DI Container
* Added options to `provision` command: `--file`, `--wp-dir` `--wp-cli`
* Exceptions covering the domain structure of the application

### Changes
* Remove direct dependency on `wp-cli/wp-cli` to use Symfony `3.*` components
* Changed interface of task (provision) file
   * Task file must return a callable
   * That callable receives instances of `WpProvision\Api\Versions`, `WpProvision\Api\WpCommandProvider` and `WpProvision\Api\ConsoleOutput`. See README.md for example.
   * Remove `WpProvision\Api\WpProvisioner`
* Internal architecture changes
* Public API might throws exceptions (and do not hide every `Throwable` silently)



## [1.0.0-alpha2]

### Fixed
* Directory paths in bootstraping

## [1.0.0-alpha1]
Initial tag

## Provides
* API to use in a static defined `provision.php` file
* CLI command to install versions defined in `provision.php`
* API to basic WP-CLI commands `core`, `plugin`, `user`, `site`


[Unreleased]: https://github.com/dnaber-de/wp-provisioner/compare/1.0.0-alpha2...master
[1.0.0-alpha2]: https://github.com/dnaber-de/wp-provisioner/compare/1.0.0-alpha1...1.0.0-alpha2
[1.0.0-alpha1]: https://github.com/dnaber-de/wp-provisioner/releases/tag/1.0.0-alpha1
