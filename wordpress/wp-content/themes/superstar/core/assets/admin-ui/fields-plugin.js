/*!
 * Author: Yakir Sitbon.
 * Project Url: https://github.com/KingYes/jquery-radio-image-select
 * Author Website: http://www.yakirs.net/
 * Version: 1.0.1
 **/
(function( $ ) {
	// Register jQuery plugin.
	$.fn.radioImageSelect = function( options ) {
		// Default var for options.
		var defaults = {
				// Img class.
				imgItemClass: 'radio-select-img-item',
				// Img Checked class.
				imgItemCheckedClass: 'item-checked',
				// Is need hide label connected?
				hideLabel: true
			},

			/**
			 * Method firing when need to update classes.
			 */
				syncClassChecked = function( img ) {
				var radioName = img.prev( 'input[type="radio"]' ).attr( 'name' );

				$( 'input[name="' + radioName + '"]' ).each( function() {
					// Define img by radio name.
					var myImg = $( this ).next( 'img' );

					// Add / Remove Checked class.
					if ( $( this ).prop( 'checked' ) ) {
						myImg.addClass( options.imgItemCheckedClass );
					} else {
						myImg.removeClass( options.imgItemCheckedClass );
					}
				} );
			};

		// Parse args..
		options = $.extend( defaults, options );

		// Start jQuery loop on elements..
		return this.each( function() {
			if ( $( this ).hasClass( 'radio-image-ready' ) ) {
				return;
			}
			$( this )
				.addClass( 'radio-image-ready' )
				// First all we are need to hide the radio input.
				.hide()
				// And add new img element by data-image source.
				.after( '<img src="' + $( this ).data( 'image' ) + '" alt="radio image" />' );

			// Define the new img element.
			var img = $( this ).next( 'img' );
			// Add item class.
			img.addClass( options.imgItemClass );

			// Check if need to hide label connected.
			if ( options.hideLabel ) {
				$( 'label[for=' + $( this ).attr( 'id' ) + ']' ).hide();
			}

			// When we are created the img and radio get checked, we need add checked class.
			if ( $( this ).prop( 'checked' ) ) {
				img.addClass( options.imgItemCheckedClass );
			}

			$( this ).on( 'change', function() {
				$( 'input[name="' + $( this ).attr( 'name' ) + '"]' ).each( function() {
					// Define img by radio name.
					var myImg = $( this ).next( 'img' );

					// Add / Remove Checked class.
					if ( $( this ).prop( 'checked' ) ) {
						myImg.addClass( options.imgItemCheckedClass );
					} else {
						myImg.removeClass( options.imgItemCheckedClass );
					}
				} );
			} );

			// Create click event on img element.
			img.on( 'click', function( e ) {
				$( this )
					// Prev to current radio input.
					.prev( 'input[type="radio"]' )
					// Set checked attr.
					.attr( 'checked', 'checked' )
					// Run change event for radio element.
					.trigger( 'change' );

				// Firing the sync classes.
				syncClassChecked( $( this ) );
			} );
		} );
	};
})( jQuery );

/*!
 * HTML5 Sortable jQuery Plugin
 * https://github.com/voidberg/html5sortable
 *
 * Original code copyright 2012 Ali Farhadi.
 * This version is mantained by Alexandru Badiu <andu@ctrlz.ro>
 *
 * Thanks to the following contributors: rodolfospalenza, bistoco, flying-sheep, ssafejava, andyburke, daemianmack, OscarGodson.
 *
 * Released under the MIT license.
 */
