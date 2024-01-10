<?php

class SiteGuard_Menu_Same_Error extends SiteGuard_Base {
	const	OPT_NAME_ENABLE = 'same_login_error';

	function __construct( ) {
		$this->render_page( );
	}
	function render_page( ) {
		global $siteguard_config, $siteguard_captcha;

		$opt_val_enable = $siteguard_config->get( self::OPT_NAME_ENABLE );
		if ( isset( $_POST['update'] ) && check_admin_referer( 'siteguard-menu-same-error-submit' ) ) {
			$error = false;
			$errors = siteguard_check_multisite( );
			if ( is_wp_error( $errors ) ) {
				echo '<div class="error settings-error"><p><strong>';
				esc_html_e( $errors->get_error_message( ), 'siteguard' );
				echo '</strong></p></div>';
				$error = true;
			}
			if ( false === $error && '1' === $_POST[ self::OPT_NAME_ENABLE ] ) {
				$ret = $siteguard_captcha->check_requirements( );
				if ( is_wp_error( $ret ) ) {
					echo '<div class="error settings-error"><p><strong>' . $ret->get_error_message( ) . '</strong></p></div>';
					$error = true;
					$siteguard_config->set( self::OPT_NAME_ENABLE, '0' );
					$siteguard_config->update( );
				}
			}
			if ( false === $error && false === $this->is_switch_value( $_POST[ self::OPT_NAME_ENABLE ] ) ) {
				echo '<div class="error settings-error"><p><strong>';
				esc_html_e( 'ERROR: Invalid input value.', 'siteguard' );
				echo '</strong></p></div>';
				$error = true;
			}
			if ( false === $error ) {
				$opt_val_enable   = $_POST[ self::OPT_NAME_ENABLE ];
				$siteguard_config->set( self::OPT_NAME_ENABLE,   $opt_val_enable );
				$siteguard_config->update( );
				?>
				<div class="updated"><p><strong><?php esc_html_e( 'Options saved.', 'siteguard' ); ?></strong></p></div>
				<?php
			}
		}

		echo '<div class="wrap">';
		echo '<img src="' . SITEGUARD_URL_PATH . 'images/sg_wp_plugin_logo_40.png" alt="SiteGuard Logo" />';
		echo '<h2>' . esc_html__( 'Same Login Error Message', 'siteguard' ) . '</h2>';
		echo '<div class="siteguard-description">'
		. esc_html__( 'You can find docs about this function on ', 'siteguard' )
		. '<a href="' . esc_url( __( 'http://www.jp-secure.com/cont/products/siteguard_wp_plugin/same_error_en.html', 'siteguard' ) )
		. '" target="_blank">'
		. esc_html__( 'here', 'siteguard' )
		. '</a>'
		. esc_html__( '.', 'siteguard' )
		. '</div>';
		?>
		<form name="form1" method="post" action="">
		<table class="form-table">
		<tr>
		<th scope="row" colspan="2">
			<ul class="siteguard-radios">
			<li>
			<input type="radio" name="<?php echo self::OPT_NAME_ENABLE ?>" id="<?php echo self::OPT_NAME_ENABLE.'_on' ?>" value="1" <?php checked( $opt_val_enable, '1' ) ?> >
			<label for="<?php echo self::OPT_NAME_ENABLE.'_on' ?>"><?php esc_html_e( 'ON', 'siteguard' ) ?></label>
			</li><li>
			<input type="radio" name="<?php echo self::OPT_NAME_ENABLE ?>" id="<?php echo self::OPT_NAME_ENABLE.'_off' ?>" value="0" <?php checked( $opt_val_enable, '0' ) ?> >
			<label for="<?php echo self::OPT_NAME_ENABLE.'_off' ?>"><?php esc_html_e( 'OFF', 'siteguard' ) ?></label>
			</li>
			</ul>
			<?php
			$error = siteguard_check_multisite( );
			if ( is_wp_error( $error ) ) {
				echo '<p class="description">';
				echo $error->get_error_message( );
				echo '</p>';
			}
			?>
		</th>
		</tr>
		</table>
		<input type="hidden" name="update" value="Y">
		<div class="siteguard-description">
		<?php esc_html_e( 'It is the function to decrease the vulnerability against the attack to examine if a user name exists. All error messages about the login should be equalized. The single error message is displayed even if anyone of a username, password, or CAPTCHA is wrong.', 'siteguard' ) ?>
		</div>
		<hr />

		<?php
		wp_nonce_field( 'siteguard-menu-same-error-submit' );
		submit_button();
		?>
		</form>
		</div>
		<?php
	}
}
