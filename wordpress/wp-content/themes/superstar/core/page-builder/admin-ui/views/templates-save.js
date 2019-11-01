/* global wp */

(function( $ ) {
	'use strict';

	wp.media.view.BtTemplates.Save = wp.media.view.Settings.extend( {
		template: wp.template( 'bt-template-local-add' ),

		events: {
			'keyup input': 'updateHandler'
		},

		initialize: function() {
			wp.media.view.Settings.prototype.initialize.apply( this, arguments );
			_.extend( this.events, wp.media.view.Settings.prototype.events );

			this.update( 'label' );
		}
		
	} );

	wp.media.view.BtTemplates.Save.Toolbar = wp.media.view.Toolbar.Select.extend( {
		initialize: function() {
			// Call 'initialize' directly on the parent class.
			wp.media.view.Toolbar.Select.prototype.initialize.apply( this, arguments );

			this.listenTo( this.controller.state().model, 'change', this.refresh );
		},

		refresh: function() {
			var label = this.controller.state().model.get( 'label' );
			this.get( 'select' ).model.set( 'disabled', ! label );
			/**
			 * call 'refresh' directly on the parent class
			 */
			wp.media.view.Toolbar.Select.prototype.refresh.apply( this, arguments );
		}
	} );
}( jQuery ));