/* Enteco Immo Connector — Admin JS */
/* global eicAdmin, jQuery */

( function ( $ ) {
	'use strict';

	var $btn     = $( '#eic-start-import' );
	var $spinner = $( '#eic-import-spinner' );
	var $result  = $( '#eic-import-result' );

	if ( ! $btn.length ) {
		return;
	}

	$btn.on( 'click', function ( e ) {
		e.preventDefault();

		$btn.prop( 'disabled', true ).text( eicAdmin.i18n.importing );
		$spinner.show();
		$result.hide().html( '' );

		$.ajax( {
			url:    eicAdmin.ajaxUrl,
			method: 'POST',
			data:   {
				action: 'eic_run_import',
				nonce:  eicAdmin.nonce,
			},
			success: function ( response ) {
				if ( response.success ) {
					var data = response.data;
					var msg  = eicAdmin.i18n.success + ' ';
					msg += '(' + data.count_success + ' importiert';
					if ( data.count_error > 0 ) {
						msg += ', ' + data.count_error + ' Fehler';
					}
					msg += ', ' + data.duration_ms + ' ms)';

					$result.html( '<div class="notice notice-success"><p>' + escHtml( msg ) + '</p></div>' ).show();
				} else {
					var errMsg = ( response.data && response.data.message ) ? response.data.message : eicAdmin.i18n.error;
					$result.html( '<div class="notice notice-error"><p>' + escHtml( errMsg ) + '</p></div>' ).show();
				}
			},
			error: function () {
				$result.html( '<div class="notice notice-error"><p>' + escHtml( eicAdmin.i18n.error ) + '</p></div>' ).show();
			},
			complete: function () {
				$btn.prop( 'disabled', false ).text( 'Import jetzt starten' );
				$spinner.hide();
			},
		} );
	} );

	/** Minimal HTML escaping — avoids XSS in dynamically built messages. */
	function escHtml( str ) {
		return String( str )
			.replace( /&/g, '&amp;' )
			.replace( /</g, '&lt;' )
			.replace( />/g, '&gt;' )
			.replace( /"/g, '&quot;' );
	}

} )( jQuery );
