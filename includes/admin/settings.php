<?php
/**
 * Handles the Admin settings
 *
 * @package TenUp\AutoshareForTwitter\Core
 */

namespace TenUp\AutoshareForTwitter\Core\Admin;

const AT_GROUP    = 'autoshare';
const AT_SETTINGS = 'autoshare';

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
		__( 'Autoshare', 'auto-share-for-twitter' ),
		__( 'Autoshare', 'auto-share-for-twitter' ),
		'manage_options',
		'autoshare',
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
		__( 'Twitter Credentials', 'auto-share-for-twitter' ),
		'',
		'autoshare'
	);

	// API Key.
	add_settings_field(
		'autoshare-api_key',
		__( 'API key', 'auto-share-for-twitter' ),
		__NAMESPACE__ . '\text_field_cb',
		'autoshare',
		'autoshare-cred_section',
		[ 'name' => 'api_key' ]
	);

	// API Secret.
	add_settings_field(
		'autoshare-api_secret',
		__( 'API secret', 'auto-share-for-twitter' ),
		__NAMESPACE__ . '\text_field_cb',
		'autoshare',
		'autoshare-cred_section',
		[ 'name' => 'api_secret' ]
	);

	// Access Token.
	add_settings_field(
		'autoshare-access_token',
		__( 'Access token', 'auto-share-for-twitter' ),
		__NAMESPACE__ . '\text_field_cb',
		'autoshare',
		'autoshare-cred_section',
		[ 'name' => 'access_token' ]
	);

	// Access Secret.
	add_settings_field(
		'autoshare-access_secret',
		__( 'Access secret', 'auto-share-for-twitter' ),
		__NAMESPACE__ . '\text_field_cb',
		'autoshare',
		'autoshare-cred_section',
		[ 'name' => 'access_secret' ]
	);

	// Twitter Handle.
	add_settings_field(
		'autoshare-twitter_handle',
		__( 'Twitter handle', 'auto-share-for-twitter' ),
		__NAMESPACE__ . '\text_field_cb',
		'autoshare',
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
		<h1><?php esc_html_e( 'Autoshare', 'auto-share-for-twitter' ); ?></h1>

		<form action='options.php' method='post'>
			<?php
			settings_fields( AT_GROUP );
			do_settings_sections( 'autoshare' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}

/**
 * Fire up the module.
 *
 * @uses autoshare_setup
 */
add_action( 'autoshare_setup', __NAMESPACE__ . '\setup' );
