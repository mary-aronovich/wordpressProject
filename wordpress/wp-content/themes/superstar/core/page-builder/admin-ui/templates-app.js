/* global wp, Backbone */

//  prefix:
//      bt = Builder Templates

(function( $, window ) {
	'use strict';
	
	var fetch = Backbone.Collection.prototype.fetch;
	Backbone.Collection.prototype.fetch = function() {
		this.trigger( 'btBeforeFetch' );
		return fetch.apply( this, arguments );
	};

	window.PojoBtTemplates = _.defaults( {
		frame: '',
		currentItem: {}
	}, window.PojoBtTemplates );

	wp.media.view.MediaFrame.BtApp = wp.media.view.MediaFrame.extend( {
		initialize: function() {
			wp.media.view.MediaFrame.prototype.initialize.apply( this, arguments );

			_.defaults( this.options, {
				selection: [],
				multiple: false,
				editing: false,
				toolbar: 'bt-select'
			} );

			this.BtItems = new wp.media.model.BtItems();
			this.createStates();
			this.bindHandlers();
		},

		BtInitialize: function() {
			this.BtUpdateTemplateItems();
			this.setState( 'bt-local' );
		},

		createStates: function() {
			var options = this.options;
			var Controller;

			if ( options.states ) {
				return;
			}

			_.each( window.PojoBtTemplates.types, function( props, type ) {
				if ( ! wp.media.controller.hasOwnProperty( props.data.controller ) ) {
					delete window.PojoBtTemplates.types[type];
					return;
				}

				Controller = wp.media.controller[props.data.controller];
				
				_.defaults( props, {
					content: props.id,
					selection: options.selection
				} );

				// States
				this.states.add( new Controller( props ) );
			}, this );
			
			this.states.add( new wp.media.controller.BtTemplates.Save( {
				priority: 120
			} ) );
		},

		BtRenderContent: function() {
			var state = this.state();
			var mode = this.content.mode();
			
			var content = state.btGetContent( mode );
			
			this.content.set( content );
		},

		BtGetCurrentItem : function() {
			return this.BtItems.get( window.PojoBtTemplates.currentItem.id );
		},

		BtUpdateTemplateItems : function() {
			var item = this.BtGetCurrentItem();

			if ( _.isUndefined( item ) ) {
				this.BtItems.add( window.PojoBtTemplates.currentItem );
			}
			else {
				item.set( window.PojoBtTemplates.currentItem );
			}

			this.BtItems.props.set( 'item', window.PojoBtTemplates.currentItem.id );
		},

		bindHandlers: function() {
			this.on( 'router:create:browse', this.createRouter, this );
			this.on( 'router:render:browse', this.browseRouter, this );
			this.on( 'content:render', this.BtRenderContent, this );
			this.on( 'toolbar:create:bt-select', this.createToolbar, this );
			this.on( 'toolbar:render:bt-select', this.BtSelectToolbar, this );
			this.on( 'toolbar:create:bt-save-template', this.BtSaveTemplateToolbar, this );
			
			this.on( 'menu:render', this.BtRenderMenu, this );
			
			this.on( 'open', this.BtInitialize, this );
		},

		BtSelectToolbar: function( view ) {
			var frame = this;
			var state = frame.state();
			var type = state.get( 'type' );
			
			view.set( state.id, {
				style: 'primary',
				priority: 80,
				text: window.PojoBtTemplates.l10n.insert_template,
				controller: state.get('library' ).items,
				requires: {
					selection: true
				},
				click: function() {
					frame.close();
					state.btImportTemplate();
				}
			} );
		},

		BtRenderMenu: function( view ) {
			view.set( {
				'templates-separator': new wp.media.View( {
					className: 'separator',
					priority: 100
				} )
			} );
		},

		BtSaveTemplateToolbar: function( toolbar ) {
			var thisFrame = this;
			
			toolbar.view = new wp.media.view.BtTemplates.Save.Toolbar({
				controller: thisFrame,
				items: {
					select: {
						text: window.PojoBtTemplates.l10n.save_template,
						style: 'primary',
						click: function() {
							var thisModel = thisFrame.state().model,
								templateName = thisModel.get( 'label' ),
								templateDesc = thisModel.get( 'description' ),
								state = thisFrame.state( 'bt-local' );
							
							state.BtAddTemplate( {
								template_name: templateName,
								template_desc: templateDesc
							} );

							thisFrame.close();
						}
					}
				}
			});
		}
	} );

	$( 'a.open-template-media' ).on( 'click', function( e ) {
		e.preventDefault();

		window.PojoBtTemplates.currentItem = {
			id: 100,
			title: ''
		};

		if ( !( window.PojoBtTemplates.frame instanceof wp.media.view.MediaFrame.BtApp ) ) {
			window.PojoBtTemplates.frame = new wp.media.view.MediaFrame.BtApp();
		}

		window.PojoBtTemplates.frame.open();
		
		if ( undefined !== $( this ).data( 'target' ) ) {
			window.PojoBtTemplates.frame.setState( $( this ).data( 'target' ) );
		}
	} );
}( jQuery, window ));