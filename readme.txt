=== Autoshare for Twitter ===
Contributors:      10up, johnwatkins0, adamsilverstein, scottlee, dinhtungdu, jeffpaul, dharm1025
Tags:              twitter, tweet, autoshare, auto-share, auto share, share, sharing, social media, posse
Requires at least: 5.7
Tested up to:      6.4
Requires PHP:      7.4
Stable tag:        2.1.1
License:           GPL-2.0-or-later
License URI:       https://spdx.org/licenses/GPL-2.0-or-later.html

Automatically tweets the post title or custom message and a link to the post.

== Description ==

Autoshare for Twitter automatically tweets your posts as soon as they’re published.  Once you hit the Publish button, the plugin sends your post’s title, featured image, and link to Twitter, along with a custom message.

Unlike a myriad of other social media, multitool solutions, Autoshare for Twitter is built solely for Twitter.  It focuses on doing one thing and does it well, with the code and interface craftsmanship we apply to every project.

With Autoshare for Twitter, developers can further customize nearly everything about the tweets, including the image, author, and link, using an extensive set of hooks built into the code. Among its other features, the WordPress plugin:

* Works in both the classic and new block editors.
* Becomes part of the pre-publish checklist step that’s part of the new block editor.
* Posts a high-quality featured image with your tweet.
* Counts characters to keep you under the tweet limit.
* Adds a link to the tweet in the block editor sidebar.

**Disclaimer:** *TWITTER, TWEET, RETWEET and the Twitter logo are trademarks of Twitter, Inc. or its affiliates.*

== Installation ==
1. Install the plugin via the plugin installer, either by searching for it or uploading a .ZIP file.
2. Activate the plugin.
3. Save Twitter connection settings, found under `Settings` > `Autoshare for Twitter`.

== Plugin Compatibility ==

= Distributor =

When using with 10up's [Distributor plugin](https://github.com/10up/distributor), posts that are distributed will not be autoshared if they are already tweeted from the origin site. Autoshare for Twitter tracks posts that have been tweeted in post meta to avoid "double tweeting". To avoid this behavior, use the `dt_blacklisted_meta` filter to exclude the 'autoshare_for_twitter_status' meta value from being distributed :

`
add_filter( 'dt_blacklisted_meta', function( $blacklisted_metas ) {
	$blacklisted_metas[] = 'autoshare_for_twitter_status';
	return $blacklisted_metas;
} )
`

== Developers ==

**Note:** Posts and pages are supported by default. Developers can use the `autoshare_for_twitter_default_post_types` filter to change the default supported post types

Custom post types can now be opted into autoshare features like so:

`
function opt_my_cpt_into_autoshare() {
	add_post_type_support( 'my-cpt', 'autoshare-for-twitter' );
}
add_action( 'init', 'opt_my_cpt_into_autoshare' );
`

In addition, adding support while registering custom post types also works. Post types are automatically set to autoshare. Future versions of this plugin could allow this to be set manually.

While the autoshare feature can be opted into for post types using the above filter, by default the editor still has to manually enable autoshare during the post prepublish flow. The `autoshare_for_twitter_enabled_default` filter allows autoshare to be enabled by default for all posts of a given post type. Editors can still manually uncheck the option during the publishing flow.

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

= Does the plugin work with Twitter API V2? =

Yes, the plugin is compatible with Twitter API v2.

= I'm encountering the error message "401: Unauthorized." What could be the possible reason for this error? =

There are a few potential reasons for this error:
1. **Incorrect Twitter API credentials**: Please ensure that you have entered the correct Twitter API credentials.
2. **Deprecated access levels**: If you are still using the old Twitter access levels (Standard (v1.1), Essential (v2), Elevated (v2), etc...), you must migrate to the new access levels (Free, Basic, Pro, etc.). Please make sure to migrate to the new access levels to ensure uninterrupted functionality. Here's how you can do it:

	1. Go to the following URL: https://developer.twitter.com/en/portal/products
	2. Look for the "Downgrade" button.
	3. Click on it to migrate to the free access level.

= I'm encountering the error message "429: Too Many Requests." What could be the possible reason for this error? =

