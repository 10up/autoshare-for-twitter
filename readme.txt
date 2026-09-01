=== Autopost for X (formerly Autoshare for Twitter) ===
Contributors:      10up, johnwatkins0, adamsilverstein, scottlee, dinhtungdu, jeffpaul, dharm1025
Tags:              twitter, tweet, share, social media, posse
Requires at least: 6.9
Tested up to:      7.1
Stable tag:        2.3.4
License:           GPL-2.0-or-later
License URI:       https://spdx.org/licenses/GPL-2.0-or-later.html

Automatically shares the post title or custom message and a link to the post to X/Twitter.

== Description ==

Autopost for X (formerly Autoshare for Twitter) automatically shares your posts to X/Twitter as soon as they’re published.  Once you hit the Publish button, the plugin sends your post’s title, featured image, and link to X/Twitter, along with a custom message.

Unlike a myriad of other social media, multitool solutions, Autopost for X is built solely for X/Twitter.  It focuses on doing one thing and does it well, with the code and interface craftsmanship we apply to every project.

With Autopost for X, developers can further customize nearly everything about the posts, including the image, author, and link, using an extensive set of hooks built into the code. Among its other features, the WordPress plugin:

* Works in both the classic and new block editors.
* Becomes part of the pre-publish checklist step that’s part of the new block editor.
* Posts a high-quality featured image with your post to X/Twitter.
* Counts characters to keep you under the X/Twitter limit.
* Adds a link to the post to X/Twitter in the block editor sidebar.

**Disclaimer:** *TWITTER, TWEET, RETWEET and the Twitter logo are trademarks of Twitter, Inc. or its affiliates.*

== Paid API Access ==

