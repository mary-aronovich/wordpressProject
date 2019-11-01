/* global wp */

(function( $ ) {
	'use strict';

	wp.media.controller.BtTemplates.Library = Backbone.Collection.extend( {
		props: new Backbone.Model( {
			group: 'all',
			search: ''
		} ),

		initialize: function( models ) {
			this.items = new Backbone.Collection( models );
			
			this.listenTo( this.items, 'add update remove reset', this.reInitialize );
		},

		reInitialize: function() {
			var library = this;
			var items = this.items.toJSON();
			var props = this.props.toJSON();

			_.each( props, function( val, filter ) {
				if ( library.filters[filter] ) {
					items = _.filter( items, library.filters[filter], val.toLowerCase() );
				}
			}, this );

			this.reset( items );
		},

		filters: {
			group: function( item ) {
				var group = this;

				return ( 'all' === group || item.group === group || '' === item.group );
			},
			search: function( item ) {
				var term = this;
				var result;

				if ( '' === term ) {
					result = true;
				}
				else {
					result = _.any( ['id', 'label'], function( key ) {
						var value = ( item[key] + '' ).toLowerCase();
						
						return value && -1 !== value.search( this );
					}, term );
				}

				return result;
			}
		}
	} );
}( jQuery ));