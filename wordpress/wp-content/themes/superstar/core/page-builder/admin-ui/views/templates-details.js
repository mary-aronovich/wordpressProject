/* global wp */

(function( $ ) {
	'use strict';

	wp.media.view.BtTemplates.Deatils = wp.media.View.extend( {
		
		initialize : function() {
			this.template = wp.template( 'bt-template-' + this.options.type + '-sidebar' );
			wp.media.View.prototype.initialize.apply( this, arguments );
		},
		
		render: function() {
			var options = _.defaults( this.model.toJSON(), this.options.data );

			this.$el.html( this.template( options ) );
		}
	} );
}( jQuery ));