/* global wp Backbone */

(function( $ ) {
	'use strict';
	
	wp.media.model.BtItems = Backbone.Collection.extend( {
		props: new Backbone.Model( {item: ''} ),
		model: Backbone.Model.extend( {
			defaults: {
				type: '',
				group: 'all'
			}
		} )
	} );

}( jQuery ));