<?php

class SiteGuard_Menu_Updates_Notify extends SiteGuard_Base {
	const	OPT_NAME_ENABLE  = 'updates_notify_enable';
	const	OPT_NAME_WPCORE  = 'notify_wpcore';
	const	OPT_NAME_PLUGINS = 'notify_plugins';
	const	OPT_NAME_THEMES  = 'notify_themes';

	function __construct( ) {
		$this->render_page( );
	}
	function is_notify_value( $value ) {
		$items = array( '0', '1', '2' );
		if ( in_array( $value, $items ) ) {
			return true;
		}
		return false;
	}
	function render_page( ) {
		global $siteguard_config, $siteguard_updates_notify;

		$opt_val_enable  = $siteguard_config->get( self::OPT_NAME_ENABLE );
		$opt_val_wpcore  = $siteguard_config->get( self::OPT_NAME_WPCORE );
		$opt_val_plugins = $siteguard_config->get( self::OPT_NAME_PLUGINS );
		$opt_val_themes  = $siteguard_config->get( self::OPT_NAME_THEMES );
		if ( isset( $_POST['update'] ) && check_admin_referer( 'siteguard-menu-updates-notify-submit' ) ) {
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
			    || ( false === $this->is_switch_value( $_POST[ self::OPT_NAME_WPCORE ] ) )
			    || ( false === $this->is_notify_value( $_POST[ self::OPT_NAME_PLUGINS ] ) )
			    || ( false === $this->is_notify_value( $_POST[ self::OPT_NAME_THEMES ] ) ) ) ) {
				echo '<div class="error settings-error"><p><strong>';
				esc_html_e( 'ERROR: Invalid input value.', 'siteguard' );
				echo '</strong></p></div>';
				$error = true;
			}
			if ( false === $error && '1' === $_POST[ self::OPT_NAME_ENABLE ] ) {
				$ret = $siteguard_updates_notify->check_requirements( );
				if ( is_wp_error( $ret ) ) {
					echo '<div class="error settings-error"><p><strong>' . $ret->get_error_message( ) . '</strong></p></div>';
					$error = true;
					$siteguard_config->set( self::OPT_NAME_ENABLE, '0' );
					$siteguard_config->update( );
				}
			}
			if ( false === $error ) {
				$opt_val_enable  = $_POST[ self::OPT_NAME_ENABLE ];
				$opt_val_wpcore  = $_POST[ self::OPT_NAME_WPCORE ];
				$opt_val_plugins = $_POST[ self::OPT_NAME_PLUGINS ];
				$opt_val_themes  = $_POST[ self::OPT_NAME_THEMES ];
				$siteguard_config->set( self::OPT_NAME_ENABLE,  $opt_val_enable );
				$siteguard_config->set( self::OPT_NAME_WPCORE,  $opt_val_wpcore );
				$siteguard_config->set( self::OPT_NAME_PLUGINS, $opt_val_plugins );
				$siteguard_config->set( self::OPT_NAME_THEMES,  $opt_val_themes );
				$siteguard_config->update( );
				if ( '1' === $opt_val_enable ) {
					SiteGuard_UpdatesNotify::feature_on( );
				} else {
					SiteGuard_UpdatesNotify::feature_off( );
				}
				?>
				<div class="updated"><p><strong><?php esc_html_e( 'Options saved.', 'siteguard' ); ?></strong></p></div>
				<?php
			}
		}

		echo '<div class="wrap">';
		echo '<img src="' . SITEGUARD_URL_PATH . 'images/sg_wp_plugin_logo_40.png" alt="SiteGuard Logo" />';
		echo '<h2>' . esc_html__( 'Updates Notify', 'siteguard' ) . '</h2>';
		echo '<div class="siteguard-description">'
		. esc_html__( 'You can find docs about this function on ', 'siteguard' )
		. '<a href="' . esc_url( __( 'http://www.jp-secure.com/cont/products/siteguard_wp_plugin/updates_notify_en.html', 'siteguard' ) )
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
			$error = $siteguard_updates_notify->check_requirements( );
			if ( is_wp_error( $error ) ) {
				echo '<p class="description">';
				echo $error->get_error_message( );
				echo '</p>';
			}
			?>
		</th>
		</tr><tr>
		<th scope="row"><?php esc_html_e( 'WordPress updates', 'siteguard' ); ?></th>
			<td>
				<input type="radio" name="<?php echo self::OPT_NAME_WPCORE ?>" id="<?php echo self::OPT_NAME_WPCORE.'_0' ?>" value="0" <?php checked( $opt_val_wpcore, '0' ) ?> >
				<label for="<?php echo self::OPT_NAME_WPCORE.'_0' ?>"><?php esc_html_e( 'Disable', 'siteguard' ) ?></label>
				<br />
				<input type="radio" name="<?php echo self::OPT_NAME_WPCORE ?>" id="<?php echo self::OPT_NAME_WPCORE.'_1' ?>" value="1" <?php checked( $opt_val_wpcore, '1' ) ?> >
				<label for="<?php echo self::OPT_NAME_WPCORE.'_1' ?>"><?php esc_html_e( 'Enable', 'siteguard' ) ?></label>
			</td>
		</tr><tr>
		<th scope="row"><?php esc_html_e( 'Plugins updates', 'siteguard' ); ?></th>
			<td>
				<input type="radio" name="<?php echo self::OPT_NAME_PLUGINS ?>" id="<?php echo self::OPT_NAME_PLUGINS.'_0' ?>" value="0" <?php checked( $opt_val_plugins, '0' ) ?> >
				<label for="<?php echo self::OPT_NAME_PLUGINS.'_0' ?>"><?php esc_html_e( 'Disable', 'siteguard' ) ?></label>
				<br />
				<input type="radio" name="<?php echo self::OPT_NAME_PLUGINS ?>" id="<?php echo self::OPT_NAME_PLUGINS.'_1' ?>" value="1" <?php checked( $opt_val_plugins, '1' ) ?> >
				<label for="<?php echo self::OPT_NAME_PLUGINS.'_1' ?>"><?php esc_html_e( 'All plugins', 'siteguard' ) ?></label>
				<br />
				<input type="radio" name="<?php echo self::OPT_NAME_PLUGINS ?>" id="<?php echo self::OPT_NAME_PLUGINS.'_2' ?>" value="2" <?php checked( $opt_val_plugins, '2' ) ?> >
				<label for="<?php echo self::OPT_NAME_PLUGINS.'_2' ?>"><?php esc_html_e( 'Active plugins only', 'siteguard' ) ?></label>
			</td>
		</tr><tr>
		<th scope="row"><?php esc_html_e( 'Themes updates', 'siteguard' ); ?></th>
			<td>
				<input type="radio" name="<?php echo self::OPT_NAME_THEMES ?>" id="<?php echo self::OPT_NAME_THEMES.'_0' ?>" value="0" <?php checked( $opt_val_themes, '0' ) ?> >
				<label for="<?php echo self::OPT_NAME_THEMES.'_0' ?>"><?php esc_html_e( 'Disable', 'siteguard' ) ?></label>
				<br />
				<input type="radio" name="<?php echo self::OPT_NAME_THEMES ?>" id="<?php echo self::OPT_NAME_THEMES.'_1' ?>" value="1" <?php checked( $opt_val_themes, '1' ) ?> >
				<label for="<?php echo self::OPT_NAME_THEMES.'_1' ?>"><?php esc_html_e( 'All themes', 'siteguard' ) ?></label>
				<br />
				<input type="radio" name="<?php echo self::OPT_NAME_THEMES ?>" id="<?php echo self::OPT_NAME_THEMES.'_2' ?>" value="2" <?php checked( $opt_val_themes, '2' ) ?> >
				<label for="<?php echo self::OPT_NAME_THEMES.'_2' ?>"><?php esc_html_e( 'Active themes only', 'siteguard' ) ?></label>
			</td>
		</tr>
		</table>
		<div class="siteguard-description">
		<?php esc_html_e( 'Basic of security is that always you use the latest version. If WordPress core, plugins, and themes updates are needed , sends email to notify administrators. Check for updates will be run every 24 hours.', 'siteguard' ) ?>
		</div>
		<hr />
		<input type="hidden" name="update" value="Y">

		<?php
		wp_nonce_field( 'siteguard-menu-updates-notify-submit' );
		submit_button( );
		?>

		</form>
		</div>

		<?php
	}
}
