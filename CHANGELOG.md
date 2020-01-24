# CHANGELOG

### 3.5.1 (2020-01-24)

* Fix sorting strings that start with a digit (#169, @mlocati)


### 3.5.0 (2019-12-02)

* CLDR data upgraded from 35.1 to 36 (#168, @mlocati)
* libphonenumber data upgraded from version 8.10.12 to version 8.10.22 (#168, @mlocati)


### 3.4.0 (2019-05-22)

* New function: `Punic\Unit::getPerFormat()` (#162, #163, @mlocati)  
  Example:
  ```php
  Punic\Unit::getPerFormat('minute', 'long`, 'en_US`)
  ```
  returns `'%1$s per minute'`
* libphonenumber data upgraded from version 8.10.1 to version 8.10.12 (#164, @mlocati)
* CLDR data updated from version 34 to version 35.1 (#164, @mlocati)  
  See http://cldr.unicode.org/index/downloads/cldr-35 for details  
  **NOTE** The plural rules for the Cornish (`kw`) language may have some issues: see https://unicode-org.atlassian.net/browse


### 3.3.1 (2018-12-07)
* Comparer no longer raises E_NOTICE warnings in case of problems (#161, @mlocati)


### 3.3.0 (2018-11-23)
* CLDR updated from 34 (#160, @mlocati, @c960657)  
  See http://cldr.unicode.org/index/downloads/cldr-34 for details
* Since CLDR 34 no longer contains telephone data, Punic now uses the data from libphonenumber.
* **BREAKING CHANGE** The two methds `Territory::getCode`/`Territory::getByCode` added in Punic 3.2.0 don't support the `internet` type anymore because CLDR removed the relevant data


### 3.2.0 (2018-11-08)

* CLDR updated from 32.0.1 to 33.1 (@mlocati)
  See #148, #156
* Draft status of CLDR data changed from *unconfirmed* to *contributed* (@mlocati)
  See #156
* Added possibility to overrides data (`Punic\Data::getOverrides`, `Punic\Data::setOverrides`, `Punic\Data::getOverridesGeneric`, `Punic\Data::setOverridesGeneric`) (@c960657)
  See #136
* Added `Punic\Number::formatPercent()` (@c960657)
  See #144
* Added `Punic\Number::formatCurrency()` (@c960657)
  See #144
* Added `Punic\Number::spellOut()` (@c960657, @mlocati)
  See #147, #155
* Allow using custom data directory (@c960657)
  See #132
* `Punic\Territory` *get* methods now support territory subdivisions (Provinces, Counties, ...) (@c960657, @mlocati)
  See #133
* Time zone aliases are now extracted from CLDR instead of hard coding them (@c960657)
  See #134
* Added `Punic\Currency::getNumericCode` and `Punic\Currency::getByNumericCode` (@c960657)
  See #138
* Added `Punic\Territory::getCode` and `Punic\Territory::getByCode` (@c960657)
  See #138
* Fix `[ALL]` languages placeholder in `punic-data` CLI command (@mlocati)
  See #131
* Fix handling of minutes/month in Calendar intervals (@c960657)
  See #143
* Support explicit timezone translations (@c960657)
  See #140
* Localise infinity and NaN (@c960657)
  See #146
* Minor performance improvements (@c960657)
  See #145
* `Punic\Plural::getRule` has been deprecated: use `Punic\Plural::getRuleOfType` (@mlocati)
  See #151
* **BREAKING CHANGE** `Punic\Territory::getChildTerritoryCodes()` changed its signature (a new argument with a default value has been added) (@c960657)
  See #138


### 3.1.0 (2018-02-09)

* CLDR updated from 31 to 32.0.1 (@mlocati)
* Added `Punic\Misc::joinAnd()` to join items in arrays or Traversables with an *and* (@c960657, @mlocati)
* Added `Punic\Misc::joinOr()` to join items in arrays or Traversables with an *or* (@c960657)
* `Punic\Misc::join()` has been deprecated (@c960657, @mlocati)
* **BREAKING CHANGE** The Punic\Misc::joinInternal() protected method changed its signature (@c960657)


### 3.0.1 (2018-02-01)

* Add `bin/punic-data` CLI command to the package (@mlocati)


### 3.0.0 (2018-02-01)

* Added support for skeleton formats (@c960657)
* Added support for date/time intervals (@c960657)
* Added punic-data CLI command to add/remove language data files (@mlocati)
* Added support for wide/narrow day period names (eg 'AM', 'PM') in `Calendar::formatDate()` (@c960657)
* Added support for time zone location-specific names in calendar formats (@c960657)
* Punic now accepts `DateTimeInterface` instances too (@c960657, @mlocati)
* Fix handling of single quotes in `Calendar::format` (@c960657)
* Fix handling of seconds fraction with more that 5 digits in `Calendar::format` (@c960657)
* Fix formatting hours in short GMT format (@c960657)
* Fix `Currency::getName()` when it receives the parameters 'zero', 'one', 'two', 'few', 'many', 'other' (@c960657)
* **BREAKING CHANGE** Moved punic from the `code` to the `src` directory (@mlocati)
* **BREAKING CHANGE** Data files are now in PHP format instead of JSON format (@c960657, @mlocati)
* **BREAKING CHANGE** The `build` CLI command has been removed (use the new `punic-data` CLI command) (@mlocati)
* **BREAKING CHANGE** The `bin/update-docs` CLI command has been removed (feature moved to the `punic-update-docs` CLI command in the `punic.github.io` repository) (@mlocati)
* **BREAKING CHANGE** The protected `Calendar::decodeFranctionsOfSeconds()` method has been renamed to `Calendar::decodeFractionsOfSeconds()` (@Remo)
* **BREAKING CHANGE** The protected `Calendar::decode...` methods changed their signature (removed the `DateTime` type hinting) (@c960657, @mlocati)


### 2.1.0 (2017-03-23)

* Added `Punic\Calendar::tryConvertIsoToPhpFormat` (@mlocati)


### 2.0.0 (2017-03-22)

* CLDR data updated from v27 to v31 (@mlocati)
* Added `Punic\Unit::getAvailableUnits` (@mlocati)
* Added `Punic\Unit::getName` (@mlocati)
* **BREAKING CHANGE** `Punic\Language::getName` return compound names only if requested (@mlocati)


### 1.6.5 (2017-02-03)

* Fix edge case on old PHP versions without the intl PHP extension (see #89) (@mlocati)


### 1.6.4 (2016-11-21)

* Fix edge case when `Collator` is an alias of `Symfony\Component\Intl\Collator\Collator` (@mlocati)


### 1.6.3 (2015-06-16)

* Fix sorting of list with non-US-ASCII chars (@mlocati, @Remo)
* Speed improvements (@mlocati)


### 1.6.2 (2015-06-10)

* Workaround for HHVM bug while handling timezone names (@mlocati)


### 1.6.1 (2015-05-11)

* Fix formatting ordinal suffix for the day of the month for English (@mlocati)


### 1.6.0 (2015-05-11)

* Fixed a bug in `Calendar::convertPhpToIsoFormat` (@mlocati)


### 1.5.0 (2015-03-26)

* Updated CLDR data to v27 (@mlocati)


### 1.4.1 (2015-01-18)

* Speed improvements (@LukasReschke)


### 1.4.0 (2015-01-14)

* Added functions to work with currencies (see `Punic\Currency`) (@mlocati)
* Added `Punic\Territory::getChildTerritoryCodes` (@mlocati)
* Added `Punic\Unit::getCountriesWithMeasurementSystem` (@mlocati)
* Added `functions to work with default paper sizes` (@mlocati)
* Detected browser languages are now sorted by relevance (see `Punic\Misc::getBrowserLocales()` and `Punic\Misc::parseHttpAcceptLanguage()`) (@mlocati)
* We now have a separate `composer.json` file for the automatic checks done by GitHub/TravisCI (@mlocati)


### 1.3.0 (2015-01-11)

* Added `Punic\Phone::getPrefixesForTerritory` (@mlocati)
* Added `Punic\Phone::getTerritoriesForPrefix` (@mlocati)
* Added `Punic\Phone::getMaxPrefixLength` (@mlocati)
* Added `Punic\Unit::getMeasurementSystems` (@mlocati)
* Added `Punic\Unit::getMeasurementSystemFor` (@mlocati)
* Added `Punic\Territory::getParentTerritoryCode` (supersedes deprecated protected `Punic\Data::getParentTerritory`) (@mlocati)


### 1.2.3 (2014-12-19)

* Added `Punic\Misc::getCharacterOrder` (@mlocati)
* Added `Added Punic\Misc::getLineOrder` (@mlocati)


### 1.2.2 (2014-12-12)

* Added `Added Punic\Language::getAll` (@mlocati)


### 1.2.1 (2014-12-11)

* Added `Punic\Misc::getBrowserLocales` (@mlocati)
* Added `Punic\Misc::parseHttpAcceptLanguage` (@mlocati)


### 1.2.0 (2014-12-11)

* Added `Punic\Territory::getTerritoriesWithInfo` (@mlocati)
* Added `Punic\Territory::getLanguages` (@mlocati)
* Added `Punic\Territory::getPopulation` (@mlocati)
* Added `Punic\Territory::getLiteracyLevel` (@mlocati)
* Added `Punic\Territory::getGrossDomesticProduct` (@mlocati)
* Added `Punic\Territory::getTerritoriesForLanguage` (@mlocati)


### 1.1.0 (2014-09-25)

* Switch from CLDR 25 to CLDR 26 (@mlocati)


### 1.0.2 (2014-09-05)

* `Punic\Calendar::toDateTime()` improved: now it can also convert FROM a timezone (@mlocati)


### 1.0.1 (2014-09-02)

* Added `punic.php` for people not using composer: simply include it and use all the Punic functions (@mlocati, @Remo)


### 1.0.0 (2014-09-01)

* First public version (@mlocati, @Remo)
