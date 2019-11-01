/* global wp */

(function( $ ) {
	'use strict';

	wp.media.model.BtTemplates = {};
	
	wp.media.model.BtTemplates.Item = Backbone.Model.extend( {
		defaults: {
			thumbnail: '',
			label: '',
			uploading: true
		}
	} );
}( jQuery ));