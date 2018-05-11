<?php
namespace TenupAutoTweet\Core;

/**
 * Default setup routine
 *
 * @return void
 */
function setup() {
	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	add_action( 'init', $n( 'i18n' ) );
	add_action( 'init', $n( 'init' ) );
	add_action( 'wp_enqueue_scripts', $n( 'scripts' ) );
	add_action( 'wp_enqueue_scripts', $n( 'styles' ) );
	add_action( 'admin_enqueue_scripts', $n( 'admin_scripts' ) );
	add_action( 'admin_enqueue_scripts', $n( 'admin_styles' ) );

	// Editor styles. add_editor_style() doesn't work outside of a theme.
	add_filter( 'mce_css', $n( 'mce_css' ) );

	do_action( 'tenup_auto_tweet_loaded' );
}

/**
 * Registers the default textdomain.
 *
 * @return void
 */
function i18n() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'tenup-auto-tweet' );
	load_textdomain( 'tenup-auto-tweet', WP_LANG_DIR . '/tenup-auto-tweet/tenup-auto-tweet-' . $locale . '.mo' );
	load_plugin_textdomain( 'tenup-auto-tweet', false, plugin_basename( TENUP_AUTO_TWEET_PATH ) . '/languages/' );
}

/**
 * Initializes the plugin and fires an action other plugins can hook into.
 *
 * @return void
 */
function init() {
	do_action( 'tenup_auto_tweet_init' );
}

/**
 * Activate the plugin
 *
 * @return void
 */
function activate() {
	// First load the init scripts in case any rewrite functionality is being loaded
	init();
	flush_rewrite_rules();
}

/**
 * Deactivate the plugin
 *
 * Uninstall routines should be in uninstall.php
 *
 * @return void
 */
function deactivate() {

}

/**
 * Generate an URL to a script, taking into account whether SCRIPT_DEBUG is enabled.
 *
 * @param string $script Script file name (no .js extension)
 * @param string $context Context for the script ('admin', 'frontend', or 'shared')
 *
 * @return string|WP_Error URL
 */
function script_url( $script, $context ) {

	if( !in_array( $context, ['admin', 'frontend', 'shared'], true) ) {
		error_log('Invalid $context specfied in TenupAutoTweet script loader.');
		return '';
	}

	return ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ?
		TENUP_AUTO_TWEET_URL . "assets/js/${context}/{$script}.js" :
		TENUP_AUTO_TWEET_URL . "dist/js/${context}.min.js" ;

}

/**
 * Generate an URL to a stylesheet, taking into account whether SCRIPT_DEBUG is enabled.
 *
 * @param string $stylesheet Stylesheet file name (no .css extension)
 * @param string $context Context for the script ('admin', 'frontend', or 'shared')
 *
 * @return string URL
 */
function style_url( $stylesheet, $context ) {

	if( !in_array( $context, ['admin', 'frontend', 'shared'], true) ) {
		error_log('Invalid $context specfied in TenupAutoTweet stylesheet loader.');
		return '';
	}

	return ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ?
		TENUP_AUTO_TWEET_URL . "assets/css/${context}/{$stylesheet}.css" :
		TENUP_AUTO_TWEET_URL . "dist/css/${stylesheet}.min.css" ;

}

/**
 * Enqueue scripts for front-end.
 *
 * @return void
 */
function scripts() {

	wp_enqueue_script(
		'tenup_auto_tweet_shared',
		script_url( 'shared', 'shared' ),
		[],
		TENUP_AUTO_TWEET_VERSION,
		true
	);

	wp_enqueue_script(
		'tenup_auto_tweet_frontend',
		script_url( 'frontend', 'frontend' ),
		[],
		TENUP_AUTO_TWEET_VERSION,
		true
	);

}

/**
 * Enqueue scripts for admin.
 *
 * @return void
 */
function admin_scripts() {

	wp_enqueue_script(
		'tenup_auto_tweet_shared',
		script_url( 'shared', 'shared' ),
		[],
		TENUP_AUTO_TWEET_VERSION,
		true
	);

	wp_enqueue_script(
		'tenup_auto_tweet_admin',
		script_url( 'admin', 'admin' ),
		[],
		TENUP_AUTO_TWEET_VERSION,
		true
	);

}

/**
 * Enqueue styles for front-end.
 *
 * @return void
 */
function styles() {

	wp_enqueue_style(
		'tenup_auto_tweet_shared',
		style_url( 'shared-style', 'shared' ),
		[],
		TENUP_AUTO_TWEET_VERSION
	);

	if( is_admin() ) {
		wp_enqueue_script(
			'tenup_auto_tweet_admin',
			style_url( 'admin-style', 'admin' ),
			[],
			TENUP_AUTO_TWEET_VERSION,
			true
		);
	}
	else {
		wp_enqueue_script(
			'tenup_auto_tweet_frontend',
			style_url( 'style', 'frontend' ),
			[],
			TENUP_AUTO_TWEET_VERSION,
			true
		);
	}

}

/**
 * Enqueue styles for admin.
 *
 * @return void
 */
function admin_styles() {

	wp_enqueue_style(
		'tenup_auto_tweet_shared',
		style_url( 'shared-style', 'shared' ),
		[],
		TENUP_AUTO_TWEET_VERSION
	);

	wp_enqueue_script(
		'tenup_auto_tweet_admin',
		style_url( 'admin-style', 'admin' ),
		[],
		TENUP_AUTO_TWEET_VERSION,
		true
	);

}

/**
 * Enqueue editor styles
 *
 * @return string
 */
function mce_css( $stylesheets ) {

	function style_url() {

		return TENUP_AUTO_TWEET_URL . ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ?
			"assets/css/frontend/editor-style.css" :
			"dist/css/editor-style.min.css" );

	}

	return $stylesheets . ',' . style_url();
}
