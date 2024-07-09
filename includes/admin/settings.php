<?php
/**
 * Handles the Admin settings
 *
 * @package TenUp\AutoshareForTwitter\Core
 */

namespace TenUp\AutoshareForTwitter\Core\Admin;

use TenUp\AutoshareForTwitter\Utils;
use TenUp\AutoshareForTwitter\List_Table\Twitter_Accounts_List_Table as Twitter_Accounts_List_Table;

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
		__( 'Autopost for X/Twitter Settings', 'autoshare-for-twitter' ),
		__( 'Autopost for X/Twitter', 'autoshare-for-twitter' ),
		'manage_options',
		'autoshare-for-twitter',
		__NAMESPACE__ . '\options_page'
	);
}

/**
 * Sanitize and validate user input.
 *
 * @param string $post The user input to sanitize and validate.
 *
 * @return array The sanitized and validated values.
 */
function sanitize_settings( $post ) {
	$options = get_option( AT_SETTINGS );

	// The post keys that should be secure.
	$secure_keys = array( 'api_key', 'api_secret', 'access_token', 'access_secret' );
	foreach ( $secure_keys as $key ) {
		if ( ! empty( $post[ $key ] ) ) {
			$value = sanitize_text_field( trim( $post[ $key ] ) );
			// If the value contains '***', use the existing option value if available, else empty string.
			if ( false !== stripos( $value, '***' ) ) {
				$post[ $key ] = isset( $options[ $key ] ) ? $options[ $key ] : '';
			}
		}
	}

	return $post;
}

/**
 * Register section and settings
 *
 * @return void
 */
function register_settings() {

	register_setting( AT_GROUP, AT_SETTINGS, __NAMESPACE__ . '\sanitize_settings' );

	// Register the general setting section.
	add_settings_section(
		'autoshare-general_section',
		'',
		__NAMESPACE__ . '\general_section_cb',
		'autoshare-for-twitter'
	);

	// Post type.
	add_settings_field(
		'autoshare-enable_for',
		__( 'Enable Autopost for', 'autoshare-for-twitter' ),
		__NAMESPACE__ . '\radio_field_cb',
		'autoshare-for-twitter',
		'autoshare-general_section',
		[
			'name'    => 'enable_for',
			'choices' => [
				'all'      => __( 'All content types', 'autoshare-for-twitter' ),
				'selected' => __( 'Selected content types only', 'autoshare-for-twitter' ),
			],
			'default' => 'selected',
			'class'   => 'enable-for',
		]
	);

	add_settings_field(
		'autoshare-post_types',
		'',
		__NAMESPACE__ . '\checkbox_field_cb',
		'autoshare-for-twitter',
		'autoshare-general_section',
		[
			'name'     => 'post_types',
			'choices'  => Utils\get_available_post_types_data(),
			'default'  => Utils\get_post_types_supported_by_default(),
			'disabled' => Utils\get_hardcoded_supported_post_types(),
			'class'    => 'all' === Utils\get_autoshare_for_twitter_settings( 'enable_for' ) ? 'post-types hidden' : 'post-types',
		]
	);

	add_settings_field(
		'autoshare-enable_default',
		__( 'Enable by default', 'autoshare-for-twitter' ),
		__NAMESPACE__ . '\checkbox_field_cb',
		'autoshare-for-twitter',
		'autoshare-general_section',
		[
			'name'    => 'enable_default',
			'choices' => __( 'Enable Autopost by default when publishing content', 'autoshare-for-twitter' ),
			'default' => true,
		]
	);

	add_settings_field(
		'autoshare-enable_upload',
		__( 'Image setting', 'autoshare-for-twitter' ),
		__NAMESPACE__ . '\checkbox_field_cb',
		'autoshare-for-twitter',
		'autoshare-general_section',
		[
			'name'    => 'enable_upload',
			'choices' => __( 'Always add the featured image to tweets', 'autoshare-for-twitter' ),
			'default' => true,
		]
	);

	// X account connection table.
	add_settings_field(
		'autoshare-autoshare_accounts',
		__( 'X/Twitter accounts', 'autoshare-for-twitter' ),
		__NAMESPACE__ . '\twitter_accounts_field_cb',
		'autoshare-for-twitter',
		'autoshare-general_section',
		[
			'name' => 'autoshare_accounts',
		]
	);

	// Register the credential setting section.
	add_settings_section(
		'autoshare-cred_section',
		__( 'X/Twitter connection settings', 'autoshare-for-twitter' ),
		__NAMESPACE__ . '\cred_section_cb',
		'autoshare-for-twitter'
	);

	// API Key.
	add_settings_field(
		'autoshare-api_key',
		__( 'API Key', 'autoshare-for-twitter' ),
		__NAMESPACE__ . '\text_field_cb',
		'autoshare-for-twitter',
		'autoshare-cred_section',
		[
			'name'        => 'api_key',
			'class'       => 'large-text',
			'placeholder' => __( 'paste your API Key here', 'autoshare-for-twitter' ),
		]
	);

	// API Secret.
	add_settings_field(
		'autoshare-api_secret',
		__( 'API Key Secret', 'autoshare-for-twitter' ),
		__NAMESPACE__ . '\text_field_cb',
		'autoshare-for-twitter',
		'autoshare-cred_section',
		[
			'name'        => 'api_secret',
			'class'       => 'large-text',
			'placeholder' => __( 'paste your API Key Secret here', 'autoshare-for-twitter' ),
		]
	);
}

