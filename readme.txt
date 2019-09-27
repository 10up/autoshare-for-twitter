=== Autotweet ===
Contributors:      10up
Tags:
Tested up to:      4.9.8
Stable tag:        0.1.0

== Description ==
Automatically tweets a post title, URL, and optional description.

**NOTE:** Post types are automatically set to autotweet. Future versions of this plugin could allow this to be set manually.

== Manual Installation ==
1. Upload the entire `/autotweet` directory to the `/wp-content/plugins/` directory.
2. Activate the plugin
3. Register post type support for types that should be allowed to auto tweet. `add_post_type_support( 'post', 'autotweet' );`

== FAQs ==
Does this plugin work with Gutenberg?
Nope, not yet.

== TODOs ==
- Reevaluate storing the Twitter credentials in the database OR do a environment check. There's potential for a local environment tweeting publicly if using a copy of the production database.
- Allow for post types to opt into/out of the autotweeting.
- Remove jQuery dependency
- Gutenberg compatibility
- Remove composer dependencies from the repo


== Changelog ==

= 0.1.0 =
* First release

== Upgrade Notice ==

= 0.1.0 =
First Release
