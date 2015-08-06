/**
 *	Attendance Manager JavaScript Functions
 */

jQuery( document ).ready( function( $ ) {
	// Scheduler for staff
	$( '#attmgr_staff_scheduler input[name^="attmgr_off"' ).on( 'click', function() {
		var name = $( this ).attr( 'name' ).replace( /off/g, 'post' );
		var target = 'select[name^="' + name + '"]';
		if ( $( this ).prop( 'checked' ) ) {
			$( target ).attr( 'disabled', 'disabled' );
		} else {
			$( target ).removeAttr( 'disabled' );
		}
	})

	// Scheduler for admin
	$( '#attmgr_admin_scheduler input[name^="attmgr_off"' ).on( 'click', function() {
		var name = $( this ).attr( 'name' ).replace( /off/g, 'post' );
		var target = 'select[name^="' + name + '"]';
		if ( $( this ).prop( 'checked' ) ) {
			$( target ).attr( 'disabled', 'disabled' );
		} else {
			$( target ).removeAttr( 'disabled' );
		}
	})
})