/**
 * Helper for outputting a text field.
 *
 * @param array $args The field arguments.
 *
 * @return void
 */
function text_field_cb( $args ) {

	$options     = get_option( AT_SETTINGS, array() );
	$key         = $args['name'];
	$name        = AT_SETTINGS . "[$key]";
	$value       = isset( $options[ $key ] ) ? $options[ $key ] : '';
	$class       = isset( $args['class'] ) ? $args['class'] : 'regular-text';
	$placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';

	// The post keys that should be secure.
	$secure_keys = array( 'api_key', 'api_secret', 'access_token', 'access_secret' );
	$value       = in_array( $key, $secure_keys, true ) ? Utils\mask_secure_values( $value ) : $value;
	?>
		<input type='text' class="<?php echo esc_attr( $class ); ?>" name=<?php echo esc_attr( $name ); ?> value="<?php echo esc_attr( $value ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>">
	<?php
}

/**
 * Helper for outputting a radio field.
 *
 * @param array $args The field arguments.
 *
 * @return void
 */
function radio_field_cb( $args ) {
	if ( empty( $args['choices'] ) ) {
		return;
	}

	$options = get_option( AT_SETTINGS );
	$key     = $args['name'];
	$name    = AT_SETTINGS . "[$key]";
	$default = ! empty( $args['default'] ) ? $args['default'] : '';
	$value   = ! empty( $options[ $key ] ) ? $options[ $key ] : $default;

	foreach ( $args['choices'] as $key => $label ) {
		printf(
			'<p><label><input type="radio" name="%1$s" value="%2$s" %3$s /> %4$s</label></p>',
			esc_attr( $name ),
			esc_attr( $key ),
			checked( $value, $key, false ),
			esc_html( $label )
		);
	}
}

/**
 * Helper for outputting a checkbox field.
 *
 * @param array $args The field arguments.
 *
 * @return void
 */
function checkbox_field_cb( $args ) {
	if ( empty( $args['choices'] ) ) {
		return;
	}

	$options = get_option( AT_SETTINGS );
	$key     = $args['name'];

	if ( ! is_array( $args['choices'] ) ) {
		$name  = AT_SETTINGS . "[$key]";
		$value = isset( $options[ $key ] ) ? $options[ $key ] : $args['default'];

		printf(
			'<label><input type="hidden" name="%1$s" value="0" /><input type="checkbox" name="%1$s" value="1" %3$s/> %4$s</label>',
			esc_attr( $name ),
			esc_attr( $key ),
			checked( $value, 1, false ),
			esc_html( $args['choices'] )
		);

		return;
	}

	$name  = AT_SETTINGS . "[$key][]";
	$value = isset( $options[ $key ] ) ? (array) $options[ $key ] : $args['default'];

	foreach ( $args['choices'] as $key => $label ) {
		$state = '';
		if ( isset( $args['disabled'] ) && in_array( $key, $args['disabled'], true ) ) {
			$state = 'checked disabled';
		} elseif ( in_array( $key, $value, true ) ) {
			$state = 'checked';
		}
		printf(
			'<p><label><input type="checkbox" name="%1$s" value="%2$s" %3$s/> %4$s</label></p>',
			esc_attr( $name ),
			esc_attr( $key ),
			esc_attr( $state ),
			esc_html( $label )
		);
	}
}

/**
 * Helper for outputting a X account list table.
 *
 * @param array $args The field arguments.
 *
 * @return void
 */
