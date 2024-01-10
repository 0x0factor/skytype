<?php

class SiteGuard_Menu_Admin_Filter extends SiteGuard_Base {
	const OPT_NAME_FEATURE = 'admin_filter_enable';
	const OPT_NAME_EXCLUDE = 'admin_filter_exclude_path';

	function __construct( ) {
		$this->render_page( );
	}
	function cvt_camma2ret( $exclude ) {
		$result = str_replace( ' ', '', $exclude );
		return    str_replace( ',', "\r\n", $result );
	}
	function cvt_ret2camma( $exclude ) {
		$result = str_replace( ' ', '', $exclude );
		$result = str_replace( ',', '', $result );
		$result = preg_replace( '/(\r\n){2,}/', "\r\n", $result );
		$result = preg_replace( '/\r\n$/', '', $result );
		$result = str_replace( "\r\n", ',', $result );
		$result = str_replace( "\r",   ',', $result );
		return    str_replace( "\n",   ',', $result );
	}
	function render_page( ) {
		global $siteguard_admin_filter, $siteguard_config;

		$opt_val_feature = $siteguard_config->get( self::OPT_NAME_FEATURE );
		$opt_val_exclude = $this->cvt_camma2ret( $siteguard_config->get( self::OPT_NAME_EXCLUDE ) );
		if ( isset( $_POST['update'] ) && check_admin_referer( 'siteguard-menu-admin-filter-submit' ) ) {
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
				$siteguard_admin_filter->feature_off( );
				$opt_val_feature = '0';
			}
			if ( false === $error && false === $this->is_switch_value( $_POST[ self::OPT_NAME_FEATURE ] ) ) {
				echo '<div class="error settings-error"><p><strong>';
				esc_html_e( 'ERROR: Invalid input value.', 'siteguard' );
				echo '</strong></p></div>';
				$error = true;
			}
			if ( false === $error ) {
				$old_opt_val_feature = $opt_val_feature;
				$old_opt_val_exclude = $opt_val_exclude;
				$opt_val_feature = $_POST[ self::OPT_NAME_FEATURE ];
				$opt_val_exclude = stripslashes( $_POST[ self::OPT_NAME_EXCLUDE ] );
				$siteguard_config->set( self::OPT_NAME_FEATURE, $opt_val_feature );
				$siteguard_config->set( self::OPT_NAME_EXCLUDE, $this->cvt_ret2camma( $opt_val_exclude ) );
				$siteguard_config->update( );
				$result = true;
				if ( '0' === $opt_val_feature ) {
					$result = $siteguard_admin_filter->feature_off( );
				} else {
					$result = $siteguard_admin_filter->feature_on( $_SERVER['REMOTE_ADDR'] );
				}
				if ( true === $result ) {
					$opt_val_exclude = $this->cvt_camma2ret( $opt_val_exclude );
					?>
					<div class="updated"><p><strong><?php esc_html_e( 'Options saved.', 'siteguard' ); ?></strong></p></div>
					<?php
				} else {
					$opt_val_feature = $old_opt_val_feature;
					$opt_val_exclude = $old_opt_val_exclude;
					$siteguard_config->set( self::OPT_NAME_FEATURE, $opt_val_feature );
					$siteguard_config->set( self::OPT_NAME_EXCLUDE, $this->cvt_ret2camma( $opt_val_exclude ) );
					$siteguard_config->update( );
					echo '<div class="error settings-error"><p><strong>';
					esc_html_e( 'ERROR: Failed to .htaccess update.', 'siteguard' );
					echo '</strong></p></div>';
				}
			}
		}

		echo '<div class="wrap">';
		echo '<img src="' . SITEGUARD_URL_PATH . 'images/sg_wp_plugin_logo_40.png" alt="SiteGuard Logo" />';
		echo '<h2>' . esc_html__( 'Admin Page IP Filter', 'siteguard' ) . '</h2>';
		echo '<div class="siteguard-description">'
		. esc_html__( 'You can find docs about this function on ', 'siteguard' )
		. '<a href="' . esc_url( __( 'http://www.jp-secure.com/cont/products/siteguard_wp_plugin/admin_filter_en.html', 'siteguard' ) )
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
			<input type="radio" name="<?php echo self::OPT_NAME_FEATURE ?>" id="<?php echo self::OPT_NAME_FEATURE . '_on' ?>" value="1" <?php checked( $opt_val_feature, '1' ) ?> >
			<label for="<?php echo self::OPT_NAME_FEATURE.'_on' ?>" ><?php echo esc_html_e( 'ON', 'siteguard' ) ?></label>
			</li>
			<li>
			<input type="radio" name="<?php echo self::OPT_NAME_FEATURE ?>" id="<?php echo self::OPT_NAME_FEATURE . '_off' ?>" value="0" <?php checked( $opt_val_feature, '0' ) ?> >
			<label for="<?php echo self::OPT_NAME_FEATURE.'_off' ?>" ><?php echo esc_html_e( 'OFF', 'siteguard' ) ?></label>
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
		<th scope="row"><label for="<?php echo self::OPT_NAME_EXCLUDE ?>"><?php echo esc_html_e( 'Exclude Path', 'siteguard' ) ?></label></th>
		<td><textarea name="<?php echo self::OPT_NAME_EXCLUDE ?>" id="<?php echo self::OPT_NAME_EXCLUDE ?>" cols=40 rows=5 ><?php echo esc_textarea( $opt_val_exclude ) ?></textarea>
		<p class="description"><?php esc_html_e( 'The path of /wp-admin/ henceforth is specified. To specify more than one, separate them with new line. ', 'siteguard' ) ?></p></td>
		</tr>
		</table>
		<input type="hidden" name="update" value="Y">
		<div class="siteguard-description">
		<?php esc_html_e( 'It is the function for the protection against the attack to the management page (under /wp-admin/.) To the access from the connection source IP address which does not login to the management page, 404 (Not Found) is returned. At the login, the connection source IP address is recorded and the access to that page is allowed. The connection source IP address which does not login for more than 24 hours is sequentially deleted. The URL (under /wp-admin/) where this function is excluded can be specified.', 'siteguard' ); ?>
		</div>
		<hr />
		<?php
		wp_nonce_field( 'siteguard-menu-admin-filter-submit' );
		submit_button( );
		?>
		</form>
		</div>

		<?php
	}
}
