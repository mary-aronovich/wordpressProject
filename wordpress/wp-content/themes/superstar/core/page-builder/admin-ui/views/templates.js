/* global wp */

(function( $ ) {
	'use strict';

	wp.media.view.BtTemplates = wp.media.View.extend( {
		className: 'attachments-browser bt-items-wrap',

		initialize: function() {
			this.createToolbar();
			this.createLibrary();
			this.createSidebar();
		},

		createLibrary: function() {
			this.items = new wp.media.view.BtTemplates.Library( {
				controller: this.controller,
				collection: this.collection,
				selection: this.options.selection,
				type: this.options.type,
				data: this.options.data
			} );
			this.views.add( this.items );
		},

		createToolbar: function() {
			var library = this.collection;
			//var group = library.props.get( 'group' );

			this.toolbar = new wp.media.view.Toolbar( {
				controller: this.controller
			} );
			this.views.add( this.toolbar );

			// Dropdown filter
			/*this.toolbar.set( 'filters', new wp.media.view.BtTemplates.Filters( {
				controller: this.controller,
				model: this.collection.props,
				priority: -80
			} ).render() );*/
			
			// Search field
			this.toolbar.set( 'search', new wp.media.view.Search( {
				controller: this.controller,
				model: this.collection.props,
				priority: 60
			} ).render() );
		},
		
		createSidebar: function() {
			var options = this.options;
			var selection = options.selection;
			var sidebar = this.sidebar = new wp.media.view.Sidebar( {
				controller: this.controller,
				type: options.type
			} );

			this.views.add( sidebar );

			selection.on( 'selection:single', this.createSingle, this );
			selection.on( 'selection:unsingle', this.disposeSingle, this );

			if ( selection.single() ) {
				this.createSingle();
			}
		},

		createSingle: function() {
			var sidebar = this.sidebar,
				single = this.options.selection.single();
			
			sidebar.set( 'details', new wp.media.view.BtTemplates.Deatils( {
				controller: this.controller,
				model: single,
				type: this.options.type,
				data: this.options.data,
				priority: 80
			} ) );
		},

		disposeSingle: function() {
			var sidebar = this.sidebar;

			sidebar.unset( 'details' );
		}
	} );
}( jQuery ));