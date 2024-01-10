<?php

require_once( 'siteguard-waf-exclude-rule-table.php' );

class SiteGuard_Menu_WAF_Tuning_Support extends SiteGuard_Base {
	protected $wp_list_table;
	function __construct( ) {
		$this->wp_list_table = new SiteGuard_WAF_Exclude_Rule_Table( );
		$this->wp_list_table->prepare_items( );
		$this->render_page( );
	}
	// convert from URL to PATH
	function set_filename( $filename ) {
		$base = basename( $filename );
		$idx = strpos( $base, '?' );
		if ( false !== $idx ) {
			return substr( $base, 0, $idx );
		} else {
			return $base;
		}
	}
	function htaccess_error( ) {
		echo '<div class="error settings-error"><p><strong>';
		esc_html_e( 'ERROR: Failed to .htaccess update.', 'siteguard' );
		echo '</strong></p></div>';
	}
	function render_page( ) {
		global $siteguard_waf_exclude_rule;
		isset( $_GET['action'] ) ? $action = $_GET['action'] : $action = 'list';
		if ( 'list' == $action && isset( $_POST['action'] ) ) {
			$action = $_POST['action'];
		}
		if ( ! in_array( $action, array( 'list', 'add', 'edit', 'delete' ) ) ) {
			$action = 'list';
		}

		$waf_exclude_rule_enable = $siteguard_waf_exclude_rule->get_enable( );
		if ( 'edit' == $action && isset( $_GET['rule'] ) ) {
			$offset = 0;
			$id = intval( $_GET['rule'] );
			$rule = $siteguard_waf_exclude_rule->get_rule( $id, $offset );
			if ( false === $rule ) {
				$filename  = '';
				$sig       = '';
				$comment   = '';
			} else {
				$filename  = $rule['filename'];
				$sig       = $rule['sig'];
				$comment   = $rule['comment'];
			}
		} else if ( 'delete' == $action ) {
			if ( isset( $_GET['rule'] ) ) {
				$ids = array( $_GET['rule'] );
			} else if ( isset( $_POST['rule'] ) ) {
				$ids = $_POST['rule'];
			}
		} else {
			$filename  = '';
			$sig       = '';
			$comment   = '';
		}
		if ( isset( $_POST['update'] ) ) {
			$update = $_POST['update'];
			switch ( $update ) {
				case 'add':
					if ( check_admin_referer( 'siteguard-menu-waf-tuning-support-add' ) ) {
						$error = false;
						$errors = siteguard_check_multisite( );
						if ( is_wp_error( $errors ) ) {
							$error = true;
						}
						if ( true == $error || ! isset( $_POST['filename'] )  || ! isset( $_POST['sig'] ) || ! isset( $_POST['comment'] ) ) {
							// error
							if ( true === $error ) {
								siteguard_error_log( 'multisite enabled: ' . __FILENAME__ );
							}
							if ( ! isset( $_POST['sig'] ) ) {
								siteguard_error_log( 'post value sig not set: ' . __FILENAME__ );
							}
							if ( ! isset( $_POST['comment'] ) ) {
								siteguard_error_log( 'post value comment not set: ' . __FILENAME__ );
							}
						} else {
							$filename  = $this->set_filename( stripslashes( $_POST['filename'] ) );
							$sig       = stripslashes( $_POST['sig'] );
							$comment   = stripslashes( $_POST['comment'] );

							$errors = $siteguard_waf_exclude_rule->add_rule( $filename, $sig, $comment );
							if ( ! is_wp_error( $errors ) ) {
								if ( $waf_exclude_rule_enable ) {
									if ( false === $siteguard_waf_exclude_rule->feature_on( ) ) {
										$this->htaccess_error( );
									}
								}
								echo '<div class="updated"><p><strong>' . esc_html__( 'New rule created', 'siteguard' ) . '</strong></p></div>';
								$action = 'list';
								$this->wp_list_table->prepare_items( );
							} else {
								$action = 'add';
							}
						}
					}
					break;
				case 'edit':
					if ( check_admin_referer( 'siteguard-menu-waf-tuning-support-edit' ) ) {
						if ( ! isset( $_POST['rule'] ) || ! isset( $_POST['filename'] )  || ! isset( $_POST['sig'] ) || ! isset( $_POST['comment'] ) ) {
							// error
						} else {
							$id        = $_POST['rule'];
							$filename  = $this->set_filename( stripslashes( $_POST['filename'] ) );
							$sig       = stripslashes( $_POST['sig'] );
							$comment   = stripslashes( $_POST['comment'] );
							$errors = $siteguard_waf_exclude_rule->update_rule( $id, $filename, $sig, $comment );
							if ( ! is_wp_error( $errors ) ) {
								if ( $waf_exclude_rule_enable ) {
									if ( false === $siteguard_waf_exclude_rule->feature_on( ) ) {
										$this->htaccess_error( );
									}
								}
								echo '<div class="updated"><p><strong>' . esc_html__( 'Rule updated', 'siteguard' ) . '</strong></p></div>';
								$action = 'list';
								$this->wp_list_table->prepare_items( );
							} else {
								$action = 'edit';
							}
						}
					}
					break;
				case 'delete':
					if ( check_admin_referer( 'siteguard-menu-waf-tuning-support-delete' ) ) {
						if ( ! isset( $_POST['rule'] ) ) {
							// error
						} else {
							$ids = $_POST['rule'];
							$siteguard_waf_exclude_rule->delete_rule( $ids );
							if ( $waf_exclude_rule_enable ) {
								if ( false === $siteguard_waf_exclude_rule->feature_on( ) ) {
									$this->htaccess_error( );
								}
							}
							echo '<div class="updated"><p><strong>' . esc_html__( 'Rule deleted', 'siteguard' ) . '</strong></p></div>';
							$action = 'list';
							$this->wp_list_table->prepare_items( );
						}
					}
					break;
				case 'apply':
					if ( isset( $_POST['action'] ) && 'delete' == $_POST['action'] ) {
						break;
					}
					if ( check_admin_referer( 'siteguard-menu-waf-tuning-support-apply' ) ) {
						if ( ! isset( $_POST['waf_exclude_rule_enable'] ) ) {
							// error
						} else {
							$error = false;
							$errors = siteguard_check_multisite( );
							if ( is_wp_error( $errors ) ) {
								$error = true;
							}
							if ( false === $error && '1' === $_POST['waf_exclude_rule_enable'] && false === $this->check_module( 'siteguard' ) ) {
								echo '<div class="error settings-error"><p><strong>';
								esc_html_e( 'To use the WAF exclude rule, WAF ( SiteGuard Lite ) should be installed on Apache.', 'siteguard' );
								echo '</strong></p></div>';
								$error = true;
								$siteguard_waf_exclude_rule->set_enable( '0' );
								if ( false === $siteguard_waf_exclude_rule->feature_off( ) ) {
									$this->htaccess_error( );
								}
								$waf_exclude_rule_enable = '0';
							}
							if ( false === $error && false === $this->is_switch_value( $_POST['waf_exclude_rule_enable'] ) ) {
								echo '<div class="error settings-error"><p><strong>';
								esc_html_e( 'ERROR: Invalid input value.', 'siteguard' );
								echo '</strong></p></div>';
								$error = true;
							}
							if ( false === $error ) {
								$old_waf_exclude_rule_enable = $waf_exclude_rule_enable;
								$waf_exclude_rule_enable = $_POST['waf_exclude_rule_enable'];
								$siteguard_waf_exclude_rule->set_enable( $waf_exclude_rule_enable );
								if ( '1' == $waf_exclude_rule_enable ) {
									$result = $siteguard_waf_exclude_rule->feature_on( );
									if ( true === $result ) {
										echo '<div class="updated"><p><strong>' . esc_html__( 'Rules applied', 'siteguard' ) . '</strong></p></div>';
									}
								} else {
									$result = $siteguard_waf_exclude_rule->feature_off( );
									if ( true === $result ) {
										echo '<div class="updated"><p><strong>' . esc_html__( 'Rules unapplied', 'siteguard' ) . '</strong></p></div>';
									}
								}
								if ( false === $result ) {
									$waf_exclude_rule_enable = $old_waf_exclude_rule_enable;
									$siteguard_waf_exclude_rule->set_enable( $waf_exclude_rule_enable );
									$this->htaccess_error( );
								}
							}
						}
					}
					break;
				default:
			}
		} else if ( 'delete' == $action ) {
			if ( isset( $_GET['rule'] ) ) {
				$ids = array( $_GET['rule'] );
			} else if ( isset( $_POST['rule'] ) ) {
				$ids = $_POST['rule'];
			}
		}

		if ( isset( $errors ) && is_wp_error( $errors ) ) {
			?>
			<div class="error">
			<ul>
			<?php
			foreach ( $errors->get_error_messages( ) as $err ) {
				echo "<li>$err</li>\n";
			}
			?>
			</ul>
			</div>
			<?php
		}

		echo '<div class="wrap">';
		echo '<img src="' . SITEGUARD_URL_PATH . 'images/sg_wp_plugin_logo_40.png" alt="SiteGuard Logo" />';
		switch ( $action ) {
			case 'list':
				echo '<h2>' . esc_html__( 'WAF Tuning Support', 'siteguard' ) . ' <a href="?page=siteguard_waf_tuning_support&action=add" class="add-new-h2">' . esc_html__( 'Add New', 'siteguard' ) . '</a></h2>';
				echo '<div class="siteguard-description">'
				. esc_html__( 'You can find docs about this function on ', 'siteguard' )
				. '<a href="' . esc_url( __( 'http://www.jp-secure.com/cont/products/siteguard_wp_plugin/waf_tuning_support_en.html', 'siteguard' ) )
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
					<input type="radio" name="waf_exclude_rule_enable" id="waf_exclude_rule_enable_on" value="1" <?php checked( $waf_exclude_rule_enable, '1' ) ?> >
					<label for="waf_exclude_rule_enable_on"><?php esc_html_e( 'ON', 'siteguard' ) ?></label>
					</li><li>
					<input type="radio" name="waf_exclude_rule_enable" id="waf_exclude_rule_enable_off" value="0" <?php checked( $waf_exclude_rule_enable, '0' ) ?> >
					<label for="waf_exclude_rule_enable_off"><?php esc_html_e( 'OFF', 'siteguard' ) ?></label>
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
					esc_html_e( 'To use the WAF Tuning Support, WAF ( SiteGuard Lite ) should be installed on Apache.', 'siteguard' );
					echo '</p>';
					?>
				</th>
				</table>
				<?php
				$this->wp_list_table->display( );
				?>
				<div class="siteguard-description">
				<?php esc_html_e( 'It is the function to create the rule to avoid the false detection in WordPress (including 403 error occurrence with normal access,) if WAF ( SiteGuard Lite ) by JP-Secure is installed on a Web server. WAF prevents the attack from the outside against the Web server, but for some WordPress or plugin functions, WAF may detect the attack which is actually not attack and block the function.
By creating the WAF exclude rule, the WAF protection function can be activated while the false detection for the specified function is prevented.', 'siteguard' ) ?>
				</div>
				<hr />
				<?php
				echo '<input type="hidden" name="update" id="update" value="apply">';
				wp_nonce_field( 'siteguard-menu-waf-tuning-support-apply' );
				submit_button( esc_attr__( 'Apply rules', 'siteguard' ) );
				?>
				</form>
				<?php
				break;
			case 'add':
			case 'edit':
				if ( 'add' == $action ) {
					echo '<h2>' . esc_html__( 'WAF Exclude Rule Add', 'siteguard' ) . '</h2>';
				} else {
					echo '<h2>' . esc_html__( 'WAF Exclude Rule Edit', 'siteguard' ) . '</h2>';
				}
				?>
				<form name="form1" method="post" action="<?php echo esc_url( menu_page_url( 'siteguard_waf_tuning_support', false ) ) ?>">
				<table class="form-table">
				<tr>
				<th scope="row"><label for="sig"><?php esc_html_e( 'Signature', 'siteguard' ) ?></label></th>
				<td>
				<textarea name="sig" id="sig" style="width:350px;" rows="5" ><?php echo esc_html( $sig ) ?></textarea>
				<p class="description"><?php esc_html_e( 'The detected signature name or signature ID is specified. To specify more than one, separate them with new line.', 'siteguard' ) ?></p>
				</td>
				</tr>
				<tr>
				<th scope="row"><label for="filename"><?php esc_html_e( 'Filename (optional)', 'siteguard' ) ?></label></th>
				<td>
				<input type="text" name="filename" id="filename" value="<?php echo esc_attr( $filename ) ?>" class="regular-text code" >
				<p class="description"><?php esc_html_e( 'The target file name is specified. URL ( the part before ? ) can also be pasted.', 'siteguard' ) ?></p>
				</td>
				</tr>
				<tr>
				<th scope="row"><label for="comment"><?php esc_html_e( 'Comment (optional)', 'siteguard' ) ?></label></th>
				<td>
				<input type="text" name="comment" id="comment" value="<?php echo esc_attr( $comment ) ?>" class="regular-text" >
				</td>
				</tr>
				</table>

				<hr />
				<?php
				if ( 'add' == $action ) {
					echo '<input type="hidden" name="update" id="update" value="add">';
					wp_nonce_field( 'siteguard-menu-waf-tuning-support-add' );
					submit_button( esc_attr__( 'Save', 'siteguard' ) );
				} else {
					echo '<input type="hidden" name="update" id="update" value="edit">';
					echo '<input type="hidden" name="rule" id="rule" value="' . esc_attr( $id ) . '">';
					wp_nonce_field( 'siteguard-menu-waf-tuning-support-edit' );
					submit_button( );
				}
				echo '</form>';
				break;
			case 'delete':
				echo '<h2>' . esc_html__( 'WAF Exclude Rule Delete', 'siteguard' ) . '</h2>';
				?>
				<form name="form1" method="post" action="<?php echo esc_url( menu_page_url( 'siteguard_waf_tuning_support', false ) ) ?>">
				<?php
				echo '<p>' .esc_html( _n( 'You have specified this rule for deletion:', 'You have specified these rules for deletion:', count( $ids ), 'siteguard' ) ) . '</p>';
				$go_delete = 0;
				foreach ( $ids as $id ) {
					$offset = 0;
					$rule = $siteguard_waf_exclude_rule->get_rule( $id, $offset );
					echo '<input type="hidden" name="rule[]" value="' . esc_attr( $id ) . '" />' . esc_html__( 'Signature', 'siteguard' ) . ' : ' . esc_html__( 'Filename', 'siteguard' ) . ' : ' . esc_html__( 'Comment', 'siteguard' ) . ' [' . esc_html( $rule['sig'] ) . ' : ' . esc_html( $rule['filename'] ) . ' : ' . esc_html( $rule['comment'] ) . "]<br />\n";
					$go_delete = 1;
				}
				if ( 1 == $go_delete ) {
					echo '<input type="hidden" name="update" id="update" value="delete">';
					wp_nonce_field( 'siteguard-menu-waf-tuning-support-delete' );
					submit_button( esc_attr__( 'Confirm Deletion', 'siteguard' ) );
				} else {
					echo '<p>' .  esc_html__( 'There are no rules selected for deletion.' , 'siteguard' ) . '</p>';
				}
				echo '</form>';
				break;
		}
		?>
		</div>
		<?php
	}
}
