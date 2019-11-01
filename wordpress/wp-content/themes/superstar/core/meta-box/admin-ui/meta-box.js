var ATMB = ATMB || {};

ATMB.methods = ( function( $ ) {
	var locations = [
			{
				key: 'page_template',
				selector: '#page_template'
			},
			{
				key: 'page_testing',
				selector: '#post_id'
			}
		],
		
		data = {
			page_template: null,
			page_testing: null
		},

		initLocation = function() {
			for ( var i = 0; i < locations.length; i++ ) {
				if ( 'page_template' === locations[ i ].key ) {
					console.log( $( locations[ i ].selector ).val() );
				}
				if ( 'page_testing' === locations[ i ].key ) {
					console.log( "I'm just testing.." );
				}
			}
		},

		updateLocation = function() {

		},

		init = function() {
			add_sortable();
		},

		add_sortable = function( wrap ) {
			wrap = wrap || $( 'ol.atmb-repeater-ol' );
			/*wrap.sortable( {
				opacity: 0.6,
				cursor: 'move',
				revert: true,
				forceHelperSize: true,
				forcePlaceholderSize: true,
				scroll: true
			} );*/

			wrap.pojo_sortable( {
				opacity: 0.6,
				cursor: 'move',
				revert: true,
				handle: 'div.row-sortable-handle',
				forceHelperSize: true,
				forcePlaceholderSize: true,
				scroll: true
			} );
		},

		update_fields = function( selector, isEnable ) {
			isEnable = isEnable || true;
			$( ':input', selector ).each( function() {
				if ( isEnable ) {
					$( this ).removeAttr( 'disabled' );
				}
				else {
					$( this ).attr( 'disabled', 'disabled' );
				}
			} );
		};
	
	return {
		init: init,
		initLocation: initLocation,
		init_sortable: add_sortable
	};
} ( jQuery ) );