function twitter_accounts_field_cb( $args ) {
	$list_table = new Twitter_Accounts_List_Table();
	$list_table->prepare_items();
	$list_table->display();
}

/**
 * Helper for outputting general section heading and description.
 *
 * @return void
 */
function general_section_cb() {
	$cred_class = Utils\is_twitter_configured() ? 'connected' : '';
	?>
	<div class="general-settings <?php echo esc_attr( $cred_class ); ?>">
		<h2><?php esc_html_e( 'X/Twitter Settings', 'autoshare-for-twitter' ); ?>
	</div>
	<?php
}

/**
 * Helper for outputting credentials section.
 *
 * @return void
 */
function cred_section_cb() {
	?>
	<section class="credentials-setup <?php echo Utils\is_twitter_configured() ? 'connected' : ''; ?>">
		<?php if ( Utils\is_twitter_configured() ) : ?>
		<p class="credentials-actions">
			<a href="JavaScript:void(0);" class="open">
				<?php esc_html_e( 'Open connection settings', 'autoshare-for-twitter' ); ?><span class="dashicons dashicons-arrow-down-alt2"></span>
			</a>
			<a href="JavaScript:void(0);" class="close">
				<?php esc_html_e( 'Close connection settings', 'autoshare-for-twitter' ); ?><span class="dashicons dashicons-arrow-up-alt2"></span>
			</a>
		</p>
		<?php endif; ?>
		<section class="credentials-instructions">
			<h4><a href="https://developer.twitter.com/en/portal/petition/essential/basic-info" target="_blank"><?php esc_html_e( '1. Sign up for a X/Twitter developer account', 'autoshare-for-twitter' ); ?></a></h4>
			<ul>
				<li><?php esc_html_e( 'Click on "Sign up for Free Account" button to proceed with free access.', 'autoshare-for-twitter' ); ?></li>
				<li><?php echo wp_kses_data( __( "Fill out the <code>Describe all of your use cases of X's data and API</code> field. You can find an example response below.", 'autoshare-for-twitter' ) ); ?>
				</li>
				<div class="copy-container-wrap">
					<p class="copy-container">
						<span class="copy-content"><?php esc_html_e( 'I am planning to add an auto-post feature on my WordPress website with the help of the Autopost for X, WordPress plugin. Whenever a new post will be published on the website, Autopost for X plugin will use the post data to curate and trigger a Post.', 'autoshare-for-twitter' ); ?></span>
						<a href="#" class="astCopyToClipCard"><span class="dashicons dashicons-clipboard"></span></a>
					</p>
				</div>
				<li><?php esc_html_e( 'Click on "Submit" button, it will redirect you to Developer portal.', 'autoshare-for-twitter' ); ?></li>
			</ul>

			<h4><?php esc_html_e( '2. Configure access to your X/Twitter app access tokens', 'autoshare-for-twitter' ); ?></h4>
			<ul>
				<li>
					<?php
					printf(
						/* translators: Placeholders %1$s - opening HTML <a> link tag, closing HTML </a> link tag */
						wp_kses_data( __( 'Go to the %1$sX/Twitter developer portal%2$s', 'autoshare-for-twitter' ) ),
						'<a href="https://developer.twitter.com/en/portal/dashboard" target="_blank">',
						'</a>'
					);
					?>
				</li>
				<li><?php esc_html_e( 'Click on Projects & Apps on the left navigation menu.', 'autoshare-for-twitter' ); ?></li>
				<li><?php esc_html_e( 'Find the App and click it to show the Settings page for the App.', 'autoshare-for-twitter' ); ?></li>
				<li><?php esc_html_e( 'Click "Setup" under User authentication settings to setup Authentication.', 'autoshare-for-twitter' ); ?></li>
				<li><?php echo wp_kses_data( __( 'Enable <code>OAuth 1.0a</code> and Set App permissions to <strong>Read and write</strong>.', 'autoshare-for-twitter' ) ); ?></li>
				<li>
					<?php
					/* translators: Placeholders %s - Site URL */
					echo wp_kses_data( sprintf( __( 'Set the <code>Website URL</code> to <code>%s</code>.', 'autoshare-for-twitter' ), esc_url( get_site_url() ) ) );
					?>
				</li>
				<li>
					<?php
					/* translators: Placeholders %s - Callback URL for X/Twitter Auth */
					echo wp_kses_data( sprintf( __( 'Set the <code>Callback URLs</code> fields to <code>%s</code> and click <code>Save</code>.', 'autoshare-for-twitter' ), esc_url( admin_url( 'admin-post.php?action=authoshare_authorize_callback' ) ) ) );
					?>
				</li>
				<li><?php esc_html_e( 'Switch from the "Settings" tab to the "Keys and tokens" tab.', 'autoshare-for-twitter' ); ?></li>
				<li><?php echo wp_kses_data( __( 'Click on the <code>Generate</code>/<code>Regenerate</code> button in the <code>Consumer Keys</code> section.', 'autoshare-for-twitter' ) ); ?></li>
				<li><?php echo wp_kses_data( __( 'Copy the <code>API Key</code> and <code>API Key Secret</code> values and paste them below.', 'autoshare-for-twitter' ) ); ?></li>
			</ul>

			<h4><?php esc_html_e( '3. Save settings', 'autoshare-for-twitter' ); ?></h4>
			<ul>
				<li><?php echo wp_kses_data( __( 'Click the <code>Save Changes</code> button below to save settings.', 'autoshare-for-twitter' ) ); ?></li>
			</ul>

			<h4><?php esc_html_e( '4. Connect your X/Twitter account', 'autoshare-for-twitter' ); ?></h4>
			<ul>
				<li><?php echo wp_kses_data( __( 'After saving settings, you will see the option to connect your X/Twitter account.', 'autoshare-for-twitter' ) ); ?></li>
				<li><?php echo wp_kses_data( __( 'Click the <code>Connect X/Twitter account</code> button and follow the instructions provided there to connect your X/Twitter account with this site.', 'autoshare-for-twitter' ) ); ?></li>
			</ul>
		</section>
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
		<h1><?php esc_html_e( 'Autopost for X/Twitter Settings', 'autoshare-for-twitter' ); ?></h1>

		<div class="autoshare-settings">
			<div class="settings-wrapper">
				<form action='options.php' method='post'>
					<?php
					settings_fields( AT_GROUP );
					do_settings_sections( 'autoshare-for-twitter' );
					submit_button();
					?>
				</form>
			</div>
			<div class="brand">
				<a href="https://10up.com" class="logo" title="<?php esc_attr_e( '10up', 'autoshare-for-twitter' ); ?>">
					<img src="<?php echo esc_url( trailingslashit( AUTOSHARE_FOR_TWITTER_URL ) . 'assets/images/10up.svg' ); ?>" alt="<?php esc_attr_e( '10up logo', 'autoshare-for-twitter' ); ?>" />
				</a>
				<p>
					<strong>
						<?php echo esc_html__( 'Autopost for X', 'autoshare-for-twitter' ) . ' ' . esc_html__( 'by', 'autoshare-for-twitter' ); ?> <a href="https://10up.com" class="logo" title="<?php esc_attr_e( '10up', 'autoshare-for-twitter' ); ?>"><?php esc_html_e( '10up', 'autoshare-for-twitter' ); ?></a>
					</strong>
				</p>
				<nav>
					<a href="https://github.com/10up/autoshare-for-twitter#faqs" target="_blank" title="<?php esc_attr_e( 'FAQs', 'autoshare-for-twitter' ); ?>">
						<?php esc_html_e( 'FAQs', 'autoshare-for-twitter' ); ?><span class="dashicons dashicons-external"></span>
					</a>
					<a href="https://github.com/10up/autoshare-for-twitter/issues" target="_blank" title="<?php esc_attr_e( 'Support', 'autoshare-for-twitter' ); ?>">
						<?php esc_html_e( 'Support', 'autoshare-for-twitter' ); ?><span class="dashicons dashicons-external"></span>
					</a>
				</nav>
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
	$new_links = [];
	if ( Utils\is_twitter_configured() ) {
		$new_links['settings'] = sprintf(
			/* translators: %s is the plugin setting page URL */
			__( '<a href="%s">Settings</a>', 'autoshare-for-twitter' ),
			esc_url( admin_url( 'options-general.php?page=autoshare-for-twitter' ) )
		);
	} else {
		$new_links['initial-setup'] = sprintf(
			/* translators: %s is the plugin setting page URL */
			__( '<a href="%s">Set up your X/Twitter account</a>', 'autoshare-for-twitter' ),
			esc_url( admin_url( 'options-general.php?page=autoshare-for-twitter' ) )
		);
	}

	return array_merge( $new_links, $links );
}

/**
 * Fire up the module.
 *
 * @uses autoshare_for_twitter_setup
 */
add_action( 'autoshare_for_twitter_setup', __NAMESPACE__ . '\setup' );