(function ($) {
	'use strict';
	var dragging, draggingHeight, placeholders = $();
	$.fn.pojo_sortable = function (options) {
		var method = String(options);
		options = $.extend({
			connectWith: false,
			placeholder: null,
			dragImage: null
		}, options);
		return this.each(function () {
			var index, items = $(this).children(options.items), handles = options.handle ? items.find(options.handle) : items;
			if (method === 'reload') {
				$(this).children(options.items).off('dragstart.h5s dragend.h5s selectstart.h5s dragover.h5s dragenter.h5s drop.h5s');
			}
			if (/^enable|disable|destroy$/.test(method)) {
				var citems = $(this).children($(this).data('items')).attr('draggable', method === 'enable');
				if (method === 'destroy') {
					$(this).off('sortupdate');
					$(this).removeData('opts');
					citems.add(this).removeData('connectWith items')
						.off('dragstart.h5s dragend.h5s dragover.h5s dragenter.h5s drop.h5s').off('sortupdate');
					handles.off('selectstart.h5s');
				}
				return;
			}
			var soptions = $(this).data('opts');
			if (typeof soptions === 'undefined') {
				$(this).data('opts', options);
			}
			else {
				options = soptions;
			}
			var startParent, newParent;
			var placeholder = ( options.placeholder === null ) ? $('<' + (/^ul|ol$/i.test(this.tagName) ? 'li' : 'div') + ' class="sortable-placeholder"/>') : $(options.placeholder).addClass('sortable-placeholder');
			$(this).data('items', options.items);
			placeholders = placeholders.add(placeholder);
			if (options.connectWith) {
				$(options.connectWith).add(this).data('connectWith', options.connectWith);
			}
			items.attr('role', 'option');
			items.attr('aria-grabbed', 'false');
// Setup drag handles
			handles.attr('draggable', 'true').not('a[href], img').on('selectstart.h5s', function() {
				if (this.dragDrop) {
					this.dragDrop();
				}
				return false;
			}).end();
// Handle drag events on draggable items
			items.on('dragstart.h5s', function(e) {
				var dt = e.originalEvent.dataTransfer;
				dt.effectAllowed = 'move';
				dt.setData('text', '');
				if (options.dragImage && dt.setDragImage) {
					dt.setDragImage(options.dragImage, 0, 0);
				}
				index = (dragging = $(this)).addClass('sortable-dragging').attr('aria-grabbed', 'true').index();
				draggingHeight = dragging.outerHeight();
				startParent = $(this).parent();
			}).on('dragend.h5s',function () {
				if (!dragging) {
					return;
				}
				dragging.removeClass('sortable-dragging').attr('aria-grabbed', 'false').show();
				placeholders.detach();
				newParent = $(this).parent();
				if (index !== dragging.index() || startParent.get(0) !== newParent.get(0)) {
					dragging.parent().triggerHandler('sortupdate', {item: dragging, oldindex: index, startparent: startParent, endparent: newParent});
				}
				dragging = null;
				draggingHeight = null;
			}).add([this, placeholder]).on('dragover.h5s dragenter.h5s drop.h5s', function(e) {
				if (!items.is(dragging) && options.connectWith !== $(dragging).parent().data('connectWith')) {
					return true;
				}
				if (e.type === 'drop') {
					e.stopPropagation();
					placeholders.filter(':visible').after(dragging);
					dragging.trigger('dragend.h5s');
					return false;
				}
				e.preventDefault();
				e.originalEvent.dataTransfer.dropEffect = 'move';
				if (items.is(this)) {
					var thisHeight = $(this).outerHeight();
					if (options.forcePlaceholderSize) {
						placeholder.height(draggingHeight);
					}
// Check if $(this) is bigger than the draggable. If it is, we have to define a dead zone to prevent flickering
					if (thisHeight > draggingHeight) {
// Dead zone?
						var deadZone = thisHeight - draggingHeight, offsetTop = $(this).offset().top;
						if (placeholder.index() < $(this).index() && e.originalEvent.pageY < offsetTop + deadZone) {
							return false;
						}
						else if (placeholder.index() > $(this).index() && e.originalEvent.pageY > offsetTop + thisHeight - deadZone) {
							return false;
						}
					}
					dragging.hide();
					$(this)[placeholder.index() < $(this).index() ? 'after' : 'before'](placeholder);
					placeholders.not(placeholder).detach();
				} else if (!placeholders.is(this) && !$(this).children(options.items).length) {
					placeholders.detach();
					$(this).append(placeholder);
				}
				return false;
			});
		});
	};
})(jQuery);