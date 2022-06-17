# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased] - TBD

## [1.1.2] - 2022-06-21
### Added
- Cypress E2E tests (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul), [@Sidsector9](https://github.com/Sidsector9) via [#167](https://github.com/10up/autoshare-for-twitter/pull/167)).
- Handle tweeting from staging/testing/local environments to prevent accidental tweets (props [@dinhtungdu](https://github.com/dinhtungdu), [@iamdharmesh](https://github.com/iamdharmesh), [@peterwilsoncc](https://github.com/peterwilsoncc), [@jeffpaul](https://github.com/jeffpaul) via [#161](https://github.com/10up/autoshare-for-twitter/pull/161)).
- Dependency security scanning (props [@jeffpaul](https://github.com/jeffpaul), [@iamdharmesh](https://github.com/iamdharmesh) via [#160](https://github.com/10up/autoshare-for-twitter/pull/160)).

### Changed
- Bump WordPress "tested up to" version 6.0 (props [@iamdharmesh](https://github.com/iamdharmesh), [@vikrampm1](https://github.com/vikrampm1), [@jeffpaul](https://github.com/jeffpaul), [@Sidsector9](https://github.com/Sidsector9) via [#162](https://github.com/10up/autoshare-for-twitter/pull/162)).

### Fixed
- Incorrect `Tweet this post` checkbox behavior in the Classic Editor (props [@iamdharmesh](https://github.com/iamdharmesh), [@cadic](https://github.com/cadic) via [#169](https://github.com/10up/autoshare-for-twitter/pull/169)).
- "Plugin asset/readme update" GitHub Action failure (props [@iamdharmesh](https://github.com/iamdharmesh), [@dkotter](https://github.com/dkotter), [@jeffpaul](https://github.com/jeffpaul) via [#166](https://github.com/10up/autoshare-for-twitter/pull/166) and [#165](https://github.com/10up/autoshare-for-twitter/pull/165)).

## [1.1.1] - 2022-04-13
### Fixed
- If Autoshare is enabled by default, it does not consider the post-level "Tweet this post" checkbox and always tweets (props [@iamdharmesh](https://github.com/iamdharmesh), [@Sidsector9](https://github.com/Sidsector9) via [#155](https://github.com/10up/autoshare-for-twitter/pull/155)).

## [1.1.0] - 2022-04-13
### Added
- Colored icons to represent autoshare status (props [@linawiezkowiak](https://github.com/linawiezkowiak), [@oszkarnagy](https://github.com/oszkarnagy), [@Sidsector9](https://github.com/Sidsector9), [@dinhtungdu](https://github.com/dinhtungdu) via [#142](https://github.com/10up/autoshare-for-twitter/pull/142)).
- Sample copy for example responses (props [@iamdharmesh](https://github.com/iamdharmesh), [@Sidsector9](https://github.com/Sidsector9), [@jeffpaul](https://github.com/jeffpaul) via [#149](https://github.com/10up/autoshare-for-twitter/pull/149)).
- PHP 8 compatibility (props [@Sidsector9](https://github.com/Sidsector9), [@faisal-alvi](https://github.com/faisal-alvi) via [#144](https://github.com/10up/autoshare-for-twitter/pull/144)).
- E2E Tests with Cypress (props [@thrijith](https://github.com/thrijith), [@iamdharmesh](https://github.com/iamdharmesh), [@dinhtungdu](https://github.com/dinhtungdu), [@jeffpaul](https://github.com/jeffpaul) via [#145](https://github.com/10up/autoshare-for-twitter/pull/145)).

### Changed
- Update dependency `abraham/twitteroauth` from 1.2.0 to 2.0.0 to ensure PHP 8.0 support (props [@Sidsector9](https://github.com/Sidsector9), [@faisal-alvi](https://github.com/faisal-alvi) via [#144](https://github.com/10up/autoshare-for-twitter/pull/144)).
- App setup instructions for getting API keys and tokens (props [@iamdharmesh](https://github.com/iamdharmesh), [@Sidsector9](https://github.com/Sidsector9), [@jeffpaul](https://github.com/jeffpaul) via [#149](https://github.com/10up/autoshare-for-twitter/pull/149)).
- Bump WordPress version "tested up to" 5.9 (props [@iamdharmesh](https://github.com/iamdharmesh), [@Sidsector9](https://github.com/Sidsector9), [@sudip-10up](https://github.com/sudip-10up) via [#151](https://github.com/10up/autoshare-for-twitter/pull/151)).

### Fixed
- Warning thrown on activating plugin for the first time (props [@Sidsector9](https://github.com/Sidsector9), [@dinhtungdu](https://github.com/dinhtungdu) via [#138](https://github.com/10up/autoshare-for-twitter/pull/138)).
- CI pipeline failures (props [@dkotter](https://github.com/dkotter), [@iamdharmesh](https://github.com/iamdharmesh), [@Sidsector9](https://github.com/Sidsector9) via [#147](https://github.com/10up/autoshare-for-twitter/pull/147)).
### Security
- Bump ajv from 6.10.2 to 6.12.6 (props [@dependabot](https://github.com/apps/dependabot) via [#141](https://github.com/10up/autoshare-for-twitter/pull/141)).
- Bump tar from 4.4.8 to 4.4.19 (props [@dependabot](https://github.com/apps/dependabot) via [#143](https://github.com/10up/autoshare-for-twitter/pull/143)).
- Bump ini from 1.3.5 to 1.3.8 (props [@dependabot](https://github.com/apps/dependabot) via [#152](https://github.com/10up/autoshare-for-twitter/pull/152)).

## [1.0.6] - 2020-09-19
- Note: this was a hotfix release to fix an issue with deploys to WordPress.org.

## [1.0.5] - 2021-09-15
### Added
- Tweeted status column to All Posts table list view (props [@thrijith](https://github.com/thrijith), [@dinhtungdu](https://github.com/dinhtungdu), [@linawiezkowiak](https://github.com/linawiezkowiak), [@oszkarnagy](https://github.com/oszkarnagy), [@jeffpaul](https://github.com/jeffpaul/) via [#121](https://github.com/10up/autoshare-for-twitter/pull/121)).

### Changed
- Bump WordPress version "tested up to" 5.8 (props [@thrijith](https://github.com/thrijith), [@barneyjeffries](https://github.com/barneyjeffries), [@jeffpaul](https://github.com/jeffpaul/) via [#126](https://github.com/10up/autoshare-for-twitter/pull/126)).
- WP Snapshot for auotmated testing to WP 5.6 (props [@dinhtungdu](https://github.com/dinhtungdu), [@jeffpaul](https://github.com/jeffpaul/) via [#107](https://github.com/10up/autoshare-for-twitter/pull/107)).
- Update dependency `abraham/twitteroauth` from ^0.7.4 to 1.2.0 to ensure PHP 7.4 support and WordPress VIP Go compatability (props [@thrijith](https://github.com/thrijith), [@dinhtungdu](https://github.com/dinhtungdu), [@rickalee](https://github.com/rickalee) via [#122](https://github.com/10up/autoshare-for-twitter/pull/122)).
- Update PHPUnit test cases to include new functions (props [@thrijith](https://github.com/thrijith) via [#124](https://github.com/10up/autoshare-for-twitter/pull/124)).

### Fixed
- Ensure that special characters are properly encoded (props [@dinhtungdu](https://github.com/dinhtungdu), [@rickalee](https://github.com/rickalee) via [#116](https://github.com/10up/autoshare-for-twitter/pull/116)).

### Security
- Bump `lodash` from 4.17.15 to 4.17.21 (props [@dependabot](https://github.com/apps/dependabot) via [#108](https://github.com/10up/autoshare-for-twitter/pull/108), [#130](https://github.com/10up/autoshare-for-twitter/pull/130)).
- Bump `elliptic` from 6.5.1 to 6.5.4 (props [@dependabot](https://github.com/apps/dependabot) via [#109](https://github.com/10up/autoshare-for-twitter/pull/109), [#123](https://github.com/10up/autoshare-for-twitter/pull/123)).
- Bump `yargs-parser` from 13.1.1 to 13.1.2 (props [@dependabot](https://github.com/apps/dependabot) via [#111](https://github.com/10up/autoshare-for-twitter/pull/111)).
- Bump `ini` from 1.3.5 to 1.3.7 (props [@dependabot](https://github.com/apps/dependabot) via [#115](https://github.com/10up/autoshare-for-twitter/pull/115)).
- Bump `y18n` from 4.0.0 to 4.0.1 (props [@dependabot](https://github.com/apps/dependabot) via [#125](https://github.com/10up/autoshare-for-twitter/pull/125)).
- Bump `ssri` from 6.0.1 to 6.0.2 (props [@dependabot](https://github.com/apps/dependabot) via [#127](https://github.com/10up/autoshare-for-twitter/pull/127)).
- Bump `rmccue/requests` from 1.7.0 to 1.8.0 (props [@dependabot](https://github.com/apps/dependabot) via [#129](https://github.com/10up/autoshare-for-twitter/pull/129)).
- Bump `hosted-git-info` from 2.8.4 to 2.8.9 (props [@dependabot](https://github.com/apps/dependabot) via [#131](https://github.com/10up/autoshare-for-twitter/pull/131)).
- Bump `browserslist` from 4.7.0 to 4.16.6 (props [@dependabot](https://github.com/apps/dependabot) via [#132](https://github.com/10up/autoshare-for-twitter/pull/132)).
- Bump `path-parse` from 1.0.6 to 1.0.7 (props [@dependabot](https://github.com/apps/dependabot) via [#134](https://github.com/10up/autoshare-for-twitter/pull/134)).

## [1.0.4] - 2020-07-02
- Note: this was a hotfix release to fix an issue with included libraries.

## [1.0.3] - 2020-07-01
### Added
- `autoshare_for_twitter_disable_on_transition_post_status` filter to disable tweeting based on post status change (props [@rickalee](https://github.com/rickalee) via [#99](https://github.com/10up/autoshare-for-twitter/pull/99)).

### Changed
- Bumped WordPress version support to 5.4.2 (props [@dinhtungdu](https://github.com/dinhtungdu), [@jeffpaul](https://github.com/jeffpaul/) via [#106](https://github.com/10up/autoshare-for-twitter/pull/106)).
- Build, test, and release processes (props [@johnwatkins0](https://github.com/johnwatkins0), [@dinhtungdu](https://github.com/dinhtungdu) via [#96](https://github.com/10up/autoshare-for-twitter/pull/96), [#97](https://github.com/10up/autoshare-for-twitter/pull/97), [#98](https://github.com/10up/autoshare-for-twitter/pull/98), [#101](https://github.com/10up/autoshare-for-twitter/pull/101)).

### Fixed
- Enable autoshare meta always set to 0 when saving draft (props [@dinhtungdu](https://github.com/dinhtungdu), [@rickalee](https://github.com/rickalee) via [#103](https://github.com/10up/autoshare-for-twitter/pull/103)).

## [1.0.2] - 2020-03-12
### Added
- WP Acceptance tests (props [@johnwatkins0](https://github.com/johnwatkins0) via [#84](https://github.com/10up/autoshare-for-twitter/pull/84)).

### Changed
- New and improved settings page UX (props [@dinhtungdu](https://github.com/dinhtungdu), [@jeffpaul](https://github.com/jeffpaul/), [@johnwatkins0](https://github.com/johnwatkins0), [@linawiezkowiak](https://github.com/linawiezkowiak), [@oszkarnagy](https://github.com/oszkarnagy) via [#78](https://github.com/10up/autoshare-for-twitter/pull/78)).

### Fixed
- Bug that caused posts to be inadvertently tweeted when switching from draft to publish (props [@johnwatkins0](https://github.com/johnwatkins0), [@rickalee](https://github.com/rickalee) via [#82](https://github.com/10up/autoshare-for-twitter/pull/82)).
- Build script in release process (props [@johnwatkins0](https://github.com/johnwatkins0) via [#77](https://github.com/10up/autoshare-for-twitter/pull/77)).

## [1.0.1] - 2020-02-03
### Added
- `autoshare_for_twitter_enabled_default` filter to allow autoshare to be enabled by default for a post type (props [@johnwatkins0](https://github.com/johnwatkins0), [@rickalee](https://github.com/rickalee), [@dinhtungdu](https://github.com/dinhtungdu) via [#71](https://github.com/10up/autoshare-for-twitter/pull/71)).

### Changed
- Bypass character texturization when the post title is tweeted (props [@johnwatkins0](https://github.com/johnwatkins0), [@rickalee](https://github.com/rickalee) via [#73](https://github.com/10up/autoshare-for-twitter/pull/73)).

### Removed
- Second instance of the `autoshare_for_twitter_tweet` filter (props [@johnwatkins0](https://github.com/johnwatkins0) via [#70](https://github.com/10up/autoshare-for-twitter/pull/70)).

## [1.0.0] - 2019-12-17
### Added
- Initial public release! 🎉
- Support Post and Page post types by default, provide Custom Post Type (props [@johnwatkins0](https://github.com/johnwatkins0) via [#25](https://github.com/10up/autoshare-for-twitter/pull/25)).
- REST API endpoint to replace AJAX callback (props [@johnwatkins0](https://github.com/johnwatkins0) via [#33](https://github.com/10up/autoshare-for-twitter/pull/33)).
- Build process, PHPCS linting, unit tests, and Travis CI (props [@johnwatkins0](https://github.com/johnwatkins0), [@adamsilverstein](https://github.com/adamsilverstein/) via [#23](https://github.com/10up/autoshare-for-twitter/pull/23), [#24](https://github.com/10up/autoshare-for-twitter/pull/24), [#28](https://github.com/10up/autoshare-for-twitter/pull/28), [#29](https://github.com/10up/autoshare-for-twitter/pull/29)).
- Plugin banner and icon images (props [@sncampbell](https://github.com/sncampbell/) via [#31](https://github.com/10up/autoshare-for-twitter/pull/31)).
- Twitter disclaimer per their trademark guidelines as part of their brand guidelines (props [@jeffpaul](https://github.com/jeffpaul/) via [#50](https://github.com/10up/autoshare-for-twitter/pull/50)).

### Changed
- Refactor v0.1.0 significantly (props [@adamsilverstein](https://github.com/adamsilverstein/), [@johnwatkins0](https://github.com/johnwatkins0), [@jeffpaul](https://github.com/jeffpaul/) via [#1](https://github.com/10up/autoshare-for-twitter/pull/1), [#49](https://github.com/10up/autoshare-for-twitter/pull/49)).

### Security
- XSS prevention - switch from .innerHTML to text (props [@adamsilverstein](https://github.com/adamsilverstein/) via [#1](https://github.com/10up/autoshare-for-twitter/pull/1)).

## [0.1.0] - 2018-05-11
- Initial closed source release (props [@scottlee](https://github.com/scottlee/)).

[Unreleased]: https://github.com/10up/autoshare-for-twitter/compare/trunk...develop
[1.1.2]: https://github.com/10up/autoshare-for-twitter/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/10up/autoshare-for-twitter/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/10up/autoshare-for-twitter/compare/1.0.6...1.1.0
[1.0.6]: https://github.com/10up/autoshare-for-twitter/compare/1.0.5...1.0.6
[1.0.5]: https://github.com/10up/autoshare-for-twitter/compare/1.0.4...1.0.5
[1.0.4]: https://github.com/10up/autoshare-for-twitter/compare/1.0.3...1.0.4
[1.0.3]: https://github.com/10up/autoshare-for-twitter/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/10up/autoshare-for-twitter/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/10up/autoshare-for-twitter/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/10up/autoshare-for-twitter/compare/1020035...1.0.0
[0.1.0]: https://github.com/10up/autoshare-for-twitter/commit/1020035f2d4843221d996bd5f8fe39d9ee850b5d
