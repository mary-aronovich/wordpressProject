/* global wp */

(function( $ ) {
	'use strict';

	wp.media.view.BtTemplates.Filters = wp.media.view.AttachmentFilters.extend( {
		createFilters: function() {
			this.filters = {
				all: {
					text: 'Select All',
					props: {
						group: 'all'
					}
				}
			};

			var groups = this.controller.state().get( 'data' ).groups;
			_.each( groups, function( text, id ) {
				this.filters[id] = {
					text: text,
					props: {
						group: id
					}
				};
			}, this );
		},

		change: function() {
			var filter = this.filters[this.el.value];

			if ( filter ) {
				this.model.set( 'group', filter.props.group );
			}
		}
	} );
}( jQuery ));