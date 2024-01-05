<?php
/**
 * Class for displaying the list of connected twitter accounts.
 *
 * @package TenUp\AutoshareForTwitter\List_Table
 */

namespace TenUp\AutoshareForTwitter\List_Table;

use TenUp\AutoshareForTwitter\Core\Twitter_Accounts as Twitter_Accounts;

if ( ! class_exists( '\\WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}


/**
 * Class for displaying the list of connected twitter accounts.
 *
 * @since 2.1.0
 *
 * @see Users_List_Table
 */
class Twitter_Accounts_List_Table extends \WP_List_Table {

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => 'twitter_account',
				'plural'   => 'twitter_accounts',
				'ajax'     => false,
			)
		);
	}

	/**
	 * Gets the list of columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'account'            => __( 'X/Twitter account', 'autoshare-for-twitter' ),
			'autoshare_accounts' => __( 'Autopost by default', 'autoshare-for-twitter' ),
			'action'             => __( 'Action', 'autoshare-for-twitter' ),
		);
	}

	/**
	 * Prepares the list of items for displaying.
	 */
	public function prepare_items() {
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$accounts    = new Twitter_Accounts();
		$this->items = $accounts->get_twitter_accounts( true );
	}

	/**
	 * Handles the account column output.
	 *
	 * @param array $item The current Twitter account item.
	 */
	public function column_account( $item ) {
		printf(
			'<img src="%1$s" alt="%2$s" class="twitter-account-profile-image" /><div class="account-details"><strong>%3$s</strong><br />%4$s</div>',
			esc_url( $item['profile_image_url'] ),
			esc_attr( $item['name'] ),
			'@' . esc_attr( $item['username'] ),
			esc_html( $item['name'] ),
		);
	}

	/**
	 * Handles the "Autoshare by default" column output.
	 *
	 * @param array $item The current Twitter account item.
	 */
	public function column_autoshare_accounts( $item ) {
		$settings_key = 'autoshare-for-twitter';
		$options      = get_option( $settings_key, array() );
		$accounts     = $options['autoshare_accounts'] ?? array();
		$account_id   = $item['id'];
		$name         = $settings_key . '[autoshare_accounts][]';

		printf(
			'<input type="checkbox" name="%1$s" value="%2$s" %3$s/>',
			esc_attr( $name ),
			esc_attr( $account_id ),
			checked( true, in_array( $account_id, $accounts, true ), false )
		);
	}

	/**
	 * Handles the action column output.
	 *
	 * @param array $item The current Twitter account item.
	 */
	public function column_action( $item ) {
		$disconnect_url = wp_nonce_url( add_query_arg( 'account_id', $item['id'], admin_url( 'admin-post.php?action=autoshare_twitter_disconnect_action' ) ), 'autoshare_twitter_disconnect_action', 'autoshare_twitter_disconnect_nonce' );
		printf(
			'<a class="button button-secondary" href="%1$s">%2$s</a>',
			esc_url( $disconnect_url ),
			esc_html__( 'Disconnect', 'autoshare-for-twitter' ),
		);
	}

	/**
	 * Handles the default column output.
	 *
	 * @param array  $item        The current Twitter account item.
	 * @param string $column_name The current column name.
	 */
	public function column_default( $item, $column_name ) {
		return $item[ $column_name ] ?? '';
	}

	/**
	 * The HTML to display when there are no connected accounts.
	 *
	 * @see WP_List_Table::no_items()
	 */
	public function no_items() {
		?>
		<p><?php esc_html_e( 'No X/Twitter accounts are connected. Please connect at least one X/Twitter account to continue using Autopost for X.', 'autoshare-for-twitter' ); ?></p>
		<?php
	}

	/**
	 * Add Connect another account button to the bottom of the table.
	 *
	 * @param string $which The location of the bulk actions: 'top' or 'bottom'.
	 */
	protected function display_tablenav( $which ) {
		if ( 'top' === $which ) {
			return;
		}

		$connect_url = wp_nonce_url( admin_url( 'admin-post.php?action=autoshare_twitter_authorize_action' ), 'autoshare_twitter_authorize_action', 'autoshare_twitter_authorize_nonce' );
		?>
		<div class="tablenav <?php echo esc_attr( $which ); ?>">
			<?php if ( 'bottom' === $which ) : ?>
				<div class="alignleft">
					<a href="<?php echo esc_url( $connect_url ); ?>" class="button button-secondary">
						<?php echo ( ! empty( $this->items ) ? esc_attr__( 'Connect another account', 'autoshare-for-twitter' ) : esc_attr__( 'Connect X/Twitter account', 'autoshare-for-twitter' ) ); ?>
					</a>
				</div>
			<?php endif; ?>
			<?php
			$this->extra_tablenav( $which );
			$this->pagination( $which );
			?>
			<br class="clear" />
		</div>
		<?php
	}
}
