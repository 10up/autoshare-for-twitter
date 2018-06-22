<?php
/**
 * Handles the Admin settings
 */
namespace TenUp\Auto_Tweet\Core\Admin;

const AT_GROUP    = 'tenup-auto-tweet';
const AT_SETTINGS = 'tenup-auto-tweet';

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
		__( '10up Auto Tweet', 'tuat' ),
		__( '10up Auto Tweet', 'tuat' ),
		'manage_options',
		'tenup-auto-tweet',
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

	// Register the credential setting section
	add_settings_section(
		'tenup-auto-tweet-cred_section',
		__( 'Twitter Credentials', 'tuat' ),
		'',
		'tenup-auto-tweet'
	);

	// API Key
	add_settings_field(
		'tenup-auto-tweet-api_key',
		__( 'API key', 'tuat' ),
		__NAMESPACE__ . '\text_field_cb',
		'tenup-auto-tweet',
		'tenup-auto-tweet-cred_section',
		[ 'name' => 'api_key' ]
	);

	// API Secret
	add_settings_field(
		'tenup-auto-tweet-api_secret',
		__( 'API secret', 'tuat' ),
		__NAMESPACE__ . '\text_field_cb',
		'tenup-auto-tweet',
		'tenup-auto-tweet-cred_section',
		[ 'name' => 'api_secret' ]
	);

	// Access Token
	add_settings_field(
		'tenup-auto-tweet-access_token',
		__( 'Access token', 'tuat' ),
		__NAMESPACE__ . '\text_field_cb',
		'tenup-auto-tweet',
		'tenup-auto-tweet-cred_section',
		[ 'name' => 'access_token' ]
	);

	// Access Secret
	add_settings_field(
		'tenup-auto-tweet-access_secret',
		__( 'Access secret', 'tuat' ),
		__NAMESPACE__ . '\text_field_cb',
		'tenup-auto-tweet',
		'tenup-auto-tweet-cred_section',
		[ 'name' => 'access_secret' ]
	);

	// Twitter Handle
	add_settings_field(
		'tenup-auto-tweet-twitter_handle',
		__( 'Twitter handle', 'tuat' ),
		__NAMESPACE__ . '\text_field_cb',
		'tenup-auto-tweet',
		'tenup-auto-tweet-cred_section',
		[ 'name' => 'twitter_handle' ]
	);

}

/**
 * Helper for ouputing a text field.
 *
 * @param array $args
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
		<h1><?php _e( '10up Auto Tweet', 'tuat' ) ?></h1>

		<form action='options.php' method='post'>
			<?php
			settings_fields( AT_GROUP );
			do_settings_sections( 'tenup-auto-tweet' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}

/**
 * Fire up the module.
 *
 * @uses auto_tweet_setup
 */
add_action( 'tenup_auto_tweet_setup', __NAMESPACE__ . '\setup' );
