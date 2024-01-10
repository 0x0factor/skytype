<?php

require_once( 'siteguard-login-history-table.php' );

class SiteGuard_Menu_Login_History extends SiteGuard_Base {
	protected $wp_list_table;
	function __construct( ) {
		$this->wp_list_table = new SiteGuard_LoginHistory_Table( );
		$this->wp_list_table->prepare_items( );
		$this->render_page( );
	}
	function render_page( ) {
		global $siteguard_config, $siteguard_login_history;
		$img_path = SITEGUARD_URL_PATH . 'images/';
		echo '<div class="wrap">';
		echo '<img src="' . $img_path . 'sg_wp_plugin_logo_40.png" alt="SiteGuard Logo" />';
		echo '<h2>' . esc_html__( 'Login history', 'siteguard' ) . "</h2>\n";
		echo '<div class="siteguard-description">'
		. esc_html__( 'You can find docs about this function on ', 'siteguard' )
		. '<a href="' . esc_url( __( 'http://www.jp-secure.com/cont/products/siteguard_wp_plugin/login_history_en.html', 'siteguard' ) ) . '" target="_blank">' . esc_html__( 'SiteGuard WP Plugin Page', 'siteguard' ) . '</a>' . esc_html__( '.', 'siteguard' ) . '</div>';
		$error = siteguard_check_multisite( );
		if ( is_wp_error( $error ) ) {
			echo '<p class="description">';
			echo $error->get_error_message( );
			echo '</p>';
		}
		?>
		<form name="form1" method="post" action="">
		<?php $this->wp_list_table->display( ) ?>
		<div class="siteguard-description">
		<?php esc_html_e( 'Login history can be referenced. Let\'s see if there are any suspicious history. History, registered 10,000 maximum, will be removed from those old and more than 10,000.', 'siteguard' ) ?>
		</div>
		<input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ) ?>">
		</form>
		</div>
		<?php
	}
}
