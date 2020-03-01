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
		__( 'Autoshare for Twitter Settings', 'autoshare-for-twitter' ),
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
		__( 'Enable Autoshare for', 'autoshare-for-twitter' ),
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
			'choices' => __( 'Enable Autoshare by default when publishing content', 'autoshare-for-twitter' ),
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

	// Register the credential setting section.
	add_settings_section(
		'autoshare-cred_section',
		__( 'Twitter connection settings', 'autoshare-for-twitter' ),
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
			'name'        => 'api_key',
			'class'       => 'large-text',
			'placeholder' => __( 'paste your API key here', 'autoshare-for-twitter' ),
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
			'name'        => 'api_secret',
			'class'       => 'large-text',
			'placeholder' => __( 'paste your API secret key here', 'autoshare-for-twitter' ),
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
			'name'        => 'access_token',
			'class'       => 'large-text',
			'placeholder' => __( 'paste your Access token secret here', 'autoshare-for-twitter' ),
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
			'name'        => 'access_secret',
			'class'       => 'large-text',
			'placeholder' => __( 'paste your Access token secret here', 'autoshare-for-twitter' ),
		]
	);

	// Twitter Handle.
	add_settings_field(
		'autoshare-twitter_handle',
		__( 'Twitter handle', 'autoshare-for-twitter' ),
		__NAMESPACE__ . '\text_field_cb',
		'autoshare-for-twitter',
		'autoshare-cred_section',
		[
			'name'        => 'twitter_handle',
			'placeholder' => __( 'enter your Twitter handle here', 'autoshare-for-twitter' ),
		]
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

	$options     = get_option( AT_SETTINGS );
	$key         = $args['name'];
	$name        = AT_SETTINGS . "[$key]";
	$value       = $options[ $key ];
	$class       = isset( $args['class'] ) ? $args['class'] : 'regular-text';
	$placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';
	?>
		<input type='text' class="<?php echo esc_attr( $class ); ?>" name=<?php echo esc_attr( $name ); ?> value="<?php echo esc_attr( $value ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>">
	<?php
}

/**
 * Helper for ouputing a radio field.
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
 * Helper for ouputing a checkbox field.
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
 * Helper for ouputing general section heading and description.
 *
 * @return void
 */
function general_section_cb() {
	$cred_class = Utils\is_twitter_configured() ? 'connected' : '';
	?>
	<div class="general-settings <?php echo esc_attr( $cred_class ); ?>">
		<h2><?php echo esc_html__( 'Twitter Settings', 'autoshare-for-twitter' ); ?>
	</div>
	<?php
}

/**
 * Helper for ouputing credentials section.
 *
 * @return void
 */