If you're seeing the error message "429: Too Many Requests" it indicates that you have exceeded the usage limits of Twitter's Free API access. With the Free API, you are allowed **1,500 Tweets per month** and **50 requests within a 24-hour period**. Since you have surpassed the daily limit, we kindly advise waiting for 24 hours before attempting to tweet again.

To avoid encountering this error in the future and to have higher usage limits, we recommend considering a subscription to either the Basic or Pro access level. These access levels provide increased quotas and additional benefits to accommodate your needs. For more information on Twitter API access levels, you can visit this link: https://developer.twitter.com/en/products/twitter-api.

== Screenshots ==

1. Create post screen with Autoshare for Twitter options.
2. Published post screen with Autoshare for Twitter options.
3. Autoshare For Twitter sidebar panel.
4. Autoshare for Twitter Settings, found under `Settings` > `Autoshare for Twitter`.

== Changelog ==
= 2.1.1 - 2023-08-22 =
* **Changed:** Improved setup instructions and error handing (props [@johnwatkins0](https://github.com/johnwatkins0), [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul) via [#255](https://github.com/10up/autoshare-for-twitter/pull/255)).
* **Changed:** Bump WordPress "tested up to" version 6.3 (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul) via [#265](https://github.com/10up/autoshare-for-twitter/pull/265)).
* **Fixed:** The custom tweet message does not work with scheduled posts (props [@GeoffLambert77](https://github.com/GeoffLambert77), [@iamdharmesh](https://github.com/iamdharmesh), [@Sidsector9](https://github.com/Sidsector9) via [#267](https://github.com/10up/autoshare-for-twitter/pull/267)).
* **Fixed:** Connected Twitter accounts visibility in the classic editor (props [@iamdharmesh](https://github.com/iamdharmesh), [@Sidsector9](https://github.com/Sidsector9) via [#269](https://github.com/10up/autoshare-for-twitter/pull/269)).
* **Fixed:** Better error handling for environments that don't match our minimum PHP version (props [@dkotter](https://github.com/dkotter), [@rahulsprajapati](https://github.com/rahulsprajapati), [@iamdharmesh](https://github.com/iamdharmesh) via [#258](https://github.com/10up/autoshare-for-twitter/pull/258)).
* **Fixed:** Ensure our E2E tests work properly on WordPress 6.3 (props [@iamdharmesh](https://github.com/iamdharmesh), [@dkotter](https://github.com/dkotter) via [#264](https://github.com/10up/autoshare-for-twitter/pull/264)).
* **Security:** Bump `tough-cookie` from 2.5.0 to 4.1.3 and `@cypress/request` from 2.88.10 to 2.88.12 (props [@dependabot[bot]](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh) via [#266](https://github.com/10up/autoshare-for-twitter/pull/266)).
* **Security:** Bump `word-wrap` from 1.2.3 to 1.2.4 (props [@dependabot[bot]](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh) via [#257](https://github.com/10up/autoshare-for-twitter/pull/257)).

= 2.1.0 - 2023-07-06 =
* **Added:** Support for tweeting via multiple Twitter accounts (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul), [@Sidsector9](https://github.com/Sidsector9) via [#238](https://github.com/10up/autoshare-for-twitter/pull/238)).
* **Added:** Obfuscation of saved Twitter keys in the UI (props [@lgrzegorski](https://github.com/lgrzegorski), [@bmarshall511](https://github.com/bmarshall511), [@iamdharmesh](https://github.com/iamdharmesh) via [#245](https://github.com/10up/autoshare-for-twitter/pull/245)).
* **Added:** GitHub Action summary for end-to-end tests (props [@iamdharmesh](https://github.com/iamdharmesh), [@ravinderk](https://github.com/ravinderk) via [#247](https://github.com/10up/autoshare-for-twitter/pull/247)).
* **Changed:** Readme updates for FAQs and formatting (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul) via [#235](https://github.com/10up/autoshare-for-twitter/pull/235), [#241](https://github.com/10up/autoshare-for-twitter/pull/241), [#242](https://github.com/10up/autoshare-for-twitter/pull/242)).
* **Changed:** Enhanced end-to-end tests by implementing mocking of Twitter API and bypassing actual Twitter API calls (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul), [@Sidsector9](https://github.com/Sidsector9) via [#238](https://github.com/10up/autoshare-for-twitter/pull/238)).
* **Changed:** Updated the Dependency Review GitHub Action (props [@jeffpaul](https://github.com/jeffpaul), [@iamdharmesh](https://github.com/iamdharmesh) via [#239](https://github.com/10up/autoshare-for-twitter/pull/239)).
* **Changed:** Fixed inconsistent tweet status for scheduled posts (props [@mae829](https://github.com/mae829), [@iamdharmesh](https://github.com/iamdharmesh), [@ravinderk](https://github.com/ravinderk) via [#246](https://github.com/10up/autoshare-for-twitter/pull/246)).
* **Changed:** Improved error handing (props [@iamdharmesh](https://github.com/iamdharmesh), [@Sidsector9](https://github.com/Sidsector9) via [#249](https://github.com/10up/autoshare-for-twitter/pull/249)).

= 2.0.0 - 2023-05-16 =
**Autoshare for Twitter 2.0.0 utilizes [Twitter's v2 API](https://developer.twitter.com/en/products/twitter-api).  If you have not already done so, please [migrate your app](https://developer.twitter.com/en/portal/projects-and-apps) to Twitter's v2 API to continue using Autoshare for Twitter.  [Learn more about migrating here](https://developer.twitter.com/en/docs/twitter-api/migrate/ready-to-migrate).**

* **Added:** Migrated to Twitter API v2 (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul), [@ravinderk](https://github.com/ravinderk), [@Sidsector9](https://github.com/Sidsector9) via [#229](https://github.com/10up/autoshare-for-twitter/pull/229)).
* **Changed:** Bump WordPress "tested up to" version 6.2 (props [@Sidsector9](https://github.com/Sidsector9), [@iamdharmesh](https://github.com/iamdharmesh) via [#228](https://github.com/10up/autoshare-for-twitter/pull/228)).
* **Changed:** Update plugin settings and guidelines to set up a Twitter app (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul), [@ravinderk](https://github.com/ravinderk) [@Sidsector9](https://github.com/Sidsector9) via [#229](https://github.com/10up/autoshare-for-twitter/pull/229)).
* **Changed:** Updated documentation (props [@jeffpaul](https://github.com/jeffpaul), [@iamdharmesh](https://github.com/iamdharmesh), via [#231](https://github.com/10up/autoshare-for-twitter/pull/231)).
* **Security:** Bump `simple-git` from 3.15.1 to 3.16.0 (props [@dependabot](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh) via [#221](https://github.com/10up/autoshare-for-twitter/pull/221)).
* **Security:** Bump `http-cache-semantics` from 4.1.0 to 4.1.1 (props [@dependabot](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh) via [#222](https://github.com/10up/autoshare-for-twitter/pull/222)).
* **Security:** Bump `@sideway/formula` from 3.0.0 to 3.0.1 (props [@dependabot](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh) via [#223](https://github.com/10up/autoshare-for-twitter/pull/223)).
* **Security:** Bump `webpack` from 5.74.0 to 5.76.0 (props [@dependabot](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh) via [#224](https://github.com/10up/autoshare-for-twitter/pull/224)).

= 1.3.0 - 2023-01-20 =
* **Added:** "Tweet now" functionality (props [@Sidsector9](https://github.com/Sidsector9), [@iamdharmesh](https://github.com/iamdharmesh), [@cadic](https://github.com/cadic), [@jeffpaul](https://github.com/jeffpaul), [@linawiezkowiak](https://github.com/linawiezkowiak), [@oszkarnagy](https://github.com/oszkarnagy) via [#188](https://github.com/10up/autoshare-for-twitter/pull/188)).
* **Added:** Toggle for adding/removing featured image from the tweet (props [@Sidsector9](https://github.com/Sidsector9), [@iamdharmesh](https://github.com/iamdharmesh), [@cadic](https://github.com/cadic), [@jeffpaul](https://github.com/jeffpaul), [@linawiezkowiak](https://github.com/linawiezkowiak), [@oszkarnagy](https://github.com/oszkarnagy) via [#188](https://github.com/10up/autoshare-for-twitter/pull/188)).
* **Added:** Show Twitter status logs for the draft post if the post has been switched back to Draft from Published, and has already been Tweeted (props [@iamdharmesh](https://github.com/iamdharmesh), [@faisal-alvi](https://github.com/faisal-alvi), [@jeffpaul](https://github.com/jeffpaul), [@linawiezkowiak](https://github.com/linawiezkowiak), [@oszkarnagy](https://github.com/oszkarnagy) via [#215](https://github.com/10up/autoshare-for-twitter/pull/215)).
* **Added:** Plugin screenshots to readme files (props [@iamdharmesh](https://github.com/iamdharmesh) via [#218](https://github.com/10up/autoshare-for-twitter/pull/218)).
* **Changed:** UI Improvements in Tweet status (props [@Sidsector9](https://github.com/Sidsector9), [@iamdharmesh](https://github.com/iamdharmesh), [@cadic](https://github.com/cadic), [@jeffpaul](https://github.com/jeffpaul), [@linawiezkowiak](https://github.com/linawiezkowiak), [@oszkarnagy](https://github.com/oszkarnagy) via [#188](https://github.com/10up/autoshare-for-twitter/pull/188)).
* **Changed:** UI Improvements in tweet message character count (props [@iamdharmesh](https://github.com/iamdharmesh), [@Sidsector9](https://github.com/Sidsector9), [@ravinderk](https://github.com/ravinderk), [@jeffpaul](https://github.com/jeffpaul), [@linawiezkowiak](https://github.com/linawiezkowiak), [@oszkarnagy](https://github.com/oszkarnagy) via [#214](https://github.com/10up/autoshare-for-twitter/pull/214)).
* **Changed:** Run GitHub Action workflows only when it required (props [@iamdharmesh](https://github.com/iamdharmesh), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#204](https://github.com/10up/autoshare-for-twitter/pull/204)).
* **Changed:** Migrated Cypress from 9.0.0 to 11.2.0 (props [@iamdharmesh](https://github.com/iamdharmesh), [@Sidsector9](https://github.com/Sidsector9) via [#205](https://github.com/10up/autoshare-for-twitter/pull/205)).
* **Changed:** Run E2E tests on the zip generated by "Build release zip" action (props [@iamdharmesh](https://github.com/iamdharmesh), [@dkotter](https://github.com/dkotter), [@Sidsector9](https://github.com/Sidsector9) via [#206](https://github.com/10up/autoshare-for-twitter/pull/206)).
* **Fixed:** E2E tests fail in the CI with warm cache (props [@cadic](https://github.com/cadic), [@iamdharmesh](https://github.com/iamdharmesh) via [#212](https://github.com/10up/autoshare-for-twitter/pull/212)).
* **Security:** Bump `decode-uri-component` from 0.2.0 to 0.2.2 (props [@dependabot](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh) via [#208](https://github.com/10up/autoshare-for-twitter/pull/208)).
* **Security:** Bump `simple-git` from 3.14.1 to 3.15.1 (props [@dependabot](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh) via [#210](https://github.com/10up/autoshare-for-twitter/pull/210)).

= 1.2.1 - 2022-12-07 =
**Note that this release bumps the WordPress minimum from 5.3 to 5.7 and PHP minimum from 7.2 to 7.4.**

* **Added:** "PR Automator" GitHub Action (props [@iamdharmesh](https://github.com/iamdharmesh), [@Sidsector9](https://github.com/Sidsector9), [@jeffpaul](https://github.com/jeffpaul) via [#194](https://github.com/10up/autoshare-for-twitter/pull/194), [#196](https://github.com/10up/autoshare-for-twitter/pull/196)).
* **Added:** "Build release zip" GitHub Action (props [@iamdharmesh](https://github.com/iamdharmesh), [@dkotter](https://github.com/dkotter) via [#201](https://github.com/10up/autoshare-for-twitter/pull/201)).
* **Changed:** Bump minimum `PHP` version from 7.2 to 7.4 (props [@Sidsector9](https://github.com/Sidsector9), [@iamdharmesh](https://github.com/iamdharmesh), [@vikrampm1](https://github.com/vikrampm1) via [#197](https://github.com/10up/autoshare-for-twitter/pull/197)).
* **Changed:** Bump minimum `WordPress` version from 5.3 to 5.7 (props [@Sidsector9](https://github.com/Sidsector9), [@iamdharmesh](https://github.com/iamdharmesh), [@vikrampm1](https://github.com/vikrampm1) via [#197](https://github.com/10up/autoshare-for-twitter/pull/197)).
* **Changed:** Bump WordPress "tested up to" version 6.1 (props [@iamdharmesh](https://github.com/iamdharmesh), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#200](https://github.com/10up/autoshare-for-twitter/pull/200)).
* **Changed:** Support Level from `Active` to `Stable` (props [@jeffpaul](https://github.com/jeffpaul), [@dkotter](https://github.com/dkotter) via [#195](https://github.com/10up/autoshare-for-twitter/pull/195)).
* **Security:** Bump `json-schema` from 0.2.3 to 0.4.0 (props [@dependabot](https://github.com/apps/dependabot), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#189](https://github.com/10up/autoshare-for-twitter/pull/189)).
* **Security:** Bump `jsprim` from 1.4.1 to 1.4.2 (props [@dependabot](https://github.com/apps/dependabot), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#189](https://github.com/10up/autoshare-for-twitter/pull/189)).
* **Security:** Bump `simple-git` from 2.47.0 to 3.14.1 (props [@dependabot](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh) via [#192](https://github.com/10up/autoshare-for-twitter/pull/192)).
* **Security:** Bump `@wordpress/env` from 4.1.3 to 5.3.0 (props [@dependabot](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh), [@faisal-alvi](https://github.com/faisal-alvi) via [#191](https://github.com/10up/autoshare-for-twitter/pull/191), [#192](https://github.com/10up/autoshare-for-twitter/pull/192)).
* **Security:** Bump `got` from 10.7.0 to 11.8.5 (props [@dependabot](https://github.com/apps/dependabot), [@faisal-alvi](https://github.com/faisal-alvi) via [#191](https://github.com/10up/autoshare-for-twitter/pull/191)).

= 1.2.0 - 2022-09-28 =
**Note that this release bumps the WordPress minimum from 4.9 to 5.3.**

* **Added:** AutoTweet panel in editor sidebar and pre-publish panel to manage enabling/disabling tweet on publish (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul), [@linawiezkowiak](https://github.com/linawiezkowiak), [@oszkarnagy](https://github.com/oszkarnagy), [@cadic](https://github.com/cadic) via [#177](https://github.com/10up/autoshare-for-twitter/pull/177)).
* **Changed** Bump minimum required WordPress version to 5.3 (props [@iamdharmesh](https://github.com/iamdharmesh) via [#177](https://github.com/10up/autoshare-for-twitter/pull/177)).
* **Changed** Updates in `CONTRIBUTING.md` file (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul) via [#172](https://github.com/10up/autoshare-for-twitter/pull/172)).
* **Changed** Update plugin icons (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul) via [#174](https://github.com/10up/autoshare-for-twitter/pull/174)).
* **Security:** Bump `terser` from 4.3.1 to 4.8.1 (props [@dependabot](https://github.com/apps/dependabot) via [#184](https://github.com/10up/autoshare-for-twitter/pull/184)).

= 1.1.2 - 2022-06-24 =
* **Added:** Cypress E2E tests (props [@iamdharmesh](https://github.com/iamdharmesh), [@jeffpaul](https://github.com/jeffpaul), [@Sidsector9](https://github.com/Sidsector9)).
* **Added:** Handle tweeting from staging/testing/local environments to prevent accidental tweets (props [@dinhtungdu](https://github.com/dinhtungdu), [@iamdharmesh](https://github.com/iamdharmesh), [@peterwilsoncc](https://github.com/peterwilsoncc), [@jeffpaul](https://github.com/jeffpaul)).
* **Added:** Dependency security scanning (props [@jeffpaul](https://github.com/jeffpaul), [@iamdharmesh](https://github.com/iamdharmesh)).
* **Changed** Bump WordPress "tested up to" version 6.0 (props [@iamdharmesh](https://github.com/iamdharmesh), [@vikrampm1](https://github.com/vikrampm1), [@jeffpaul](https://github.com/jeffpaul), [@Sidsector9](https://github.com/Sidsector9)).
* **Fixed:** Incorrect `Tweet this post` checkbox behavior in the Classic Editor (props [@iamdharmesh](https://github.com/iamdharmesh), [@cadic](https://github.com/cadic)).
* **Fixed:** "Plugin asset/readme update" GitHub Action failure (props [@iamdharmesh](https://github.com/iamdharmesh), [@dkotter](https://github.com/dkotter), [@jeffpaul](https://github.com/jeffpaul)).

= 1.1.1 - 2022-04-13 =
* **Fixed:** If Autoshare is enabled by default, it does not consider the post-level "Tweet this post" checkbox and always tweets (props [@iamdharmesh](https://github.com/iamdharmesh), [@Sidsector9](https://github.com/Sidsector9)).

= 1.1.0 - 2022-04-13 =
* **Added:** Colored icons to represent autoshare status (props [@linawiezkowiak](https://github.com/linawiezkowiak), [@oszkarnagy](https://github.com/oszkarnagy), [@Sidsector9](https://github.com/Sidsector9), [@dinhtungdu](https://github.com/dinhtungdu)).
* **Added:** Sample copy for example responses (props [@iamdharmesh](https://github.com/iamdharmesh), [@Sidsector9](https://github.com/Sidsector9), [@jeffpaul](https://github.com/jeffpaul)).
* **Added:** PHP 8 compatibility (props [@Sidsector9](https://github.com/Sidsector9), [@faisal-alvi](https://github.com/faisal-alvi)).
* **Added:** E2E Tests with Cypress (props [@thrijith](https://github.com/thrijith), [@iamdharmesh](https://github.com/iamdharmesh), [@dinhtungdu](https://github.com/dinhtungdu), [@jeffpaul](https://github.com/jeffpaul)).
* **Changed:** Update dependency `abraham/twitteroauth` from 1.2.0 to 2.0.0 to ensure PHP 8.0 support (props [@Sidsector9](https://github.com/Sidsector9), [@faisal-alvi](https://github.com/faisal-alvi)).
* **Changed:** App setup instructions for getting API keys and tokens (props [@iamdharmesh](https://github.com/iamdharmesh), [@Sidsector9](https://github.com/Sidsector9), [@jeffpaul](https://github.com/jeffpaul)).
* **Changed:** Bump WordPress version "tested up to" 5.9 (props [@iamdharmesh](https://github.com/iamdharmesh), [@Sidsector9](https://github.com/Sidsector9), [@sudip-10up](https://github.com/sudip-10up)).
* **Fixed:** Warning thrown on activating plugin for the first time (props [@Sidsector9](https://github.com/Sidsector9), [@dinhtungdu](https://github.com/dinhtungdu)).
* **Fixed:** CI pipeline failures (props [@dkotter](https://github.com/dkotter), [@iamdharmesh](https://github.com/iamdharmesh), [@Sidsector9](https://github.com/Sidsector9)).
* **Security:** Bump ajv from 6.10.2 to 6.12.6 (props [@dependabot](https://github.com/apps/dependabot)).
* **Security:** Bump tar from 4.4.8 to 4.4.19 (props [@dependabot](https://github.com/apps/dependabot)).
* **Security:** Bump ini from 1.3.5 to 1.3.8 (props [@dependabot](https://github.com/apps/dependabot)).

= 1.0.6 - 2020-09-19 =
* Note: this was a hotfix release to fix an issue with deploys to WordPress.org.

= 1.0.5 - 2021-09-15 =
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

= 1.0.4 - 2020-07-02 =
* Note: this was a hotfix release to fix an issue with included libraries.

= 1.0.3 - 2020-07-01 =
* **Added:** `autoshare_for_twitter_disable_on_transition_post_status` filter to disable tweeting based on post status change (props [@rickalee](https://profiles.wordpress.org/rickalee))
* **Changed:** Bumped WordPress version support to 5.4.2 (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu), [@jeffpaul](https://profiles.wordpress.org/jeffpaul/))
* **Changed:** Build, test, and release processes (props [@johnwatkins0](https://profiles.wordpress.org/johnwatkins0), [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu))
* **Fixed:*# Enable autoshare meta always set to 0 when saving draft (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu), [@rickalee](https://profiles.wordpress.org/rickalee))

= 1.0.2 - 2020-03-12 =
* **Added:** WP Acceptance tests (props [@johnwatkins0](https://profiles.wordpress.org/johnwatkins0/))
* **Changed:** New and improved settings page UX (props [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/), [@jeffpaul](https://profiles.wordpress.org/jeffpaul/), [@johnwatkins0](https://profiles.wordpress.org/johnwatkins0/), [@linawiezkowiak](https://profiles.wordpress.org/linawiezkowiak/), [@oszkarnagy](https://profiles.wordpress.org/oszkarnagy/))
* **Fixed:** Bug that caused posts to be inadvertently tweeted when switching from draft to publish (props [@johnwatkins0](https://profiles.wordpress.org/johnwatkins0/), [@rickalee](https://profiles.wordpress.org/rickalee/))
* **Fixed:** Build script in release process (props [@johnwatkins0](https://profiles.wordpress.org/johnwatkins0/))

= 1.0.1 - 2020-02-03 =
* **Added:** `autoshare_for_twitter_enabled_default` filter to allow autoshare to be enabled by default for a post type (props [@johnwatkins0](https://profiles.wordpress.org/johnwatkins0/), [@rickalee](https://profiles.wordpress.org/rickalee/), [@dinhtungdu](https://profiles.wordpress.org/dinhtungdu/))
* **Changed:** bypass character texturization when the post title is tweeted (props [@johnwatkins0](https://profiles.wordpress.org/johnwatkins0/), [@rickalee](https://profiles.wordpress.org/rickalee/))
* **Removed:** second instance of the `autoshare_for_twitter_tweet` filter (props [@johnwatkins0](https://profiles.wordpress.org/johnwatkins0/))

= 1.0.0 - 2019-12-17 =
* **Added:** Initial public release! 🎉
* **Added:** Plugin renamed to "Autoshare for Twitter"
* **Added:** Support Post and Page post types by default, provide Custom Post Type (props [@johnwatkins0](https://profiles.wordpress.org/johnwatkins0/))
* **Added:** Gutenberg support (props [@johnwatkins0](https://profiles.wordpress.org/johnwatkins0/))
* **Added:** REST API endpoint to replace AJAX callback (props [@johnwatkins0](https://profiles.wordpress.org/johnwatkins0/))
* **Added:** Build process, PHPCS linting, unit tests, and Travis CI (props [@johnwatkins0](https://profiles.wordpress.org/johnwatkins0/), [@adamsilverstein](https://profiles.wordpress.org/adamsilverstein/))
* **Added:** Plugin banner and icon images (props Stephanie Campbell)
* **Changed:** Refactor v0.1.0 significantly (props [@adamsilverstein](https://profiles.wordpress.org/adamsilverstein/), [@johnwatkins0](https://profiles.wordpress.org/johnwatkins0/), [@jeffpaul](https://profiles.wordpress.org/jeffpaul/))
* **Security:** XSS prevention - switch from .innerHTML to text (props [@adamsilverstein](https://profiles.wordpress.org/adamsilverstein/))

= 0.1.0 - 2018-05-11 =
* Initial private release (props [@scottlee](https://profiles.wordpress.org/scottlee/))

== Upgrade Notice ==
= 2.0.0 =
Autoshare for Twitter 2.0.0 utilizes [Twitter's v2 API](https://developer.twitter.com/en/products/twitter-api).  If you have not already done so, please [migrate your app](https://developer.twitter.com/en/portal/projects-and-apps) to Twitter's v2 API to continue using Autoshare for Twitter.  [Learn more about migrating here](https://developer.twitter.com/en/docs/twitter-api/migrate/ready-to-migrate).

= 1.2.1 =
This release bumps the WordPress minimum from 5.3 to 5.7 and PHP minimum from 7.2 to 7.4.

= 1.2.0 =
This release bumps the WordPress minimum from 4.9 to 5.3.

= 0.1.0 =
First Release