jQuery(document).ready(function($) {
	//ATMB.methods.initLocation();
	ATMB.methods.init();

	$( document ).on( 'click', 'a.atmb-button-collapse', function( e ) {
		$( '#' + $( this ).data( 'toggle_class' ) )
			.slideToggle( 'fast' );
		
		$( this ).toggleClass( 'open close' );
	} );
	
	function atmb_disable_or_enable_fields( selector, is_enable ) {
		is_enable = is_enable || false;
		$( ':input', selector ).each( function() {
			if ( is_enable ) {
				$( this ).removeAttr( 'disabled' );
			}
			else {
				$( this ).attr( 'disabled', 'disabled' );
			}
		} );
	}
	
	$( 'a.atmb-btn-add-repeater-clone' ).on( 'click', function( e ) {
		e.preventDefault();
		var repeater_wrap = $( this ).closest( '.atmb-repeater-wrap' ),
			repeater_clone = repeater_wrap.find( '.atmb-repeater-clone' ),
			new_row = repeater_clone.clone(),
			date_obj = new Date();
		
		new_row
			.html( new_row.html().replace( /SKIP_FIELD/g, date_obj.getTime() ) )
			.removeClass( 'atmb-repeater-clone hidden' )
			.addClass( 'atmb-repeater-row' );
		
		repeater_wrap
			.find( 'ol.atmb-repeater-ol' )
			.append( new_row );
		
		new_row.trigger( 'pojo_metabox_repeater_new_item' );

		atmb_disable_or_enable_fields( new_row, true );
		ATMB.methods.init_sortable();
	} );

	$( document ).on( 'click', 'a.atmb-btn-repeater-remove-row', function( e ) {
		if ( confirm( POJO_ADMIN.lang_remove_row ) ) {
			$( this )
				.closest( '.atmb-repeater-row' )
				.hide( 'fast' )
				.remove();
		}
	} );
	
	atmb_disable_or_enable_fields( $( '.atmb-repeater-clone' ) );

	$('div.atmb-wrap-fields').on('change', '.select-show-or-hide-fields :input', function() {
		if ( $(this).prop('disabled') ) {
			return;
		}
		
		if ( 'radio' === $( this ).attr( 'type' ) && ! $( this ).prop( 'checked' ) ) {
			return;
		}

		var $fieldValue = $(this).val();
		if ( 'checkbox' === $(this).attr('type') ) {
			$fieldValue = $(this).prop('checked') ? 'on' : 'off';
		}

		var $wrap_fields = $(this).closest('div.atmb-wrap-fields'),
		//$all_fields = $wrap_fields.find('div[class^="' + $(this).attr('name') + '-"]'),
			$all_fields = $('div[data-show_on_' + $(this).attr('name') + ']', $wrap_fields),
		//$found_fields = $wrap_fields.find('div.' + $(this).attr('name') + '-' + $(this).val());
			$found_fields = $('div[data-show_on_' + $(this).attr('name') + '="' + $fieldValue + '"]', $wrap_fields);
		//$allChildShowOrHideFields = $found_fields.not('[name="' + $(this).attr('name') + '"]').find(':input');

		$all_fields.hide();

		atmb_disable_or_enable_fields($all_fields);
		atmb_disable_or_enable_fields($found_fields, true);
		//alert($wrap_fields.find(':input').serialize());

		$found_fields
			.slideDown('fast')
			.find(':input')
			.trigger('change');
		//$allChildShowOrHideFields.trigger('change')

	}).on('change', '.update-wrap-on-change :input', function() {
			var $wrapFields = $(this).closest('div.atmb-wrap-fields'),
				$fields = $wrapFields.find(':input').serialize();

			$.post(ajaxurl, 'post_id=' + $('#post_ID').val() + '&action=atmb_update_wrap&reference=' + $(this).attr('name') + '&' + $fields, function(msg) {
				if ( msg === '' ) {
					return;
				}
				$wrapFields.html(msg);
				//alert(msg);
				$('.select-show-or-hide-fields :input').trigger('change');
			});
		});

	setTimeout( function() {
		$('div.atmb-wrap-fields .select-show-or-hide-fields :input').trigger('change');
	}, 400 );

	$( document ).on('click', 'div.at-upload-image-wrap a.at-image-upload',function(e) {
		var $field_wrap = $(this).closest('div.at-upload-image-wrap'),
			$field_input = $field_wrap.find('input.at-image-upload-field'),
			$ul_wrap = $field_wrap.find('ul.at-image-ul-wrap'),
			$add_image_button = $( this ).closest( 'div.single-image' ),
			$media_frame;

		$media_frame = wp.media.frames.downloadable_file = wp.media( {
			// Set the title of the modal.
			title   : 'Add Image',
			button  : {
				text: $( this ).data( 'label_add_to_post' )
			},
			library: {
				type: 'image'
			},
			multiple: $field_input.data('multiple')
		} );

		$media_frame.on('select', function() {
			var selection = $media_frame.state().get('selection'),
				attachment_ids = $field_input.val();

			selection.map(function(attachment) {
				attachment = attachment.toJSON();

				if ( attachment.id ) {
					if ( $field_input.data('multiple') ) {
						attachment_ids = attachment_ids ? attachment_ids + ',' + attachment.id : attachment.id;
					}
					else {
						attachment_ids = attachment.id;
						$('li.image', $ul_wrap).remove();

						$add_image_button.addClass( 'hidden' );
					}
					var preview_image = attachment.url;
					if ( undefined !== attachment.sizes.thumbnail ) {
						preview_image = attachment.sizes.thumbnail.url;
					}
					$ul_wrap.append('' +
						'<li class="image" data-attachment_id="' + attachment.id + '">' +
						'   <img src="' + preview_image + '" />' +
						'   <a href="javascript:void(0);" class="image-delete button">Remove</a>' +
						'</li>');
				}

			});

			$field_input.val(attachment_ids);
		});

		// Finally, open the modal.
		$media_frame.open();
	}).on('click', 'div.at-upload-image-wrap a.image-delete', function(e) {
			var $field_wrap = $(this).closest('div.at-upload-image-wrap'),
				$field_input = $field_wrap.find('input.at-image-upload-field'),
				$ul_wrap = $field_wrap.find('ul.at-image-ul-wrap'),
				$add_image_button = $field_wrap.find( 'div.single-image' ),
				attachment_ids = '';

			$(this).closest('li.image').remove();

			$( 'li', $ul_wrap ).each( function() {
				attachment_ids = attachment_ids ? attachment_ids + ',' + $(this).data('attachment_id') : $( this ).data( 'attachment_id' );
			} );

			$field_input.val( attachment_ids );
			$add_image_button.removeClass( 'hidden' );
		});
});