<?php
/** 
 *	Attendance Manager
 */

class ATTMGR {
	// Settings
	const TABLEPREFIX             = 'attmgr_';
	const TEXTDOMAIN              = 'attendance-manager';
	const URL                     = 'http://attmgr.com/';
	const PLUGIN_ID               = 'attmgr';
	const PLUGIN_FILE             = 'attendance-manager.php';
	const PLUGIN_VERSION          = '0.4.5';
	const DB_VERSION              = '0.4.0';

	public $mypluginurl           = null;
	public $mypluginpath          = null;
	public $mypluginfile          = null;

	public $user = array(
		'operator'                => null,
		'acting'                  => null,
	);

	public $page = array(
		'post'                    => null,
		'redirect_to'             => null,
		'qs'                      => null,
		'startdate'               => null,
		'ancestor'                => null,
	);

	public $option = array(
		'general' => array(
			'editable_term'       => null,
			'starttime'           => null,
			'endtime'             => null,
			'interval'            => null,
			'cron_interval'       => null,
			'preserve_past'       => null,
			'time_style'          => null,

		),

		'specialpages' => array(
			'staff_scheduler'     => null,
			'admin_scheduler'     => null,
			'login_page'          => null,
		),
	);

	/** 
	 *	CONSTRUCT
	 */
	public function __construct() {
		load_plugin_textdomain( ATTMGR::TEXTDOMAIN, false, dirname( dirname( plugin_basename(__FILE__) ) ).'/languages' );
		ATTMGR_Activation::load();
		$this->mypluginurl  = dirname( plugin_dir_url( __FILE__ ) ).'/';
		$this->mypluginpath = dirname( plugin_dir_path( __FILE__ ) ).'/';
		$this->mypluginfile = $this->mypluginpath.ATTMGR::PLUGIN_FILE;
		$this->option = self::get_option();

		add_action( 'plugins_loaded', array( 'ATTMGR_User', 'load' ) );
		add_action( 'plugins_loaded', array( &$this, 'load' ) );
		add_action( 'parse_request', array( &$this, 'parse_request' ) );
		add_action( 'plugins_loaded', array( 'ATTMGR', 'reload_textdomain' ) );

		add_action( 'plugins_loaded', array( 'ATTMGR_Updation', 'db_update' ) );
		add_action( 'plugins_loaded', array( 'ATTMGR_Function', 'load' ) );
	}
	
	/** 
	 *	Load
	 */
	public function load() {
		$this->current_user();
	}

	/** 
	 *	parse_request
	 */
	public function parse_request( $query_vars ) {
		$this->current_page( $query_vars );
		$this->user['operator']->acting( $query_vars );
		$this->user['acting'] = $this->user['operator']->is_acting();
	}

	/** 
	 *	Get plugin options
	 */
	public function get_option( $group = null, $key = null ) {
		$option = get_option( ATTMGR::PLUGIN_ID );
		// Set default option
		if ( empty( $option ) ) {
			$option = self::default_option();
			update_option( ATTMGR::PLUGIN_ID, $option );
		}

		// Group
		if ( ! empty( $group ) ) {
			if ( isset( $option[ $group ] ) ) {
				if ( ! empty( $key ) && isset( $option[ $group ][ $key ] ) ) {
					return $option[ $group ][ $key ];
				} else {
					return $option[ $group ];
				}
			}
		} else {
			return $option;
		}
	}

	/** 
	 *	Set default plugins options
	 */
	public function default_option(){
		$default_option = array(
			'general' => array(
				'editable_term'       => 7,
				'starttime'           => '09:00',
				'endtime'             => '18:00',
				'interval'            => 30,	// (min)
				'cron_interval'       => 'daily',
				'preserve_past'       => '60',	// (days)
				'time_style'          => '24h',
			),

			'specialpages' => array(
				'staff_scheduler'     => 'staff_scheduler',
				'admin_scheduler'     => 'admin_scheduler',
				'login_page'          => '',
			),

		);
		return $default_option;
	}

