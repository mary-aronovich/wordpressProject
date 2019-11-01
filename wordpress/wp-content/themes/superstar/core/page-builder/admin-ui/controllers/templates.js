/* global wp, ajaxurl */

(function( $ ) {
	'use strict';

	wp.media.controller.BtTemplates = wp.media.controller.State.extend( {
		defaults: {
			id: 'bt-local',
			menu: 'default',
			type: '',
			toolbar: 'bt-select'
		},

		initialize: function() {
			var items = this.get( 'data' ).items;
			var library = this.get( 'library' );
			var selection = this.get( 'selection' );

			if ( !( library instanceof wp.media.controller.BtTemplates.Library ) ) {
				library = new wp.media.controller.BtTemplates.Library( items );
				library.props.on( 'change', this.BtResetLibrary, this );

				this.set( 'library', library );
			}

			if ( !( selection instanceof wp.media.model.Selection ) ) {
				this.set( 'selection', new wp.media.model.Selection( selection, {
					multiple: false
				} ) );
			}
		},

		activate: function() {
			this.frame.on( 'open', this.refresh, this );
		},

		deactivate: function() {
			this.frame.off( 'open', this.refresh, this );
		},

		refresh: function() {
			this.BtResetFilter();
			this.BtUpdateSelection();
		},

		BtFetchItems: function() {
			var library = this.get( 'library' );

			library.items.fetch( {
				reset: true,
				url: ajaxurl,
				data: {
					action: 'bt_fetch_template_items',
					template_type: this.get( 'type' )
				}
			} );
			
			this.set( 'library', library );
		},

		BtResetLibrary: function() {
			var library = this.get( 'library' );
			var group = library.props.get( 'group' );
			var item = this.frame.BtGetCurrentItem();

			item.set( 'group', group );

			library.reInitialize();
			this.set( 'library', library );

			this.BtUpdateSelection();
		},

		BtResetFilter: function() {
			var library = this.get( 'library' );
			var item = this.frame.BtGetCurrentItem();
			//var groups  = this.get('data').groups;
			var groups = [];
			var group = item.get( 'group' );

			if ( _.isUndefined( groups[group] ) ) {
				group = 'all';
			}
			
			library.props.set( 'group', group );
			library.props.set( 'search', '' );
		},

		BtUpdateSelection: function() {
			var selection = this.get( 'selection' );
			var type = this.get( 'type' );
			var key = type + '-icon';
			var item = this.frame.BtGetCurrentItem();
			var icon = item.get( key );
			var selected;

			if ( type === item.get( 'type' ) && icon ) {
				selected = this.get( 'library' ).findWhere( {id: icon} );
			}

			selection.reset( selected ? selected : [] );
		},

		btGetContent: function() {
			this.BtResetFilter();

			return new wp.media.view.BtTemplates( {
				controller: this.frame,
				model: this,
				collection: this.get( 'library' ),
				selection: this.get( 'selection' ),
				type: this.get( 'type' )
			} );
		},
		
		BtAddTemplate: function( options ) {
			var $this = this;
			
			PJ_Page_Builder.fixInputValues( $( '#page-builder' ) );
			var builderData = {};
			builderData.builder = $( 'div.pb-active-row', '#pb-rows' ).find( ':input' ).serialize();
			
			builderData = _.defaults( builderData, {
				type: $this.get( 'type' ),
				template_name: options.template_name,
				template_desc: options.template_desc
			} );

			var newItem = $this.get( 'library' ).items.add( {
				label: options.template_name,
				description: options.template_desc,
				uploading: true,
				percent: 35
			} );

			$this.get( 'selection' ).reset( newItem );
			
			wp.ajax.post( 'bt_save_template', builderData )
				.done( function( response ) {
					var library = $this.get( 'library' ),
						theModel = library.findWhere( {id: newItem.id} );
					
					newItem.set( response );
					newItem.set( 'uploading', false );

					theModel.set( response );
					theModel.set( 'uploading', false );

					$this.get( 'selection' ).reset( newItem );
				} )
				.fail( function( response ) {
					newItem.destroy();
				} );
		},

		btImportTemplate: function() {
			var templateId = this.get( 'selection' ).single().id,
				templateType = this.get( 'type' );

			PJ_Page_Builder.importTemplate( templateId, templateType );
		}

	} );
}( jQuery ));