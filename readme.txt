=== Autoshare for Twitter ===
Contributors:      10up, johnwatkins0, adamsilverstein, scottlee, dinhtungdu
Tags:              twitter, tweet, autoshare, auto-share, auto share, share, social media
Requires at least: 4.9
Tested up to:      5.8
Requires PHP:      7.2
Stable tag:        1.0.6
License:           GPL-2.0-or-later
License URI:       https://spdx.org/licenses/GPL-2.0-or-later.html

Automatically tweets the post title or custom message and a link to the post.

== Description ==

Automatically tweets the post title or custom message and a link to the post.  Additional technical details can be found in [our GitHub repository](https://github.com/10up/autoshare-for-twitter#overview).

**Disclaimer:** *TWITTER, TWEET, RETWEET and the Twitter logo are trademarks of Twitter, Inc. or its affiliates.*

== Manual Installation ==

1. Upload the entire `/autoshare-for-twitter` directory to the `/wp-content/plugins/` directory.
2. Activate the plugin
3. Register post type support for types that should be allowed to autoshare: `add_post_type_support( 'post', 'autoshare-for-twitter' );`

== Frequently Asked Questions ==

= Does this plugin work with Gutenberg? =

Yes, yes it does!  For more details on this, see [#44](https://github.com/10up/autoshare-for-twitter/pull/44).

== Changelog ==

= 1.0.6 =
* Note: this was a hotfix release to fix an issue with deploys to WordPress.org.

= 1.0.5 =
* **Added:** Tweeted status column to All Posts table list view (props [@thrijith](https://profiles.wordpress.org/thrijith/), [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu), [@linawiezkowiak](https://profiles.wordpress.org/linawiezkowiak/), [@oszkarnagy](https://profiles.wordpress.org/oszkarnagy/), [@jeffpaul](https://profiles.wordpress.org/jeffpaul/)).
* **Changed:** Bump WordPress version "tested up to" 5.8 (props [@thrijith](https://profiles.wordpress.org/thrijith/), [@barneyjeffries](https://profiles.wordpress.org/barneyjeffries/), [@jeffpaul](https://profiles.wordpress.org/jeffpaul/)).
* **Changed:** WP Snapshot for auotmated testing to WP 5.6 (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu), [@jeffpaul](https://profiles.wordpress.org/jeffpaul/)).
* **Changed:** Update dependency `abraham/twitteroauth` from ^0.7.4 to 1.2.0 to ensure PHP 7.4 support and WordPress VIP Go compatability (props [@thrijith](https://profiles.wordpress.org/thrijith/), [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu), [@rickalee](https://profiles.wordpress.org/rickalee)).
* **Changed:** Update PHPUnit test cases to include new functions (props [@thrijith](https://profiles.wordpress.org/thrijith/)).
* **Fixed:** Ensure that special characters are properly encoded (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu), [@rickalee](https://profiles.wordpress.org/rickalee)).
* **Security:** Bump `lodash` from 4.17.15 to 4.17.21 (props [@dependabot](https://github.com/apps/dependabot)).
* **Security:** Bump `elliptic` from 6.5.1 to 6.5.4 (props [@dependabot](https://github.com/apps/dependabot)).
* **Security:** Bump `yargs-parser` from 13.1.1 to 13.1.2 (props [@dependabot](https://github.com/apps/dependabot)).
* **Security:** Bump `ini` from 1.3.5 to 1.3.7 (props [@dependabot](https://github.com/apps/dependabot)).
* **Security:** Bump `y18n` from 4.0.0 to 4.0.1 (props [@dependabot](https://github.com/apps/dependabot)).
* **Security:** Bump `ssri` from 6.0.1 to 6.0.2 (props [@dependabot](https://github.com/apps/dependabot)).
* **Security:** Bump `rmccue/requests` from 1.7.0 to 1.8.0 (props [@dependabot](https://github.com/apps/dependabot)).
* **Security:** Bump `hosted-git-info` from 2.8.4 to 2.8.9 (props [@dependabot](https://github.com/apps/dependabot)).
* **Security:** Bump `browserslist` from 4.7.0 to 4.16.6 (props [@dependabot](https://github.com/apps/dependabot)).
* **Security:** Bump `path-parse` from 1.0.6 to 1.0.7 (props [@dependabot](https://github.com/apps/dependabot)).

= 1.0.4 =
* Note: this was a hotfix release to fix an issue with included libraries.

= 1.0.3 =
* **Added:** `autoshare_for_twitter_disable_on_transition_post_status` filter to disable tweeting based on post status change (props [@rickalee](https://profiles.wordpress.org/rickalee))
* **Changed:** Bumped WordPress version support to 5.4.2 (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu), [@jeffpaul](https://profiles.wordpress.org/jeffpaul/))
* **Changed:** Build, test, and release processes (props [@johnwatkins0](https://profiles.wordpress.org/johnwatkins0), [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu))
* **Fixed:*# Enable autoshare meta always set to 0 when saving draft (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu), [@rickalee](https://profiles.wordpress.org/rickalee))

= 1.0.2 =
* **Added:** WP Acceptance tests (props [@johnwatkins0](https://profiles.wordpress.org/johnwatkins0/))
* **Changed:** New and improved settings page UX (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/), [@jeffpaul](https://profiles.wordpress.org/jeffpaul/), [@johnwatkins0](https://profiles.wordpress.org/johnwatkins0/), [@linawiezkowiak](https://profiles.wordpress.org/linawiezkowiak/), [@oszkarnagy](https://profiles.wordpress.org/oszkarnagy/))
* **Fixed:** Bug that caused posts to be inadvertently tweeted when switching from draft to publish (props [@johnwatkins0](https://profiles.wordpress.org/johnwatkins0/), [@rickalee](https://profiles.wordpress.org/rickalee/))
* **Fixed:** Build script in release process (props [@johnwatkins0](https://profiles.wordpress.org/johnwatkins0/))

= 1.0.1 =
* **Added:** `autoshare_for_twitter_enabled_default` filter to allow autoshare to be enabled by default for a post type (props [@johnwatkins0](https://profiles.wordpress.org/johnwatkins0/), [@rickalee](https://profiles.wordpress.org/rickalee/), [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/))
* **Changed:** bypass character texturization when the post title is tweeted (props [@johnwatkins0](https://profiles.wordpress.org/johnwatkins0/), [@rickalee](https://profiles.wordpress.org/rickalee/))
* **Removed:** second instance of the `autoshare_for_twitter_tweet` filter (props [@johnwatkins0](https://profiles.wordpress.org/johnwatkins0/))

= 1.0.0 =
* **Added:** Initial public release! 🎉
* **Added:** Plugin renamed to "Autoshare for Twitter"
* **Added:** Support Post and Page post types by default, provide Custom Post Type (props [@johnwatkins0](https://profiles.wordpress.org/johnwatkins0/))
* **Added:** Gutenberg support (props [@johnwatkins0](https://profiles.wordpress.org/johnwatkins0/))
* **Added:** REST API endpoint to replace AJAX callback (props [@johnwatkins0](https://profiles.wordpress.org/johnwatkins0/))
* **Added:** Build process, PHPCS linting, unit tests, and Travis CI (props [@johnwatkins0](https://profiles.wordpress.org/johnwatkins0/), [@adamsilverstein](https://profiles.wordpress.org/adamsilverstein/))
* **Added:** Plugin banner and icon images (props Stephanie Campbell)
* **Changed:** Refactor v0.1.0 significantly (props [@adamsilverstein](https://profiles.wordpress.org/adamsilverstein/), [@johnwatkins0](https://profiles.wordpress.org/johnwatkins0/), [@jeffpaul](https://profiles.wordpress.org/jeffpaul/))
* **Security:** XSS prevention - switch from .innerHTML to text (props [@adamsilverstein](https://profiles.wordpress.org/adamsilverstein/))

= 0.1.0 =
* Initial private release (props [@scottlee](https://profiles.wordpress.org/scottlee/))

== Upgrade Notice ==

= 0.1.0 =
First Release
