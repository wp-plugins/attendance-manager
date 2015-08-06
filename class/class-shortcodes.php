<?php
/**
 *	Shortcodes
 */

class ATTMGR_Shortcode {
	/**
	 *	Init
	 */
	public function init() {
		add_shortcode( ATTMGR::PLUGIN_ID.'_staff_scheduler', array( 'ATTMGR_Shortcode', 'staff_scheduler' ) );
		add_shortcode( ATTMGR::PLUGIN_ID.'_admin_scheduler', array( 'ATTMGR_Shortcode', 'admin_scheduler' ) );

		add_shortcode( ATTMGR::PLUGIN_ID.'_daily', array( 'ATTMGR_Shortcode', 'daily' ) );
		add_filter( ATTMGR::PLUGIN_ID.'_shortcode_daily', array( 'ATTMGR_Shortcode', 'daily_schedule' ), 99 );

		add_shortcode( ATTMGR::PLUGIN_ID.'_weekly', array( 'ATTMGR_Shortcode', 'weekly' ) );
		add_filter( ATTMGR::PLUGIN_ID.'_shortcode_weekly', array( 'ATTMGR_Shortcode', 'weekly_schedule' ), 99 );

		add_shortcode( ATTMGR::PLUGIN_ID.'_weekly_all', array( 'ATTMGR_Shortcode', 'weekly_all' ) );
		add_filter( ATTMGR::PLUGIN_ID.'_shortcode_weekly_all', array( 'ATTMGR_Shortcode', 'weekly_all_schedule' ), 99 );

		add_shortcode( ATTMGR::PLUGIN_ID.'_monthly_all', array( 'ATTMGR_Shortcode', 'monthly_all' ) );
		add_filter( ATTMGR::PLUGIN_ID.'_shortcode_monthly_all', array( 'ATTMGR_Shortcode', 'monthly_all_schedule' ), 99 );

		add_shortcode( ATTMGR::PLUGIN_ID.'_calendar', array( 'ATTMGR_Calendar', 'show_calendar' ) );
	}

	/**
	 *	Scheduler for staff
	 */
	public function staff_scheduler( $atts, $content = null ) {
		global $attmgr;
		extract(
			shortcode_atts(
				array(
				),
				$atts
			)
		);
		$html = apply_filters( ATTMGR::PLUGIN_ID.'_shortcode_staff_scheduler', $atts, $content );
		return $html;
	}

	/**
	 *	Scheduler for Admin
	 */
	public function admin_scheduler( $atts, $content = null ) {
		global $attmgr;
		extract(
			shortcode_atts(
				array(
				),
				$atts
			)
		);
		$html = apply_filters( ATTMGR::PLUGIN_ID.'_shortcode_admin_scheduler', $atts, $content );
		return $html;
	}

	/**
	 *	The staff who works today
	 */
	public function daily( $atts, $content = null ) {
		global $attmgr;
		extract(
			shortcode_atts(
				array(
				),
				$atts
			)
		);
		$html = apply_filters( ATTMGR::PLUGIN_ID.'_shortcode_daily', $atts, $content );
		return $html;
	}

	/**
	 *	Schedule for a week of staff
	 */
	public function weekly( $atts, $content = null ) {
		global $attmgr;
		extract(
			shortcode_atts(
				array(
					'id' => null,
				),
				$atts
			)
		);
		$html = apply_filters( ATTMGR::PLUGIN_ID.'_shortcode_weekly', $atts, $content );
		return $html;
	}

	/**
	 *	Schedule for a week of all staff
	 */
	public function weekly_all( $atts, $content = null ) {
		global $attmgr;
		extract(
			shortcode_atts(
				array(
				),
				$atts
			)
		);
		$html = apply_filters( ATTMGR::PLUGIN_ID.'_shortcode_weekly_all', $atts, $content );
		return $html;
	}

	/**
	 *	Schedule for a month of all staff
	 */
	public function monthly_all( $atts, $content = null ) {
		global $attmgr;
		extract(
			shortcode_atts(
				array(
				),
				$atts
			)
		);
		$html = apply_filters( ATTMGR::PLUGIN_ID.'_shortcode_monthly_all', $atts, $content );
		return $html;
	}

