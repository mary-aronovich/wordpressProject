/* global wp */

(function( $ ) {
	'use strict';

	wp.media.view.BtTemplates.Library = wp.media.View.extend( {
		tagName: 'ul',
		className: 'attachments bt-items clearfix',

		initialize: function() {
			this._viewsByCid = {};
			
			this.collection.on( 'reset', this.refresh, this );
			this.collection.items.on( 'btBeforeFetch', this.renderLoading, this );
			this.controller.on( 'open', this.scrollToSelected, this );
		},

		render: function() {
			this.$( 'span.bt-loading' ).remove();
			
			this.collection.each( function( model ) {
				this.views.add( this.renderItem( model ), {
					at: this.collection.indexOf( model )
				} );
			}, this );

			return this;
		},
		
		renderLoading: function() {
			this.clearItems();
			this.$el.html( '<span class="bt-loading">Loading..</span>' );
		},

		renderItem: function( model ) {
			var view = new wp.media.view.BtTemplates.Item( {
				controller: this.controller,
				model: model,
				collection: this.collection,
				selection: this.options.selection,
				type: this.options.type,
				data: this.options.data
			} );

			return this._viewsByCid[view.cid] = view;
		},

		clearItems: function() {
			_.each( this._viewsByCid, function( view ) {
				delete this._viewsByCid[view.cid];
				view.remove();
			}, this );
		},

		refresh: function() {
			this.clearItems();
			this.render();
		},

		ready: function() {
			this.scrollToSelected();
		},

		scrollToSelected: function() {
			var single = this.options.selection.single();
			var singleView;

			if ( !single ) {
				return;
			}

			singleView = this.getView( single );
			if ( singleView && !this.isInView( singleView.$el ) ) {
				this.$el.scrollTop( singleView.$el.offset().top - this.$el.offset().top + this.$el.scrollTop() - parseInt( this.$el.css( 'paddingTop' ) ) );
			}
		},

		getView: function( model ) {
			return _.findWhere( this._viewsByCid, {model: model} );
		},

		isInView: function( $elem ) {
			var $window = $( window );
			var docViewTop = $window.scrollTop();
			var docViewBottom = docViewTop + $window.height();
			var elemTop = $elem.offset().top;
			var elemBottom = elemTop + $elem.height();

			return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
		}
	} );
}( jQuery ));