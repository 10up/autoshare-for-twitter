<?php
/**
 * Handles the Admin settings
 *
 * @package TenUp\AutoshareForTwitter\Core
 */

namespace TenUp\AutoshareForTwitter\Core\Admin;

use TenUp\AutoshareForTwitter\Utils;

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
	add_filter( 'plugin_action_links_' . plugin_basename( AUTOSHARE_FOR_TWITTER ), __NAMESPACE__ . '\action_links' );
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
		__NAMESPACE__ . '\cred_section_cb',
		'autoshare-for-twitter'
	);

	// API Key.
	add_settings_field(
		'autoshare-api_key',
		__( 'API key', 'autoshare-for-twitter' ),
		__NAMESPACE__ . '\text_field_cb',
		'autoshare-for-twitter',
		'autoshare-cred_section',
		[
			'name'  => 'api_key',
			'class' => 'large-text',
		]
	);

	// API Secret.
	add_settings_field(
		'autoshare-api_secret',
		__( 'API secret', 'autoshare-for-twitter' ),
		__NAMESPACE__ . '\text_field_cb',
		'autoshare-for-twitter',
		'autoshare-cred_section',
		[
			'name'  => 'api_secret',
			'class' => 'large-text',
		]
	);

	// Access Token.
	add_settings_field(
		'autoshare-access_token',
		__( 'Access token', 'autoshare-for-twitter' ),
		__NAMESPACE__ . '\text_field_cb',
		'autoshare-for-twitter',
		'autoshare-cred_section',
		[
			'name'  => 'access_token',
			'class' => 'large-text',
		]
	);

	// Access Secret.
	add_settings_field(
		'autoshare-access_secret',
		__( 'Access secret', 'autoshare-for-twitter' ),
		__NAMESPACE__ . '\text_field_cb',
		'autoshare-for-twitter',
		'autoshare-cred_section',
		[
			'name'  => 'access_secret',
			'class' => 'large-text',
		]
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
	$class   = isset( $args['class'] ) ? $args['class'] : 'regular-text';
	?>
	<input type='text' class="<?php echo esc_attr( $class ); ?>" name=<?php echo esc_attr( $name ); ?> value="<?php echo esc_attr( $value ); ?>">
	<?php
}

/**
 * Helper for ouputing credentials section.
 *
 * @param array $args The field arguments.
 *
 * @return void
 */
function cred_section_cb( $args ) {
	$wrapper_class = Utils\is_twitter_configured() ? 'connected' : '';
	?>
	<?php if ( 'connected' === $wrapper_class ) : ?>
		<p class="credentials-actions <?php echo esc_attr( $wrapper_class ); ?>">
			<a href="JavaScript:void(0);" class="open">
				<?php echo esc_html__( 'Open credentials settings', 'autoshare-for-twitter' ); ?><span class="dashicons dashicons-arrow-down-alt2"></span>
			</a>
			<a href="JavaScript:void(0);" class="close">
				<?php echo esc_html__( 'Close credentials settings', 'autoshare-for-twitter' ); ?><span class="dashicons dashicons-arrow-up-alt2"></span>
			</a>
		</p>
		<?php return; ?>
	<?php endif; ?>
	<section class="credentials-setup">
		<h4>1. Step 1</h4>
		<p>Step 1 detail.</p>
		<h4>2. Step 2</h4>
		<p>Step 2 detail.</p>
	</section>
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

		<div class="autoshare-settings">
			<form action='options.php' method='post'>
				<?php
				settings_fields( AT_GROUP );
				do_settings_sections( 'autoshare-for-twitter' );
				submit_button();
				?>
			</form>
			<div class="brand">
				<a href="https://10up.com" class="logo" title="<?php echo esc_html__( '10up', 'autoshare-for-twitter' ); ?>">
					<img src="<?php echo esc_url( trailingslashit( AUTOSHARE_FOR_TWITTER_URL ) . 'assets/images/10up.svg' ); ?>" alt="<?php echo esc_html__( '10up logo', 'autoshare-for-twitter' ); ?>" />
				</a>
				<p>
					<strong>
						<?php echo esc_html__( 'Autoshare for Twitter', 'autoshare-for-twitter' ) . ' ' . esc_html__( 'by', 'autoshare-for-twitter' ); ?> <a href="https://10up.com" class="logo" title="<?php echo esc_html__( '10up', 'autoshare-for-twitter' ); ?>"><?php echo esc_html__( '10up', 'autoshare-for-twitter' ); ?></a>
					</strong>
				</p>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Plugin action links for AT.
 *
 * @param array $links Current action links.
 *
 * @return array
 */
function action_links( $links ) {
	if ( Utils\is_twitter_configured() ) {
		$links['settings'] = sprintf(
			/* translators: %s is the plugin setting page URL */
			__( '<a href="%s">Settings</a>', 'autoshare-for-twitter' ),
			esc_url( admin_url( 'options-general.php?page=autoshare-for-twitter' ) )
		);
	} else {
		$links['initial-setup'] = sprintf(
			/* translators: %s is the plugin setting page URL */
			__( '<a href="%s">Setup your Twitter account</a>', 'autoshare-for-twitter' ),
			esc_url( admin_url( 'options-general.php?page=autoshare-for-twitter' ) )
		);
	}

	return $links;
}

/**
 * Fire up the module.
 *
 * @uses autoshare_for_twitter_setup
 */
add_action( 'autoshare_for_twitter_setup', __NAMESPACE__ . '\setup' );