	/**
	 *	Monthly all schedule
	 */
	public function monthly_all_schedule( $atts, $content = null ) {
		global $attmgr, $wpdb;
		extract(
			shortcode_atts(
				array(
					'start' => 0,			// 0:Sun, 1:Mon, ... 6:Sut 
					'name_key'  => 'display_name',
				),
				$atts
			)
		);
		ob_start();
		$staff = ATTMGR_User::get_all_staff();
		if ( empty( $staff ) ) {
			printf( '<div class="alert">%s</div>', __( 'There are no staff.', ATTMGR::TEXTDOMAIN ) );
		} else {
			$staff_ids = array();
			foreach ( $staff as $s ) {
				$staff_ids[] = $s->data['ID'];
			}

			// month
			if ( ! empty( $attmgr->page['qs']['month'] ) ) {
				$args['month'] = $attmgr->page['qs']['month'];
			}
			else {
				$args['month'] = date( 'Y-m', current_time('timestamp') );
			}
			list( $y, $m ) = explode( '-', $args['month'] );

			// calnedar data
			$prefix = $wpdb->prefix.ATTMGR::TABLEPREFIX;
			$table = $prefix.'schedule';
			$query = "SELECT * FROM $table "
					."WHERE date LIKE %s AND staff_id IN (".implode( ',', $staff_ids ).") "
					."ORDER BY date ASC ";
			$records = $wpdb->get_results( $wpdb->prepare( $query, array( $args['month'].'%' ) ), ARRAY_A );
			$extends = array();
			if ( ! empty( $records ) ) {
				foreach( $records as $r ) {
					$d = intval( substr( $r['date'], 8, 2 ) );
					if ( ! empty( $r['starttime'] ) && ! empty( $r['endtime'] ) ) {
						$extends[ $d ]['staff'][] = $r['staff_id'];
						$extends[ $d ]['name_key'] = $name_key;
					}
				}
				for( $i = 1; $i <= 31; $i++ ) {
					if ( ! isset( $extends[ $i ] ) ) {
						$extends[ $i ] = array();
					}
				}
				ksort( $extends );
			}
			$data = ATTMGR_Calendar::set( $y, $m, $start, $extends );
			// html
			$subject = ATTMGR_Calendar::show_navi( $y, $m );
			$subject .= ATTMGR_Calendar::html( $atts, $data, array( 'ATTMGR_Shortcode', 'set_monthly_data' ) );
			$search = sprintf( '<table class="%s_calendar', ATTMGR::PLUGIN_ID );
			$replace = sprintf( '<table class="%1$s_calendar %1$s_monthly_schedule', ATTMGR::PLUGIN_ID );
			echo str_replace( $search, $replace, $subject );
		}
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	/**
	 *	Set deta for monthly schedule
	 */
	public static function set_monthly_data( $args ) {
		global $attmgr;

		extract( $args['data'] );
		/*
			[d] => 1
			[status] => 1
			[y] => 2014
			[m] => 04
			[staff] => array(
					...
				)
			[name_key] -> 'display_name'
        */
		$list = array();
		if ( ! empty( $staff ) ) {
			sort( $staff );
			foreach ( $staff as $staff_id ) {
				$s = new ATTMGR_User( $staff_id );
				if ( $s->data[ATTMGR::PLUGIN_ID.'_mypage_id'] ) {
					$url = get_permalink( $s->data[ATTMGR::PLUGIN_ID.'_mypage_id'] );
					$name = $s->data[ $name_key ];
					$list[] = sprintf( '<div class="staff"><a href="%s">%s</a><div>', $url, $name );
				} else {
					$list[] = sprintf( '<div class="staff">%s</div>', $name );
				}
			}
		}
		$format = <<<EOD
<div>
<div class="day">%DAY%</div>
<div class="info">%STAFF%</div>
</div>
EOD;
		$search = array(
			'%DAY%',
			'%STAFF%',
		);
		$replace = array(
			$args['data']['d'],
			implode( "\n", $list ),
		);
		$html = str_replace( $search, $replace, $format );
		return $html;
	}

	/**
	 *	Weekly all schedule
	 */
	public function weekly_all_schedule( $atts, $content = null ) {
		global $attmgr, $wpdb;
		extract(
			shortcode_atts(
				array(
					'name_key' => 'display_name',
				),
				$atts
			)
		);
		$staff = ATTMGR_User::get_all_staff();
		if ( empty( $staff ) ) {
			printf( '<div class="alert">%s</div>', __( 'There are no staff.', ATTMGR::TEXTDOMAIN ) );
		} else {
			$startdate = $attmgr->page['startdate'];
			list( $y, $m, $d ) = explode( '-', $startdate );
			$m = intval( $m );
			$d = intval( $d );
			$starttime = mktime( 0, 0, 0, $m, $d, $y );

			$term = 7;
			$endtime = mktime( 0, 0, 0, $m, $d + $term, $y );
			$enddate = date( 'Y-m-d', $endtime );

			// Head
			$head = '';
			for ( $i = 0; $i < $term; $i++ ) {
				$t = $starttime + 60*60*24*$i;
				$w = date( 'w', $t );
				$date = sprintf( '<span class="date">%d/%d</span><span class="dow">(%s)</span>', date( 'n', $t ), date( 'j', $t ), ATTMGR_Calendar::dow( $w ) );
				$head .= sprintf( '<th class="%s">%s</th>'."\n", ATTMGR_Calendar::dow_lower( $w ), $date );
			}
			$head = sprintf( '<tr><th>&nbsp;</th>'."\n".'%s</tr>', $head );

			// body
			$prefix = $wpdb->prefix.ATTMGR::TABLEPREFIX;
			$table = $prefix.'schedule';
			$query = "SELECT * FROM $table "
					."WHERE staff_id = %d "
					."AND ( date>=%s AND date<= %s ) ";

			$body = '';
			foreach ( $staff as $s ) {
				$staff_id = $s->data['ID'];
				$records = $wpdb->get_results( $wpdb->prepare( $query, array( $staff_id, $startdate, $enddate ) ), ARRAY_A );
				$schedule = array();
				if ( !empty( $records ) ) {
					foreach ( $records as $r ) {
						$schedule[ $r['date'] ] = $r;
						$schedule[ $r['date'] ]['starttime'] = substr( $schedule[ $r['date'] ]['starttime'], 0, 5 );
						$schedule[ $r['date'] ]['endtime'] = substr( $schedule[ $r['date'] ]['endtime'], 0, 5 );
					}
				}
				$line = '';
				for ( $i = 0; $i < 7; $i++ ) {
					$d = date( 'Y-m-d', $starttime + 60*60*24*$i );
					$w = date( 'w', $starttime + 60*60*24*$i );
					$dow = ATTMGR_Calendar::dow( $w );
					if ( isset( $schedule[ $d ] ) ) {
						$time = sprintf( '%s ~ %s', $schedule[ $d ]['starttime'], $schedule[ $d ]['endtime'] );
					}
					else {
						$time = '-';
					}
					$line .= sprintf( '<td>%s</td>'."\n", $time );
				}
				$portrait = null;
				$portrait = ATTMGR_Function::get_portrait( $portrait, $s );
				$name = $s->data[ $name_key ];
				if ( ! empty( $s->data[ATTMGR::PLUGIN_ID.'_mypage_id'] ) ) {
					$name = sprintf( '<a href="%s">%s</a>', get_permalink( $s->data[ATTMGR::PLUGIN_ID.'_mypage_id'] ), $name );
				}

				$body .= sprintf( '<tr><td class="portrait">%s%s</td>%s</tr>'."\n", $portrait, $name, $line );
			}

			ob_start();
			$format = <<<EOD
%NAVI%
<table class="%CLASS%">
%HEAD%
%BODY%
</table>
EOD;
			$search = array(
				'%NAVI%',
				'%CLASS%',
				'%HEAD%',
				'%BODY%',
			);
			$replace = array(
				ATTMGR_Calendar::show_navi_weekly( $startdate ),
				ATTMGR::PLUGIN_ID.'_weekly_all',
				$head,
				$body,
			);
			$subject = str_replace( $search, $replace, $format );
			echo $subject;
		}
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	/**
	 *	Weekly personal schedule
	 */
	public function weekly_schedule( $atts, $content = null ) {
		global $attmgr, $wpdb;
		extract(
			shortcode_atts(
				array(
					'id' => null,
				),
				$atts
			)
		);
		$staff_id = $id;
		$staff = new ATTMGR_User( $staff_id );
		if ( empty( $staff ) ) {
			printf( '<div class="alert">%s</div>', __( 'There are no staff.', ATTMGR::TEXTDOMAIN ) );
		} else {
			$startdate = $attmgr->page['startdate'];
			list( $y, $m, $d ) = explode( '-', $startdate );
			$m = intval( $m );
			$d = intval( $d );
			$starttime = mktime( 0, 0, 0, $m, $d, $y );

			$term = 7;
			$endtime = mktime( 0, 0, 0, $m, $d + $term, $y );
			$enddate = date( 'Y-m-d', $endtime );

			// Head
			$head = '';
			for ( $i = 0; $i < $term; $i++ ) {
				$t = $starttime + 60*60*24*$i;
				$w = date( 'w', $t );
				$date = sprintf( '<span class="date">%d/%d</span><span class="dow">(%s)</span>', date( 'n', $t ), date( 'j', $t ), ATTMGR_Calendar::dow( $w ) );
				$head .= sprintf( '<th class="%s">%s</th>'."\n", ATTMGR_Calendar::dow_lower( $w ), $date );
			}
			$head = sprintf( '<tr>%s</tr>', $head );

			// body
			$prefix = $wpdb->prefix.ATTMGR::TABLEPREFIX;
			$table = $prefix.'schedule';
			$query = "SELECT * FROM $table "
					."WHERE staff_id = %d "
					."AND ( date>=%s AND date<= %s ) ";

			$body = '';
			$records = $wpdb->get_results( $wpdb->prepare( $query, array( $staff_id, $startdate, $enddate ) ), ARRAY_A );
			$schedule = array();
			if ( !empty( $records ) ) {
				foreach ( $records as $r ) {
					$schedule[ $r['date'] ] = $r;
					$schedule[ $r['date'] ]['starttime'] = substr( $schedule[ $r['date'] ]['starttime'], 0, 5 );
					$schedule[ $r['date'] ]['endtime'] = substr( $schedule[ $r['date'] ]['endtime'], 0, 5 );
				}
			}
			$line = '';
			for ( $i = 0; $i < 7; $i++ ) {
				$d = date( 'Y-m-d', $starttime + 60*60*24*$i );
				$w = date( 'w', $starttime + 60*60*24*$i );
				$dow = ATTMGR_Calendar::dow( $w );
				if ( isset( $schedule[ $d ] ) ) {
					$time = sprintf( '%s ~ %s', $schedule[ $d ]['starttime'], $schedule[ $d ]['endtime'] );
				}
				else {
					$time = '-';
				}
				$line .= sprintf( '<td>%s</td>'."\n", $time );
			}
			$body .= sprintf( '<tr>%s</tr>'."\n", $line );

			ob_start();
			$format = <<<EOD
<table class="%CLASS%">
%HEAD%
%BODY%
</table>
EOD;
			$search = array(
				'%CLASS%',
				'%HEAD%',
				'%BODY%',
			);
			$replace = array(
				ATTMGR::PLUGIN_ID.'_weekly',
				$head,
				$body,
			);
			$subject = str_replace( $search, $replace, $format );
			echo $subject;
		}
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	/**
	 *	Daily schedule
	 */
	public function daily_schedule( $atts, $content = null ) {
		global $attmgr, $wpdb;
		extract(
			shortcode_atts(
				array(
					'name_key' => 'display_name',
				),
				$atts
			)
		);
		$staff = ATTMGR_User::get_all_staff();
		if ( empty( $staff ) ) {
			printf( '<div class="alert">%s</div>', __( 'There are no staff.', ATTMGR::TEXTDOMAIN ) );
		} else {
			if ( isset( $attmgr->page['qs']['date'] ) ) {
				$date = $attmgr->page['qs']['date'];
			} else {
				$date = date( 'Y-m-d', current_time( 'timestamp' ) );
			}
			$calendar = ATTMGR_Calendar::set_weekly( $date );
			$staff = ATTMGR_User::get_working_staff( $date );
			ob_start();
			echo '<ul class="staff_block">'."\n";
			foreach ( $staff as $s ) {
				$p = null;
				if ( $s->data[ATTMGR::PLUGIN_ID.'_mypage_id'] ) {
					$p = get_post( $s->data[ATTMGR::PLUGIN_ID.'_mypage_id'] );
				}
				$format = <<<EOD
<li>
	<div class="thumb">
		%PORTRAIT%
	</div>
	<div class="post-info">
		<div class="name">%NAME%</div>
		<div class="attendance">%ATTENDANCE%<div>
	</div>
	<div class="clear">&nbsp;</div>
</li>
EOD;
				$portrait = null;
				$portrait = ATTMGR_Function::get_portrait( $portrait, $s );
				$name = $s->data[ $name_key ];
				if ( !empty( $s->data[ATTMGR::PLUGIN_ID.'_mypage_id'] ) ) {
					$name = sprintf( '<a href="%s">%s</a>', get_permalink( $s->data[ATTMGR::PLUGIN_ID.'_mypage_id'] ), $name );
				}
				$attendance = $s->is_work( $date );
				$starttime = ATTMGR_Form::time_form( $attendance['starttime'], '%d:%02d' );
				$endtime   = ATTMGR_Form::time_form( $attendance['endtime'], '%d:%02d' );

				$search = array(
					'%PORTRAIT%',
					'%NAME%',
					'%ATTENDANCE%',
				);
				$replace = array(
					$portrait,
					$name,
					sprintf( '%s ~ %s', $starttime, $endtime ),
				);
				$line = str_replace( $search, $replace, $format );
				echo $line;
			}
		}
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
}
?>
