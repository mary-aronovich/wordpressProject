/* global tinyMCE, tinyMCEPreInit, quicktags, QTags, ajaxurl, wp, _, confirm, Pojo_Admin_Helpers, POJO_ADMIN */

var Pojo_Admin_Main = ( function( $, window, document, undefined ) {
	'use strict';
	
	var init = function() {
			_admin_preview_form();

			_admin_ui_tweaks();

			_post_formats_metaboxes();
			
			Pojo_Admin_Helpers.init();
			
			$( '#pojo-customizer-import' ).on( 'change', 'input.pojo-import-file', function() {
				$( this ).closest( '#pojo-customizer-import' ).find( 'input.pojo-import-submit' ).removeAttr( 'disabled' );
			} );

			// on every ajax call (example on new widget added)
			$( document ).ajaxSuccess( function( evt, request, settings ) {
				if ( ! settings.data || undefined === settings.data.split ) {
					return;
				}
				var qs = settings.data.split("+").join(" ");
				var params = {},
					tokens,
					re = /[?&]?([^=]+)=([^&]*)/g;
				while ( ( tokens = re.exec( qs ) ) ) {
					params[ decodeURIComponent( tokens[1] ) ] = decodeURIComponent( tokens[2] );
				}
				if ( params['widget-id'] && ! params.delete_widget ) {
					Pojo_Admin_Helpers.init_image_upload();
				}
			} )
			
				.on( 'widget-added', function( e, widget_el ) {
					Pojo_Admin_Helpers.init_image_upload();
				} );

			$( 'input.atmb-field-radio-image' ).radioImageSelect( {
				hideLabel: false
			} );
		},

		_admin_preview_form = function() {
			var $wp_wrap = $( '#wpwrap' );
			$( 'a.btn-admin-preview-shortcode' ).on( 'click', function() {
				$wp_wrap
					.fadeOut( 'slow' )
					.after( '<div id="admin-preview-iframe">' +
						'   <div class="iframe-inside">' +
						'       <a href="javascript:void(0);" class="btn-close-iframe button">Close</a>' +
						'       <br />' +
						'       <iframe />' +
						'   </div>' +
						'</div>' );

				$.post( ajaxurl, { action: $( this ).data( 'action' ), id: $( this ).data( 'id' ) }, function( data ) {
					var win = $( '#admin-preview-iframe' ).find( 'iframe' ).contents()[0];
					win.write( data );
					win.write( '<style>body{background: #fff !important;}</style>' );
					win.close();
				} );
			} );
			
			var index_trash = 1,
				last_element_id = '';

			last_element_id = 'my-widget-visual-editor-' + index_trash;
			
			$( document ).on( 'click', 'a.btn-pb-open-visual-editor', function() {
				var textarea_editor = $( this ).next( 'textarea' ),
					tinyMCEexecCommandAdd = 'mceAddControl';
				
				if ( window.tinyMCE.majorVersion >= 4 ) {
					tinyMCEexecCommandAdd = 'mceAddEditor';
				}
				//wp_wrap.fadeOut( 'slow' );

				if ( 1 > $( '#admin-visual-editor-preview-iframe' ).length ) {
					$wp_wrap.append(
							'<div id="admin-visual-editor-preview-iframe" class="pojo-admin-modal">' +
							'   <div class="iframe-inside">' +
							'       <div class="wrapper">' +
							'           <div class="wrapper-inner">' +
							'               <div class="modal-header">' +
							'                   <h3 class="modal-title">' + 'Editor' + '</h3>' +
							'                   <a class="media-modal-close pojo-modal-close" href="#" title="Close"><span class="media-modal-icon"></span></a>' +
							'               </div>' +
							'               <div id="iframe-text-editor-loading"><span class="spinner"></span></div>' +
							'               <div class="modal-content">' +
							'                   <div class="text-editor-wrap"></div>' +
							'               </div>' +
							'               <div class="modal-footer">' +
							'                   <a href="#" data-target_element="' + textarea_editor.attr( 'id' ) + '" class="button button-primary btn-save-iframe button-large">Update</a>' +
							'               </div>' +
							'           </div>' +
							'       </div>' +
							'   </div>' +
							'</div>' +
							'<div id="admin-visual-editor-preview-iframe-backdrop"></div>'
					);

					/*$wp_wrap.append( '<div id="admin-visual-editor-preview-iframe">' +
					 '   <div class="iframe-inside">' +
					 '<a class="media-modal-close" href="#" title="Close"><span class="media-modal-icon"></span></a>' +
					 '       <a href="javascript:void(0);" class="btn-save-iframe button button-primary" data-target_element="' + textarea_editor.attr( 'id' ) + '">Update Content</a>' +
					 '       <br />' +
					 '       <div id="iframe-text-editor-loading"><span class="spinner"></span></div>' +
					 '       <div class="text-editor-wrap"></div>' +
					 '   </div>' +
					 '</div>' +
					 '<div id="admin-visual-editor-preview-iframe-backdrop"></div>' );*/

					$( '#admin-visual-editor-preview-iframe' )
						.fadeIn( 'slow' );

					index_trash++;
					//last_element_id = 'my-widget-visual-editor-' + index_trash;
					$.post( ajaxurl, { action: $( this ).data( 'action' ), id: last_element_id }, function( data ) {
						var $win = $( '#admin-visual-editor-preview-iframe' ).find( 'div.text-editor-wrap' );
						$win.html( data );
						
						window.tinyMCEPreInit.mceInit[ last_element_id ] = _.extend({}, tinyMCEPreInit.mceInit.content );
						window.tinyMCEPreInit.mceInit[ last_element_id ].wp_autoresize_on = false;

						if ( _.isUndefined( tinyMCEPreInit.qtInit[ last_element_id ] ) ) {
							window.tinyMCEPreInit.qtInit[ last_element_id ] = _.extend( {}, tinyMCEPreInit.qtInit.replycontent, { id: last_element_id } );
						}

						QTags.instances[0] = false;
						//QTags._buttonsInit();
						quicktags( window.tinyMCEPreInit.qtInit[ last_element_id ] );

						window.switchEditors.go( last_element_id, 'tmce' );

						window.tinyMCE.execCommand( tinyMCEexecCommandAdd, false, last_element_id );

						setTimeout( function() {
							var textarea = $( '#' + last_element_id );
							if ( textarea.is( ':visible' ) ) {
								textarea.val( textarea_editor.val() );
							} else {
								tinyMCE.activeEditor.setContent( textarea_editor.val() );
							}

							$( '#iframe-text-editor-loading' ).fadeOut( 'fast' );
							$win.show();
						}, 500 );
					} );
				} else {
					$( '#admin-visual-editor-preview-iframe' )
						.fadeIn( 'slow' )
						.find( 'a.btn-save-iframe' )
						.data( 'target_element', textarea_editor.attr( 'id' ) );
					$( '#admin-visual-editor-preview-iframe-backdrop' ).show();

					var $editor_loading = $( '#iframe-text-editor-loading' );
					$editor_loading.show();

					$( '#admin-visual-editor-preview-iframe' ).find( 'div.text-editor-wrap' ).hide();

					setTimeout( function() {
						var textarea = $( '#' + last_element_id );
						var isHTMLMode = ( typeof tinyMCE !== 'undefined' ) && tinyMCE.activeEditor && ! tinyMCE.activeEditor.isHidden();
						//if ( textarea.is( ':visible' ) ) {
						if ( ! isHTMLMode ) {
							textarea.val( textarea_editor.val() );
						} else {
							tinyMCE.activeEditor.setContent( textarea_editor.val() );
						}

						$editor_loading.fadeOut( 'fast' );
						$( '#admin-visual-editor-preview-iframe' ).find( 'div.text-editor-wrap' ).show();
					}, 500 );
				}
				
				$( 'body' ).addClass( 'no-allow-scroll' );
			} );
			
			$( document ).on( 'click', 'a.btn-save-iframe', function( e ) {
				e.preventDefault();
				//wp_wrap.fadeIn( 'fast' );
				var $textarea = $( '#' + last_element_id ),
					return_val = '';
				
				if ( $textarea.is( ':visible' ) ) {
					$textarea.text( $textarea.val() );
					return_val = $textarea.html();
					// hotfix for newlines..
					//return_val = return_val.replace( /(\r\n|\n|\r)/gm, '' );
					//return_val = window.switchEditors.wpautop( return_val ).replace( /(\r\n|\n|\r)/gm, '' );
				} else {
					return_val = $( '<div />' ).text( tinyMCE.activeEditor.getContent() ).html();
				}
				
				$( '#' + $( this ).data( 'target_element' ) )
					.html( return_val )
					.trigger( 'change' );
				
				$( '#admin-visual-editor-preview-iframe, #admin-visual-editor-preview-iframe-backdrop' ).hide();

				$( 'body' ).removeClass( 'no-allow-scroll' );
			} );
			
			$( document ).on( 'click', '#admin-visual-editor-preview-iframe-backdrop, #admin-visual-editor-preview-iframe a.media-modal-close', function( e ) {
				e.preventDefault();
				$( '#admin-visual-editor-preview-iframe, #admin-visual-editor-preview-iframe-backdrop' ).hide();
				$( 'body' ).removeClass( 'no-allow-scroll' );
			} );
			
			$( document ).on( 'click', 'a.btn-close-iframe', function( e ) {
				e.preventDefault();
				$wp_wrap.fadeIn( 'fast' );
				$( '#admin-preview-iframe' ).remove();
			} );
		},
		
		_post_formats_metaboxes = function() {
			var $wrapperPanels = $( '#pojo-formats-panel-sortables' ),
				$allFormatsPanels = $( '> div.postbox', $wrapperPanels ),

				hideFormats = function() {
					$allFormatsPanels.hide();
				},
				
				showFormat = function( format ) {
					$( '#pojo-format-' + format ).show();
				};
			
			$( 'input.post-format', '#post-formats-select' ).on( 'change', function() {
				hideFormats();
				var format_selected = $( this ).val();
				if ( '0' === format_selected ) {
					return;
				}
				
				showFormat( format_selected );
			} );
			
			$( 'input.post-format:checked', '#post-formats-select' ).trigger( 'change' );

			$allFormatsPanels.each( function() {
				$( 'label[for=' + $( this ).prop( 'id' ) + '-hide]' ).remove();
			} );

			$allFormatsPanels.find( '.hndle' ).removeClass( 'hndle' );
		},
		
		_admin_ui_tweaks = function() {
			$( 'div.handlediv, h3.hndle', '#pojo-slideshow-slides' ).remove();
			//$( 'div.inside, #minor-publishing', 'body.post-type-pojo_slideshow #post-body > #post-body-content' ).remove();
			
			$( '#adminmenu #toplevel_page_pojo-home div.wp-menu-image' ).addClass( 'picon-theme-options');
			
			$( 'body.post-type-pojo_slideshow #post-body' )
				.find( '#post-body-content div.inside' )
				.remove();
				//.end()
				//.find( '#minor-publishing' )
				//.find( '.misc-pub-post-status, .misc-pub-visibility, .misc-pub-curtime, #minor-publishing-actions', '#minor-publishing' )
				//.remove();
				
		};
	
	return {
		init: init
	};
} ( jQuery, window, document ) );

jQuery( document ).ready( function( $ ) {
	Pojo_Admin_Main.init();
} );