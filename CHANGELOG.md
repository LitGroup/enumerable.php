# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
## Added
- Abstract test case for enumerable values `LitGroup\Enumerable\Test\EnumerableTestCase`.

## [0.3.0] - 2016-06-28
### Added
- `Enumerable::getValueOf()` which returns value by index. (Replaces deprecated `getValue()`).
- Checking of duplication of indexes.
- Added validation of type of index. Index can be `string` or `int`.

### Changed
- `Enumerable` now throws `\LogicException` instead of `\DomainException`
  on initialization failure.

### Deprecated
- `Enumerable::getValue()` will be removed since v0.4.0. Use `getValueOf()` instead.


## [0.2.0] - 2016-03-02
### Changed
* (BC Break) `Enumerable` cannot be serialized anymore.
* Now enumerable types works as runtime constants and can be correctly
  checked on identity with operator `===`.

## [0.1.0] - 2015-11-16
Initial version
