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
 * Helper for outputting general section heading and description.
 *
 * @return void
 */
function general_section_cb() {
	$cred_class = Utils\is_twitter_configured() ? 'connected' : '';
	?>
	<div class="general-settings <?php echo esc_attr( $cred_class ); ?>">
		<h2><?php esc_html_e( 'Twitter Settings', 'autoshare-for-twitter' ); ?>
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
			<h4><a href="https://developer.twitter.com/en/portal/petition/essential/basic-info" target="_blank"><?php esc_html_e( '1. Sign up for a Twitter developer account', 'autoshare-for-twitter' ); ?></a></h4>
			<p><?php echo wp_kses_data( 'Once you complete Sign up onboarding process, you will get your API Keys. Copy the <code>API key</code> and <code>API secret key</code> values and paste them below. In case you skipped Onboarding process, you can create project and app by following step 2, otherwise please skip step 2.', 'autoshare-for-twitter' ); ?></p>

			<h4><?php esc_html_e( '2. Create a Project and create an App for your website', 'autoshare-for-twitter' ); ?></h4>
			<ul>
				<li><?php echo wp_kses_data( __( 'Click the <code>+ New Project</code> button in the', 'autoshare-for-twitter' ) ); ?> <a href="https://developer.twitter.com/en/portal/dashboard" title="<?php esc_html_e( 'Twitter developer portal', 'autoshare-for-twitter' ); ?>" target="_blank"><?php esc_html_e( 'Twitter developer portal.', 'autoshare-for-twitter' ); ?></a></li>
				<li><?php echo wp_kses_data( __( 'Fill out the project name, select the appropriate use-case, provide a project description to create project.', 'autoshare-for-twitter' ) ); ?></li>
				<li><?php echo wp_kses_data( __( 'Next, you can either create a new App or connect an existing App. Click <code>create a new App instead</code> in order to create a new App.', 'autoshare-for-twitter' ) ); ?></li>
				<li><?php echo wp_kses_data( __( 'Fill out the App name and Once you click complete, you will get your API Keys. Copy the <code>API key</code> and <code>API secret key</code> values and paste them below. you can Regenerate these keys any time from Twitter developer portal', 'autoshare-for-twitter' ) ); ?></li>
			</ul>

			<h4><?php esc_html_e( '3. Configure access to your Twitter app access tokens', 'autoshare-for-twitter' ); ?></h4>
			<ul>
				<li>
					<?php
					printf(
						/* translators: Placeholders %1$s - opening HTML <a> link tag, closing HTML </a> link tag */
						wp_kses_data( __( 'Go to the %1$sTwitter developer portal%2$s', 'autoshare-for-twitter' ) ),
						'<a href="https://developer.twitter.com/en/portal/dashboard" target="_blank">',
						'</a>'
					);
					?>
				</li>
				<li><?php esc_html_e( 'Click on Projects & Apps on the left navigation', 'autoshare-for-twitter' ); ?></li>
				<li><?php esc_html_e( 'Find the App and click on it to show the Settings page for the App', 'autoshare-for-twitter' ); ?></li>
				<li><?php esc_html_e( 'Click "Setup" under User authentication settings to setup Authentication', 'autoshare-for-twitter' ); ?></li>
				<li><?php echo wp_kses_data( 'Enable <code>OAuth 1.0a</code> and Set App permissions to "<strong>Read and write</strong>".', 'autoshare-for-twitter' ); ?></li>
				<li><?php echo wp_kses_data( 'Set the <code>Website URL</code> and <code>Callback URLs</code> fields to https://yourdomain.yourdomainextension and Click `Save`.', 'autoshare-for-twitter' ); ?></li>
				<li><?php esc_html_e( 'Switch from the "Settings" tab to the "Keys and tokens" tab', 'autoshare-for-twitter' ); ?></li>
				<li><?php echo wp_kses_data( __( 'Click on the <code>Generate</code> button of <code>Access Token and Secret</code> section.', 'autoshare-for-twitter' ) ); ?></li>
				<li><?php echo wp_kses_data( __( 'Copy the <code>Access token</code> and <code>Access token secret</code> values and paste them below.', 'autoshare-for-twitter' ) ); ?></li>
			</ul>

			<h4><?php esc_html_e( '4. Apply for Elevated access.', 'autoshare-for-twitter' ); ?></h4>
			<p><?php esc_html_e( 'Twitter has introduced few access levels with Twitter API v2 release, which provides quick onboarding to Essential access and you can apply additional access like Elevated, Elevated+ and Academic Research access. Plugin requires Elevated access to work. So, you can apply for Elevated access by following below steps.', 'autoshare-for-twitter' ); ?>
			<a href="https://developer.twitter.com/en/docs/twitter-api/getting-started/about-twitter-api#Access" target="_blank"><?php esc_html_e( 'More information.', 'autoshare-for-twitter' ); ?></a></p>
			<ul>
				<li>
					<?php
					printf(
						/* translators: Placeholders %1$s - opening HTML <a> link tag, closing HTML </a> link tag */
						esc_html__( 'Go to Project Page, Click on the %1$sApply for Elevated%2$s button.', 'autoshare-for-twitter' ),
						'<a href="https://developer.twitter.com/en/portal/petition/standard/basic-info" target="_blank">',
						'</a>'
					);
					?>
				</li>
				<li><?php echo wp_kses_data( __( 'Fill out the Basic info and click <code>Next</code>', 'autoshare-for-twitter' ) ); ?></li>
				<li><?php echo wp_kses_data( __( 'Fill out the <code>In your words</code> field, in <code>How will you use the Twitter API or Twitter Data?</code> section. You can find example response below.', 'autoshare-for-twitter' ) ); ?>
				</li>
				<div class="copy-container-wrap">
					<p class="copy-container">
						<span class="copy-content"><?php esc_html_e( 'I have WordPress website and I am planning to add auto Tweet functionality to my website. This will be used inside the WordPress plugin that auto Tweet from my website whenever I publish a blog post or page.', 'autoshare-for-twitter' ); ?></span>
						<a href="#" class="astCopyToClipCard"><span class="dashicons dashicons-clipboard"></span></a>
					</p>
				</div>				
				<li><?php echo wp_kses_data( __( 'Choose <strong>No</strong> for <code>Are you planning to analyze Twitter data?</code>', 'autoshare-for-twitter' ) ); ?></li>
				<li><?php echo wp_kses_data( __( 'Choose <strong>Yes</strong> for <code>Will your App use Tweet, Retweet, Like, Follow, or Direct Message functionality?</code> and fill out the <code>Please describe your planned use of these features.</code> field. You can find example response below.', 'autoshare-for-twitter' ) ); ?></li>
				<div class="copy-container-wrap">
					<p class="copy-container">
						<span class="copy-content"><?php esc_html_e( 'Yes, The app will use Tweet functionality to Publish a tweet on Twitter whenever I publish a new post or article on my website.', 'autoshare-for-twitter' ); ?></span>
						<a href="#" class="astCopyToClipCard"><span class="dashicons dashicons-clipboard"></span></a>
					</p>
				</div>
				<li><?php echo wp_kses_data( __( 'Choose <strong>No</strong> for <code>Do you plan to display Tweets or aggregate data about Twitter content outside Twitter?</code>', 'autoshare-for-twitter' ) ); ?></li>
				<li><?php echo wp_kses_data( __( 'Choose <strong>No</strong> for <code>Will your product, service, or analysis make Twitter content or derived information available to a government entity?</code>', 'autoshare-for-twitter' ) ); ?></li>
				<li><?php esc_html_e( 'Review and accept the developer agreement and submit.' ); ?></li>
				<li><?php esc_html_e( 'You will be notified with updates about your application on your email address.' ); ?></li>
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
				<a href="https://10up.com" class="logo" title="<?php esc_attr_e( '10up', 'autoshare-for-twitter' ); ?>">
					<img src="<?php echo esc_url( trailingslashit( AUTOSHARE_FOR_TWITTER_URL ) . 'assets/images/10up.svg' ); ?>" alt="<?php esc_attr_e( '10up logo', 'autoshare-for-twitter' ); ?>" />
				</a>
				<p>
					<strong>
						<?php echo esc_html__( 'Autoshare for Twitter', 'autoshare-for-twitter' ) . ' ' . esc_html__( 'by', 'autoshare-for-twitter' ); ?> <a href="https://10up.com" class="logo" title="<?php esc_attr_e( '10up', 'autoshare-for-twitter' ); ?>"><?php esc_html_e( '10up', 'autoshare-for-twitter' ); ?></a>
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
			__( '<a href="%s">Set up your Twitter account</a>', 'autoshare-for-twitter' ),
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
