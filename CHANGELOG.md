# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased] - TBD

## [2.1.1] - 2023-08-XX

### Changed
- Improved setup instructions and error handing (props [@johnwatkins0](https://github.com/johnwatkins0), [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul) via [#255](https://github.com/10up/autoshare-for-twitter/pull/255))
- Bump WordPress "tested up to" version 6.3 (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul) via [#265](https://github.com/10up/autoshare-for-twitter/pull/265))

### Fixed
- Better error handling for environments that don't match our minimum PHP version (props [@dkotter](https://github.com/dkotter), [@rahulsprajapati](https://github.com/rahulsprajapati), [@iamdharmesh](https://github.com/iamdharmesh) via [#258](https://github.com/10up/autoshare-for-twitter/pull/258))
- Ensure our E2E tests work properly on WordPress 6.3 (props [@iamdharmesh](https://github.com/iamdharmesh), [@dkotter](https://github.com/dkotter) via [#264](https://github.com/10up/autoshare-for-twitter/pull/264))
- The custom tweet message does not work with scheduled posts.  (props [@GeoffLambert77](https://github.com/GeoffLambert77), [@iamdharmesh](https://github.com/iamdharmesh), [@Sidsector9](https://github.com/Sidsector9) via [#267](https://github.com/10up/autoshare-for-twitter/pull/267))
- Connected Twitter accounts visibility in the classic editor. (props [@iamdharmesh](https://github.com/iamdharmesh), [@Sidsector9](https://github.com/Sidsector9) via [#269](https://github.com/10up/autoshare-for-twitter/pull/269))

### Security
- Bump `tough-cookie` from 2.5.0 to 4.1.3 and `@cypress/request` from 2.88.10 to 2.88.12 (props [@dependabot[bot]](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh) via [#266](https://github.com/10up/autoshare-for-twitter/pull/266))
- Bump `word-wrap` from 1.2.3 to 1.2.4 (props [@dependabot[bot]](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh) via [#257](https://github.com/10up/autoshare-for-twitter/pull/257))

## [2.1.0] - 2023-07-06
### Added
- Support for tweeting via multiple Twitter accounts (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul), [@Sidsector9](https://github.com/Sidsector9) via [#238](https://github.com/10up/autoshare-for-twitter/pull/238)).
- Obfuscation of saved Twitter keys in the UI (props [@lgrzegorski](https://github.com/lgrzegorski), [@bmarshall511](https://github.com/bmarshall511), [@iamdharmesh](https://github.com/iamdharmesh) via [#245](https://github.com/10up/autoshare-for-twitter/pull/245)).
- GitHub Action summary for end-to-end tests (props [@iamdharmesh](https://github.com/iamdharmesh), [@ravinderk](https://github.com/ravinderk) via [#247](https://github.com/10up/autoshare-for-twitter/pull/247)).

### Changed
- Readme updates for FAQs and formatting (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul) via [#235](https://github.com/10up/autoshare-for-twitter/pull/235), [#241](https://github.com/10up/autoshare-for-twitter/pull/241), [#242](https://github.com/10up/autoshare-for-twitter/pull/242)).
- Enhanced end-to-end tests by implementing mocking of Twitter API and bypassing actual Twitter API calls (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul), [@Sidsector9](https://github.com/Sidsector9) via [#238](https://github.com/10up/autoshare-for-twitter/pull/238)).
- Updated the Dependency Review GitHub Action (props [@jeffpaul](https://github.com/jeffpaul), [@iamdharmesh](https://github.com/iamdharmesh) via [#239](https://github.com/10up/autoshare-for-twitter/pull/239)).
- Fixed inconsistent tweet status for scheduled posts (props [@mae829](https://github.com/mae829), [@iamdharmesh](https://github.com/iamdharmesh), [@ravinderk](https://github.com/ravinderk) via [#246](https://github.com/10up/autoshare-for-twitter/pull/246)).
- Improved error handing (props [@iamdharmesh](https://github.com/iamdharmesh), [@Sidsector9](https://github.com/Sidsector9) via [#249](https://github.com/10up/autoshare-for-twitter/pull/249)).

## [2.0.0] - 2023-05-16
**Autoshare for Twitter 2.0.0 utilizes [Twitter's v2 API](https://developer.twitter.com/en/products/twitter-api).  If you have not already done so, please [migrate your app](https://developer.twitter.com/en/portal/projects-and-apps) to Twitter's v2 API to continue using Autoshare for Twitter.  [Learn more about migrating here](https://developer.twitter.com/en/docs/twitter-api/migrate/ready-to-migrate).**

### Added
- Migrated to Twitter API v2 (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul), [@ravinderk](https://github.com/ravinderk), [@Sidsector9](https://github.com/Sidsector9) via [#229](https://github.com/10up/autoshare-for-twitter/pull/229)).

### Changed
- Bump WordPress "tested up to" version 6.2 (props [@Sidsector9](https://github.com/Sidsector9), [@iamdharmesh](https://github.com/iamdharmesh) via [#228](https://github.com/10up/autoshare-for-twitter/pull/228)).
- Update plugin settings and guidelines to set up a Twitter app (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul), [@ravinderk](https://github.com/ravinderk) [@Sidsector9](https://github.com/Sidsector9) via [#229](https://github.com/10up/autoshare-for-twitter/pull/229)).
- Updated documentation (props [@jeffpaul](https://github.com/jeffpaul), [@iamdharmesh](https://github.com/iamdharmesh) via [#231](https://github.com/10up/autoshare-for-twitter/pull/231)).

### Security
- Bump `simple-git` from 3.15.1 to 3.16.0 (props [@dependabot](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh) via [#221](https://github.com/10up/autoshare-for-twitter/pull/221)).
- Bump `http-cache-semantics` from 4.1.0 to 4.1.1 (props [@dependabot](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh) via [#222](https://github.com/10up/autoshare-for-twitter/pull/222)).
- Bump `@sideway/formula` from 3.0.0 to 3.0.1 (props [@dependabot](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh) via [#223](https://github.com/10up/autoshare-for-twitter/pull/223)).
- Bump `webpack` from 5.74.0 to 5.76.0 (props [@dependabot](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh) via [#224](https://github.com/10up/autoshare-for-twitter/pull/224)).

## [1.3.0] - 2023-01-20
### Added
- "Tweet now" functionality (props [@Sidsector9](https://github.com/Sidsector9), [@iamdharmesh](https://github.com/iamdharmesh), [@cadic](https://github.com/cadic), [@jeffpaul](https://github.com/jeffpaul), [@linawiezkowiak](https://github.com/linawiezkowiak), [@oszkarnagy](https://github.com/oszkarnagy) via [#188](https://github.com/10up/autoshare-for-twitter/pull/188)).
- Toggle for adding/removing featured image from the tweet (props [@Sidsector9](https://github.com/Sidsector9), [@iamdharmesh](https://github.com/iamdharmesh), [@cadic](https://github.com/cadic), [@jeffpaul](https://github.com/jeffpaul), [@linawiezkowiak](https://github.com/linawiezkowiak), [@oszkarnagy](https://github.com/oszkarnagy) via [#188](https://github.com/10up/autoshare-for-twitter/pull/188)).
- Show Twitter status logs for the draft post if the post has been switched back to Draft from Published, and has already been Tweeted (props [@iamdharmesh](https://github.com/iamdharmesh), [@faisal-alvi](https://github.com/faisal-alvi), [@jeffpaul](https://github.com/jeffpaul), [@linawiezkowiak](https://github.com/linawiezkowiak), [@oszkarnagy](https://github.com/oszkarnagy) via [#215](https://github.com/10up/autoshare-for-twitter/pull/215)).
- Plugin screenshots to readme files (props [@iamdharmesh](https://github.com/iamdharmesh) via [#218](https://github.com/10up/autoshare-for-twitter/pull/218)).

### Changed
- UI Improvements in Tweet status (props [@Sidsector9](https://github.com/Sidsector9), [@iamdharmesh](https://github.com/iamdharmesh), [@cadic](https://github.com/cadic), [@jeffpaul](https://github.com/jeffpaul), [@linawiezkowiak](https://github.com/linawiezkowiak), [@oszkarnagy](https://github.com/oszkarnagy) via [#188](https://github.com/10up/autoshare-for-twitter/pull/188)).
- UI Improvements in tweet message character count (props [@iamdharmesh](https://github.com/iamdharmesh), [@Sidsector9](https://github.com/Sidsector9), [@ravinderk](https://github.com/ravinderk), [@jeffpaul](https://github.com/jeffpaul), [@linawiezkowiak](https://github.com/linawiezkowiak), [@oszkarnagy](https://github.com/oszkarnagy) via [#214](https://github.com/10up/autoshare-for-twitter/pull/214)).
- Run GitHub Action workflows only when it required (props [@iamdharmesh](https://github.com/iamdharmesh), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#204](https://github.com/10up/autoshare-for-twitter/pull/204)).
- Migrated Cypress from 9.0.0 to 11.2.0 (props [@iamdharmesh](https://github.com/iamdharmesh), [@Sidsector9](https://github.com/Sidsector9) via [#205](https://github.com/10up/autoshare-for-twitter/pull/205)).
- Run E2E tests on the zip generated by "Build release zip" action (props [@iamdharmesh](https://github.com/iamdharmesh), [@dkotter](https://github.com/dkotter), [@Sidsector9](https://github.com/Sidsector9) via [#206](https://github.com/10up/autoshare-for-twitter/pull/206)).

### Fixed
- E2E tests fail in the CI with warm cache (props [@cadic](https://github.com/cadic), [@iamdharmesh](https://github.com/iamdharmesh) via [#212](https://github.com/10up/autoshare-for-twitter/pull/212)).

### Security
- Bump `decode-uri-component` from 0.2.0 to 0.2.2 (props [@dependabot](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh) via [#208](https://github.com/10up/autoshare-for-twitter/pull/208)).
- Bump `simple-git` from 3.14.1 to 3.15.1 (props [@dependabot](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh) via [#210](https://github.com/10up/autoshare-for-twitter/pull/210)).

## [1.2.1] - 2022-12-07
**Note that this release bumps the WordPress minimum from 5.3 to 5.7 and PHP minimum from 7.2 to 7.4.**

### Added
- "PR Automator" GitHub Action (props [@iamdharmesh](https://github.com/iamdharmesh), [@Sidsector9](https://github.com/Sidsector9), [@jeffpaul](https://github.com/jeffpaul) via [#194](https://github.com/10up/autoshare-for-twitter/pull/194), [#196](https://github.com/10up/autoshare-for-twitter/pull/196)).
- "Build release zip" GitHub Action (props [@iamdharmesh](https://github.com/iamdharmesh), [@dkotter](https://github.com/dkotter) via [#201](https://github.com/10up/autoshare-for-twitter/pull/201)).

### Changed
- Bump minimum `PHP` version from 7.2 to 7.4 (props [@Sidsector9](https://github.com/Sidsector9), [@iamdharmesh](https://github.com/iamdharmesh), [@vikrampm1](https://github.com/vikrampm1) via [#197](https://github.com/10up/autoshare-for-twitter/pull/197)).
- Bump minimum `WordPress` version from 5.3 to 5.7 (props [@Sidsector9](https://github.com/Sidsector9), [@iamdharmesh](https://github.com/iamdharmesh), [@vikrampm1](https://github.com/vikrampm1) via [#197](https://github.com/10up/autoshare-for-twitter/pull/197)).
- Bump WordPress "tested up to" version 6.1 (props [@iamdharmesh](https://github.com/iamdharmesh), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#200](https://github.com/10up/autoshare-for-twitter/pull/200)).
- Support Level from `Active` to `Stable` (props [@jeffpaul](https://github.com/jeffpaul), [@dkotter](https://github.com/dkotter) via [#195](https://github.com/10up/autoshare-for-twitter/pull/195)).

### Security
- Bump `json-schema` from 0.2.3 to 0.4.0 (props [@dependabot](https://github.com/apps/dependabot), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#189](https://github.com/10up/autoshare-for-twitter/pull/189)).
- Bump `jsprim` from 1.4.1 to 1.4.2 (props [@dependabot](https://github.com/apps/dependabot), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#189](https://github.com/10up/autoshare-for-twitter/pull/189)).
- Bump `simple-git` from 2.47.0 to 3.14.1 (props [@dependabot](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh) via [#192](https://github.com/10up/autoshare-for-twitter/pull/192)).
- Bump `@wordpress/env` from 4.1.3 to 5.3.0 (props [@dependabot](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh), [@faisal-alvi](https://github.com/faisal-alvi) via [#191](https://github.com/10up/autoshare-for-twitter/pull/191), [#192](https://github.com/10up/autoshare-for-twitter/pull/192)).
- Bump `got` from 10.7.0 to 11.8.5 (props [@dependabot](https://github.com/apps/dependabot), [@faisal-alvi](https://github.com/faisal-alvi) via [#191](https://github.com/10up/autoshare-for-twitter/pull/191)).

## [1.2.0] - 2022-09-28
**Note that this release bumps the WordPress minimum from 4.9 to 5.3.**

### Added
- AutoTweet panel in editor sidebar and pre-publish panel to manage enabling/disabling tweet on publish (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul), [@linawiezkowiak](https://github.com/linawiezkowiak), [@oszkarnagy](https://github.com/oszkarnagy), [@cadic](https://github.com/cadic) via [#177](https://github.com/10up/autoshare-for-twitter/pull/177)).

### Changed
- Bump minimum required WordPress version to 5.3 (props [@iamdharmesh](https://github.com/iamdharmesh) via [#177](https://github.com/10up/autoshare-for-twitter/pull/177)).
- Updates in `CONTRIBUTING.md` file (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul) via [#172](https://github.com/10up/autoshare-for-twitter/pull/172)).
- Update plugin icons (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul) via [#174](https://github.com/10up/autoshare-for-twitter/pull/174)).

### Security
- Bump `terser` from 4.3.1 to 4.8.1 (props [@dependabot](https://github.com/apps/dependabot) via [#184](https://github.com/10up/autoshare-for-twitter/pull/184)).

## [1.1.2] - 2022-06-24
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
[2.1.1]: https://github.com/10up/autoshare-for-twitter/compare/2.1.0...2.1.1
[2.1.0]: https://github.com/10up/autoshare-for-twitter/compare/2.0.0...2.1.0
[2.0.0]: https://github.com/10up/autoshare-for-twitter/compare/1.3.0...2.0.0
[1.3.0]: https://github.com/10up/autoshare-for-twitter/compare/1.2.1...1.3.0
[1.2.1]: https://github.com/10up/autoshare-for-twitter/compare/1.2.0...1.2.1
[1.2.0]: https://github.com/10up/autoshare-for-twitter/compare/1.1.2...1.2.0
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
