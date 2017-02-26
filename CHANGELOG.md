# Schemify Change Log

All notable changes to this project will be documented in this file, according to [the Keep a Changelog standards](http://keepachangelog.com/).

This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

* Default Schemas may now be set for custom post types at registration, using the `schemify_schema` property ([#3](https://github.com/stevegrunwell/schemify/issues/3)).
* Added the `SearchResultsPage` Schema, and applied it when `is_search()` is true ([#5](https://github.com/stevegrunwell/schemify/issues/5)).
* Fixed issue where the Yoast SEO compatibility layer was injecting default images into Schemas that wouldn't otherwise have an `image` property.
* Register the `Event` Schema ([#6](https://github.com/stevegrunwell/schemify/issues/6)).
* Fixed recursive schema bug for media objects ([#8](https://github.com/stevegrunwell/schemify/issues/8)).
* Update Composer dependencies used for development.


## [0.1.0]

* Initial public release.


[Unreleased]: https://github.com/stevegrunwell/schemify/compare/master...develop
[0.1.0]: https://github.com/stevegrunwell/schemify/releases/tag/v0.1.0
