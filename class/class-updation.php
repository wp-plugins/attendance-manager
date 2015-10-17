<?php
/**
 *	Plugin updation
 */

class ATTMGR_Updation {
	/**
	 *	Load
	 */
	public function load() {
		add_action( 'plugins_loaded', array( 'ATTMGR_Updation', 'db_update' ) );
		add_action( 'plugins_loaded', array( 'ATTMGR_Updation', 'plugin_update' ) );
	}

	/** 
	 *	Plugin update
	 */
	public function plugin_update() {
		global $wpdb, $attmgr;

		if ( ! is_admin() ) {
			return;
		}
		$installed_version = get_option( ATTMGR::PLUGIN_ID.'_version' );
		$new_version = $installed_version;

		if ( $installed_version['plugin'] < ATTMGR::PLUGIN_VERSION ) {
			$new_version['plugin'] = ATTMGR::PLUGIN_VERSION;
			update_option( ATTMGR::PLUGIN_ID.'_version', $new_version );
		}
		return;
	}

	/** 
	 *	DB update
	 */
	public function db_update() {
		global $wpdb, $attmgr;

		if ( ! is_admin() ) {
			return;
		}
		$installed_version = get_option( ATTMGR::PLUGIN_ID.'_version' );
		$new_version = $installed_version;

		if ( $installed_version['db'] < ATTMGR::DB_VERSION ) {
			$new_version['db'] = ATTMGR::DB_VERSION;
			update_option( ATTMGR::PLUGIN_ID.'_version', $new_version );
		}
		return;
	}

}
?>
