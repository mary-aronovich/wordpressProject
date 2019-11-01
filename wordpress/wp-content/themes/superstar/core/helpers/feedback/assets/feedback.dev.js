/* global wp, ajaxurl, Backbone */

(function( $, window ) {
	'use strict';
	
	// Namespaces
	window.PojoFeedback = {};
	window.PojoFeedback.views = {};
	window.PojoFeedback.models = {};
	
	window.PojoFeedback.models.Model = Backbone.Model.extend( {
		defaults: {
			campaign: '1',
			rating: '',
			comment: '',
			_nonce: ''
		},

		validate: function( attrs, options ) {
			if ( _.isEmpty( attrs.rating ) ) {
				return 'rating:empty';
			}
		}
	} );
	
	window.PojoFeedback.views.Modal = new wp.media.view.Modal( {
		controller: {
			trigger: function() {}
		}
	} );
	
	window.PojoFeedback.views.ModalContentView = wp.media.view.Settings.extend( {
		tagName:   'div',
		className: 'pojo-feedback-modal',
		
		template: wp.template( 'pojo-feedback-panel' ),

		events: {
			'keyup input': 'updateHandler',
			'keyup textarea': 'updateHandler',
			'click .feedback-submit': 'submitFeedback'
		},

		initialize: function() {
			_.extend( this.events, wp.media.view.Settings.prototype.events );
			wp.media.view.Settings.prototype.initialize.apply( this, arguments );

			this.listenTo( this.model, 'change', this.onModelChanged );
		},

		render: function() {
			var parentReturn = wp.media.view.Settings.prototype.render.apply( this, arguments );
			this.onModelChanged();
			return parentReturn;
		},
		
		onModelChanged: function() {
			this.$buttonSubmit = this.$( '.feedback-submit' );
			
			if ( this.model.isValid() ) {
				this.$buttonSubmit.removeProp( 'disabled' );
			} else {
				this.$buttonSubmit.prop( 'disabled', 'disabled' );
			}
		},
		
		showSpinner: function() {
			this.$( 'span.spinner' ).addClass( 'is-active' );
		},
		
		hideSpinner: function() {
			this.$( 'span.spinner' ).removeClass( 'is-active' );
		},
		
		showMessage: function( msg, status ) {
			var $message = this.$( 'div.messages' );
			status = status || 'success';
			
			if ( 'success' === status ) {
				$message.removeClass( 'error' );
				$message.addClass( 'updated' );
			} else {
				$message.removeClass( 'updated' );
				$message.addClass( 'error' );
			}

			$message.html( msg );
		},
		
		submitFeedback: function() {
			var thisView = this;
			if ( ! thisView.model.isValid() ) {
				return;
			}

			thisView.showSpinner();
			wp.ajax.post( 'pojo_send_feedback', thisView.model.toJSON() )
				.done( function( response ) {
					thisView.showMessage( response.msg );
					thisView.$( 'div.feedback-form' ).hide();
					
					$( 'div.feedback-camp-' + thisView.model.get('campaign') ).hide();
				} )
				.fail( function( response ) {
					thisView.showMessage( response.msg, 'error' );
					thisView.hideSpinner();
				} );
		}
	} );
	
	$( document ).ready( function() {
		$( 'a.pojo-feedback-button' ).on( 'click', function( event ) {
			event.preventDefault();

			window.PojoFeedback.views.Modal.content( new window.PojoFeedback.views.ModalContentView( {
				model: new window.PojoFeedback.models.Model( {
					campaign: $( this ).data( 'campaign' ),
					_nonce: $( this ).data( 'nonce' )
				} )
			} ) );
			window.PojoFeedback.views.Modal.open();
		} );
		
		$( 'a.pojo-dismiss-feedback-button' ).on( 'click', function( event ) {
			event.preventDefault();
			
			$( this ).closest( 'div.updated' ).fadeOut( 'fast' );

			$.post( ajaxurl, {
				campaign: $( this ).data( 'campaign' ),
				action: 'dismiss_pojo_feedback'
			} );
		} );
	} );
	
}( jQuery, window ));