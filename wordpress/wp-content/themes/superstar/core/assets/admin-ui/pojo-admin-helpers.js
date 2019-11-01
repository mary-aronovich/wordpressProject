/* global jQuery, wp, _, postL10n, confirm */
/*!
 * Pojo Admin Helpers.
 * @link http://pojo.me/
 */
'use strict';

var Pojo_Admin_Helpers = ( function( $, window ) {

	var _is_changed = false,
		
		_init = function() {
			_init_image_upload();
			_init_media();
			_init_tabs();
			_on_before_unload();
		},
		
		_init_image_upload = function() {
			$( 'div.pojo-setting-upload-image-wrap' ).each( function() {
				var is_ready = false;
				if ( $( this ).hasClass( 'setting-upload-image-ready' ) ) {
					is_ready = true;
				}
				
				if ( 1 <= $( this ).closest( 'div.pb-row-clone' ).length ) {
					is_ready = true;
				}
				
				if ( 1 <= $( this ).closest( 'div.pb-clone-widget' ).length ) {
					is_ready = true;
				}
				
				if ( 1 <= $( this ).closest( 'div.field-repeater-clone' ).length ) {
					is_ready = true;
				}
				
				if ( 1 <= $( this ).closest( '#widget-list' ).length ) {
					is_ready = true;
				}
				
				if ( 1 <= $( this ).closest( '#available-widgets' ).length ) {
					is_ready = true;
				}
				
				if ( is_ready ) {
					return;
				}
				
				$( this ).addClass( 'setting-upload-image-ready' );

				$( this ).on( 'click', 'a.at-image-upload',function( e ) {
					var $field_wrap = $( this ).closest( 'div.pojo-setting-upload-image-wrap' ),
						$field_input = $field_wrap.find( 'input.at-image-upload-field' ),
						$ul_wrap = $field_wrap.find( 'ul.at-image-ul-wrap' ),
						$add_image_button = $( this ).closest( 'div.single-image' ),
						$media_frame;

					$media_frame = wp.media.frames.downloadable_file = wp.media( {
						// Set the title of the modal.
						title: 'Add Image',
						button: {
							text: 'Add to Post'
						},
						multiple: $field_input.data( 'multiple' )
					} );

					$media_frame.on( 'select', function() {
						var selection = $media_frame.state().get( 'selection' ),
							attachment_urls = $field_input.val();

						selection.map( function( attachment ) {

							attachment = attachment.toJSON();

							if ( attachment.id ) {
								if ( $field_input.data( 'multiple' ) ) {
									attachment_urls = attachment_urls ? attachment_urls + ',' + attachment.id : attachment.id;
								}
								else {
									attachment_urls = attachment.url;
									$( 'li.image', $ul_wrap ).remove();

									$add_image_button.addClass( 'hidden' );
								}
								$ul_wrap.append( '' +
									'<li class="image" data-attachment_id="' + attachment.id + '">' +
									'   <img src="' + attachment.url + '" />' +
									'   <a href="javascript:void(0);" class="image-delete button">Remove</a>' +
									'</li>' );
							}

						} );

						$field_input
							.val( attachment_urls )
							.trigger( 'change' );
					} );

					// Finally, open the modal.
					$media_frame.open();
				} ).on( 'click', 'a.image-delete', function( e ) {
					var $field_wrap = $( this ).closest( 'div.pojo-setting-upload-image-wrap' ),
						$field_input = $field_wrap.find( 'input.at-image-upload-field' ),
						$ul_wrap = $field_wrap.find( 'ul.at-image-ul-wrap' ),
						$add_image_button = $field_wrap.find( 'div.single-image' ),
						attachment_ids = '';

					$( this ).closest( 'li.image' ).remove();

					$( 'li', $ul_wrap ).each( function() {
						attachment_ids = attachment_ids ? attachment_ids + ',' + $( this ).data( 'attachment_id' ) : $( this ).data( 'attachment_id' );
					} );

					$field_input
						.val( attachment_ids )
						.trigger( 'change' );
					$add_image_button.removeClass( 'hidden' );
				} );
			} );

			$( '.pojo-setting-upload-file-wrap' ).each( function() {
				var is_ready = false;
				if ( $( this ).hasClass( 'setting-upload-file-ready' ) ) {
					is_ready = true;
				}

				if ( 1 <= $( this ).closest( 'div.pb-row-clone' ).length ) {
					is_ready = true;
				}

				if ( 1 <= $( this ).closest( 'div.pb-clone-widget' ).length ) {
					is_ready = true;
				}

				if ( 1 <= $( this ).closest( 'div.field-repeater-clone' ).length ) {
					is_ready = true;
				}

				if ( 1 <= $( this ).closest( '#widget-list' ).length ) {
					is_ready = true;
				}

				if ( 1 <= $( this ).closest( '#available-widgets' ).length ) {
					is_ready = true;
				}

				if ( is_ready ) {
					return;
				}

				$( this ).addClass( 'setting-upload-file-ready' );

				$( this ).on( 'click', 'a.pojo-button-file-upload', function( e ) {
					e.preventDefault();

					var $field_wrap = $( this ).closest( '.pojo-setting-upload-file-wrap' ),
						$field_input = $field_wrap.find( 'input.pojo-input-file-upload' ),
						$media_frame;

					$media_frame = wp.media.frames.downloadable_file = wp.media( {
						// Set the title of the modal.
						title: $( this ).data( 'uploader-title' ),
						button: {
							text: $( this ).data( 'uploader-button-text' )
						},
						multiple: false
					} );

					$media_frame.on( 'select', function() {
						var selection = $media_frame.state().get( 'selection' ),
							attachment_urls = $field_input.val();

						selection.map( function( attachment ) {

							attachment = attachment.toJSON();

							if ( attachment.id ) {
								if ( $field_input.data( 'multiple' ) ) {
									attachment_urls = attachment_urls ? attachment_urls + ',' + attachment.id : attachment.id;
								}
								else {
									attachment_urls = attachment.url;
								}
							}

						} );

						$field_input
							.val( attachment_urls )
							.trigger( 'change' );
					} );

					// Finally, open the modal.
					$media_frame.open();
				} );
			} );
		},
		
		_init_media = function() {
			// This is new code for WordPress media.
			
			var media = wp.media,
				_local_frame = [],
				
				class_parent_wrap = 'div.pojo-media-manager',
				class_field = 'input.pojo-media-field',
				class_preview_wrap = 'ul.pojo-media-preview-html',
				class_button_insert_media = 'a.pojo-insert-media',
				class_button_empty_media = 'a.pojo-empty-media',

				defaults = {
					state: 'gallery-library',
					label_add_to_post: 'Add to Post',
					class_name: '',
					fetch: 'id' // `id`, `url`
				},

				_fetch_selection = function( ids, options ) {
					var id_array = ids.split(','),
						args = {
							orderby: 'post__in',
							order: 'ASC',
							type: 'image',
							perPage: -1,
							post__in: id_array
						},
						attachments = wp.media.query( args ),
						selection = new wp.media.model.Selection( attachments.models, {
							props: attachments.props.toJSON(),
							multiple: true
						} );

					if ( 'gallery-library' === options.state && id_array.length && !isNaN( parseInt( id_array[0], 10 ) ) ) {
						options.state = 'gallery-edit';
					}

					return selection;
				};
			
			$( document ).on( 'click', class_button_empty_media, function( e ) {
				e.preventDefault();
				
				if ( confirm( 'Are you sure?' ) ) {
					$( this )
						.addClass( 'hidden' )
						.closest( class_parent_wrap )
						.find( class_preview_wrap + ' > li' )
						.remove()
						.end()
						.find( class_field )
						.val( '' );
				}
			} )
				
				.on( 'click', class_preview_wrap + ' > li > img', function( e ) {
					$( this )
						.closest( class_parent_wrap )
						.find( class_button_insert_media )
						.trigger( 'click' );
				} )
				
				.on( 'click', class_button_insert_media, function( e ) {
				e.preventDefault();
				
				var this_button = $( this ),
					parent_wrap = this_button.closest( class_parent_wrap ),
					options = $.extend( defaults, this_button.data() ),
					field = parent_wrap.find( class_field ),
					preview_wrap = parent_wrap.find( class_preview_wrap ),
					current_selection = _fetch_selection( field.val(), options ),
					// Random id
					frame_id = _.random( 0, 999999999999999999 );

				// If the media frame already exists, reopen it.
				if ( _local_frame[ frame_id ] ) {
					_local_frame[ frame_id ].open();
					return;
				}

				// Create the media frame.
				_local_frame[ frame_id ] = wp.media( {
					frame: options.frame,
					state: options.state,
					library: {
						type: 'image'
					},
					button: {
						text: options.label_add_to_post
					},
					className: 'media-frame ' + options.class_name,
					selection: current_selection
				} );
				

				_local_frame[ frame_id ].on( 'select update insert', function( e ) {
					var selection,
						state = _local_frame[ frame_id ].state();

					// multiple items
					if ( 'undefined' !== typeof e ) {
						selection = e;
					}
					// single item
					else {
						selection = state.get( 'selection' );
					}
					
					var values,
						element,
						display,
						preview_img = '',
						preview_html = '';

					values = selection.map( function( attachment ) {
						element = attachment.toJSON();
						
						if ( 'id' === options.fetch ) {
							display = state.display( attachment ).toJSON();
							//preview_html = element.sizes[ display.size ].url;
							preview_img = undefined === element.sizes.thumbnail ? element.url : element.sizes.thumbnail.url;
							
							preview_html += '<li><img src="' + preview_img + '" /></li>';
							return element.id;
						}
						else if ( 'url' === options.fetch ) {
							display = state.display( attachment ).toJSON();
							//preview_html = element.sizes[ display.size ].url;
							preview_img = undefined === element.sizes.thumbnail ? element.url : element.sizes.thumbnail.url;
							
							preview_html += '<li><img src="' + preview_img + '" /></li>';
							return element.url;
						}
						
						return '';
					} );

					preview_wrap.html( preview_html );
					field
						.val( values.join( ',' ) )
						.trigger( 'change' );
					parent_wrap.find( class_button_empty_media ).removeClass( 'hidden' );
				} );

				// Finally, open the modal
				_local_frame[ frame_id ].open();
			} );
		},

		_init_tabs = function() {
			var navTabActiveClass = 'nav-tab-active',
				tabActiveClass = 'tab-active';
			
			$( '.pojo-admin-tabs a.pojo-tab-link' ).on( 'click', function( e ) {
				e.preventDefault();

				var $tabContent = $( this )
					.closest( '.pojo-admin-tabs' )
					.next( 'div.pojo-admin-tabs-content' );
				
				$( this )
					.closest( '.pojo-admin-tabs' )
					.find( '.pojo-tab-link' )
					.removeClass( navTabActiveClass );
				
				$( this ).addClass( navTabActiveClass );
				
				$tabContent.find( '.pojo-admin-tab-panel' ).removeClass( tabActiveClass );
				$( $( this ).attr( 'href' ) ).addClass( tabActiveClass );
			} );
			
			$( '.pojo-admin-tabs li a' ).eq( 0 ).addClass( navTabActiveClass );
			$( '.pojo-admin-tabs-content .pojo-admin-tab-panel' ).eq( 0 ).addClass( tabActiveClass );
		},

		_on_before_unload = function() {
			$( window ).on( 'beforeunload.edit-post', function() {
				if ( _is_changed ) {
					return postL10n.saveAlert;
				}
			} );
		},
		
		_editor_changed = function() {
			_is_changed = true;
		};

	return {
		init: _init,
		init_image_upload: _init_image_upload,
		editorChanged: _editor_changed
	};
}( jQuery, window ) );