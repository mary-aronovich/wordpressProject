/* global jQuery, POJO_ADMIN */

var PF = PF || {};
PF.methods = ( function( $, window ) {
	'use strict';
	
	var _update_fields = function( selector, is_enable ) {
			is_enable = is_enable || false;
			$( '.atmb-wrap-fields :input', selector ).each( function() {
				if ( is_enable ) {
					$( this ).removeAttr( 'disabled' );
				} else {
					$( this ).attr( 'disabled', 'disabled' );
				}
			} );
		},
		
		_init = function() {
			var pf_format_chosen = $( 'input.ppf-format-id' );
			pf_format_chosen.on( 'change', function() {
				if ( ! $( this ).prop( 'checked' ) ) {
					return;
				}
				
				$( 'h2.nav-tab-wrapper.ppf-radio-select-wrapper .nav-tab' )
					.removeClass( 'nav-tab-active' );
				
				$( this )
					.closest( '.nav-tab' )
					.addClass( 'nav-tab-active' );
				
				var current_val = $( this ).val();
				if ( undefined === current_val|| '' === current_val || undefined === POJO_ADMIN.pf_formats[ current_val ] ) {
					return;
				}
				
				$.each( POJO_ADMIN.pf_formats[ current_val ].actions, function( i, data ) {
					var current_selector = $( data.selector );
					if ( 'hide' === data.type ) {
						current_selector.slideUp( 'fast' );
						_update_fields( current_selector );
					} else if ( 'show' === data.type ) {
						current_selector.slideDown( 'fast' );
						_update_fields( current_selector, true );
					}
				} );

				$( 'div.atmb-wrap-fields .select-show-or-hide-fields :input' ).trigger( 'change' );
			} );
			pf_format_chosen.trigger( 'change' );
		};
	
	return {
		init: _init
	};
} ( jQuery, window ) );

jQuery( document ).ready( function( $ ) {
	'use strict';
	PF.methods.init();
} );