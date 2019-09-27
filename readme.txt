=== Tenup Auto Tweet ===
Contributors:      10up, adamsilverstein, johnwatkins0
Tags:              twitter
Requires at least: 4.7
Tested up to:      5.2.3
Requires PHP:      7.0
Stable tag:        0.1.0
License:           GPL-2.0-or-later
License URI:       https://spdx.org/licenses/GPL-2.0-or-later.html

Automatically tweets a post title, URL, and optional description.

== Description ==
Automatically tweets a post title, URL, and optional description.

**NOTE:** Post types are automatically set to auto-tweet. Future versions of this plugin could allow this to be set manually.

== Manual Installation ==
1. Upload the entire `/tenup-auto-tweet` directory to the `/wp-content/plugins/` directory.
2. Activate the plugin
3. Register post type support for types that should be allowed to auto tweet. `add_post_type_support( 'post', 'tenup-auto-tweet' );`

== FAQs ==
Does this plugin work with Gutenberg?
Nope, not yet.

== Changelog ==

= 1.0.0 =
== Added ==
* Initial public release! 🎉
* Support Post and Page post types by default, provide Custom Post Type (props @johnwatkins0)
* REST API endpoint to replace AJAX callback (props @johnwatkins0)
* Build process, PHPCS linting, unit tests, and Travis CI (props @johnwatkins0, @adamsilverstein)
* Plugin banner and icon images (props Stephanie Campbell)

== Changed ==
* Refactor v0.1.0 significantly (props @adamsilverstein)

== Security ==
* XSS prevention - switch from .innerHTML to text (props @adamsilverstein)

= 0.1.0 =
* Initial closed source release

== Upgrade Notice ==

= 0.1.0 =
First Release
