<?php
/**
 * Handles the Admin settings
 *
 * @package TenUp\AutoTweet\Core
 */

namespace TenUp\AutoTweet\Core\Admin;

const AT_GROUP    = 'autotweet';
const AT_SETTINGS = 'autotweet';

/**
 * Main setup function for the Admin
 *
 * @return void
 */
function setup() {

	add_action( 'admin_menu', __NAMESPACE__ . '\admin_menu' );
	add_action( 'admin_init', __NAMESPACE__ . '\register_settings' );
}

/**
 * Adds a page under Settings for management of the plugin
 *
 * @return void
 */
function admin_menu() {

	add_options_page(
		__( 'Autotweet', 'autotweet' ),
		__( 'Autotweet', 'autotweet' ),
		'manage_options',
		'autotweet',
		__NAMESPACE__ . '\options_page'
	);
}

/**
 * Register section and settings
 *
 * @return void
 */
function register_settings() {

	register_setting( AT_GROUP, AT_SETTINGS );

	// Register the credential setting section.
	add_settings_section(
		'autotweet-cred_section',
		__( 'Twitter Credentials', 'autotweet' ),
		'',
		'autotweet'
	);

	// API Key.
	add_settings_field(
		'autotweet-api_key',
		__( 'API key', 'autotweet' ),
		__NAMESPACE__ . '\text_field_cb',
		'autotweet',
		'autotweet-cred_section',
		[ 'name' => 'api_key' ]
	);

	// API Secret.
	add_settings_field(
		'autotweet-api_secret',
		__( 'API secret', 'autotweet' ),
		__NAMESPACE__ . '\text_field_cb',
		'autotweet',
		'autotweet-cred_section',
		[ 'name' => 'api_secret' ]
	);

	// Access Token.
	add_settings_field(
		'autotweet-access_token',
		__( 'Access token', 'autotweet' ),
		__NAMESPACE__ . '\text_field_cb',
		'autotweet',
		'autotweet-cred_section',
		[ 'name' => 'access_token' ]
	);

	// Access Secret.
	add_settings_field(
		'autotweet-access_secret',
		__( 'Access secret', 'autotweet' ),
		__NAMESPACE__ . '\text_field_cb',
		'autotweet',
		'autotweet-cred_section',
		[ 'name' => 'access_secret' ]
	);

	// Twitter Handle.
	add_settings_field(
		'autotweet-twitter_handle',
		__( 'Twitter handle', 'autotweet' ),
		__NAMESPACE__ . '\text_field_cb',
		'autotweet',
		'autotweet-cred_section',
		[ 'name' => 'twitter_handle' ]
	);

}

/**
 * Helper for ouputing a text field.
 *
 * @param array $args The field arguments.
 *
 * @return void
 */
function text_field_cb( $args ) {

	$options = get_option( AT_SETTINGS );
	$key     = $args['name'];
	$name    = AT_SETTINGS . "[$key]";
	$value   = $options[ $key ];
	?>
	<input type='text' name=<?php echo esc_attr( $name ); ?> value="<?php echo esc_attr( $value ); ?>">
	<?php
}

/**
 * Primary output handler for the settings page.
 *
 * @return void
 */
function options_page() {

	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Autotweet', 'autotweet' ); ?></h1>

		<form action='options.php' method='post'>
			<?php
			settings_fields( AT_GROUP );
			do_settings_sections( 'autotweet' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}

/**
 * Fire up the module.
 *
 * @uses autotweet_setup
 */
add_action( 'autotweet_setup', __NAMESPACE__ . '\setup' );
