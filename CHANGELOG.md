# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased] - TBD

## [1.0.3] - 2020-07-01
### Added
- `autoshare_for_twitter_disable_on_transition_post_status` filter to disable tweeting based on post status change (props [@rickalee](https://github.com/rickalee) via [#99](https://github.com/10up/autoshare-for-twitter/pull/99))

### Changed
- Bumped WordPress version support to 5.4.2 (props [@dinhtungdu](https://github.com/dinhtungdu), [@jeffpaul](https://github.com/jeffpaul/) via [#106](https://github.com/10up/autoshare-for-twitter/pull/106))
- Build, test, and release processes (props [@johnwatkins0](https://github.com/johnwatkins0), [@dinhtungdu](https://github.com/dinhtungdu) via [#96](https://github.com/10up/autoshare-for-twitter/pull/96), [#97](https://github.com/10up/autoshare-for-twitter/pull/97), [#98](https://github.com/10up/autoshare-for-twitter/pull/98), [#101](https://github.com/10up/autoshare-for-twitter/pull/101))

### Fixed
- Enable autoshare meta always set to 0 when saving draft (props [@dinhtungdu](https://github.com/dinhtungdu), [@rickalee](https://github.com/rickalee) via [#103](https://github.com/10up/autoshare-for-twitter/pull/103))

## [1.0.2] - 2020-03-12
### Added
- WP Acceptance tests (props [@johnwatkins0](https://github.com/johnwatkins0) via [#84](https://github.com/10up/autoshare-for-twitter/pull/84))

### Changed
- New and improved settings page UX (props [@dinhtungdu](https://github.com/dinhtungdu), [@jeffpaul](https://github.com/jeffpaul/), [@johnwatkins0](https://github.com/johnwatkins0), [@linawiezkowiak](https://github.com/linawiezkowiak), [@oszkarnagy](https://github.com/oszkarnagy) via [#78](https://github.com/10up/autoshare-for-twitter/pull/78))

### Fixed
- Bug that caused posts to be inadvertently tweeted when switching from draft to publish (props [@johnwatkins0](https://github.com/johnwatkins0), [@rickalee](https://github.com/rickalee) via [#82](https://github.com/10up/autoshare-for-twitter/pull/82))
- Build script in release process (props [@johnwatkins0](https://github.com/johnwatkins0) via [#77](https://github.com/10up/autoshare-for-twitter/pull/77))

## [1.0.1] - 2020-02-03
### Added
- `autoshare_for_twitter_enabled_default` filter to allow autoshare to be enabled by default for a post type (props [@johnwatkins0](https://github.com/johnwatkins0), [@rickalee](https://github.com/rickalee), [@dinhtungdu](https://github.com/dinhtungdu) via [#71](https://github.com/10up/autoshare-for-twitter/pull/71))

### Changed
- Bypass character texturization when the post title is tweeted (props [@johnwatkins0](https://github.com/johnwatkins0), [@rickalee](https://github.com/rickalee) via [#73](https://github.com/10up/autoshare-for-twitter/pull/73))

### Removed
- Second instance of the `autoshare_for_twitter_tweet` filter (props [@johnwatkins0](https://github.com/johnwatkins0) via [#70](https://github.com/10up/autoshare-for-twitter/pull/70))

## [1.0.0] - 2019-12-17
### Added
- Initial public release! 🎉
- Support Post and Page post types by default, provide Custom Post Type (props [@johnwatkins0](https://github.com/johnwatkins0) via [#25](https://github.com/10up/autoshare-for-twitter/pull/25))
- REST API endpoint to replace AJAX callback (props [@johnwatkins0](https://github.com/johnwatkins0) via [#33](https://github.com/10up/autoshare-for-twitter/pull/33))
- Build process, PHPCS linting, unit tests, and Travis CI (props [@johnwatkins0](https://github.com/johnwatkins0), [@adamsilverstein](https://github.com/adamsilverstein/) via [#23](https://github.com/10up/autoshare-for-twitter/pull/23), [#24](https://github.com/10up/autoshare-for-twitter/pull/24), [#28](https://github.com/10up/autoshare-for-twitter/pull/28), [#29](https://github.com/10up/autoshare-for-twitter/pull/29))
- Plugin banner and icon images (props [@sncampbell](https://github.com/sncampbell/) via [#31](https://github.com/10up/autoshare-for-twitter/pull/31))
- Twitter disclaimer per their trademark guidelines as part of their brand guidelines (props [@jeffpaul](https://github.com/jeffpaul/) via [#50](https://github.com/10up/autoshare-for-twitter/pull/50))

### Changed
- Refactor v0.1.0 significantly (props [@adamsilverstein](https://github.com/adamsilverstein/), [@johnwatkins0](https://github.com/johnwatkins0), [@jeffpaul](https://github.com/jeffpaul/) via [#1](https://github.com/10up/autoshare-for-twitter/pull/1), [#49](https://github.com/10up/autoshare-for-twitter/pull/49))

### Security
- XSS prevention - switch from .innerHTML to text (props [@adamsilverstein](https://github.com/adamsilverstein/) via [#1](https://github.com/10up/autoshare-for-twitter/pull/1))

## [0.1.0] - 2018-05-11
- Initial closed source release (props [@scottlee](https://github.com/scottlee/))

[Unreleased]: https://github.com/10up/autoshare-for-twitter/compare/trunk...develop
[1.0.3]: https://github.com/10up/autoshare-for-twitter/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/10up/autoshare-for-twitter/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/10up/autoshare-for-twitter/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/10up/autoshare-for-twitter/compare/1020035...1.0.0
[0.1.0]: https://github.com/10up/autoshare-for-twitter/commit/1020035f2d4843221d996bd5f8fe39d9ee850b5d
