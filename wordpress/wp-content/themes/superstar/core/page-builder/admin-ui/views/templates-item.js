/* global wp */

(function( $ ) {
	'use strict';

	wp.media.view.BtTemplates.Item = wp.media.view.Attachment.extend( {
		className: 'attachment bt-item',
		events: {
			'click .attachment-preview': 'toggleSelectionHandler',
			'click a': 'preventDefault'
		},

		initialize: function() {
			this.template = wp.template( 'bt-template-' + this.options.type + '-item' );
			wp.media.view.Attachment.prototype.initialize.apply( this, arguments );
		},

		render: function() {
			this.$el.html( this.template( this.model.toJSON() ) );
			this.updateSelect();

			return this;
		}
	} );
}( jQuery ));