<?php

class SiteGuard_Menu_CAPTCHA extends SiteGuard_Base {
	const OPT_NAME_ENABLE             = 'captcha_enable';
	const OPT_NAME_LOGIN              = 'captcha_login';
	const OPT_NAME_COMMENT            = 'captcha_comment';
	const OPT_NAME_LOSTPASSWORD       = 'captcha_lostpasswd';
	const OPT_NAME_REGISTUSER         = 'captcha_registuser';

	function __construct( ) {
		$this->render_page( );
	}
	function is_captcha_switch_value( $value ) {
		$items = array( '0', '1', '2' );
		if ( in_array( $value, $items ) ) {
			return true;
		}
		return false;
	}
	function render_page( ) {
		global $siteguard_config, $siteguard_captcha;

		$opt_val_enable             = $siteguard_config->get( self::OPT_NAME_ENABLE );
		$opt_val_login              = $siteguard_config->get( self::OPT_NAME_LOGIN );
		$opt_val_comment            = $siteguard_config->get( self::OPT_NAME_COMMENT );
		$opt_val_lostpassword       = $siteguard_config->get( self::OPT_NAME_LOSTPASSWORD );
		$opt_val_registuser         = $siteguard_config->get( self::OPT_NAME_REGISTUSER );
		if ( isset( $_POST['update'] ) && check_admin_referer( 'siteguard-menu-captcha-submit' ) ) {
			$error = false;
			$errors = siteguard_check_multisite( );
			if ( is_wp_error( $errors ) ) {
				echo '<div class="error settings-error"><p><strong>';
				esc_html_e( $errors->get_error_message( ), 'siteguard' );
				echo '</strong></p></div>';
				$error = true;
			}
			if ( false == $error && '1' == $_POST[ self::OPT_NAME_ENABLE ] ) {
				$ret = $siteguard_captcha->check_requirements( );
				if ( is_wp_error( $ret ) ) {
					echo '<div class="error settings-error"><p><strong>' . $ret->get_error_message( ) . '</strong></p></div>';
					$error = true;
					$siteguard_config->set( self::OPT_NAME_ENABLE, '0' );
					$siteguard_config->update( );
				}
			}
			if ( ( false === $error )
			  && ( ( false === $this->is_switch_value( $_POST[ self::OPT_NAME_ENABLE ] ) )
			    || ( false === $this->is_captcha_switch_value( $_POST[ self::OPT_NAME_LOGIN ] ) )
			    || ( false === $this->is_captcha_switch_value( $_POST[ self::OPT_NAME_COMMENT ] ) )
			    || ( false === $this->is_captcha_switch_value( $_POST[ self::OPT_NAME_LOSTPASSWORD ] ) )
			    || ( false === $this->is_captcha_switch_value( $_POST[ self::OPT_NAME_REGISTUSER ] ) ) ) ) {
				echo '<div class="error settings-error"><p><strong>';
				esc_html_e( 'ERROR: Invalid input value.', 'siteguard' );
				echo '</strong></p></div>';
				$error = true;
			}
			if ( false === $error ) {
				$opt_val_enable             = $_POST[ self::OPT_NAME_ENABLE ];
				$opt_val_login              = $_POST[ self::OPT_NAME_LOGIN ];
				$opt_val_comment            = $_POST[ self::OPT_NAME_COMMENT ];
				$opt_val_lostpassword       = $_POST[ self::OPT_NAME_LOSTPASSWORD ];
				$opt_val_registuser         = $_POST[ self::OPT_NAME_REGISTUSER ];
				$siteguard_config->set( self::OPT_NAME_ENABLE,             $opt_val_enable );
				$siteguard_config->set( self::OPT_NAME_LOGIN,              $opt_val_login );
				$siteguard_config->set( self::OPT_NAME_COMMENT,            $opt_val_comment );
				$siteguard_config->set( self::OPT_NAME_LOSTPASSWORD,       $opt_val_lostpassword );
				$siteguard_config->set( self::OPT_NAME_REGISTUSER,         $opt_val_registuser );
				$siteguard_config->update( );
				?>
				<div class="updated"><p><strong><?php esc_html_e( 'Options saved.', 'siteguard' ); ?></strong></p></div>
				<?php
			}
		}

		echo '<div class="wrap">';
		echo '<img src="' . SITEGUARD_URL_PATH . 'images/sg_wp_plugin_logo_40.png" alt="SiteGuard Logo" />';
		echo '<h2>' . esc_html__( 'CAPTCHA', 'siteguard' ) . '</h2>';
		echo '<div class="siteguard-description">'
		. esc_html__( 'You can find docs about this function on ', 'siteguard' )
		. '<a href="' . esc_url( __( 'http://www.jp-secure.com/cont/products/siteguard_wp_plugin/captcha_en.html', 'siteguard' ) )
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
			$error = $siteguard_captcha->check_requirements( );
			if ( is_wp_error( $error ) ) {
				echo '<p class="description">';
				echo $error->get_error_message( );
				echo '</p>';
			}
			?>
		</th>
		</tr><tr>
		<th scope="row"><?php esc_html_e( 'Login page', 'siteguard' ); ?></th>
			<td>
				<input type="radio" name="<?php echo self::OPT_NAME_LOGIN ?>" id="<?php echo self::OPT_NAME_LOGIN.'_jp' ?>" value="1" <?php checked( $opt_val_login, '1' ) ?> >
				<label for="<?php echo self::OPT_NAME_LOGIN.'_jp' ?>"><?php esc_html_e( 'Hiragana (Japanese)', 'siteguard' ) ?></label>
				<br />
				<input type="radio" name="<?php echo self::OPT_NAME_LOGIN ?>" id="<?php echo self::OPT_NAME_LOGIN.'_en' ?>" value="2" <?php checked( $opt_val_login, '2' ) ?> >
				<label for="<?php echo self::OPT_NAME_LOGIN.'_en' ?>"><?php esc_html_e( 'Alphanumeric', 'siteguard' ) ?></label>
				<br />
				<input type="radio" name="<?php echo self::OPT_NAME_LOGIN ?>" id="<?php echo self::OPT_NAME_LOGIN.'_off' ?>" value="0" <?php checked( $opt_val_login, '0' ) ?> >
				<label for="<?php echo self::OPT_NAME_LOGIN.'_off' ?>"><?php esc_html_e( 'Disable', 'siteguard' ) ?></label>
			</td>
		</tr><tr>
		<th scope="row"><?php esc_html_e( 'Comment page', 'siteguard' ); ?></th>
			<td>
				<input type="radio" name="<?php echo self::OPT_NAME_COMMENT ?>" id="<?php echo self::OPT_NAME_COMMENT.'_jp' ?>" value="1" <?php checked( $opt_val_comment, '1' ) ?> >
				<label for="<?php echo self::OPT_NAME_COMMENT.'_jp' ?>"><?php esc_html_e( 'Hiragana (Japanese)', 'siteguard' ) ?></label>
				<br />
				<input type="radio" name="<?php echo self::OPT_NAME_COMMENT ?>" id="<?php echo self::OPT_NAME_COMMENT.'_en' ?>" value="2" <?php checked( $opt_val_comment, '2' ) ?> >
				<label for="<?php echo self::OPT_NAME_COMMENT.'_en' ?>"><?php esc_html_e( 'Alphanumeric', 'siteguard' ) ?></label>
				<br />
				<input type="radio" name="<?php echo self::OPT_NAME_COMMENT ?>" id="<?php echo self::OPT_NAME_COMMENT.'_off' ?>" value="0" <?php checked( $opt_val_comment, '0' ) ?> >
				<label for="<?php echo self::OPT_NAME_COMMENT.'_off' ?>"><?php esc_html_e( 'Disable', 'siteguard' ) ?></label>
			</td>
		</tr><tr>
		<th scope="row"><?php esc_html_e( 'Lost password page', 'siteguard' ); ?></th>
			<td>
				<input type="radio" name="<?php echo self::OPT_NAME_LOSTPASSWORD ?>" id="<?php echo self::OPT_NAME_LOSTPASSWORD.'_jp' ?>" value="1" <?php checked( $opt_val_lostpassword, '1' ) ?> >
				<label for="<?php echo self::OPT_NAME_LOSTPASSWORD.'_jp' ?>"><?php esc_html_e( 'Hiragana (Japanese)', 'siteguard' ) ?></label>
				<br />
				<input type="radio" name="<?php echo self::OPT_NAME_LOSTPASSWORD ?>" id="<?php echo self::OPT_NAME_LOSTPASSWORD.'_en' ?>" value="2" <?php checked( $opt_val_lostpassword, '2' ) ?> >
				<label for="<?php echo self::OPT_NAME_LOSTPASSWORD.'_en' ?>"><?php esc_html_e( 'Alphanumeric', 'siteguard' ) ?></label>
				<br />
				<input type="radio" name="<?php echo self::OPT_NAME_LOSTPASSWORD ?>" id="<?php echo self::OPT_NAME_LOSTPASSWORD.'_off' ?>" value="0" <?php checked( $opt_val_lostpassword, '0' ) ?> >
				<label for="<?php echo self::OPT_NAME_LOSTPASSWORD.'_off' ?>"><?php esc_html_e( 'Disable', 'siteguard' ) ?></label>
			</td>
		</tr><tr>
		<th scope="row"><?php esc_html_e( 'Registration user page', 'siteguard' ); ?></th>
			<td>
				<input type="radio" name="<?php echo self::OPT_NAME_REGISTUSER ?>" id="<?php echo self::OPT_NAME_REGISTUSER.'_jp' ?>" value="1" <?php checked( $opt_val_registuser, '1' ) ?> >
				<label for="<?php echo self::OPT_NAME_REGISTUSER.'_jp' ?>"><?php esc_html_e( 'Hiragana (Japanese)', 'siteguard' ) ?></label>
				<br />
				<input type="radio" name="<?php echo self::OPT_NAME_REGISTUSER ?>" id="<?php echo self::OPT_NAME_REGISTUSER.'_en' ?>" value="2" <?php checked( $opt_val_registuser, '2' ) ?> >
				<label for="<?php echo self::OPT_NAME_REGISTUSER.'_en' ?>"><?php esc_html_e( 'Alphanumeric', 'siteguard' ) ?></label>
				<br />
				<input type="radio" name="<?php echo self::OPT_NAME_REGISTUSER ?>" id="<?php echo self::OPT_NAME_REGISTUSER.'_off' ?>" value="0" <?php checked( $opt_val_registuser, '0' ) ?> >
				<label for="<?php echo self::OPT_NAME_REGISTUSER.'_off' ?>"><?php esc_html_e( 'Disable', 'siteguard' ) ?></label>
			</td>
		</tr>
		</table>
		<div class="siteguard-description">
		<?php esc_html_e( 'It is the function to decrease the vulnerability against an illegal login attempt attack such as a brute force attack or a password list attack, or to receive less comment spam. For the character of CAPTCHA, hiragana and alphanumeric characters can be selected.', 'siteguard' ) ?>
		</div>
		<input type="hidden" name="update" value="Y">
		<hr />

		<?php
		wp_nonce_field( 'siteguard-menu-captcha-submit' );
		submit_button();
		?>
		</form>
		</div>
		<?php
	}
}
