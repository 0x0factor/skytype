<?php

class SiteGuard_Menu_Login_Lock extends SiteGuard_Base {
	const	OPT_NAME_ENABLE    = 'loginlock_enable';
	const	OPT_NAME_INTERVAL  = 'loginlock_interval';
	const	OPT_NAME_THRESHOLD = 'loginlock_threshold';
	const	OPT_NAME_LOCKSEC   = 'loginlock_locksec';

	function __construct( ) {
		$this->render_page( );
	}
	function is_interval_value( $value ) {
		$items = array( '1', '5', '30' );
		if ( in_array( $value, $items ) ) {
			return true;
		}
		return false;
	}
	function is_threshold_value( $value ) {
		$items = array( '3', '10', '100' );
		if ( in_array( $value, $items ) ) {
			return true;
		}
		return false;
	}
	function is_locksec_value( $value ) {
		$items = array( '30', '60', '300' );
		if ( in_array( $value, $items ) ) {
			return true;
		}
		return false;
	}
	function render_page( ) {
		global $siteguard_config;

		$opt_val_enable    = $siteguard_config->get( self::OPT_NAME_ENABLE );
		$opt_val_interval  = $siteguard_config->get( self::OPT_NAME_INTERVAL );
		$opt_val_threshold = $siteguard_config->get( self::OPT_NAME_THRESHOLD );
		$opt_val_locksec   = $siteguard_config->get( self::OPT_NAME_LOCKSEC );
		if ( isset( $_POST['update'] ) && check_admin_referer( 'siteguard-menu-login-lock-submit' ) ) {
			$error = false;
			$errors = siteguard_check_multisite( );
			if ( is_wp_error( $errors ) ) {
				echo '<div class="error settings-error"><p><strong>';
				esc_html_e( $errors->get_error_message( ), 'siteguard' );
				echo '</strong></p></div>';
				$error = true;
			}
			if ( ( false === $error )
			  && ( ( false === $this->is_switch_value( $_POST[ self::OPT_NAME_ENABLE ] ) )
			    || ( false === $this->is_interval_value( $_POST[ self::OPT_NAME_INTERVAL ] ) )
			    || ( false === $this->is_threshold_value( $_POST[ self::OPT_NAME_THRESHOLD ] ) )
			    || ( false === $this->is_locksec_value( $_POST[ self::OPT_NAME_LOCKSEC ] ) ) ) ) {
				echo '<div class="error settings-error"><p><strong>';
				esc_html_e( 'ERROR: Invalid input value.', 'siteguard' );
				echo '</strong></p></div>';
				$error = true;
			}
			if ( false === $error ) {
				$opt_val_enable    = $_POST[ self::OPT_NAME_ENABLE ];
				$opt_val_interval  = $_POST[ self::OPT_NAME_INTERVAL ];
				$opt_val_threshold = $_POST[ self::OPT_NAME_THRESHOLD ];
				$opt_val_locksec   = $_POST[ self::OPT_NAME_LOCKSEC ];
				$siteguard_config->set( self::OPT_NAME_ENABLE,    $opt_val_enable );
				$siteguard_config->set( self::OPT_NAME_INTERVAL,  $opt_val_interval );
				$siteguard_config->set( self::OPT_NAME_THRESHOLD, $opt_val_threshold );
				$siteguard_config->set( self::OPT_NAME_LOCKSEC,   $opt_val_locksec );
				$siteguard_config->update( );
				?>
				<div class="updated"><p><strong><?php esc_html_e( 'Options saved.', 'siteguard' ); ?></strong></p></div>
				<?php
			}
		}

		echo '<div class="wrap">';
		echo '<img src="' . SITEGUARD_URL_PATH . 'images/sg_wp_plugin_logo_40.png" alt="SiteGuard Logo" />';
		echo '<h2>' . esc_html__( 'Login Lock', 'siteguard' ) . '</h2>';
		echo '<div class="siteguard-description">'
		. esc_html__( 'You can find docs about this function on ', 'siteguard' )
		. '<a href="' . esc_url( __( 'http://www.jp-secure.com/cont/products/siteguard_wp_plugin/login_lock_en.html', 'siteguard' ) )
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
		</tr><tr>
		<th scope="row"><?php esc_html_e( 'Interval', 'siteguard' ); ?></th>
			<td>
				<input type="radio" name="<?php echo self::OPT_NAME_INTERVAL ?>" id="<?php echo self::OPT_NAME_INTERVAL.'_1' ?>" value="1" <?php checked( $opt_val_interval, '1' ) ?> >
				<label for="<?php echo self::OPT_NAME_INTERVAL.'_1' ?>"><?php esc_html_e( '1 second', 'siteguard' ) ?></label>
				<br />
				<input type="radio" name="<?php echo self::OPT_NAME_INTERVAL ?>" id="<?php echo self::OPT_NAME_INTERVAL.'_5' ?>" value="5" <?php checked( $opt_val_interval, '5' ) ?> >
				<label for="<?php echo self::OPT_NAME_INTERVAL.'_5' ?>"><?php esc_html_e( '5 seconds', 'siteguard' ) ?></label>
				<br />
				<input type="radio" name="<?php echo self::OPT_NAME_INTERVAL ?>" id="<?php echo self::OPT_NAME_INTERVAL.'_30' ?>" value="30" <?php checked( $opt_val_interval, '30' ) ?> >
				<label for="<?php echo self::OPT_NAME_INTERVAL.'_30' ?>"><?php esc_html_e( '30 seconds', 'siteguard' ) ?></label>
			</td>
		</tr><tr>
		<th scope="row"><?php esc_html_e( 'Threshold', 'siteguard' ); ?></th>
			<td>
				<input type="radio" name="<?php echo self::OPT_NAME_THRESHOLD ?>" id="<?php echo self::OPT_NAME_THRESHOLD.'_3' ?>" value="3" <?php checked( $opt_val_threshold, '3' ) ?> >
				<label for="<?php echo self::OPT_NAME_THRESHOLD.'_3' ?>"><?php esc_html_e( '3 times', 'siteguard' ) ?></label>
				<br />
				<input type="radio" name="<?php echo self::OPT_NAME_THRESHOLD ?>" id="<?php echo self::OPT_NAME_THRESHOLD.'_10' ?>" value="10" <?php checked( $opt_val_threshold, '10' ) ?> >
				<label for="<?php echo self::OPT_NAME_THRESHOLD.'_10' ?>"><?php esc_html_e( '10 times', 'siteguard' ) ?></label>
				<br />
				<input type="radio" name="<?php echo self::OPT_NAME_THRESHOLD ?>" id="<?php echo self::OPT_NAME_THRESHOLD.'_100' ?>" value="100" <?php checked( $opt_val_threshold, '100' ) ?> >
				<label for="<?php echo self::OPT_NAME_THRESHOLD.'_100' ?>"><?php esc_html_e( '100 times', 'siteguard' ) ?></label>
			</td>
		</tr><tr>
		<th scope="row"><?php esc_html_e( 'Lock Time', 'siteguard' ); ?></th>
			<td>
				<input type="radio" name="<?php echo self::OPT_NAME_LOCKSEC ?>" id="<?php echo self::OPT_NAME_LOCKSEC.'_30' ?>" value="30" <?php checked( $opt_val_locksec, '30' ) ?> >
				<label for="<?php echo self::OPT_NAME_LOCKSEC.'_30' ?>"><?php esc_html_e( '30 seconds', 'siteguard' ) ?></label>
				<br />
				<input type="radio" name="<?php echo self::OPT_NAME_LOCKSEC ?>" id="<?php echo self::OPT_NAME_LOCKSEC.'_60' ?>" value="60" <?php checked( $opt_val_locksec, '60' ) ?> >
				<label for="<?php echo self::OPT_NAME_LOCKSEC.'_60' ?>"><?php esc_html_e( '1 minute', 'siteguard' ) ?></label>
				<br />
				<input type="radio" name="<?php echo self::OPT_NAME_LOCKSEC ?>" id="<?php echo self::OPT_NAME_LOCKSEC.'_300' ?>" value="300" <?php checked( $opt_val_locksec, '300' ) ?> >
				<label for="<?php echo self::OPT_NAME_LOCKSEC.'_300' ?>"><?php esc_html_e( '5 minutes', 'siteguard' ) ?></label>
			</td>
		</tr>
		</table>
		<div class="siteguard-description">
		<?php esc_html_e( 'It is the function to decrease the vulnerability against an illegal login attempt attack such as a brute force attack or a password list attack. Especially, it is the function to prevent an automated attack. The connection source IP address the number of login failure of which reaches the specified number within the specified period is blocked for the specified time. Each user account is not locked.', 'siteguard' ) ?>
		</div>
		<hr />
		<input type="hidden" name="update" value="Y">

		<?php
		wp_nonce_field( 'siteguard-menu-login-lock-submit' );
		submit_button( );
		?>

		</form>
		</div>

		<?php
	}
}
