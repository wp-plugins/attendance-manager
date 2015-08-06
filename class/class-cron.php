<?php
/**
 *	CRON
 */

class ATTMGR_CRON {
	/**
	 *	Initialize
	 */
	public function init(){
		add_filter( 'cron_schedules', array( 'ATTMGR_CRON', 'add_interval' ) );
		add_action( ATTMGR::PLUGIN_ID.'_cron',  array( 'ATTMGR_CRON', 'cron_do' ) );
		add_action( 'wp', array( 'ATTMGR_CRON', 'update' ) );
	}

	/** 
	 *	Add CRON interval
	 */
	public static function add_interval( $schedules ) {
		$schedules['10sec'] = array(
			'interval' => 10,
			'display' => '10sec'
		);
		$schedules['halfhour'] = array(
			'interval' => 60*30,
			'display' => 'harfhour'
		);
		return $schedules;
	}

	/** 
	 *	CRON
	 */
	public function cron_do() {
		global $attmgr, $wpdb;

		$preserve_day = current_time('timestamp') - ( $attmgr->option['general']['preserve_past'] *60*60*24 );
		$prefix = $wpdb->prefix.ATTMGR::TABLEPREFIX;
		$table = $prefix.'schedule';
		$query = "DELETE FROM {$table} WHERE `date`<%s";
		$ret = $wpdb->query( $wpdb->prepare( $query, array( date( 'Y-m-d', $preserve_day ) ) ), ARRAY_A );
	}

	/** 
	 *	Update CRON
	 */
	public static function update() {
		global $attmgr;

		if ( ! wp_next_scheduled( ATTMGR::PLUGIN_ID.'_cron' ) ) {
			wp_schedule_event( time(), $attmgr->option['general']['cron_interval'], ATTMGR::PLUGIN_ID.'_cron' );
		}
	}
}
