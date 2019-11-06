<?php
/**
 * Handles the Admin settings
 *
 * @package TenUp\AutoshareForTwitter\Core
 */

namespace TenUp\AutoshareForTwitter\Core\Admin;

const AT_GROUP    = 'autoshare-for-twitter';
const AT_SETTINGS = 'autoshare-for-twitter';

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
		__( 'Autoshare for Twitter', 'autoshare-for-twitter' ),
		__( 'Autoshare for Twitter', 'autoshare-for-twitter' ),
		'manage_options',
		'autoshare-for-twitter',
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
		'autoshare-cred_section',
		__( 'Twitter Credentials', 'autoshare-for-twitter' ),
		'',
		'autoshare-for-twitter'
	);

	// API Key.
	add_settings_field(
		'autoshare-api_key',
		__( 'API key', 'autoshare-for-twitter' ),
		__NAMESPACE__ . '\text_field_cb',
		'autoshare-for-twitter',
		'autoshare-cred_section',
		[ 'name' => 'api_key' ]
	);

	// API Secret.
	add_settings_field(
		'autoshare-api_secret',
		__( 'API secret', 'autoshare-for-twitter' ),
		__NAMESPACE__ . '\text_field_cb',
		'autoshare-for-twitter',
		'autoshare-cred_section',
		[ 'name' => 'api_secret' ]
	);

	// Access Token.
	add_settings_field(
		'autoshare-access_token',
		__( 'Access token', 'autoshare-for-twitter' ),
		__NAMESPACE__ . '\text_field_cb',
		'autoshare-for-twitter',
		'autoshare-cred_section',
		[ 'name' => 'access_token' ]
	);

	// Access Secret.
	add_settings_field(
		'autoshare-access_secret',
		__( 'Access secret', 'autoshare-for-twitter' ),
		__NAMESPACE__ . '\text_field_cb',
		'autoshare-for-twitter',
		'autoshare-cred_section',
		[ 'name' => 'access_secret' ]
	);

	// Twitter Handle.
	add_settings_field(
		'autoshare-twitter_handle',
		__( 'Twitter handle', 'autoshare-for-twitter' ),
		__NAMESPACE__ . '\text_field_cb',
		'autoshare-for-twitter',
		'autoshare-cred_section',
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
		<h1><?php esc_html_e( 'Autoshare for Twitter', 'autoshare-for-twitter' ); ?></h1>

		<form action='options.php' method='post'>
			<?php
			settings_fields( AT_GROUP );
			do_settings_sections( 'autoshare-for-twitter' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}

/**
 * Fire up the module.
 *
 * @uses autoshare_for_twitter_setup
 */
add_action( 'autoshare_for_twitter_setup', __NAMESPACE__ . '\setup' );
