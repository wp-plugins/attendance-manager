<?php
/** 
 *	WP Admin page
 */

class ATTMGR_Admin_Page {
	/**
	 *	Initialize
	 */
	public function init() {
		add_action( 'admin_menu', array( 'ATTMGR_Admin_Page', 'add_menu' ) );
		add_action( 'admin_enqueue_scripts', array( 'ATTMGR_Admin_Page', 'style' ) );
	}

	/**
	 *	CSS for admin page
	 */
	public static function style() {
		global $attmgr;
		wp_enqueue_style( ATTMGR::PLUGIN_ID.'_admin_style', $attmgr->mypluginurl.'admin.css' );
	}

	/** 
	 *	Plugin menu
	 */
	public static function add_menu() {
		add_menu_page(
			__( 'Attendance Manager', ATTMGR::TEXTDOMAIN ),
			__( 'Attendance Manager', ATTMGR::TEXTDOMAIN ),
			8,
			ATTMGR::PLUGIN_ID.'-general',
			array( 'ATTMGR_Admin_Page', 'setting_page' )
		);
		add_submenu_page(
			ATTMGR::PLUGIN_ID.'-general',
			__( 'Attendance Manager', ATTMGR::TEXTDOMAIN ).' '.__( 'General', ATTMGR::TEXTDOMAIN ),
			__( 'General', ATTMGR::TEXTDOMAIN ),
			8,
			ATTMGR::PLUGIN_ID.'-general',
			array( 'ATTMGR_Admin_Page', 'setting_page' )
		);
		add_submenu_page(
			ATTMGR::PLUGIN_ID.'-general',
			__( 'Attendance Manager', ATTMGR::TEXTDOMAIN ).' '.__( 'Special Pages', ATTMGR::TEXTDOMAIN ),
			__( 'Special Pages', ATTMGR::TEXTDOMAIN ),
			8,
			ATTMGR::PLUGIN_ID.'-specialpages',
			array( 'ATTMGR_Admin_Page', 'setting_page' )
		);
	}

