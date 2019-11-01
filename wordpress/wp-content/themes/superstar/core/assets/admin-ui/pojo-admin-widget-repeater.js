/* global jQuery, _, confirm, POJO_ADMIN, Pojo_Admin_Helpers, PJ_Page_Builder */

var PJ_Admin_Widget_Repeater = {};

( function( $, window, document, undefined ) {
	'use strict';

	PJ_Admin_Widget_Repeater = {
		cache: {
			$document: $( document ),
			$window: $( window )
		},

		cacheElements: function() {
			this.cache.$body = $( 'body' );
			
			this.cache.isInPojoBuilder = 1 <= $( '#page-builder' ).length;
		},

		buildElements: function() {
			var self = this;
			self.buildModals();
		},

		bindEvents: function() {
			var self = this;

			this.cache.$document.on( 'click', 'div.field-repeater a.btn-add-row', function( e ) {
				var $repeater_wrap = $( this ).closest( 'div.field-repeater' ),
					$field_list = $repeater_wrap.find( 'div.field-repeater-list' ),
					new_row = $repeater_wrap.find( 'div.field-repeater-clone' ).clone(),
					field_new_id = new Date().getTime();

				new_row
					.html( new_row.html().replace( /REPEATER_ID/g, field_new_id ) )
					.removeClass( 'field-repeater-clone hidden' )
					.addClass( 'field-repeater-row' );

				$field_list
					.append( new_row );
				
				if ( self.cache.isInPojoBuilder ) {
					$field_list.find( ':input:not(.pb-input-widget-content)' ).each( function() {
						PJ_Page_Builder.initWidget( $( this ) );
					} );
					PJ_Page_Builder.widgetTriggerChange();
				}
				
				Pojo_Admin_Helpers.init_image_upload();
				self.initSortable();
				self.recountWidgetsNumbers();
			} )

				.on( 'click', 'div.field-repeater a.btn-remove-current-row', function( e ) {
					if ( confirm( POJO_ADMIN.lang_remove_row ) ) {
						//var $repeater_wrap = $( this ).closest( 'div.field-repeater' );
						
						$( this )
							.closest( 'div.field-repeater-row' )
							.remove();
						
						self.recountWidgetsNumbers();
					}
				} )
				.ajaxSuccess( function( e, xhr, settings ) {
					if ( undefined === settings.data ) {
						return;
					}
					
					if ( undefined === settings.data.search ) {
						return;
					}
					
					if ( -1 !== settings.data.search( 'action=save-widget' ) ) {
						return;
					}

					if ( PJ_Admin_Widget_Repeater.cache.isInPojoBuilder ) {
						return;
					}
					
					self.initSortable();
					self.recountWidgetsNumbers();
					self.buildModals();
				} )
				
				.on( 'widget-updated widget-added', function() {
					self.initSortable();
					self.recountWidgetsNumbers();
					self.buildModals();
				} )

				.on( 'click', 'a.widget-button-collapse', function( e ) {
					$( '#' + $( this ).data( 'toggle_class' ) )
						.slideToggle( 'fast' );
				} )
			
			
				.on( 'click', 'div.field-repeater a.btn-edit-current-row', function() {
					var $fieldWrapper = $( this ).closest( 'div.field-repeater-row' ),
						$fieldContent = $fieldWrapper.find( 'div.pojo-admin-modal, div.pojo-admin-modal-backdrop' );
					
					$fieldContent.fadeIn( 'fast' );
				} )
			
				.on( 'click', 'a.pojo-modal-close', function( e ) {
					e.preventDefault();
					var $fieldWrapper = $( this ).closest( 'div.field-repeater-row' ),
						$fieldContent = $fieldWrapper.find( 'div.pojo-admin-modal, div.pojo-admin-modal-backdrop' );
					
					$fieldContent.fadeOut( 'fast' );
				} );

			$( document ).on( 'dragend.h5s', 'div.field-repeater-list', function() {
				self.recountWidgetsNumbers();
			} );
			self.recountWidgetsNumbers();
		},
		
		buildModals: function() {
			var $modalTemplate = $( '#tmpl-pojo-admin-modal' );
			if ( 1 > $modalTemplate.length ) {
				return;
			}
			var modalTemplate = $modalTemplate.html();
			
			$( 'div.field-row-content' ).each( function() {
				if ( $( this ).hasClass( 'pojo-admin-modal-ready' ) ) {
					return;
				}
				
				var $fieldRow = $( this ).closest( 'div.field-repeater-row, div.field-repeater-clone' ),
					html = modalTemplate;
				
				html = html
					.replace( /\{header\}/g, $fieldRow.find( 'div.number-row' ).html() )
					.replace( /\{content\}/g, $( this ).html() );
				
				$( this )
					.html( html )
					.addClass( 'pojo-admin-modal-ready' );
			} );
		},
		
		recountWidgetsNumbers: function() {
			var $repeaterList = $( 'div.field-repeater-list' );
			$repeaterList.each( function( index ) {
				$( this ).find( 'div.field-repeater-row' ).each( function( i ) {
					$( this ).find( 'span.number-row-span' ).text( '#' + ( i + 1 ) );
				} );
			} );

			if ( this.cache.isInPojoBuilder ) {
				$repeaterList.each( function() {
					$( this ).find( ':input:first' ).trigger( 'change' );
				} );
			}
		},
		
		initSortable: function() {
			$( 'div.field-repeater-list' ).pojo_sortable( {
				opacity: 0.6,
				cursor: 'move',
				scroll: true,
				revert: true,
				forceHelperSize: true,
				forcePlaceholderSize: true,
				handle: 'div.field-handle',
				items: 'div.field-repeater-row'
			} );
		},

		init: function() {
			this.cacheElements();
			this.buildElements();
			this.bindEvents();
		}
	};

	$( document ).ready( function( $ ) {
		PJ_Admin_Widget_Repeater.init();
	} );

}( jQuery, window, document ) );