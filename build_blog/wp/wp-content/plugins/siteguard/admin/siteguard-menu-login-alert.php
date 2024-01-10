<?php

class SiteGuard_Menu_Login_Alert extends SiteGuard_Base {
	const	OPT_NAME_FEATURE  = 'loginalert_enable';
	const	OPT_NAME_SUBJECT  = 'loginalert_subject';
	const	OPT_NAME_BODY     = 'loginalert_body';
	const	OPT_NAME_ADMIN    = 'loginalert_admin_only';

	function __construct( ) {
		$this->render_page( );
	}
	function render_page( ) {
		global $siteguard_config;

		$opt_val_feature   = $siteguard_config->get( self::OPT_NAME_FEATURE );
		$opt_val_subject   = $siteguard_config->get( self::OPT_NAME_SUBJECT );
		$opt_val_body      = $siteguard_config->get( self::OPT_NAME_BODY );
		$opt_val_admin     = $siteguard_config->get( self::OPT_NAME_ADMIN );
		if ( isset( $_POST['update'] ) && check_admin_referer( 'siteguard-menu-login-alert-submit' ) ) {
			$error = false;
			$errors = siteguard_check_multisite( );
			if ( is_wp_error( $errors ) ) {
				echo '<div class="error settings-error"><p><strong>';
				esc_html_e( $errors->get_error_message( ), 'siteguard' );
				echo '</strong></p></div>';
				$error = true;
			}
			if ( false === $error && false === $this->is_switch_value( $_POST[ self::OPT_NAME_FEATURE ] ) ) {
				echo '<div class="error settings-error"><p><strong>';
				esc_html_e( 'ERROR: Invalid input value.', 'siteguard' );
				echo '</strong></p></div>';
				$error = true;
			}
			if ( false === $error ) {
				$opt_val_feature   = $_POST[ self::OPT_NAME_FEATURE ];
				$opt_val_subject   = $_POST[ self::OPT_NAME_SUBJECT ];
				$opt_val_body      = $_POST[ self::OPT_NAME_BODY ];
				if ( isset( $_POST[ self::OPT_NAME_ADMIN ] ) ) {
					$opt_val_admin = $_POST[ self::OPT_NAME_ADMIN ];
				} else {
					$opt_val_admin = '0';
				}
				$siteguard_config->set( self::OPT_NAME_FEATURE,   $opt_val_feature );
				$siteguard_config->set( self::OPT_NAME_SUBJECT,   $opt_val_subject );
				$siteguard_config->set( self::OPT_NAME_BODY,      $opt_val_body );
				$siteguard_config->set( self::OPT_NAME_ADMIN,     $opt_val_admin );
				$siteguard_config->update( );
				?>
				<div class="updated"><p><strong><?php esc_html_e( 'Options saved.', 'siteguard' ); ?></strong></p></div>
				<?php
			}
		}

		echo '<div class="wrap">';
		echo '<img src="' . SITEGUARD_URL_PATH . 'images/sg_wp_plugin_logo_40.png" alt="SiteGuard Logo" />';
		echo '<h2>' . esc_html__( 'Login Alert', 'siteguard' ) . '</h2>';
		echo '<div class="siteguard-description">'
		. esc_html__( 'You can find docs about this function on ', 'siteguard' )
		. '<a href="' . esc_url( __( 'http://www.jp-secure.com/cont/products/siteguard_wp_plugin/login_alert_en.html', 'siteguard' ) )
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
			?>
		</th>
		</tr><tr>
		<th scope="row"><label for="<?php echo self::OPT_NAME_SUBJECT ?>"><?php esc_html_e( 'Subject', 'siteguard' ); ?></label></th>
		<td>
			<input type="text" name="<?php echo self::OPT_NAME_SUBJECT ?>" id="<?php echo self::OPT_NAME_SUBJECT ?>" size="50" value="<?php echo esc_attr( $opt_val_subject ) ?>" >
		</td>
		</tr><tr>
		<th scope="row"><label for="<?php echo self::OPT_NAME_BODY ?>"><?php esc_html_e( 'Body', 'siteguard' ); ?></label></th>
		<td>
			<textarea name="<?php echo self::OPT_NAME_BODY ?>" id="<?php echo self::OPT_NAME_BODY ?>" cols="50" rows="5" ><?php echo esc_textarea( $opt_val_body ) ?></textarea>
		</td>
		</tr><tr>
		<th scope="row"><?php esc_html_e( 'Recipients', 'siteguard' ) ?></th>
        <td>
            <input type="checkbox" name="<?php echo self::OPT_NAME_ADMIN ?>" id="<?php echo self::OPT_NAME_ADMIN ?>" value="1" <?php echo ( '1' == $opt_val_admin ? 'checked' : '' ) ?> >
            <label for="<?php echo self::OPT_NAME_ADMIN ?>"><?php esc_html_e( 'Admin only', 'siteguard' ) ?></label>
        </td>
		</tr>
		</table>
		<input type="hidden" name="update" value="Y">
		<div class="siteguard-description">
		<?php esc_html_e( 'It is the function to make it easier to notice unauthorized login. E-mail will be sent to a login user when logged in. If you receive an e-mail to there is no logged-in idea, please suspect unauthorized login. The subject and the mail body, the following variables can be used. (Site Name:%SITENAME%, User Name:%USERNAME%, DATE:%DATE%, Time:%TIME%, IP Address:%IPADDRESS%, User-Agent:%USERAGENT%, Referer:%REFERER%) Access by the XML-RPC will not be notified.', 'siteguard' ) ?>
		</div>
		<hr />
		<?php
		wp_nonce_field( 'siteguard-menu-login-alert-submit' );
		submit_button( );
		?>
		</form>
		</div>
		<?php
	}
}
