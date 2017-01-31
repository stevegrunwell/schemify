# Contributing to Schemify

First, thank you for your interest in helping to bring reliable, flexible structured data to WordPress!

Schemify aims to bring structured data to WordPress, by automatically building [JSON-LD objects](http://json-ld.org/) that conform to [Schema.org specifications](http://schema.org/). Schemify sets reasonable defaults that serve the majority of users, while enabling developers to control the data being used to represent their content.

## Branching workflow

Schemify has two major branches: `develop` and `master`. The `develop` branch represents the features currently in-progress, while `master` corresponds to the latest release. For a good explanation of this pattern, please [see the 10up Engineering Best Practices](https://10up.github.io/Engineering-Best-Practices/version-control/#plugins).

When starting a new feature branch, please be sure to branch off of `develop`; the only time anything is merged into `master` is during a release.


## Unit tests

Schemify strives to provide a high level of useful tests for each corresponding schema. We use a combination of [Mockery](http://docs.mockery.io/en/latest/) (for object mocking) and [WP_Mock](https://github.com/10up/wp_mock) for functional mocking. If your contributions are updating any code, _please_ include appropriate tests!

[![Code Climate](https://codeclimate.com/github/stevegrunwell/schemify/badges/gpa.svg)](https://codeclimate.com/github/stevegrunwell/schemify)
[![Test Coverage](https://codeclimate.com/github/stevegrunwell/schemify/badges/coverage.svg)](https://codeclimate.com/github/stevegrunwell/schemify/coverage)


## Change log

In order to ensure a clean upgrade path for users, [Schemify adheres to semantic versioning](http://semver.org/) and maintains a change log according to [the Keep a Changelog standards](http://keepachangelog.com/).

When submitting a pull request, please add a new change log entry under the "Unreleased" heading.