In early 2026, X effectively [removed free API access](https://devcommunity.x.com/t/announcing-the-launch-of-x-api-pay-per-use-pricing/256476) for all users except public utilities.

Most users will now require paid API access via the [X Developer Console](https://console.x.com/).

== Installation ==
1. Install the plugin via the plugin installer, either by searching for it or uploading a .ZIP file.
2. Activate the plugin.
3. Save X/Twitter connection settings, found under `Settings` > `Autopost for X`.

== Plugin Compatibility ==

= Distributor =

When using with 10up's [Distributor plugin](https://github.com/10up/distributor), posts that are distributed will not be autoposted if they are already posted to X/Twitter from the origin site. Autopost for X tracks posts that have been posted to X/Twitter in post meta to avoid "double posting". To avoid this behavior, use the `dt_blacklisted_meta` filter to exclude the 'autoshare_for_twitter_status' meta value from being distributed :

`
add_filter( 'dt_blacklisted_meta', function( $blacklisted_metas ) {
	$blacklisted_metas[] = 'autoshare_for_twitter_status';
	return $blacklisted_metas;
} )
`

== Developers ==

**Note:** Posts and pages are supported by default. Developers can use the `autoshare_for_twitter_default_post_types` filter to change the default supported post types

Custom post types can now be opted into autopost features like so:

`
function opt_my_cpt_into_autoshare() {
	add_post_type_support( 'my-cpt', 'autoshare-for-twitter' );
}
add_action( 'init', 'opt_my_cpt_into_autoshare' );
`

In addition, adding support while registering custom post types also works. Post types are automatically set to autopost. Future versions of this plugin could allow this to be set manually.

While the autopost feature can be opted into for post types using the above filter, by default the editor still has to manually enable autopost during the post prepublish flow. The `autoshare_for_twitter_enabled_default` filter allows autopost to be enabled by default for all posts of a given post type. Editors can still manually uncheck the option during the publishing flow.

Example:

`
function enable_autoshare_by_default_for_core_post_type( $enabled, $post_type ) {
	if ( 'post' === $post_type ) {
		return true;
	}

	return $enabled;
}
add_filter( 'autoshare_for_twitter_enabled_default', 'enable_autoshare_by_default_for_core_post_type', 10, 2 );
`

Additional technical details can be found in [our GitHub repository](https://github.com/10up/autoshare-for-twitter#overview).

== Frequently Asked Questions ==

= Does this plugin work with Gutenberg? =

Yes, yes it does!  For more details on this, see [#44](https://github.com/10up/autoshare-for-twitter/pull/44).

= Does the plugin work with X/Twitter API V2? =

Yes, the plugin is compatible with X/Twitter API v2.

= I'm encountering the error message "401: Unauthorized." What could be the possible reason for this error? =

There are a few potential reasons for this error:
1. **Incorrect X/Twitter API credentials**: Please ensure that you have entered the correct X/Twitter API credentials.
2. **Deprecated access levels**: If you are still using the old X/Twitter access levels (Standard (v1.1), Essential (v2), Elevated (v2), etc...), you must migrate to the new access levels (Free, Basic, Pro, etc.). Please make sure to migrate to the new access levels to ensure uninterrupted functionality. Here's how you can do it:

	1. Go to the following URL: https://developer.twitter.com/en/portal/products
	2. Look for the "Downgrade" button.
	3. Click on it to migrate to the free access level.

= I'm encountering the error message "429: Too Many Requests." What could be the possible reason for this error? =

If you're seeing the error message "429: Too Many Requests" it indicates that you have exceeded the usage limits of X/Twitter's Free API access. With the Free API, you are allowed **500 requests per month** and **17 requests within a 24-hour period**. Please note that these limits were accurate at the time of writing and may have been updated. For the most up-to-date information, please refer to the X API [documentation](https://developer.x.com/en/docs/x-api/rate-limits). Since you have surpassed the daily limit, we kindly advise waiting for 24 hours before attempting to post again.

To avoid encountering this error in the future and to have higher usage limits, we recommend considering a subscription to either the Basic or Pro access level. These access levels provide increased quotas and additional benefits to accommodate your needs. For more information on X/Twitter API access levels, you can visit this link: https://developer.x.com/en/products/x-api.

= Where do I report security bugs found in this plugin? =

Please report security bugs found in the source code of the Autoshare for Twitter plugin through the [Patchstack Vulnerability Disclosure  Program](https://patchstack.com/database/vdp/9e5fba6a-26c2-4dd7-a2ae-b8628c65835e).  The Patchstack team will assist you with verification, CVE assignment, and notify the developers of this plugin.

== Screenshots ==

1. Create post screen with Autopost for X/Twitter options.
2. Published post screen with Autopost for X/Twitter options.
3. Autopost for X/Twitter sidebar panel.
4. Autopost for X/Twitter Settings, found under `Settings` > `Autopost for X/Twitter`.

== Changelog ==

= 2.3.4 - 2026-09-01 =
**Note that this version bumps the WordPress minimum supported version from 6.8 to 6.9.**

* **Added:** Notice to readme files regarding X's removal of free API access (props [@peterwilsoncc](https://github.com/peterwilsoncc), [@dkotter](https://github.com/dkotter), [@jeffpaul](https://github.com/jeffpaul) via [#426](https://github.com/10up/autoshare-for-twitter/pull/426)).
* **Changed:** Bump WordPress minimum supported version to 6.9 (props [@phpbits](https://github.com/phpbits), [@jeffpaul](https://github.com/jeffpaul), [@dkotter](https://github.com/dkotter), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#430](https://github.com/10up/autoshare-for-twitter/pull/430)).
* **Changed:** Bump "tested up to header" to indicate WordPress 7.1 support (props [@phpbits](https://github.com/phpbits), [@jeffpaul](https://github.com/jeffpaul), [@dkotter](https://github.com/dkotter), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#430](https://github.com/10up/autoshare-for-twitter/pull/430)).
* **Changed:** Bump "tested up to header" to indicate WordPress 7.0 support (props [@peterwilsoncc](https://github.com/peterwilsoncc), [@dkotter](https://github.com/dkotter), [@jeffpaul](https://github.com/jeffpaul) via [#426](https://github.com/10up/autoshare-for-twitter/pull/426)).

= 2.3.3 - 2026-02-02 =
* **Fixed:** Bug preventing the autosharing of posts from working on scheduled posts (props [@justinmaurerdotdev](https://github.com/justinmaurerdotdev), [@peterwilsoncc](https://github.com/peterwilsoncc), [@dkotter](https://github.com/dkotter) via [#399](https://github.com/10up/autoshare-for-twitter/pull/399)).

= 2.3.2 - 2026-01-29 =
**Note that this version bumps the WordPress minimum supported version from 6.6 to 6.8.**

* **Security:** Fix access control issue for retweeting (props [@nblirwn](https://github.com/nblirwn), [@dkotter](https://github.com/dkotter), [@peterwilsoncc](https://github.com/peterwilsoncc), [@jeffpaul](https://github.com/jeffpaul) via [GHSA-9j39-fp7c-5453](https://github.com/10up/autoshare-for-twitter/security/advisories/GHSA-9j39-fp7c-5453)).
* **Added:** Support for the WordPress.org plugin preview (props [@dkotter](https://github.com/dkotter), [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul) via [#297](https://github.com/10up/autoshare-for-twitter/pull/297)).
* **Changed:** Bumped WordPress "tested up to" version 6.9 (props [@peterwilsoncc](https://github.com/peterwilsoncc), [@dkotter](https://github.com/dkotter) via [#390](https://github.com/10up/autoshare-for-twitter/pull/390)).
* **Changed:** Bumped WordPress minimum supported version from 6.6 to 6.8 (props [@jeffpaul](https://github.com/jeffpaul) via [#397](https://github.com/10up/autoshare-for-twitter/pull/397)).
* **Changed:** Updated npm dependencies via `npm audit fix` (props [@peterwilsoncc](https://github.com/peterwilsoncc), [@dkotter](https://github.com/dkotter) via [#392](https://github.com/10up/autoshare-for-twitter/pull/392)).
* **Fixed:** Clear tweet text field only after post publish completes in block editor (props [@chandrapatel](https://github.com/chandrapatel), [@iamdharmesh](https://github.com/iamdharmesh) via [#378](https://github.com/10up/autoshare-for-twitter/pull/378)).

= 2.3.1 - 2025-07-14 =
**Note that this version bumps the WordPress minimum supported version from 6.5 to 6.6.**

* **Changed:** Bump WordPress "tested up to" version 6.8 (props [@Sourabh208](https://github.com/Sourabh208), [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul) via [#367](https://github.com/10up/autoshare-for-twitter/pull/367), [#370](https://github.com/10up/autoshare-for-twitter/pull/370)).
* **Changed:** Bump WordPress minimum supported version to 6.6 (props [@peterwilsoncc](https://github.com/peterwilsoncc), [@Sourabh208](https://github.com/Sourabh208), [@jeffpaul](https://github.com/jeffpaul) via [#359](https://github.com/10up/autoshare-for-twitter/pull/359), [#367](https://github.com/10up/autoshare-for-twitter/pull/367), [#370](https://github.com/10up/autoshare-for-twitter/pull/370)).
* **Changed:** Make the API rate limit wording more clear (props [@jeckman](https://github.com/jeckman), [@iamdharmesh](https://github.com/iamdharmesh), [@dkotter](https://github.com/dkotter) via [#365](https://github.com/10up/autoshare-for-twitter/pull/365)).
* **Fixed:** Ensure that no rate limits are shown if the reset time has passed (props [@iamdharmesh](https://github.com/iamdharmesh), [@dkotter](https://github.com/dkotter) via [#356](https://github.com/10up/autoshare-for-twitter/pull/356)).
* **Security:** Bump `@wordpress/scripts` from 27.9.0 to 30.10.0 (props [@dependabot](https://github.com/apps/dependabot), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#359](https://github.com/10up/autoshare-for-twitter/pull/359)).
* **Security:** Bump `cookie` from 0.6.0 to 0.7.1, `express` from 4.21.0 to 4.21.2, `serialize-javascript` from 6.0.0 to 6.0.2 and `mocha` from 10.2.0 to 11.1.0 (props [@dependabot](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh) via [#361](https://github.com/10up/autoshare-for-twitter/pull/361)).
* **Security:** Bump `http-proxy-middleware` from 2.0.6 to 2.0.9 (props [@dependabot](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh) via [#369](https://github.com/10up/autoshare-for-twitter/pull/369)).
* **Security:** Bump `tar-fs` from 3.0.8 to 3.0.9 (props [@dependabot](https://github.com/apps/dependabot), [@dkotter](https://github.com/dkotter) via [#373](https://github.com/10up/autoshare-for-twitter/pull/373)).

Further changelog entries can be found in the [CHANGELOG.md](https://github.com/10up/autoshare-for-twitter/blob/trunk/CHANGELOG.md) file.

== Upgrade Notice ==

= 2.3.4 =
This release bumps the WordPress minimum from 6.5 to 6.6.

= 2.3.2 =
Security release: this patches an access control issue with retweets. Please upgrade promptly. This release bumps the WordPress minimum from 6.6 to 6.8.

= 2.3.1 =
This release bumps the WordPress minimum from 6.5 to 6.6.

= 2.3.0 =
This release bumps the WordPress minimum from 5.7 to 6.5.

= 2.2.0 =
Autoshare for Twitter rebranded / renamed to Autopost for X.

= 2.0.0 =
Autoshare for Twitter 2.0.0 utilizes [Twitter's v2 API](https://developer.twitter.com/en/products/twitter-api).  If you have not already done so, please [migrate your app](https://developer.twitter.com/en/portal/projects-and-apps) to Twitter's v2 API to continue using Autoshare for Twitter.  [Learn more about migrating here](https://developer.twitter.com/en/docs/twitter-api/migrate/ready-to-migrate).

= 1.2.1 =
This release bumps the WordPress minimum from 5.3 to 5.7 and PHP minimum from 7.2 to 7.4.

= 1.2.0 =
This release bumps the WordPress minimum from 4.9 to 5.3.

= 0.1.0 =
First Release
