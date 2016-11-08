# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## 0.6.0 - 2016-11-08
### Added
- From now `Enumerable` implements `Equatable` interface from package `litgroup/equatable`.

### Changed
- (BC Break) Requires PHP >=7.0

## 0.5.0 - 2016-09-26
### Added
- `Enumerable::getRawValue()` which replaces `Enumerable::getIndex()`.
- `EnumerableTestCase::assertEnumHasRawValue()` and `EnumerableTestCase::assertEnumHasRawValues()`.

### Changed
- `EnumerableTestCase::assertEnumValuesCount()` is `static` from now.

### Deprecated
- `Enumerable::getIndex()`. Use `Enumerable::getRawValue()` instead.
- `EnumerableTestCase::assertEnumIndex()`. Use `EnumerableTestCase::assertEnumHasRawValue()` instead.
- `EnumerableTestCase::assertEnumIndexs()`. Use `EnumerableTestCase::assertEnumHasRawValues()` instead.

### Fixed
- Bug #11. Now Enumerable will throw a `LogicException` if factory method of enumerable returns scalar value.

## 0.4.1 - 2016-07-11
### Changed
- EnumerableTestCase::assertEnumIndexes() now checks, that tested enumerable
  class contains amount of values equal to amount of values in the payload of
  the assertion.
- `Enumerable` can check type of enumerable value in some cases.

## 0.4.0 - 2016-07-01
### Added
- Abstract test case for enumerable values `LitGroup\Enumerable\Test\EnumerableTestCase`.

### Removed
- `Enumerable::getValue()` was removed. Use `Enumerable::getValueOf()` instead.

## 0.3.0 - 2016-06-28
### Added
- `Enumerable::getValueOf()` which returns value by index. (Replaces deprecated `getValue()`).
- Checking of duplication of indexes.
- Added validation of type of index. Index can be `string` or `int`.

### Changed
- `Enumerable` now throws `\LogicException` instead of `\DomainException`
  on initialization failure.

### Deprecated
- `Enumerable::getValue()` will be removed since v0.4.0. Use `getValueOf()` instead.


## 0.2.0 - 2016-03-02
### Changed
- (BC Break) `Enumerable` cannot be serialized anymore.
- Now enumerable types works as runtime constants and can be correctly
  checked on identity with operator `===`.

## 0.1.0 - 2015-11-16
### Added
- Basic implementation for `Enumerable`.
