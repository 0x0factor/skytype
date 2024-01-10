<?php
/*
  This function based on WP Updates Notifier 1.4.1 by Scott Cariss.
*/
class SiteGuard_UpdatesNotify extends SiteGuard_Base {
	const CRON_NAME = 'siteguard_update_check';

	function __construct( ) {
		add_action( self::CRON_NAME, array( $this, 'do_update_check' ) ); // action to link cron task to actual task
	}

	public function init( ) {
		global $siteguard_config;
		$siteguard_config->set( 'notify_wpcore', '1' );
		$siteguard_config->set( 'notify_plugins', '2' );
		$siteguard_config->set( 'notify_themes',  '2' );
		$siteguard_config->set( 'notified', array( 'core' => '', 'plugin' => array(), 'theme' => array() ) );
		$siteguard_config->set( 'last_check_time', false );
		// We need save the configuration before calling self::check_requirements.
		$siteguard_config->update( );
		if ( true === self::check_requirements( ) ) {
			$siteguard_config->set( 'updates_notify_enable', '1' );
			$siteguard_config->update( );
			self::feature_on( );
		} else {
			$siteguard_config->set( 'updates_notify_enable', '0' );
			$siteguard_config->update( );
		}
	}
	public static function check_requirements( ) {
		$error = siteguard_check_multisite( );
		if ( is_wp_error( $error ) ) {
			return $error;
		}
		$error = self::check_disable_wp_cron( );
		if ( is_wp_error( $error ) ) {
			return $error;
		}
		$error = self::check_wp_cron_access( );
		if ( is_wp_error( $error ) ) {
			return $error;
		}
		return true;
	}
	static function check_disable_wp_cron( ) {
		if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) {
			$message  = esc_html__( "DISABLE_WP_CRON is defined true. This function can't be used.", 'siteguard' );
			$error = new WP_Error( 'siteguard_updates_notify', $message );
			return $error;
		}
		return true;
	}
	static function check_wp_cron_access( ) {
		$result = wp_remote_post( site_url( '/wp-cron.php' ) );
		if ( ! is_wp_error( $result ) && 200 === $result['response']['code'] ) {
			return true;
		}
		$message  = esc_html__( 'Please solve the problem that can not be accessed wp-cron.php. Might be access control.', 'siteguard' );
		$error = new WP_Error( 'siteguard_updates_notify', $message );
		return $error;
	}
	public function feature_on( ) {
		// Already scheduled
		if ( false !== wp_get_schedule( self::CRON_NAME ) ) {
			return;
		}

		// Schedule cron for this plugin.
		wp_schedule_event( time(), 'daily', self::CRON_NAME );
	}

	public function feature_off() {
		wp_clear_scheduled_hook( self::CRON_NAME ); // clear cron
	}

	public function do_update_check() {
		global $siteguard_config;
		$message = ''; // start with a blank message
		if ( '0' != $siteguard_config->get( 'notify_wpcore' ) ) {  // are we to check for WordPress core?
			$core_updated = self::core_update_check( $message ); // check the WP core for updates
		} else {
			$core_updated = false; // no core updates
		}
		if ( '0' != $siteguard_config->get( 'notify_plugins' ) ) { // are we to check for plugin updates?
			$plugins_updated = self::plugins_update_check( $message, $siteguard_config->get( 'notify_plugins' ) ); // check for plugin updates
		} else {
			$plugins_updated = false; // no plugin updates
		}
		if ( '0' != $siteguard_config->get( 'notify_themes' ) ) { // are we to check for theme updates?
			$themes_updated = self::themes_update_check( $message, $siteguard_config->get( 'notify_themes' ) ); // check for theme updates
		} else {
			$themes_updated = false; // no theme updates
		}
		if ( $core_updated || $plugins_updated || $themes_updated ) { // Did anything come back as need updating?
			$message = esc_html__( 'There are updates available for your WordPress site:', 'siteguard' ) . "\n" . $message . "\n";
			$message .= sprintf( esc_html__( 'Please visit %s to update.', 'siteguard' ), admin_url( 'update-core.php' ) ) . "\n\n--\nSiteGuard WP Plugin";
			self::send_notify( $message ); // send our notification email.
		}

		self::log_last_check_time();
	}

	private static function core_update_check( &$message ) {
		global $siteguard_config, $wp_version;
		do_action( 'wp_version_check' ); // force WP to check its core for updates
		$update_core = get_site_transient( 'update_core' ); // get information of updates
		$notified = $siteguard_config->get( 'notified' );
		if ( 'upgrade' == $update_core->updates[0]->response ) { // is WP core update available?
			if ( $update_core->updates[0]->current != $notified['core'] ) { // have we already notified about this version?
				require_once( ABSPATH . WPINC . '/version.php' ); // Including this because some plugins can mess with the real version stored in the DB.
				$new_core_ver = $update_core->updates[0]->current; // The new WP core version
				$old_core_ver = $wp_version; // the old WP core version
				$message .= "\n" . sprintf( esc_html__( 'WP-Core: WordPress is out of date. Please update from version %s to %s', 'siteguard' ), $old_core_ver, $new_core_ver ) . "\n";
				$notified['core'] = $new_core_ver; // set core version we are notifying about
				$siteguard_config->set( 'notified', $notified );
				$siteguard_config->update( );
				return true; // we have updates so return true
			} else {
				return false; // There are updates but we have already notified in the past.
			}
		}
		$notified['core'] = ''; // no updates lets set this nothing
		$siteguard_config->set( 'notified', $notified );
		$siteguard_config->update( );
		return false; // no updates return false
	}

	private static function plugins_update_check( &$message, $allOrActive ) {
		global $siteguard_config, $wp_version;
		$cur_wp_version = preg_replace( '/-.*$/', '', $wp_version );
		$notified = $siteguard_config->get( 'notified' );
		do_action( 'wp_update_plugins' ); // force WP to check plugins for updates
		$update_plugins = get_site_transient( 'update_plugins' ); // get information of updates
		if ( ! empty( $update_plugins->response ) ) { // any plugin updates available?
			$plugins_need_update = $update_plugins->response; // plugins that need updating
			if ( 2 == $allOrActive ) { // are we to check just active plugins?
				$active_plugins      = array_flip( get_option( 'active_plugins' ) ); // find which plugins are active
				$plugins_need_update = array_intersect_key( $plugins_need_update, $active_plugins ); // only keep plugins that are active
			}
			$plugins_need_update = self::check_plugins_against_notified( $plugins_need_update ); // additional filtering of plugins need update
			if ( is_array( $plugins_need_update ) && count( $plugins_need_update ) >= 1 ) { // any plugins need updating after all the filtering gone on above?
				require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' ); // Required for plugin API
				require_once( ABSPATH . WPINC . '/version.php' ); // Required for WP core version
				foreach ( $plugins_need_update as $key => $data ) { // loop through the plugins that need updating
					$plugin_info = get_plugin_data( WP_PLUGIN_DIR . '/' . $key ); // get local plugin info
					$info        = plugins_api( 'plugin_information', array( 'slug' => $data->slug ) ); // get repository plugin info
					$message .= "\n" . sprintf( esc_html__( 'Plugin: %s is out of date. Please update from version %s to %s', 'siteguard' ), $plugin_info['Name'], $plugin_info['Version'], $data->new_version ) . "\n";
					$message .= "\t" . sprintf( esc_html__( 'Details: %s', 'siteguard' ), $data->url ) . "\n";
					$message .= "\t" . sprintf( esc_html__( 'Changelog: %s%s', 'siteguard' ), $data->url, 'changelog/' ) . "\n";
					if ( isset( $info->tested ) && version_compare( $info->tested, $wp_version, '>=' ) ) {
						$compat = sprintf( esc_html__( 'Compatibility with WordPress %1$s: 100%% (according to its author)' ), $cur_wp_version );
					} elseif ( isset( $info->compatibility[ $wp_version ][ $data->new_version ] ) ) {
						$compat = $info->compatibility[ $wp_version ][ $data->new_version ];
						$compat = sprintf( esc_html__( 'Compatibility with WordPress %1$s: %2$d%% (%3$d "works" votes out of %4$d total)' ), $wp_version, $compat[0], $compat[2], $compat[1] );
					} else {
						$compat = sprintf( esc_html__( 'Compatibility with WordPress %1$s: Unknown' ), $wp_version );
					}
					$message .= "\t" . sprintf( esc_html__( 'Compatibility: %s', 'siteguard' ), $compat ) . "\n";
					$notified['plugin'][ $key ] = $data->new_version; // set plugin version we are notifying about
				}
				$siteguard_config->set( 'notified', $notified );
				$siteguard_config->update( );
				return true; // we have plugin updates return true
			}
		} else {
			if ( 0 != count( $notified['plugin'] ) ) { // is there any plugin notifications?
				$notified['plugin'] = array(); // set plugin notifications to empty as all plugins up-to-date
				$siteguard_config->set( 'notified', $notified );
				$siteguard_config->update( );
			}
		}
		return false; // No plugin updates so return false
	}

	private static function themes_update_check( &$message, $allOrActive ) {
		global $siteguard_config;
		$notified = $siteguard_config->get( 'notified' );
		do_action( 'wp_update_themes' ); // force WP to check for theme updates
		$update_themes = get_site_transient( 'update_themes' ); // get information of updates
		if ( ! empty( $update_themes->response ) ) { // any theme updates available?
			$themes_need_update = $update_themes->response; // themes that need updating
			if ( 2 == $allOrActive ) { // are we to check just active themes?
				$active_theme       = array( get_option( 'template' ) => array() ); // find current theme that is active
				$themes_need_update = array_intersect_key( $themes_need_update, $active_theme ); // only keep theme that is active
			}
			$themes_need_update = self::check_themes_against_notified( $themes_need_update ); // additional filtering of themes need update
			if ( is_array( $themes_need_update ) && count( $themes_need_update ) >= 1 ) { // any themes need updating after all the filtering gone on above?
				foreach ( $themes_need_update as $key => $data ) { // loop through the themes that need updating
					$theme_info = wp_get_theme( $key ); // get theme info
					$message .= "\n" . sprintf( esc_html__( 'Theme: %s is out of date. Please update from version %s to %s', 'siteguard' ), $theme_info['Name'], $theme_info['Version'], $data['new_version'] ) . "\n";
					$notified['theme'][ $key ] = $data['new_version']; // set theme version we are notifying about
				}
				$siteguard_config->set( 'notified', $notified );
				$siteguard_config->update( );
				return true; // we have theme updates return true
			}
		} else {
			if ( 0 != count( $notified['theme'] ) ) { // is there any theme notifications?
				$notified['theme'] = array(); // set theme notifications to empty as all themes up-to-date
				$siteguard_config->set( 'notified', $notified );
				$siteguard_config->update( );
			}
		}
		return false; // No theme updates so return false
	}

	public static function check_plugins_against_notified( $plugins_need_update ) {
		global $siteguard_config;
		$notified = $siteguard_config->get( 'notified' );
		if ( is_array( $plugins_need_update ) ) {
			foreach ( $plugins_need_update as $key => $data ) {   // loop through plugins that need update
				if ( isset( $notified['plugin'][ $key ] ) ) { // has this plugin been notified before?
					if ( $data->new_version == $notified['plugin'][ $key ] ) { // does this plugin version match that of the one that's been notified?
						unset( $plugins_need_update[ $key ] ); // don't notify this plugin as has already been notified
					}
				}
			}
		}
		return $plugins_need_update;
	}

	public static function check_themes_against_notified( $themes_need_update ) {
		global $siteguard_config;
		$notified = $siteguard_config->get( 'notified' );
		if ( is_array( $themes_need_update ) ) {
			foreach ( $themes_need_update as $key => $data ) {   // loop through themes that need update
				if ( isset( $notified['theme'][ $key ] ) ) { // has this theme been notified before?
					if ( $data['new_version'] == $notified['theme'][ $key ] ) { // does this theme version match that of the one that's been notified?
						unset( $themes_need_update[ $key ] ); // don't notify this theme as has already been notified
					}
				}
			}
		}
		return $themes_need_update;
	}

	public function send_notify( $message ) {
		global $siteguard_config;
		$subject = sprintf( esc_html__( 'WordPress: Updates Available @ %s', 'siteguard' ), home_url() );

		$user_query = new WP_User_Query( array( 'role' => 'Administrator' ) );
		if ( is_array( $user_query->results ) ) {
			foreach ( $user_query->results as $user ) {
				$user_email = $user->get( 'user_email' );
				if ( true !== @wp_mail( $user_email, $subject, $message ) ) {;
					siteguard_error_log( 'Failed send mail. To:' . $user_email . ' Subject:' . esc_html( $subject ) );
				}
			}
		}
	}

	private function log_last_check_time() {
		global $siteguard_config;
		$siteguard_config->set( 'last_check_time', current_time( 'timestamp' ) );
		$siteguard_config->update( );
	}

	private static function get_schedules() {
		$schedules = wp_get_schedules();
		uasort( $schedules, array( __CLASS__, 'sort_by_interval' ) );
		return $schedules;
	}


	private static function get_intervals() {
		$intervals   = array_keys( self::get_schedules() );
		return $intervals;
	}


	private static function sort_by_interval( $a, $b ) {
		return $a['interval'] - $b['interval'];
	}
}