	/**
	 *	Plugin setting page
	 */
	public static function setting_page( $args = null ) {
		global $wpdb;
		extract(
			wp_parse_args(
				$args,
				array(
					'title' => __( 'Attendance Manager settings', ATTMGR::TEXTDOMAIN ),
					'options_key' => ATTMGR::PLUGIN_ID,
				)
			)
		);

		$options_group = 'general';
		if ( isset( $_SERVER['QUERY_STRING'] ) ) {
			parse_str( $_SERVER['QUERY_STRING'], $qs );
			if ( isset( $qs['page'] ) ) {
				$options_group = substr( $qs['page'], strlen( ATTMGR::PLUGIN_ID.'-' ) );
			}
		}
		$message = '';
		// Reset
		if ( isset( $_POST[ATTMGR::PLUGIN_ID.'_reset'] ) ) {
			$default_option = ATTMGR::default_option();
			$attmgr_options = get_option( $options_key );
			$attmgr_options[ $options_group ] = $default_option[ $options_group ]; 
			update_option( $options_key, $attmgr_options );
			$message = __( 'Settings are reset', ATTMGR::TEXTDOMAIN );
		// Update
		} elseif ( isset( $_POST[ATTMGR::PLUGIN_ID.'_options'] ) ) {
			$attmgr_options = get_option( $options_key );
			$before = $attmgr_options;
			$attmgr_options[ $options_group ] = $_POST[ATTMGR::PLUGIN_ID.'_options' ];
			// preserve_past
			if ( isset( $_POST[ATTMGR::PLUGIN_ID.'_options']['preserve_past'] ) ) {
				$attmgr_options[ $options_group ]['preserve_past'] = floor( abs( $_POST[ATTMGR::PLUGIN_ID.'_options']['preserve_past'] ) );
			}
			update_option( $options_key, $attmgr_options );
			$message = __( 'Settings are updated', ATTMGR::TEXTDOMAIN );
		}
		if ( $message ) {
			echo '<div id="message" class="updated fade"><p>'.$message.'</p></div>';
		}


		$attmgr_options = get_option( $options_key );
		$option = $attmgr_options[ $options_group ];

		switch ( $options_group ) {
			case 'general':
			?>
<div id="<?php echo $options_key; ?>" class="wrap">
<?php screen_icon( 'options-general' ); ?>
<h2><?php echo esc_html( $title ); ?></h2>
<div class="metabox-holder has-right-sidebar">

<div id="post-body">
<div id="post-body-content">
<div class="postbox">
<h3><span><?php _e( 'General', ATTMGR::TEXTDOMAIN ); ?></span></h3>
<div class="inside">
<form method="post" action="<?php echo $_SERVER['PHP_SELF'].'?page=attmgr-general'; ?>"> 

<table class="form-table">
<tr valign="top"><td colspan="2"><strong><?php _e( 'Scheduler settings', ATTMGR::TEXTDOMAIN ); ?></strong></td></tr>

<tr valign="top">
<th scope="row"><?php _e( 'Start time', ATTMGR::TEXTDOMAIN ); ?></th>
<td>
<input type="text" name="attmgr_options[starttime]" id="" value="<?php echo $option['starttime']; ?>" size="5" /> ex. 10:00
<p class="description"></p>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e( 'End time', ATTMGR::TEXTDOMAIN ); ?></th>
<td>
<input type="text" name="attmgr_options[endtime]" id="" value="<?php echo $option['endtime']; ?>" size="5" /> ex. 26:00
<p class="description"></p>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e( 'Interval', ATTMGR::TEXTDOMAIN ); ?></th>
<td>
<input type="text" name="attmgr_options[interval]" id="" value="<?php echo $option['interval']; ?>" size="5" /> 
<?php _e('minutes', ATTMGR::TEXTDOMAIN ); ?>
<p class="description"></p>
</td>
</tr>

<tr><td colspan="2"><hr /></td></tr>
<tr valign="top"><td colspan="2"><strong><?php _e( 'CRON settings', ATTMGR::TEXTDOMAIN ); ?></strong></td></tr>

<tr valign="top">
<th scope="row"><?php _e( 'The days which do not delete the past schedule', ATTMGR::TEXTDOMAIN ); ?></th>
<td>
<input type="text" name="attmgr_options[preserve_past]" id="" value="<?php echo $option['preserve_past']; ?>" size="5" />
<?php _e( 'days', ATTMGR::TEXTDOMAIN ); ?>
<p class="description"></p>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('CRON interval', ATTMGR::TEXTDOMAIN ); ?></th>
<td>
<select name="attmgr_options[cron_interval]">
<option value="halfhour" <?php echo ( $option['cron_interval']=='halfhour') ? 'selected':''; ?>><?php _e( 'every harf hour', ATTMGR::TEXTDOMAIN ); ?></option>
<option value="hourly" <?php echo ( $option['cron_interval']=='hourly') ? 'selected':''; ?>><?php _e( 'hourly', ATTMGR::TEXTDOMAIN ); ?></option>
<option value="daily" <?php echo ( $option['cron_interval']=='daily') ? 'selected':''; ?>><?php _e( 'daily', ATTMGR::TEXTDOMAIN ); ?></option>
<option value="10sec" <?php echo ( $option['cron_interval']=='10sec') ? 'selected':''; ?>><?php _e( 'debug(every 10 seconds)', ATTMGR::TEXTDOMAIN ); ?></option>
</select>
<p class="description"></p>
</td>
</tr>

<tr><td colspan="2"><hr /></td></tr>
<tr valign="top">
<th scope="row">&nbsp;</th>
<td>
<input type="hidden" name="attmgr_options[editable_term]" id="" value="<?php echo $option['editable_term']; ?>" /> 
<input type="hidden" name="attmgr_options[time_style]" id="" value="<?php echo $option['time_style']; ?>" />
<input type="submit" name="save" class="button-primary" value="<?php _e( 'Update', ATTMGR::TEXTDOMAIN ); ?>" class="large-text code" /></td>
</tr>
</table>

</form>
</div>
</div>
</div>
</div>
<div class="inner-sidebar">
<?php do_action( ATTMGR::PLUGIN_ID.'_plugin_info' ); ?>
</div>
</div>
</div>
			<?php

				break;

			case 'specialpages':
			?>
<div id="<?php echo $options_key; ?>" class="wrap">
<?php screen_icon( 'options-general' ); ?>
<h2><?php echo esc_html( $title ); ?></h2>
<div class="metabox-holder">
<div id="post-body">
<div id="post-body-content">
<div class="postbox">
<h3><span><?php _e( 'Special Pages', ATTMGR::TEXTDOMAIN ); ?></span></h3>
<div class="inside">
<form method="post" action="<?php echo $_SERVER['PHP_SELF'].'?page=attmgr-specialpages'; ?>"> 

<table class="form-table">
<tr valign="top"><td colspan="2"><strong><?php _e( 'Special page slug', ATTMGR::TEXTDOMAIN ); ?></strong></td></tr>

<tr valign="top">
<th scope="row"><?php _e( 'Scheduler for staff', ATTMGR::TEXTDOMAIN ); ?></th>
<td>
<input type="text" name="attmgr_options[staff_scheduler]" id="" value="<?php echo $option['staff_scheduler']; ?>" />
<p class="description">
<?php printf(__('The page which inserted the short code %s', ATTMGR::TEXTDOMAIN ), '"<span style="color:#093">[attmgr_staff_scheduler]</span>"'); ?>
</p>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e( 'Scheduler for admin', ATTMGR::TEXTDOMAIN ); ?></th>
<td>
<input type="text" name="attmgr_options[admin_scheduler]" id="" value="<?php echo $option['admin_scheduler']; ?>" />
<p class="description">
<?php printf(__('The page which inserted the short code %s', ATTMGR::TEXTDOMAIN ), '"<span style="color:#093">[attmgr_admin_scheduler]</span>"'); ?>
</p>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e( 'Login page', ATTMGR::TEXTDOMAIN ); ?></th>
<td>
<input type="text" name="attmgr_options[login_page]" id="" value="<?php echo $option['login_page']; ?>" size="20" />
<p class="description">
<?php _e( 'A standard login page will be used if a "login page" is not specified.', ATTMGR::TEXTDOMAIN ); ?><br>
<?php _e( 'If a login page is made separately, specify the name of that page.', ATTMGR::TEXTDOMAIN ); ?>
</p>
</td>
</tr>

<tr><td colspan="2"><hr /></td></tr>
<tr valign="top">
<th scope="row">&nbsp;</th>
<td>
<input type="submit" name="save" class="button-primary" value="<?php _e( 'Update', ATTMGR::TEXTDOMAIN );?>" class="large-text code" /></td>
</tr>
</table>

</form>
</div>
</div>
</div>
</div>
</div>
</div>
			<?php

				break;


			default:
		}
	}


}
?>