	/**
	 *	Get current page;
	 */
	public function current_page( $query_vars ) {
		global $wpdb;

		$page = array();

		// Current post
		if ( ! empty( $query_vars->query_vars['name'] ) ) {
			$prefix = $wpdb->prefix;
			$query = 'SELECT ID FROM '.$prefix.'posts WHERE `post_name`=%s';
			$cp = $wpdb->get_row( $wpdb->prepare( $query, array( $query_vars->query_vars['name'] ) ), OBJECT );
			$current_post = get_post( $cp->ID );
		} elseif ( ! empty( $query_vars->query_vars['pagename'] ) ) {
			$current_post = get_page_by_path( $query_vars->query_vars['pagename'] );
		} elseif ( ! empty( $query_vars->query_vars['p'] ) ) {
			$current_post = get_post( $query_vars->query_vars['p'] );
		} elseif ( ! empty( $query_vars->query_vars['page_id'] ) ) {
			$current_post = get_post( $query_vars->query_vars['page_id'] );
		} else {
			$current_post = null;
			if ( 'page' == get_option( 'show_on_front' ) ) {
				$page_on_front = get_option( 'page_on_front' );
				$page_for_posts = get_option( 'page_for_posts' );
				if ( $page_on_front ) {
					$current_post = get_post( $page_on_front );
				} elseif ( $page_for_posts ) {
					$current_post = get_post( $page_for_posts );
				}
			}
		}
		$page['post'] = $current_post;

		// Redirect to
		$redirect_info = array();
		if ( ! is_user_logged_in() ) {
			if ( ! empty( $_SERVER['REDIRECT_URL'] ) ) {
				$redirect_url = ( empty( $_SERVER['HTTPS'] ) ? 'http://' : 'https://' )
							   .$_SERVER['HTTP_HOST']
							   .$_SERVER['REDIRECT_URL'];
				if ( ! empty( $_SERVER['REDIRECT_QUERY_STRING'] ) ) {
					$redirect_url .= ( strstr( $redirect_url, '?' ) ) ? '&' : '?';
					$redirect_url .= $_SERVER['REDIRECT_QUERY_STRING'];
				}
				$redirect_url = urlencode( $redirect_url );
				$redirect_info = array(
					'redirect_to='.$redirect_url
				);
			}
		}
		$page['redirect_to'] = $redirect_info;

		// Query string
		$page['qs'] = array();
		if ( ! empty( $_SERVER['QUERY_STRING'] ) ) {
			parse_str( $_SERVER['QUERY_STRING'], $page['qs'] );
		}

		// Date
		$currenttime = current_time( 'timestamp' );
		$page['startdate'] = date( 'Y-m-d', $currenttime );
		if ( ! empty( $page['qs']['date'] ) ||  ! empty( $page['qs']['week'] ) ) {
			$startdate = ( ! empty( $page['qs']['date'] ) ) ? $page['qs']['date'] : $page['qs']['week'];
			if ( preg_match( '/^([2-9][0-9]{3})-(0[1-9]{1}|1[0-2]{1})-(0[1-9]{1}|[1-2]{1}[0-9]{1}|3[0-1]{1})$/', $startdate ) ) {
				$page['startdate'] = $startdate;
			}
		}

		// Ancestor
		if ( ! empty( $current_post ) ) {
			$ancestor_id = array_pop( get_post_ancestors( $current_post->ID ) );
			$ancestor = ( $ancestor_id ) ? get_post( $ancestor_id ) : $current_post;
			$page['ancestor'] = array(
				'ID'        => $ancestor->ID,
				'post_name' => urldecode( $ancestor->post_name ),
			);
		}
		else {
			$page['ancestor'] = array();
		}

		$page = apply_filters( ATTMGR::PLUGIN_ID.'_current_page', $page, $query_vars );

		$this->page = $page;
		return;
	}

	/**
	 *	Get current info;
	 */
	public function current_user() {
		// Current operator
		get_currentuserinfo();
		$this->user['operator'] = new ATTMGR_User();

		return;
	}

	/**
	 *	Reload textdomain
	 */
	public function reload_textdomain(){
		if ( is_textdomain_loaded( ATTMGR::TEXTDOMAIN ) ) {
			unload_textdomain( ATTMGR::TEXTDOMAIN );
		}
		load_plugin_textdomain( ATTMGR::TEXTDOMAIN, false, dirname( dirname( plugin_basename(__FILE__) ) ).'/languages');
	}

}
?>
