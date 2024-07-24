=== Autopost for X (formerly Autoshare for Twitter) ===
Contributors:      10up, johnwatkins0, adamsilverstein, scottlee, dinhtungdu, jeffpaul, dharm1025
Tags:              twitter, tweet, share, social media, posse
Tested up to:      6.6
Stable tag:        2.2.1
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

If you're seeing the error message "429: Too Many Requests" it indicates that you have exceeded the usage limits of Twitter's Free API access. With the Free API, you are allowed **1,500 Posts per month** and **50 requests within a 24-hour period**. Since you have surpassed the daily limit, we kindly advise waiting for 24 hours before attempting to post again.

To avoid encountering this error in the future and to have higher usage limits, we recommend considering a subscription to either the Basic or Pro access level. These access levels provide increased quotas and additional benefits to accommodate your needs. For more information on X/Twitter API access levels, you can visit this link: https://developer.twitter.com/en/products/twitter-api.

== Screenshots ==

1. Create post screen with Autopost for X/Twitter options.
2. Published post screen with Autopost for X/Twitter options.
3. Autopost for X/Twitter sidebar panel.
4. Autopost for X/Twitter Settings, found under `Settings` > `Autopost for X/Twitter`.

== Changelog ==

= 2.2.1 - 2024-07-08 =
* **Changed:** Bump WordPress "tested up to" version 6.5 (props [@iamdharmesh](https://github.com/iamdharmesh), [@dkotter](https://github.com/dkotter), [@sudip-md](https://github.com/sudip-md), [@jeffpaul](https://github.com/jeffpaul) via [#319](https://github.com/10up/autoshare-for-twitter/pull/319)).
* **Fixed:** Ampersands no longer converted to HTML entities when adding query parameters to the post URL with the `autoshare_for_twitter_post_url` filter (props [@justinmaurerdotdev](https://github.com/justinmaurerdotdev), [@iamdharmesh](https://github.com/iamdharmesh) via [#324](https://github.com/10up/autoshare-for-twitter/pull/324)).
* **Security:** Bump `express` from 4.18.2 to 4.19.2 (props [@dependabot](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh) via [#321](https://github.com/10up/autoshare-for-twitter/pull/321)).
* **Security:** Bump `follow-redirects` from 1.15.5 to 1.15.6 (props [@dependabot](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh) via [#321](https://github.com/10up/autoshare-for-twitter/pull/321)).
* **Security:** Bump `ip` from 1.1.8 to 1.1.9 (props [@dependabot](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh) via [#321](https://github.com/10up/autoshare-for-twitter/pull/321)).
* **Security:** Bump `webpack-dev-middleware` from 5.3.3 to 5.3.4 (props [@dependabot](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh) via [#321](https://github.com/10up/autoshare-for-twitter/pull/321)).

= 2.0.0 - 2023-01-04 =
**Autoshare for Twitter rebranded / renamed to Autopost for X.**
* **Changed:** Updated repo automator workflow (props [@iamdharmesh](https://github.com/iamdharmesh), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#273](https://github.com/10up/autoshare-for-twitter/pull/273)).
* **Changed:** Bump `Cypress` version from 11.2.0 to 13.0.0 (props [@iamdharmesh](https://github.com/iamdharmesh), [@Sidsector9](https://github.com/Sidsector9) via [#281](https://github.com/10up/autoshare-for-twitter/pull/281)).
* **Changed:** Bump `@10up/cypress-wp-utils` version from 0.1.0 to 0.2.0 (props [@iamdharmesh](https://github.com/iamdharmesh), [@Sidsector9](https://github.com/Sidsector9) via [#281](https://github.com/10up/autoshare-for-twitter/pull/281)).
* **Changed:** Bump `@wordpress/env` version from 5.7.0 to 8.6.0 (props [@iamdharmesh](https://github.com/iamdharmesh), [@Sidsector9](https://github.com/Sidsector9) via [#281](https://github.com/10up/autoshare-for-twitter/pull/281)).
* **Changed:** Replaced the custom build process with WP-Scripts (props [@iamdharmesh](https://github.com/iamdharmesh), [@peterwilsoncc](https://github.com/peterwilsoncc), [@ravinderk](https://github.com/ravinderk) via [#282](https://github.com/10up/autoshare-for-twitter/pull/282)).
* **Changed:** Disabled auto-sync for pull requests with the target branch (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul) via [#291](https://github.com/10up/autoshare-for-twitter/pull/291)).
* **Changed:** Bump WordPress "tested up to" version 6.4 (props [@qasumitbagthariya](https://github.com/qasumitbagthariya), [@jeffpaul](https://github.com/jeffpaul) via [#292](https://github.com/10up/autoshare-for-twitter/pull/292)).
* **Changed:** Renamed plugin from "Autoshare for Twitter" to "Autopost for X (formerly Autoshare for Twitter)" (props [@jeffpaul](https://github.com/jeffpaul), [@iamdharmesh](https://github.com/iamdharmesh), [Morgan Hartnett](https://www.morganhartnett.com/) via [#293](https://github.com/10up/autoshare-for-twitter/pull/293)).
* **Fixed:** Resolved the issue with Twitter accounts' visibility in the classic editor (props [@iamdharmesh](https://github.com/iamdharmesh), [@ravinderk](https://github.com/ravinderk) via [#277](https://github.com/10up/autoshare-for-twitter/pull/277)).
* **Fixed:** Fixed bugs related to calculating tweet length (props [@justinmaurerdotdev](https://github.com/justinmaurerdotdev), [@iamdharmesh](https://github.com/iamdharmesh) via [#288](https://github.com/10up/autoshare-for-twitter/pull/288)).
* **Fixed:** Addressed auto-posting tweets for automatically published posts (props [@iamdharmesh](https://github.com/iamdharmesh), [@sunnmagic](https://github.com/sunnmagic), [@peterwilsoncc](https://github.com/peterwilsoncc), [@jeffpaul](https://github.com/jeffpaul) via [#294](https://github.com/10up/autoshare-for-twitter/pull/294)).
* **Fixed:** Fixed deprecation warning regarding implicit float-to-int conversion in PHP 8.2 (props [@justinmaurerdotdev](https://github.com/justinmaurerdotdev), [@iamdharmesh](https://github.com/iamdharmesh) via [#301](https://github.com/10up/autoshare-for-twitter/pull/301)).
* **Security:** Bump `@babel/traverse` from 7.22.17 to 7.23.2 (props [@dependabot[bot]](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh) via [#289](https://github.com/10up/autoshare-for-twitter/pull/289)).
* **Security:** Bump `@wordpress/scripts`` from 26.12.0 to 26.19.0 (props [@dependabot[bot]](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh) via [#298](https://github.com/10up/autoshare-for-twitter/pull/298)).

Further changelog entries can be found in the [CHANGELOG.md](https://github.com/10up/autoshare-for-twitter/blob/trunk/CHANGELOG.md) file.

== Upgrade Notice ==
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
