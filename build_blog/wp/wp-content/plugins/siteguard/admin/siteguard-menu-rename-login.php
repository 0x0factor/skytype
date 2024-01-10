<?php

class SiteGuard_Menu_Rename_Login extends SiteGuard_Base {
	const	OPT_NAME_FEATURE           = 'renamelogin_enable';
	const	OPT_NAME_RENAME_LOGIN_PATH = 'renamelogin_path';

	function __construct( ) {
		$this->render_page( );
	}
	function render_page( ) {
		global $siteguard_rename_login, $siteguard_config;

		$opt_val_feature           = $siteguard_config->get( self::OPT_NAME_FEATURE );
		$opt_val_rename_login_path = $siteguard_config->get( self::OPT_NAME_RENAME_LOGIN_PATH );
		if ( isset( $_POST['update'] ) && check_admin_referer( 'siteguard-menu-rename-login-submit' ) ) {
			$error = false;
			$errors = siteguard_check_multisite( );
			if ( is_wp_error( $errors ) ) {
				echo '<div class="error settings-error"><p><strong>';
				esc_html_e( $errors->get_error_message( ), 'siteguard' );
				echo '</strong></p></div>';
				$error = true;
			}
			if ( false === $error && '1' === $_POST[ self::OPT_NAME_FEATURE ] && false === $this->check_module( 'rewrite' ) ) {
				echo '<div class="error settings-error"><p><strong>';
				esc_html_e( 'To use this function, “mod_rewrite” should be loaded on Apache.', 'siteguard' );
				echo '</strong></p></div>';
				$error = true;
				$siteguard_config->set( self::OPT_NAME_FEATURE, '0' );
				$siteguard_config->update( );
				$siteguard_rename_login->feature_off( );
				$opt_val_feature = '0';
			}
			if ( false === $error && false === $this->is_switch_value( $_POST[ self::OPT_NAME_FEATURE ] ) ) {
				echo '<div class="error settings-error"><p><strong>';
				esc_html_e( 'ERROR: Invalid input value.', 'siteguard' );
				echo '</strong></p></div>';
				$error = true;
			}
			if ( false === $error && '1' === $_POST[ self::OPT_NAME_FEATURE ] ) {
				$incompatible_plugins = $siteguard_rename_login->get_active_incompatible_plugins( );
				if ( null !== $incompatible_plugins ) {
					echo '<div class="error settings-error"><p><strong>';
					echo esc_html__( 'This function and Plugin "', 'siteguard' ) . esc_html__( implode( ', ', $incompatible_plugins ) ) . esc_html__( '" cannot be used at the same time.', 'siteguard' );
					echo '</strong></p></div>';
					$error = true;
					$siteguard_config->set( self::OPT_NAME_FEATURE, '0' );
					$siteguard_config->update( );
					$siteguard_rename_login->feature_off( );
					$opt_val_feature = '0';
					$opt_val_rename_login_path = stripslashes( $_POST[ self::OPT_NAME_RENAME_LOGIN_PATH ] );
				}
			}
			if ( false === $error && 1 != preg_match( '/^[a-zA-Z0-9_-]+$/', $_POST[ self::OPT_NAME_RENAME_LOGIN_PATH ] ) ) {
				echo '<div class="error settings-error"><p><strong>';
				esc_html_e( 'It is only an alphanumeric character, a hyphen, and an underbar that can be used for New Login Path.', 'siteguard' );
				echo '</strong></p></div>';
				$opt_val_rename_login_path = stripslashes( $_POST[ self::OPT_NAME_RENAME_LOGIN_PATH ] );
				$error = true;
			}
			if ( false === $error && 1 === preg_match( '/^(wp-admin|wp-content|wp-includes|wp-login$|login$)/', $_POST[ self::OPT_NAME_RENAME_LOGIN_PATH ], $matches ) ) {
				echo '<div class="error settings-error"><p><strong>';
				echo esc_html( $matches[0] ) . esc_html__( ' can not be used for New Login Path.', 'siteguard' );
				echo '</strong></p></div>';
				$opt_val_rename_login_path = stripslashes( $_POST[ self::OPT_NAME_RENAME_LOGIN_PATH ] );
				$error = true;
			}
			if ( false === $error ) {
				$old_opt_val_feature           = $opt_val_feature;
				$old_opt_val_rename_login_path = $opt_val_rename_login_path;
				$opt_val_feature           = $_POST[ self::OPT_NAME_FEATURE ];
				$opt_val_rename_login_path = $_POST[ self::OPT_NAME_RENAME_LOGIN_PATH ];
				$siteguard_config->set( self::OPT_NAME_FEATURE,           $opt_val_feature );
				$siteguard_config->set( self::OPT_NAME_RENAME_LOGIN_PATH, $opt_val_rename_login_path );
				$siteguard_config->update( );
				$result = true;
				if ( '0' === $opt_val_feature ) {
					$result = $siteguard_rename_login->feature_off( );
				} else {
					$result = $siteguard_rename_login->feature_on( );
					if ( true === $result ) {
						$siteguard_rename_login->send_notify( );
					}
				}
				if ( true === $result ) {
					?>
					<div class="updated"><p><strong><?php esc_html_e( 'Options saved.', 'siteguard' ); ?></strong></p></div>
					<?php
				} else {
					$siteguard_config->set( self::OPT_NAME_FEATURE,           $old_opt_val_feature );
					$siteguard_config->set( self::OPT_NAME_RENAME_LOGIN_PATH, $old_opt_val_rename_login_path );
					$siteguard_config->update( );
					$opt_val_feature               = $old_opt_val_feature;
					$opt_val_val_rename_login_path = $old_opt_val_rename_login_path;
					echo '<div class="error settings-error"><p><strong>';
					esc_html_e( 'ERROR: Failed to .htaccess update.', 'siteguard' );
					echo '</strong></p></div>';
				}
			}
		}

		echo '<div class="wrap">';
		echo '<img src="' . SITEGUARD_URL_PATH . 'images/sg_wp_plugin_logo_40.png" alt="SiteGuard Logo" />';
		echo '<h2>' . esc_html__( 'Rename Login', 'siteguard' ) . '</h2>';
		echo '<div class="siteguard-description">'
		. esc_html__( 'You can find docs about this function on ', 'siteguard' )
		. '<a href="' . esc_url( __( 'http://www.jp-secure.com/cont/products/siteguard_wp_plugin/rename_login_en.html', 'siteguard' ) )
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
			<input type="radio" name="<?php echo self::OPT_NAME_FEATURE ?>" id="<?php echo self::OPT_NAME_FEATURE.'_on' ?>" value="1" <?php checked( $opt_val_feature, '1' ) ?> >
			<label for="<?php echo self::OPT_NAME_FEATURE.'_on' ?>"><?php echo esc_html_e( 'ON', 'siteguard' ) ?></label>
			</li><li>
			<input type="radio" name="<?php echo self::OPT_NAME_FEATURE ?>" id="<?php echo self::OPT_NAME_FEATURE.'_off' ?>" value="0" <?php checked( $opt_val_feature, '0' ) ?> >
			<label for="<?php echo self::OPT_NAME_FEATURE.'_off' ?>"><?php echo esc_html_e( 'OFF', 'siteguard' ) ?></label>
			</li>
			</ul>
			<?php
			$error = siteguard_check_multisite( );
			if ( is_wp_error( $error ) ) {
				echo '<p class="description">';
				echo $error->get_error_message( );
				echo '</p>';
			}
			echo '<p class="description">';
			esc_html_e( 'To use this function, “mod_rewrite” should be loaded on Apache.', 'siteguard' );
			echo '</p>';
			?>
		</th>
		</tr><tr>
		<th scope="row"><label for="<?php echo self::OPT_NAME_RENAME_LOGIN_PATH ?>"><?php esc_html_e( 'New Login Path', 'siteguard' ); ?></label></th>
		<td>
			<?php echo site_url() . '/' ?><input type="text" name="<?php echo self::OPT_NAME_RENAME_LOGIN_PATH ?>" id="<?php echo self::OPT_NAME_RENAME_LOGIN_PATH ?>" value="<?php echo esc_attr( $opt_val_rename_login_path ) ?>" >
			<?php
			echo '<p class="description">';
			esc_html_e( 'An alphanumeric character, a hyphen, and an underbar can be used.', 'siteguard' );
			echo '</p>';
			?>
		</td>
		</tr>
		</table>
		<input type="hidden" name="update" value="Y">
		<div class="siteguard-description">
		<?php esc_html_e( 'It is the function to decrease the vulnerability against an illegal login attempt attack such as a brute force attack or a password list attack. The login page name (wp-login.php) is changed. The initial value is “login_&lt;5 random digits&gt;” but it can be changed to a favorite name.', 'siteguard' ) ?>
		</div>
		<hr />
		<?php
		wp_nonce_field( 'siteguard-menu-rename-login-submit' );
		submit_button( );
		?>
		</form>
		</div>
		<?php
	}
}
