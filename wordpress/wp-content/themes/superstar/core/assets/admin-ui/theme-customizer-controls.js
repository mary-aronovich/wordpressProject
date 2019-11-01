/* global wp */
'use strict';

var pojo_admin_customizer_controls = ( function( $, window ) {
	var _bodyListener = function( e ) {
			if ( e.data.typo_wrap.hasClass( 'customize-control-font-open' ) ) {
				e.data.wrap.trigger( 'click' );
			}
		},
		
		_change_customize_setting = function( id, value ) {
			wp.customize.control.instance( id ).setting.set( value );
		},
		//customize-theme-controls
		_control_customizer_toggle_wrap = function( typo_wrap, force_hide ) {
			force_hide = force_hide || false;

			var open_class = 'customize-control-toggle-wrap-open';
			
			if ( force_hide ) {
				typo_wrap
					.removeClass( open_class )
					.hide();
			} else {
				typo_wrap
					.toggleClass( open_class )
					.slideToggle( 'fade' );
			}
		},

		init = function() {
			var api = wp.customize;

			$( 'input.atmb-field-radio-image' ).radioImageSelect( {
				hideLabel: false
			} );
			
			$( 'a.pojo-btn-control-toggle-wrap' ).on( 'click', function( event ) {
				event.stopPropagation();
				_control_customizer_toggle_wrap( $( this ).next( 'div.customize-control-toggle-wrap' ) );
			} );

			$( 'div.accordion-container > #customize-theme-controls' ).on( 'click keydown', '.accordion-section-title', function( event ) {
				if ( event.type === 'keydown' && 13 !== event.which ) { // "return" key
					return;
				}
				event.preventDefault(); // Keep this AFTER the key filter above

				_control_customizer_toggle_wrap( $( '#customize-theme-controls' ).find( 'div.customize-control-toggle-wrap' ), true );
			});

			//$( 'div.at-upload-image-wrap' ).on( 'click', 'a.at-image-upload',function( e ) {
			$( 'div.pojo-customizer-upload-image-wrap' ).on( 'click', 'a.at-image-upload',function( event ) {
				var $field_wrap = $( this ).closest( 'div.pojo-customizer-upload-image-wrap' ),
					$field_input = $field_wrap.find( 'input.at-image-upload-field' ),
					$ul_wrap = $field_wrap.find( 'ul.at-image-ul-wrap' ),
					$add_image_button = $( this ).closest( 'div.single-image' ),
					$media_frame,
					$media_customize_setting_link = $field_input.data( 'customize-setting-link' );

				$media_frame = wp.media.frames.downloadable_file = wp.media( {
					// Set the title of the modal.
					title: 'Add Image',
					button: {
						text: 'Insert into theme'
					},
					library: {
						type: 'image'
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
								attachment_urls = attachment_urls ? attachment_urls + ',' + attachment.url : attachment.url;
							}
							else {
								attachment_urls = attachment.url;
								$( 'li.image', $ul_wrap ).remove();
								
								$add_image_button.addClass( 'hidden' );
								
								if ( 'background' === $field_wrap.data( 'field_type' ) ) {
									$field_wrap
										.closest( 'div.customize-control-content' )
										.find( 'a.pojo-btn-control-toggle-wrap' )
										.css( 'background-image', 'url("' + attachment_urls + '")' );
								}
							}

							$ul_wrap.append( '' +
								'<li class="image" data-attachment_url="' + attachment.url + '">' +
								'   <img src="' + attachment.url + '" />' +
								'   <a href="javascript:void(0);" class="image-delete button">Remove</a>' +
								'</li>' );
						}

					} );

					$field_input
						.val( attachment_urls )
						.trigger('change');
					//_change_customize_setting( $media_customize_setting_link, attachment_urls );
				} );

				// Finally, open the modal.
				$media_frame.open();
			} ).on( 'click', 'a.image-delete', function( event ) {
					var $field_wrap = $( this ).closest( 'div.pojo-customizer-upload-image-wrap' ),
						$field_input = $field_wrap.find( 'input.at-image-upload-field' ),
						$ul_wrap = $field_wrap.find( 'ul.at-image-ul-wrap' ),
						$add_image_button = $field_wrap.find( 'div.single-image' ),
						attachment_urls = '';

					$( this ).closest( 'li.image' ).remove();

					$( 'li', $ul_wrap ).each( function() {
						attachment_urls = attachment_urls ? attachment_urls + ',' + $( this ).data( 'attachment_url' ) : $( this ).data( 'attachment_url' );
					} );

					$field_input
						.val( attachment_urls )
						.trigger( 'change' );
					$add_image_button.removeClass( 'hidden' );
					if ( 'background' === $field_wrap.data( 'field_type' ) ) {
						var a_toggle = $field_wrap
							.closest( 'div.customize-control-content' )
							.find( 'a.pojo-btn-control-toggle-wrap' );
						a_toggle
							.css( 'background-image', 'url("' + a_toggle.data( 'default_image' ) + '")' );
					}
					//_change_customize_setting( $field_input.data( 'customize-setting-link' ), attachment_urls );
				} );
			
			$.each( wp.customize.settings.controls, function( id, data ) {
				if ( 'typography' === data.type || 'pojo_background' === data.type || 'two_color' === data.type ) {
					var control = api.control.instance( id ),
						picker = control.container.find( '.typography-field-color' );

					picker.wpColorPicker( {
						change: function( event, options ) {
							control.settings[ event.target.attributes['data-customize-setting-link'].value ].set( picker.wpColorPicker( 'color' ) );
						},
						clear: function( event, options ) {
							control.settings[ event.target.attributes['data-customize-setting-link'].value ].set( false );
						}
					} );
				}
			} );
		};

	return {
		init: init
	};

}( jQuery, window ) );

jQuery( document ).ready( function( $ ) {
	setTimeout( function() {
		pojo_admin_customizer_controls.init();
	}, 100 );

} );