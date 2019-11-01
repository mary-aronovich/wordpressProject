/* global wp, ajaxurl */

(function( $, window ) {
	'use strict';

	wp.media.controller.BtTemplates.Save = wp.media.controller.State.extend( {
		defaults: {
			id: 'bt-save',
			menu: 'default',
			title: window.PojoBtTemplates.l10n.save_template,
			content: 'bt-save-template',
			toolbar: 'bt-save-template'
		},

		initialize: function() {
			this.model = this.model || new Backbone.Model();
		},

		activate: function() {
			this.frame.on( 'open', this.refresh, this );
		},

		deactivate: function() {
			this.frame.off( 'open', this.refresh, this );
		},

		refresh: function() {},

		btGetContent: function() {
			return new wp.media.view.BtTemplates.Save( {
				controller: this.frame,
				model: this.model
			} );
		}

	} );
}( jQuery, window ));