function cred_section_cb() {
	?>
	<section class="credentials-setup <?php echo Utils\is_twitter_configured() ? 'connected' : ''; ?>">
		<?php if ( Utils\is_twitter_configured() ) : ?>
		<p class="credentials-actions">
			<a href="JavaScript:void(0);" class="open">
				<?php echo esc_html__( 'Open connection settings', 'autoshare-for-twitter' ); ?><span class="dashicons dashicons-arrow-down-alt2"></span>
			</a>
			<a href="JavaScript:void(0);" class="close">
				<?php echo esc_html__( 'Close connection settings', 'autoshare-for-twitter' ); ?><span class="dashicons dashicons-arrow-up-alt2"></span>
			</a>
		</p>
		<?php endif; ?>
		<section class="credentials-instructions">
			<h4><a href="https://developer.twitter.com/en/apply/user.html" target="_blank"><?php echo esc_html__( '1. Apply for a Twitter developer account', 'autoshare-for-twitter' ); ?></a></h4>
			<h4><?php esc_html_e( '2. Create a Twitter app for your website', 'autoshare-for-twitter' ); ?></h4>
			<ul>
				<li><?php echo wp_kses_data( __( 'Click the <code>Create an app</code> button on the', 'autoshare-for-twitter' ) ); ?> <a href="https://developer.twitter.com/en/apps" title="<?php esc_html_e( 'Twitter develop apps page.', 'autoshare-for-twitter' ); ?>"><?php esc_html_e( 'Twitter developer apps page.', 'autoshare-for-twitter' ); ?></a></li>
				<li><?php echo wp_kses_data( __( 'Fill out the <code>App name</code> and <code>Application description</code> fields.', 'autoshare-for-twitter' ) ); ?></li>
				<li><?php echo wp_kses_data( __( 'Set the <code>Website URL</code> and <code>Callback URLs</code> fields to https://yourdomain.yourdomainextension.', 'autoshare-for-twitter' ) ); ?></li>
				<li><?php echo wp_kses_data( __( 'Fill out the <code>Tell us how this app will be used</code> field, no other fields or URLs are required or necessary.', 'autoshare-for-twitter' ) ); ?></li>
			</ul>
			<h4><?php esc_html_e( '3. Configure access to your Twitter app API keys', 'autoshare-for-twitter' ); ?></h4>
			<ul>
				<li><?php echo wp_kses_data( __( 'Click on the <code>Keys and tokens</code> tab within your newly created Twitter developer app.', 'autoshare-for-twitter' ) ); ?></li>
				<li><?php echo wp_kses_data( __( 'Copy the <code>API key</code> and <code>API secret key</code> values from your Twitter app <code>Consumer API keys</code> section and paste them below.', 'autoshare-for-twitter' ) ); ?></li>
			</ul>
			<h4><?php esc_html_e( '4. Configure access to your Twitter app access tokens', 'autoshare-for-twitter' ); ?></h4>
			<ul>
				<li><?php echo wp_kses_data( __( 'Click on the <code>Generate</code> button from your Twitter app <code>Access token & access token secret</code> section.', 'autoshare-for-twitter' ) ); ?></li>
				<li><?php echo wp_kses_data( __( 'Copy the <code>Access token</code> and <code>Access token secret</code> values and paste them below.', 'autoshare-for-twitter' ) ); ?></li>
			</ul>
			<h4><?php esc_html_e( '5. Confirm Twitter handle', 'autoshare-for-twitter' ); ?></h4>
			<ul>
				<li><?php esc_html_e( 'Fill out your Twitter handle that will be used to tweet your posts, pages, etc.', 'autoshare-for-twitter' ); ?></li>
			</ul>
			<h4><?php esc_html_e( '6. Connect your Twitter developer app with this site', 'autoshare-for-twitter' ); ?></h4>
			<ul>
				<li><?php echo wp_kses_data( __( 'Click the <code>Save Changes</code> button below.', 'autoshare-for-twitter' ) ); ?></li>
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
		<h1><?php esc_html_e( 'Autoshare for Twitter Settings', 'autoshare-for-twitter' ); ?></h1>

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
				<nav>
					<a href="https://github.com/10up/autoshare-for-twitter#faqs" target="_blank" title="<?php echo esc_attr__( 'FAQs', 'autoshare-for-twitter' ); ?>">
						<?php echo esc_html__( 'FAQs', 'autoshare-for-twitter' ); ?><span class="dashicons dashicons-external"></span>
					</a>
					<a href="https://github.com/10up/autoshare-for-twitter/issues" target="_blank" title="<?php echo esc_attr__( 'Support', 'autoshare-for-twitter' ); ?>">
						<?php echo esc_html__( 'Support', 'autoshare-for-twitter' ); ?><span class="dashicons dashicons-external"></span>
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
	if ( Utils\is_twitter_configured() ) {
		$links['settings'] = sprintf(
			/* translators: %s is the plugin setting page URL */
			__( '<a href="%s">Settings</a>', 'autoshare-for-twitter' ),
			esc_url( admin_url( 'options-general.php?page=autoshare-for-twitter' ) )
		);
	} else {
		$links['initial-setup'] = sprintf(
			/* translators: %s is the plugin setting page URL */
			__( '<a href="%s">Set up your Twitter account</a>', 'autoshare-for-twitter' ),
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
