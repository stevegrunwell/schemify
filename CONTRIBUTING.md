# Contributing to Schemify

First, thank you for your interest in helping to bring reliable, flexible structured data to WordPress!

Schemify aims to bring structured data to WordPress, by automatically building [JSON-LD objects](http://json-ld.org/) that conform to [Schema.org specifications](http://schema.org/). Schemify sets reasonable defaults that serve the majority of users, while enabling developers to control the data being used to represent their content.

## Branching workflow

Schemify has two major branches: `develop` and `master`. The `develop` branch represents the features currently in-progress, while `master` corresponds to the latest release. For a good explanation of this pattern, please [see the 10up Engineering Best Practices](https://10up.github.io/Engineering-Best-Practices/version-control/#plugins).

When starting a new feature branch, please be sure to branch off of `develop`; the only time anything is merged into `master` is during a release.


## Introducing new Schemas

Eventually, it would be great if Schemify could represent every available Schema. To aid in this process, the `SchemaInheritanceTest` class uses official (albeit experimental) JSON representations of Schema definitions and compares them to the class structure of the plugin.

As these tests require additional downloads (stored in `tests/data/`), they are not run by default. If you're introducing a new Schema to Schemify, please run the following tests:

```sh
$ phpunit --group=schemaDefinitions
```

These tests accomplish two things:

1. Verifying the inheritance tree (e.g. ensuring that "Article" extends "CreativeWork", which in-turn extends "Thing").
2. Analyze the properties used by each Schema to ensure they match the specification.

The first time you run these tests, the test suite **will** take much longer than usual as a side-effect of downloading the individual Schema definitions. Fortunately, these results will be cached for 90 days, so subsequent runs will be *much* faster.


## Unit tests

Schemify strives to provide a high level of useful tests for each corresponding schema, using the [WordPress core test framework](https://make.wordpress.org/core/handbook/testing/automated-testing/phpunit/) If your contributions are updating any code, _please_ include appropriate tests!

[![Code Climate](https://codeclimate.com/github/stevegrunwell/schemify/badges/gpa.svg)](https://codeclimate.com/github/stevegrunwell/schemify)
[![Test Coverage](https://codeclimate.com/github/stevegrunwell/schemify/badges/coverage.svg)](https://codeclimate.com/github/stevegrunwell/schemify/coverage)


## Change log

In order to ensure a clean upgrade path for users, [Schemify adheres to semantic versioning](http://semver.org/) and maintains a change log according to [the Keep a Changelog standards](http://keepachangelog.com/).

When submitting a pull request, please add a new change log entry under the "Unreleased" heading.
