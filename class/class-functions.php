<?php
/** 
 *	Functions
 */

class ATTMGR_Function {
	/*
	 *	Load 
	 */
	public function load() {
		add_action( 'init', array( 'ATTMGR_Updation', 'plugin_update') );
		add_action( 'init', array( 'ATTMGR_Function', 'init' ) );
		add_action( 'init', array( 'ATTMGR_Shortcode', 'init' ) );
		add_action( 'init', array( 'ATTMGR_User', 'init' ) );
		add_action( 'init', array( 'ATTMGR_Calendar', 'init' ) );
		add_action( 'init', array( 'ATTMGR_Form', 'init' ) );
		add_action( 'init', array( 'ATTMGR_Info', 'init' ) );
		add_action( 'init', array( 'ATTMGR_Admin_Page', 'init' ) );
		add_action( 'init', array( 'ATTMGR_CRON', 'init' ) );
	}

	/**
	 *	Initialize
	 */
	public function init(){
		add_action( 'wp_enqueue_scripts', array( 'ATTMGR_Function', 'front_script' ) );
		add_action( ATTMGR::PLUGIN_ID.'_front_script', array( 'ATTMGR_Function', 'add_front_script' ) );
	}

	/** 
	 *	Load css and js for front page
	 */
	public function front_script() {
		do_action( ATTMGR::PLUGIN_ID.'_front_script' );
	}

	/**
	 *	Front script
	 */
	public function add_front_script() {
		global $attmgr;
		$option = get_option( ATTMGR::PLUGIN_ID );
		// css
		wp_enqueue_style(
			ATTMGR::PLUGIN_ID.'_style',				// handle
			$attmgr->mypluginurl.'front.css',		// src
			false, 									// deps
			ATTMGR::PLUGIN_VERSION, 				// ver
			'all'									// media
		);
		// js
		wp_enqueue_script( 
			ATTMGR::PLUGIN_ID.'_script',			// handle
			$attmgr->mypluginurl.'front.js',		// src
			array( 'jquery' ),						// deps
			ATTMGR::PLUGIN_VERSION, 				// ver
			true 									// in footer
		);
	}

	/**
	 *	Get user portrait
	 */
	public function get_portrait( $portrait, $staff ) {
		global $attmgr;

		$p = get_the_post_thumbnail( $staff->data[ATTMGR::PLUGIN_ID.'_mypage_id'], 'thumbnail' );
		if ( !empty( $p ) ) {
			$portrait = $p;
		} else {
			$portrait = sprintf( '<img src="%simg/nopoatrait.png" />', $attmgr->mypluginurl );
		}
		if ( ! empty( $staff->data[ATTMGR::PLUGIN_ID.'_mypage_id'] ) ) {
			$portrait = sprintf( '<a href="%s">%s</a>', get_permalink( $staff->data[ATTMGR::PLUGIN_ID.'_mypage_id'] ), $portrait );
		}
		return $portrait;
	}

}
