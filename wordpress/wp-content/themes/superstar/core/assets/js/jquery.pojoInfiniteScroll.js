/*!
 * @author Pojo.me
 * 
 * Big thanks to Paul Irish: https://github.com/paulirish/infinite-scroll
 */
;(function ( $, window, document, undefined ) {
	var pluginName = "pojoInfiniteScroll",
		defaults = {
			debug: false,
			triggerSelector: 'a.pojo-load-more',
			itemSelector : 'div.post',
			loaderSelector: 'div.pojo-infscr-loader',
			paginationSelector: 'div.align-pagination',
			loading: {
				msgText: "<em>Loading..</em>",
				selector: 'div.pojo-loading-wrap'
			},
			
			// Callbacks
			finished: function( data ) {}
		};

	function Plugin( element, options ) {
		this.element = $( element );
		this.settings = $.extend( {}, defaults, options );
		this._defaults = defaults;
		this._name = pluginName;
		
		this.state = {
			isDone: false,
			isProcessing: false,
			currPage: 1
		};
		
		this.init();
	}

	Plugin.prototype = {
		init: function() {
			var _this = this,
				$this = _this.element,
				settings = _this.settings,
				loader = $( settings.loaderSelector );

			if ( 0 >= loader.length ) {
				return;
			}

			loader.removeClass( 'hidden' );
			$( settings.paginationSelector ).addClass( 'hidden' );

			$( settings.triggerSelector ).on( 'click', function( e ) {
				e.preventDefault();
				if ( _this.state.isProcessing || _this.state.isDone ) {
					return;
				}
				_this.startProcessing();
				
				var this_trigger = $( this );
				//this_trigger.fadeOut();
				_this.state.currPage++;

				var cacheHeight =  $( window ).scrollTop() + 'px';

				var desturl = $( this ).data( 'url_structure' ).replace( '9999999', _this.state.currPage );
				var box = $( '<div/>' );

				_this._debug( 'heading into ajax', desturl );
				box.load( desturl + ' ' + settings.itemSelector, undefined, function( responseText ) {
					var children = box.children();
					// if it didn't return anything
					if ( 0 === children.length ) {
						_this.done();
					}

					// use a documentFragment because it works when content is going into a table or UL
					var frag = document.createDocumentFragment();
					while ( box[0].firstChild ) {
						frag.appendChild( box[0].firstChild );
					}
					
					_this._debug( 'contentSelector', $( $this )[0] );
					$( $this )[0].appendChild( frag );
					// previously, we would pass in the new DOM element as context for the callback
					// however we're now using a documentfragment, which doesn't have parents or children,
					// so the context is the contentContainer guy, and we pass in an array
					// of the elements collected as the first argument.
					var data = children.get();
					settings.finished( data );

					//var scrollTo = $( window ).scrollTop() + cacheHeight + 'px';
					// TODO: Better way for ScrollTop?
					$( 'html, body' ).animate( { scrollTop: cacheHeight }, 100, function() {
						_this.endProcessing();
					} );

					if ( _this.state.currPage >= this_trigger.data( 'max_page' ) ) {
						_this.done();
					}
				} );
			} );
		},
		
		startProcessing: function() {
			this._debug( 'Start Processing' );
			this.state.isProcessing = true;
			$( this.settings.loaderSelector ).addClass( 'processing' );
		},
		
		endProcessing: function() {
			$( this.settings.loaderSelector ).removeClass( 'processing' );
			this._debug( 'End Processing' );
			this.state.isProcessing = false;
		},
		
		done: function() {
			$( this.settings.loaderSelector ).addClass( 'done' );
			this.state.isDone = true;
			this._debug( 'Done !' );
		},

		_debug: function() {
			if ( true !== this.settings.debug ) {
				return;
			}

			if ( typeof console !== 'undefined' && typeof console.log === 'function' ) {
				// Modern browsers
				// Single argument, which is a string
				if ( (Array.prototype.slice.call( arguments )).length === 1 && typeof Array.prototype.slice.call( arguments )[0] === 'string' ) {
					console.log( (Array.prototype.slice.call( arguments )).toString() );
				} else {
					console.log( Array.prototype.slice.call( arguments ) );
				}
			} else if ( !Function.prototype.bind && typeof console !== 'undefined' && typeof console.log === 'object' ) {
				// IE8
				Function.prototype.call.call( console.log, console, Array.prototype.slice.call( arguments ) );
			}
		}
	};

	$.fn[ pluginName ] = function ( options ) {
		this.each( function() {
			if ( !$.data( this, "plugin_" + pluginName ) ) {
				$.data( this, "plugin_" + pluginName, new Plugin( this, options ) );
			}
		} );

		return this;
	};
})( jQuery, window, document );