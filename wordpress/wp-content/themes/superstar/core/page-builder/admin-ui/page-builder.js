/* global jQuery, Pojo_Admin_Helpers, PJ_Admin_Widget_Repeater, Pojo_Admin_Main, POJO_ADMIN, confirm, ajaxurl, prompt */

var PJ_Page_Builder = ( function( $, window ) {
	'use strict';
	
	var _MAX_BLOCK_SIZE = 12,

		loading_class = 'pb-loading',
		
		_updateWidgetData = function( $widget_options ) {
			$widget_options.val(
				$widget_options.data( 'size' ) + ';' + $widget_options.data( 'parent' ) + ';' + $widget_options.data( 'id_base' )
			);
			$widget_options.trigger( 'change' );
		},
		
		_widgetTriggerChange = function() {
			$( 'div.pb-widget' ).each( function() {
				$( this ).find( ':input:first' ).trigger( 'change' );
			} );
		},
		
		_columnTriggerChange = function() {
			var $rows = $( '#pb-rows' ),
				$activeRows = $rows.find( 'div.pb-row.pb-active-row' ),
				hasMultiRows = ( 1 < $activeRows.length );

			if ( hasMultiRows ) {
				$rows.addClass( 'pb-has-multi-rows' );
			} else {
				$rows.removeClass( 'pb-has-multi-rows' );
			}
			
			$activeRows.each( function() {
				var $thisRow = $( this ),
					$activeColumns = $thisRow.find( 'div.pb-active-column' ),
					hasMultiColumns = ( 1 < $activeColumns.length );
				
				if ( hasMultiColumns ) {
					$thisRow.addClass( 'pb-row-has-multi-columns' );
				} else {
					$thisRow.removeClass( 'pb-row-has-multi-columns' );
				}

				$activeColumns.each( function() {
					var $thisColumn = $( this ),
						hasWidgets = ( 1 <= $thisColumn.find( '.pb-widget' ).length );

					if ( hasWidgets ) {
						$thisColumn.addClass( 'has-widgets' );
					} else {
						$thisColumn.removeClass( 'has-widgets' );
					}
				} );

			} );
		},
		
		_isCloneWidget = function( element ) {
			return element.closest( 'div.pb-widget' ).hasClass( 'pb-clone-widget' );
		},
		
		_initWidget = function( $selector ) {
			if ( undefined !== $selector.data( 'pb_name' ) ) {
				return;
			}
			
			var isInCloseWrapper = 1 <= $selector.closest( 'div.field-repeater-clone' ).length;
			
			if ( isInCloseWrapper ) {
				return;
			}
			
			$selector.data( 'pb_name', $selector.prop( 'name' ) );
			$selector.removeAttr( 'name' );
		},
		
		_startInitWidgets = function( first_time ) {
			first_time = first_time || false;
			
			var $rows = $( '#pb-rows' );
			
			$( '#pb-widgets' ).find( '.pb-widget' ).each( function() {
				$( this ).draggable( {
					connectToSortable: '#pb-rows > div.pb-row > div.pb-advanced-columns > div.pb-advanced-column > div.pb-widget-columns',
					helper: 'clone',
					revert: 'invalid',
					start: function( event, ui ) {},
					stop: function() {}
				} );
			} );

			$rows.sortable( {
				handle: 'div.pb-row-sortable-handle',
				forceHelperSize: true,
				forcePlaceholderSize: true,
				placeholder: 'pb-row-placeholder',
				items: 'div.pb-row'
			} )

				.find( '> div.pb-row > div.pb-advanced-columns > div.pb-advanced-column > div.pb-widget-columns' ).sortable( {
					forceHelperSize: true,
					forcePlaceholderSize: true,
					placeholder: 'pb-builder-placeholder',
					handle: 'div.pb-widget-top',
					connectWith: '#pb-widgets, #pb-rows > div.pb-row > div.pb-advanced-columns > div.pb-advanced-column > div.pb-widget-columns',
					items: 'div.pb-widget:not(.pb-widget-loading)'
				} )
				.droppable( {
					accept: "#pb-widgets .pb-widget, #pb-rows .pb-widget",
					activeClass: "pb-state-hover"
				} )
				.on( 'sortstart', function( event, ui ) {
					ui.placeholder.css( 'width', ( ui.helper.css( 'width' ).replace( 'px', '' ) - 27 ) + 'px' );
					ui.placeholder.css( 'height', ( ui.helper.css( 'height' ).replace( 'px', '' ) - 10 ) + 'px' );
				} )
				.on( 'sortstop', function( event, ui ) {
					if ( ui.item.hasClass( 'pb-clone-widget' ) ) {
						ui.item
							.html( ui.item.html().replace( /REPLACE_TO_ID/g, new Date().getTime() ) )
							.removeClass( 'pb-clone-widget' )
							.find( 'a.pb-action-toggle' )
							.trigger( 'click' );
						
						ui.item.css( {
							width: '',
							height: ''
						} );
						
						_startInitWidgets();
						ui.item.find( ':input:not(.pb-input-widget-content)' ).each( function() {
							_initWidget( $( this ) );
						} );
						_widgetTriggerChange();
					}
					var $widgetOptions = ui.item.find( 'input.pb-widget-options' );
					$widgetOptions.data( 'parent', ui.item.closest( 'div.pb-advanced-column' ).find( 'input.pb-advanced-column-id' ).val() );
					_updateWidgetData( $widgetOptions );
					
					//ui.item.find( 'input.pb-widget-parent' ).val( ui.item.closest( 'div.pb-row' ).find( 'input.pd-row-id' ).val() );
					_updateBlockOrder();
				} );
			
			$( 'input.pojo-color-picker-hex' ).each( function() {
				if ( ! _isCloneWidget( $( this ) ) ) {
					var $picker = $( this );
					$picker.wpColorPicker( {
						change: function( event, options ) {
							$picker
								.val( $picker.wpColorPicker( 'color' ) )
								.trigger( 'change' );
						}	
					} );
				}
			} );
			
			var $widget_input_titles = $( 'input.widefat.pb-widget-title, input.widefat.pb-widget-pb_admin_label', '#pb-rows' );
			$widget_input_titles.on( 'change', function() {
				var $widgetWrapper = $( this ).closest( 'div.pb-widget-content' ),
					$widgetTitle = $widgetWrapper.find( 'input.widefat.pb-widget-title' ),
					$adminLabel = $widgetWrapper.find( 'input.widefat.pb-widget-pb_admin_label' );
				
				var currentTitle = $adminLabel.val();
				if ( '' === currentTitle && 1 <= $widgetTitle.length ) {
					currentTitle = $widgetTitle.val();
				}
				
				if ( '' !== currentTitle ) {
					currentTitle = ': ' + currentTitle;
				}
				$( this )
					.closest( 'div.pb-widget-inside' )
					.find( 'div.pb-widget-title h4 span.in-widget-title' )
					.html( currentTitle );
			} );
			$widget_input_titles.trigger( 'change' );

			$rows.find( 'input.pb-field-radio-image' ).radioImageSelect( {
				hideLabel: false
			} );
			
			Pojo_Admin_Helpers.init_image_upload();
			PJ_Admin_Widget_Repeater.initSortable();
			PJ_Admin_Widget_Repeater.recountWidgetsNumbers();
			PJ_Admin_Widget_Repeater.buildModals();

			if ( 1 < $( 'div.pb-active-row' ).length ) {
				$( 'div.pb-row-tools' ).fadeIn( 'fast' );
			}

			$rows.find( 'div.pb-row.pb-active-row' ).each( function() {
				var $activeColumns = $( this ).find( 'div.pb-active-column' ),
					activeColumnsCount = $activeColumns.length;
				
				if ( 1 > activeColumnsCount ) {
					$( this ).find( 'a.pb-add-column' ).trigger( 'click' );
					return;
				}
				
				if ( 1 < activeColumnsCount ) {
					//$( this ).find( 'div.pb-remove-column-action' ).fadeIn( 'fast' );
				}
			} );

			_columnTriggerChange();
		},
		
		_updateBlockOrder = function() {
			Pojo_Admin_Helpers.editorChanged();
			
			_columnTriggerChange();
		},
		
		_fix_input_values = function( wrap ) {
			wrap.find( ':input' ).each( function(  ) {
				var tag_name = $( this ).prop( 'tagName' ).toLowerCase();

				if ( 'input' === tag_name ) {
					var input_type = $( this ).attr( 'type' );
					if ( 'checkbox' === input_type || 'radio' === input_type ) {
						if ( $( this ).prop( 'checked' ) ) {
							$( this ).attr( 'checked', 'checked' );
						} else {
							$( this ).removeAttr( 'checked' );
						}
					} else {
						$( this ).attr( 'value', $( this ).val() );
					}
				}
				else if ( 'select' === tag_name ) {
					var current_value = $( this ).val();
					$( this ).find( 'option' ).each( function() {
						if ( $( this ).is( ':selected' ) ) {
							$( this ).attr( 'selected', 'selected' );
						} else {
							$( this ).removeAttr( 'selected' );
						}
					} );
				}
				else if ( 'textarea' === tag_name ) {
					$( this ).html( $( this ).val() );
				}
			} );
		},

		_duplicate_row = function( row_wrap ) {
			_fix_input_values( row_wrap );
			
			var row_id = row_wrap.find( 'input.pd-row-id' ).val(),
				$new_row_wrap = row_wrap.clone(),
				new_row_id = new Date().getTime(),
				re = new RegExp( row_id, 'g' ),
				index = 0;

			$new_row_wrap
				.html( $new_row_wrap.html().replace( re, new_row_id ) );
			
			$new_row_wrap.find( '.pb-advanced-column' ).each( function() {
				var $thisColumn = $( this ),
					column_id = $thisColumn.find( 'input.pb-advanced-column-id' ).val(),
					new_column_id = ( ++index + '' + new Date().getTime() ),
					re = new RegExp( column_id, 'g' );
				
				if ( 'COLUMN_REPLACE_TO_ID' === column_id ) {
					return;
				}
				
				$thisColumn.html( $thisColumn.html().replace( re, new_column_id ) );

				$thisColumn.find( '.pb-widget' ).each( function() {
					var widget_id = $( this ).find( 'input.pb-widget-id' ).val(),
						new_widget_id = ( ++index + '' + new Date().getTime() ),
						re = new RegExp( widget_id, 'g' );

					$( this )
						.html( $( this ).html().replace( re, new_widget_id ) );
				} );
			} );

			row_wrap.after( $new_row_wrap );

			if ( 1 < $( 'div.pb-active-row' ).length ) {
				$( 'div.pb-row-tools' ).fadeIn( 'fast' );
			}

			$.post( ajaxurl, 'action=pb_get_duplicate_row&' + $new_row_wrap.find( ':input' ).serialize(), function( data ) {
				$new_row_wrap.replaceWith( data );
				
				_startInitWidgets();
				_updateBlockOrder();

				$( 'div.pb-widget :input:not(.pb-input-widget-content)', '#pb-rows' ).each( function() {
					_initWidget( $( this ) );
				} );
				_widgetTriggerChange();

				$( 'div.pb-row-tools' ).show();
			} );
		},
		
		_duplicate_widget = function( widget_wrap ) {
			_fix_input_values( widget_wrap );
			
			var widget_id = widget_wrap.find( 'input.pb-widget-id' ).val(),
				$new_widget_wrap = widget_wrap.clone(),
				new_widget_id = new Date().getTime(),
				re = new RegExp( widget_id, 'g' );
			
			$new_widget_wrap
				.html( $new_widget_wrap.html().replace( re, new_widget_id ) )
				.addClass( 'pb-widget-loading' )
				.find( 'div.pb-widget-content' )
				.css( 'display', 'none' );

			widget_wrap.after( $new_widget_wrap );
			
			$.post( ajaxurl, 'action=pb_get_duplicate_widget&' + $new_widget_wrap.find( ':input' ).serialize(), function( data ) {
				$new_widget_wrap.replaceWith( data );

				_startInitWidgets();
				_updateBlockOrder();

				$( 'div.pb-widget :input:not(.pb-input-widget-content)', '#pb-rows' ).each( function() {
					_initWidget( $( this ) );
				} );
				_widgetTriggerChange();
			} );
		},
		
		_addColumns = function( $row, sizes ) {
			sizes = sizes || [ 12 ];

			var $columnsWrapper = $row.find( 'div.pb-advanced-columns' ),
				$columnClone = $columnsWrapper.find( 'div.pb-advanced-column-clone' );

			$.each( sizes, function( index, size ) {
				var $newColumn = $columnClone.clone(),
					newColumnID = new Date().getTime() + 50 + index;
				
				$newColumn
					.find( 'input.pb-advanced-column-id' )
					.val( newColumnID );

				$newColumn
					.data( 'current_size', size )
					.removeClass( 'pb-column-12' )
					.addClass( 'pb-column-' + size )
					.find( 'input.pb-advanced-column-size' )
					.val( size );

				$newColumn
					.find( '.pb-column-display-width' )
					.text( parseInt( size * 100 / _MAX_BLOCK_SIZE, 10 ) + '%' );

				$newColumn
					.html( $newColumn.html().replace( /COLUMN_REPLACE_TO_ID/g, newColumnID ) )
					.removeClass( 'pb-advanced-column-clone' )
					.addClass( 'pb-active-column' )
					.appendTo( $columnsWrapper );
			} );
		},

		_init = function() {
			_startInitWidgets( true );

			$( document ).on( 'change', 'div.pb-row.pb-active-row div.pb-widget :input', function() {
				var $widget = $( this ).closest( 'div.pb-widget' );
				$widget.find( 'input.pb-input-widget-content' ).val(
					$widget.find( ':input:not(.pb-input-widget-content)' ).map( function( i, el ) {
						var input_type = $( this ).attr( 'type' );

						if ( 'checkbox' === input_type || 'radio' === input_type ) {
							if ( ! $( this ).prop( 'checked' ) ) {
								return '';
							}
						}
						
						var dataName = $( this ).data( 'pb_name' );
						if ( undefined === dataName ) {
							return '';
						}
						
						var value = $( this ).val();
						value = encodeURIComponent( value );
						
						return encodeURIComponent( dataName ) + '=' + value;
					} ).get().join( '&' )
				);
			} );

			var $widgetInputs = $( 'div.pb-widget :input:not(.pb-input-widget-content)', '#pb-rows' );
			$widgetInputs.each( function() {
				_initWidget( $( this ) );
			} );
			_widgetTriggerChange();
			
			// Make sure JS is turn on
			$( '#post' ).append( '<input type="hidden" name="_pojo_builder_ready" value="true" />' );
			
			$( document ).on( 'click', 'a.pb-resize-btn', function( e ) {
				var $current_item = $( this ).closest( 'div.pb-widget' ),
					current_size = parseInt( $current_item.data( 'current_size' ), null ),
					next_size = current_size;

				if ( 'add' === $( this ).data( 'resize_action' ) && _MAX_BLOCK_SIZE > current_size ) {
					next_size = current_size + 1;
				} else if ( 'less' === $( this ).data( 'resize_action' ) && 1 < current_size ) {
					next_size = current_size - 1;
				}
				
				$current_item
					.removeClass( 'pb-column-' + current_size )
					.addClass( 'pb-column-' + next_size )
					.data( 'current_size', next_size )
					// find any element with this class.
					.find( '.pb-current-size' )
					.text( parseInt( next_size * 100 / _MAX_BLOCK_SIZE, 10 ) + '%' );

				var $widget_options = $current_item.find( 'input.pb-widget-options' );
				$widget_options.data( 'size', next_size );
				
				_updateWidgetData( $widget_options );
			} )
				
				.on( 'click', 'a.pb-action-toggle, a.pb-widget-control-close', function( e ) {
					$( this )
						.closest( '.pb-widget' )
						.find( 'div.pb-widget-content' )
						.slideToggle( 300, 'easeOutQuad' );
				} )
				
				.on( 'click', 'a.pb-widget-control-remove', function( e ) {
					if ( confirm( POJO_ADMIN.lang_remove_widget ) ) {
						$( this )
							.closest( '.pb-widget' )
							.remove();

						_updateBlockOrder();
					}
				} )
				
				.on( 'click', 'a.pb-widget-control-duplicate', function( e ) {
					_duplicate_widget( $( this ).closest( '.pb-widget' ) );
				} )
				
				.on( 'click', 'a.pb-btn-row-duplicate', function( e ) {
					_duplicate_row( $( this ).closest( '.pb-row' ) );
				} )
				
				.on( 'click', 'a.pb-open-add-row-toggle', function( e ) {
					e.preventDefault();
					
					$( 'div.pb-add-row-options' ).slideToggle( 'fast' );
				} )
				
				.on( 'click', 'a.pb-add-row-btn', function( e ) {
					e.preventDefault();
					
					var $newRow = $( '#pb-rows' ).find( 'div.pb-row-clone' ).clone(),
						new_row_id = new Date().getTime(),
						sizes = $( this ).data( 'sizes' ) + '';
					
					sizes = sizes.split( ',' );

					$newRow
						.find( 'input.pd-row-id' )
						.val( new_row_id );
					
					$newRow
						.html( $newRow.html().replace( /ROW_REPLACE_TO_ID/g, new_row_id ) )
						.removeClass( 'pb-row-clone' )
						.addClass( 'pb-active-row' );
					
					_addColumns( $newRow, sizes );
					
					$newRow.appendTo( '#pb-rows' );

					if ( 1 < $( 'div.pb-active-row' ).length ) {
						$( 'div.pb-row-tools' ).fadeIn( 'fast' );
					}

					$( 'div.pb-add-row-options' ).slideUp( 'fast' );

					_startInitWidgets();
				} )
				
				.on( 'click', 'a.pb-btn-delete-row', function( e ) {
					var active_rows = $( 'div.pb-active-row' ).length;
					if ( 1 < active_rows ) {
						if ( confirm( POJO_ADMIN.lang_remove_row ) ) {
							$( this )
								.closest( 'div.pb-active-row' )
								.remove();

							if ( 2 === active_rows ) {
								//$( 'div.pb-row-tools' ).fadeOut( 'fast' );
							}
							_updateBlockOrder();
						}
					}
				} )

				.on( 'click', 'a.pb-column-resize-btn', function( e ) {
					var MAX_COLUMN_SIZE = 12,
						$current_item = $( this ).closest( 'div.pb-advanced-column' ),
						current_size = parseInt( $current_item.data( 'current_size' ), null ),
						next_size = current_size;

					if ( 'add' === $( this ).data( 'resize_action' ) && MAX_COLUMN_SIZE > current_size ) {
						next_size = current_size + 1;
					} else if ( 'less' === $( this ).data( 'resize_action' ) && 1 < current_size ) {
						next_size = current_size - 1;
					}

					$current_item
						.removeClass( 'pb-column-' + current_size )
						.addClass( 'pb-column-' + next_size )
						.data( 'current_size', next_size )
					
						// find any element with this class.
						.find( '.pb-column-display-width' )
						.text( parseInt( next_size * 100 / MAX_COLUMN_SIZE, 10 ) + '%' );

					$current_item
						.find( 'input.pb-advanced-column-size' )
						.val( next_size );
				} )
				
				.on( 'click', 'a.pb-add-column', function( e ) {
					e.preventDefault();

					var $rowWrapper = $( this ).closest( 'div.pb-row' );
					_addColumns( $rowWrapper );
					
					_startInitWidgets();
				} )
				
				.on( 'click', 'a.pb-remove-column', function( e ) {
					e.preventDefault();

					var $rowWrapper = $( this ).closest( 'div.pb-advanced-columns' ),
						activeColumns = $rowWrapper.find( 'div.pb-active-column' ).length;
					
					if ( 1 < activeColumns ) {
						if ( confirm( POJO_ADMIN.lang_remove_row ) ) {
							$( this )
								.closest( 'div.pb-advanced-column' )
								.remove();

							//if ( 2 === activeColumns ) {
								//$rowWrapper.find( 'div.pb-remove-column-action' ).fadeOut( 'fast' );
							//}
							_updateBlockOrder();
						}
					}
				} )

				.on( 'click', 'a.pb-btn-row-setting-toggle', function( e ) {
					$( this )
						.toggleClass( 'active' )
						.closest( 'div.pb-row' )
						.find( 'div.pb-row-setting-content' )
						.slideToggle( 300, 'easeOutQuad' );
				} )
			
				.on( 'change', '#page-builder :input', function() {
					var $widget_wrap = $( this ).closest( 'div.pb-widget' );
					if ( $widget_wrap.hasClass( 'pb-clone-widget' ) ) {
						return;
					}
					Pojo_Admin_Helpers.editorChanged();
				} );
			
			if ( 1 > $( 'div.pb-active-row' ).length ) {
				$( 'a.pb-add-row-btn:first' ).trigger( 'click' );
			} else {
				$( 'div.pb-row-tools' ).fadeIn( 'fast' );
			}
		},
		
		_import_template = function( id, type ) {
			var $current_li = $( this ).closest( 'li' );
			$current_li.addClass( loading_class );

			$.post( ajaxurl, { action: 'pb_import_template', template_id: id, template_type: type }, function( data ) {
				$( '#pb-rows' ).append( data );

				_startInitWidgets();
				_updateBlockOrder();

				$( 'div.pb-widget :input:not(.pb-input-widget-content)', '#pb-rows' ).each( function() {
					_initWidget( $( this ) );
				} );
				_widgetTriggerChange();

				$current_li.removeClass( loading_class );
			} );
		};
	
	return {
		init: _init,
		initWidget: _initWidget,
		widgetTriggerChange: _widgetTriggerChange,
		importTemplate: _import_template,
		fixInputValues: _fix_input_values
	};
} ( jQuery, window ) );

jQuery( document ).ready( function( $ ) {
	'use strict';
	PJ_Page_Builder.init();
} );