<?php

class SiteGuard_Menu_Protect_XMLRPC extends SiteGuard_Base {
	const	OPT_NAME_FEATURE  = 'protect_xmlrpc_enable';
	const	OPT_NAME_TYPE     = 'protect_xmlrpc_type';
	const	OPT_NAME_XMLRPC   = 'disable_xmlrpc_enable';
	const	OPT_NAME_PINGBACK = 'disable_pingback_enable';

	protected $opt_val_xmlrpc;
	protected $opt_val_pingback;
	protected $opt_val_feature;
	protected $opt_val_type;

	function __construct( ) {
		$this->render_page( );
	}
	function is_switch_value( $value ) {
		$items = array( '0', '1' );
		if ( in_array( $value, $items ) ) {
			return true;
		}
		return false;
	}
	function db_to_page( ) {
		if ( '0' === $this->opt_val_xmlrpc ) {
			if ( '0' === $this->opt_val_pingback ) {
				$this->opt_val_feature = '0';
				$this->opt_val_type = '0';
			} else {
				$this->opt_val_feature = '1';
				$this->opt_val_type = '0';
			}
		} else {
			$this->opt_val_feature = '1';
			$this->opt_val_type = '1';
		}
	}	
	function page_to_db( ) {
		if ( '0' === $this->opt_val_feature ) {
			$this->opt_val_xmlrpc   = '0';
			$this->opt_val_pingback = '0';
		} else {
			if ( '0' === $this->opt_val_type ) {
				$this->opt_val_xmlrpc   = '0';
				$this->opt_val_pingback = '1';
			} else {
				$this->opt_val_xmlrpc   = '1';
				$this->opt_val_pingback = '0';
			}
		}
	}
	function render_page( ) {
		global $siteguard_config, $siteguard_xmlrpc;

		$this->opt_val_xmlrpc    = $siteguard_config->get( self::OPT_NAME_XMLRPC );
		$this->opt_val_pingback  = $siteguard_config->get( self::OPT_NAME_PINGBACK );

		$this->db_to_page( );

		if ( isset( $_POST['update'] ) && check_admin_referer( 'siteguard-menu-protect-xmlrpc-submit' ) ) {
			$error = false;
			$errors = siteguard_check_multisite( );
			if ( is_wp_error( $errors ) ) {
				echo '<div class="error settings-error"><p><strong>';
				esc_html_e( $errors->get_error_message( ), 'siteguard' );
				echo '</strong></p></div>';
				$error = true;
			}
			if ( false === $error 
				&& ( ( false === $this->is_switch_value( $_POST[ self::OPT_NAME_FEATURE ] ) )
				  || ( false === $this->is_switch_value( $_POST[ self::OPT_NAME_TYPE ] ) ) ) ) {
				echo '<div class="error settings-error"><p><strong>';
				esc_html_e( 'ERROR: Invalid input value.', 'siteguard' );
				echo '</strong></p></div>';
				$error = true;
			}
			if ( false === $error ) {
				$old_opt_val_feature = $this->opt_val_feature;
				$old_opt_val_type    = $this->opt_val_type;
				$this->opt_val_feature = $_POST[ self::OPT_NAME_FEATURE ];
				$this->opt_val_type    = $_POST[ self::OPT_NAME_TYPE ];
				$this->page_to_db( );
				$siteguard_config->set( self::OPT_NAME_XMLRPC,   $this->opt_val_xmlrpc );
				$siteguard_config->set( self::OPT_NAME_PINGBACK, $this->opt_val_pingback );
				$siteguard_config->update( );
				$result = true;
				if ( '0' === $this->opt_val_xmlrpc ) {
					$result = $siteguard_xmlrpc->feature_off( );
				} else {
					$result = $siteguard_xmlrpc->feature_on( );
				}
				if ( true === $result ) {
					?>
					<div class="updated"><p><strong><?php esc_html_e( 'Options saved.', 'siteguard' ); ?></strong></p></div>
					<?php
				} else {
					$this->opt_val_feature  = $old_opt_val_feature;
					$this->opt_val_val_type = $old_opt_val_type;
					$this->page_to_db( );
					$siteguard_config->set( self::OPT_NAME_XMLRPC,   $this->opt_val_xmlrpc );
					$siteguard_config->set( self::OPT_NAME_PINGBACK, $this->opt_val_pingback );
					$siteguard_config->update( );
					echo '<div class="error settings-error"><p><strong>';
					esc_html_e( 'ERROR: Failed to .htaccess update.', 'siteguard' );
					echo '</strong></p></div>';
				}
			}
		}

		echo '<div class="wrap">';
		echo '<img src="' . SITEGUARD_URL_PATH . 'images/sg_wp_plugin_logo_40.png" alt="SiteGuard Logo" />';
		echo '<h2>' . esc_html__( 'Protect XMLRPC', 'siteguard' ) . '</h2>';
		echo '<div class="siteguard-description">'
		. esc_html__( 'You can find docs about this function on ', 'siteguard' )
		. '<a href="' . esc_url( __( 'http://www.jp-secure.com/cont/products/siteguard_wp_plugin/xmlrpc_en.html', 'siteguard' ) )
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
			<input type="radio" name="<?php echo self::OPT_NAME_FEATURE ?>" id="<?php echo self::OPT_NAME_FEATURE.'_on' ?>" value="1" <?php checked( $this->opt_val_feature, '1' ) ?> >
			<label for="<?php echo self::OPT_NAME_FEATURE.'_on' ?>"><?php echo esc_html_e( 'ON', 'siteguard' ) ?></label>
			</li><li>
			<input type="radio" name="<?php echo self::OPT_NAME_FEATURE ?>" id="<?php echo self::OPT_NAME_FEATURE.'_off' ?>" value="0" <?php checked( $this->opt_val_feature, '0' ) ?> >
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
		<th scope="row"><?php esc_html_e( 'Type', 'siteguard' ); ?></th>
			<td>
				<input type="radio" name="<?php echo self::OPT_NAME_TYPE ?>" id="<?php echo self::OPT_NAME_TYPE.'_0' ?>" value="0" <?php checked( $this->opt_val_type, '0' ) ?> >
				<label for="<?php echo self::OPT_NAME_TYPE.'_0' ?>"><?php esc_html_e( 'Disable Pingback', 'siteguard' ) ?></label>
				<br />
				<input type="radio" name="<?php echo self::OPT_NAME_TYPE ?>" id="<?php echo self::OPT_NAME_TYPE.'_1' ?>" value="1" <?php checked( $this->opt_val_type, '1' ) ?> >
				<label for="<?php echo self::OPT_NAME_TYPE.'_1' ?>"><?php esc_html_e( 'Disable XMLRPC', 'siteguard' ) ?></label>
			</td>
		</tr>
		</table>
		<input type="hidden" name="update" value="Y">
		<div class="siteguard-description">
		<?php esc_html_e( 'To disable the Pingback, or disable the entire XMLRPC ( xmlrpc.php ), to prevent abuse. When you disable the whole XMLRPC, you will not be able to use plug-ins and apps that use XMLRPC. If there is trouble, please do not use this function.', 'siteguard' ) ?>
		</div>
		<hr />
		<?php
		wp_nonce_field( 'siteguard-menu-protect-xmlrpc-submit' );
		submit_button( );
		?>
		</form>
		</div>
		<?php
	}
}
