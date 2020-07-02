=== Autoshare for Twitter ===
Contributors:      10up, johnwatkins0, adamsilverstein, scottlee, dinhtungdu
Tags:              twitter, tweet, autoshare, auto-share, auto share, share, social media
Requires at least: 4.7
Tested up to:      5.4.2
Requires PHP:      7.0
Stable tag:        1.0.3
License:           GPL-2.0-or-later
License URI:       https://spdx.org/licenses/GPL-2.0-or-later.html

Automatically tweets the post title or custom message and a link to the post.

== Description ==

Automatically tweets the post title or custom message and a link to the post.

**Disclaimer:** *TWITTER, TWEET, RETWEET and the Twitter logo are trademarks of Twitter, Inc. or its affiliates.*

**Note:** Post types are automatically set to autoshare. Future versions of this plugin could allow this to be set manually.

== Manual Installation ==

1. Upload the entire `/autoshare-for-twitter` directory to the `/wp-content/plugins/` directory.
2. Activate the plugin
3. Register post type support for types that should be allowed to autoshare: `add_post_type_support( 'post', 'autoshare-for-twitter' );`

== Frequently Asked Questions ==

= Does this plugin work with Gutenberg? =

Yes, yes it does!  For more details on this, see [#44](https://github.com/10up/autoshare-for-twitter/pull/44).

== Changelog ==

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
