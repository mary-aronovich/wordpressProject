/*!
 * pojo.me
 */
/* global Modernizr */

// Define Pojo var if not defined.
var Pojo = Pojo || {};

Pojo.methods = ( function( $, window, document, undefined ) {
	'use strict';
	
	var is_rtl = false,

		hasAdminBar = false,
		
		adminBarSize = 32,

		// Init firing when document get ready.
		init = function() {
			is_rtl = $( 'body' ).hasClass( 'rtl' );

			hasAdminBar = ( 1 <= $( 'body.admin-bar' ).length );
			
			if ( 'bootstrap' === Pojo.css_framework_type ) {
				/**
				 * Bootstrap plugins.
				 */
				$( 'a.pojo-popover' ).popover();
				$( 'a.pojo-tooltip' ).tooltip();
			}
			
			/**
			 * Superfish.
			 */
			$( 'ul.sf-menu' ).superfish( Pojo.superfish_args );
			
			/**
			 * Mobile Menu.
			 */
			$( 'select.at-select-mobile' ).on( 'change', function() {
				window.location.href = $( this ).val();
			} );
			
			// Isotope.
			initIsotope();

			// Resize Handler.
			$( window )
				.on( 'resize', onWindowResize )
				.on( 'load', onWindowResize );

			onWindowResize();
			
			// Start init animation..
			initAnimation();

			initTestimonials();

			//initMenuEffect();
			
			initScrollToTop();

			initContactForm();
			
			initStickyHeader();
			
			initOuterSlidebar();
			
			initHoverDir();

			initNewItems();
			
			initAnimatedNumbers();
			
			initParallax();
			
			initBackgroundVideo();
			
			initScrollUp();
			
			initElementor();

			$( 'div.pojo-gallery-pager' ).bxSlider( {
				minSlides: 5,
				maxSlides: 6,
				slideWidth: 180,
				auto: false,
				pager: false,
				slideMargin: 10
			} );

			$( 'div.recent-carousel' ).each( function() {
				$( this ).bxSlider( $( this ).data( 'slider_options' ) );
			} );

			$( 'ul.pojo-bxslider-handle' ).each( function() {
				$( this ).bxSlider( $( this ).data( 'slider_options' ) );
			} );

			$( 'div.dropdown-login' ).on( 'click', function( e ) {
				e.stopPropagation();
			} );
			
			$( 'ul.at-product-screenshots-slider' ).bxSlider( {
				auto: true,
				mode: 'fade',
				pager: false,
				controls: false
			} );
			
			$( 'a.search-toggle' ).on( 'click', function( e ) {
				e.preventDefault();
				$( $( this ).data( 'target' ) ).slideToggle( 'fast' );
				
				$( this )
					.find( 'i.fa' )
					.toggleClass( 'fa-times fa-search' );
			} );
		},
		
		initIsotope = function() {
			var filter_items_selectors = '#grid-items, #masonry-items, div.recent-post-wrap-grid, div.recent-galleries-wrap-grid, div.posts-group-wrap-grid',
				
				$filter_items = $( filter_items_selectors );

			$filter_items.each( function() {
				var $this_items = $( this ),
					layout_mode = 'masonry-items' === $this_items.attr( 'id' ) ? 'masonry' : 'fitRows',

					pojoImagesLoaded = $( 'body' ).imagesLoaded().always( function( instance ) {
						$this_items.isotope( {
							layoutMode: layout_mode,
							itemSelector: '.grid-item',
							isOriginLeft: ! is_rtl,
							transitionDuration: '0.5s'
						} );
						// Make sure it's loaded.
						$this_items.isotope();

						$( window ).trigger( 'pojo_isotope_loaded' );
					} );
			} );
			
			$( window )
				.on( 'smartresize', function() {
					$filter_items.isotope();
				} );
			
			$( 'ul.category-filters' ).on( 'click', 'a', function( e ) {
				var $category_filters = $( this ).closest( 'ul.category-filters' );
				$category_filters
					.find( 'a' )
					.removeClass( 'active' );
					
				$( this ).addClass( 'active' );

				$category_filters
					.next( filter_items_selectors )
					.isotope( {
						filter: $( this ).attr( 'data-filter' )
					} );
			} );

			$( '#grid-items, #masonry-items' ).pojoInfiniteScroll( {
				//debug: true,
				itemSelector: '.pojo-class-item',
				finished: function( newElements ) {
					$( '#grid-items, #masonry-items' ).isotope( 'appended', $( newElements ) );
					var pojoImagesLoaded = $( 'body' ).imagesLoaded( function() {
						$filter_items.isotope();
						onWindowResize();
						initNewItems();
						initHoverDir();
					} );
				}
			} );

			$( '#list-items' ).pojoInfiniteScroll( {
				//debug: true,
				itemSelector: 'article.post',
				finished: function( newElements ) {
					onWindowResize();
					initNewItems();
				}
			} );

			$( '#square-items' ).pojoInfiniteScroll( {
				//debug: true,
				itemSelector: '.pojo-class-item',
				finished: function( newElements ) {
					onWindowResize();
					initNewItems();
				}
			} );
		},
		
		initMenuEffect = function() {
			var menu_element = $( '#nav-main .at-menu-with-animation' );
			
			if ( 0 >= menu_element.length ) {
				return;
			}
				
			var hover_element = $( '<li class="hover-element"></li>' )
					.appendTo( menu_element )
					.css( 'position', 'absolute' ),
				
				li_elements = $( '> li', menu_element ),
				
				li_current = $( '> li.active', menu_element )[0] || $( '> li.current-menu-ancestor', menu_element )[0] || $( li_elements[0] ).addClass( 'active' )[0],

				change_position = function( to_element ) {
					hover_element
						.dequeue()
						.animate( {
							width: to_element.offsetWidth,
							left: to_element.offsetLeft
						}, 250, 'jswing' );
				};

			menu_element.css( 'position', 'relative' );
			
			li_elements.on( 'mouseenter', function() {
				// Move to this element.
				change_position( this );
			} ).on( 'click', function(e) {
					li_current = $( this ) ;
				});

			menu_element.on( 'mouseleave', function() {
				// Move back to current element.
				change_position( li_current );
			} );
			
			change_position( li_current );
		},
		
		initAnimation = function() {
			$( '.at-animation-almost-visible' ).waypoint( function() {
				var main_wrap = $( this );
				main_wrap.addClass( 'at-run-animation' );
				if ( main_wrap.hasClass( 'animation-slow-image' ) || main_wrap.hasClass( 'animation-slow-image-rand' ) ) {
					$( 'img', main_wrap ).sort( function() {
						if ( main_wrap.hasClass( 'animation-slow-image-rand' ) ) {
							return 0.5 - Math.random();
						}
						return -1;
					} ).each( function( i ) {
						var image_selector = $( this );
						setTimeout( function() {
							image_selector.addClass( 'at-run-animation' );
						}, i * 100 );
					} );
				}
			}, { offset: '80%' } );
		},

		initTestimonials = function() {
			$( 'ul.at-testimonials-slider' ).bxSlider( {
				mode: 'fade',
				pager: false,
				controls: false,
				auto: true
			} );
		},
		
		initScrollToTop = function() {
			$( document ).on( 'click', 'a.btn-scroll-to-top', function( e ) {
				e.preventDefault();
				$( 'body, html' ).animate( { scrollTop: 0 }, 1200 );
			} );
		},
		
		onWindowResize = function() {
			$( '.overlay-plus' ).each( function() {
				$( this ).css( 'line-height', $( this ).height() + 'px' );
			} );
			
			// Save ratio.
			// original height / original width x new width = new height
			$( 'div.custom-embed > iframe' ).each( function() {
				var ratio = $( this ).closest( 'div.custom-embed' ).data( 'save_ratio' );
				if ( 'string' !== typeof ratio ) {
					return;
				}
				ratio = ratio.split( ':' );
				if ( 2 !== ratio.length ) {
					return;
				}
				$( this ).css( 'height', ratio[1] / ratio[0] * $( this ).width() );
			} );
			
			//$('.pojo-slideshow-wrapper .slide' ).css( 'height', $('.pojo-slideshow-wrapper .slide img' ).height() );

			/*$('.pojo-slideshow-wrapper' ).each( function() {
				var img = $( this ).find( 'img' );
				$( this ).find( '.slide' ).css( 'height', img.height() );
			} );
			console.log('test height');*/
		},

		initStickyHeader = function() {
			var $stickyHeader = $( 'div.sticky-header' );
			
			if ( 1 > $stickyHeader.length ) {
				return;
			}
			
			var container_target = $( 'div.sticky-header-running' );
			if ( 1 > container_target.length ) {
				return;
			}
			
			var $imgPrimary = $( 'img.logo-img-primary' ),
				$imgSecondary = $( 'img.logo-img-secondary' );

			if ( 1 <= $imgPrimary.length && 1 <= $imgSecondary.length ) {
				if ( $imgPrimary.prop( 'src' ) !== $imgSecondary.prop( 'src' ) ) {
					var $new_img = $imgSecondary.clone();
					$new_img
						.removeAttr( 'id' )
						.addClass( 'pojo-visible-phone' );

					$imgPrimary
						.addClass( 'pojo-hidden-phone' )
						.after( $new_img );
				}
			}
			
			container_target.waypoint( function( direction ) {
				switch ( direction ) {
					case 'down' :
						$stickyHeader
							.slideDown( 'slow' )
							.css( 'position', 'fixed' )
							.css( 'top', 0 );
						break;
					
					case 'up' :
						$stickyHeader
							.slideUp( 'fast' );
						break;
				}
			} );

			// Add space for Elementor Menu Anchor link
			$( window ).on( 'elementor/frontend/init', function() {
				elementorFrontend.hooks.addFilter( 'frontend/handlers/menu_anchor/scroll_top_distance', function( scrollTop ) {
					return scrollTop - $stickyHeader.outerHeight();
				} );
			} );
		},

		initOuterSlidebar = function() {
			var pojoImagesLoaded = $( '#outer-slidebar' ).imagesLoaded().always( function( instance ) {
				var toggle = $( 'a', '#outer-slidebar-toggle' ),
					_get_container_height = function() {
						$container = $( '#outer-slidebar' );
						
						var container_height = $container.height() + 3;

						if ( hasAdminBar ) {
							container_height -= adminBarSize;
						}
						return container_height;
					},
					$container = $( '#outer-slidebar' );

				toggle.on( 'click', function( e ) {
					e.preventDefault();
					if ( ! $( this ).hasClass( 'open' ) ) {
						$container.animate( {
							marginTop: 0
						}, 500, 'easeOutQuint' );
						toggle.addClass( 'open' );
					} else {
						$container.animate( {
							marginTop: -_get_container_height()
						}, 500, 'easeOutQuint' );
						toggle.removeClass( 'open' );
					}
					
					$( window )
						.trigger( 'resize' )
						.trigger( 'smartresize' );
				} );

				$( window ).on( 'resize pojo_isotope_loaded', function() {
					if ( ! toggle.hasClass( 'open' ) ) {
						$container.css( {
							marginTop: -_get_container_height(),
							display: 'block'
						} );
					}
				} )
					.trigger( 'resize' );
			} );
		},

		initHoverDir = function() {
			$( 'div.hover-dir > div.grid-item' ).each( function() {
				$( this ).hoverdir( {
					hoverElem: 'div.hover-object'
				} );
			} );
		},
		
		initNewItems = function() {
			// Build sliders
			var slider_class_ready = 'slider-ready';
			$( 'ul.pojo-simple-gallery' ).each( function() {
				if ( ! $( this ).hasClass( slider_class_ready ) ) {
					$( this ).addClass( slider_class_ready ).bxSlider( {
						auto: true,
						mode: 'fade',
						pager: false,
						adaptiveHeight: true
					} );
				}
			} );
		},

		initAnimatedNumbers = function() {
			$( '.pojo-animated-numbers' ).waypoint( function() {
				var $this = $( this ),
					data = $this.data();

				$this.numerator( {
					duration: data.duration,
					toValue: data.to_value
				} );
			}, { offset: '90%' } );
		},

		initParallax = function() {
			var isMobile = Modernizr.mq( 'only screen and (max-width: 992px)' );
			if ( isMobile ) {
				return;
			}
			
			if ( ! $( 'html' ).hasClass( 'no-touch' ) ) {
				return;
			}
			
			var skrollrInstance = skrollr.init( {
				forceHeight: false,
				skrollrBody: 'container'
			} );

			$( window ).on( 'pojo_isotope_loaded', function() {
				skrollrInstance.refresh();
			} );
		},

		initBackgroundVideo = function() {
			var isInserted = false,

				insertYTAPI = function() {
					isInserted = true;

					var tag = document.createElement( 'script' ),
						first = document.getElementsByTagName( 'script' )[0];

					tag.src = 'https://www.youtube.com/iframe_api';
					first.parentNode.insertBefore( tag, first );
				},

				onYoutubeApiReady = function( callback ) {
					if ( ! isInserted ) {
						insertYTAPI();
					}

					if ( window.YT && YT.loaded ) {
						callback( YT );
					} else {
						// If not ready check again by timeout..
						setTimeout( function() {
							onYoutubeApiReady( callback );
						}, 350 );
					}
				};
			
			var calcSizeSections = function() {
				var $cssBlock = $( '#pojo-size-sections' ),
					cssContent = '';
				
				if ( 1 > $cssBlock.length ) {
					$cssBlock = $( '<style type="text/css" id="pojo-size-sections"></style>' ).appendTo( 'head:first' );
				}

				$( 'section.section.has-video-background' ).each( function() {
					var $thisSection = $( this ),
						css = '',
						the_id = '#' + $thisSection.attr( 'id' ),
						wh100 = $thisSection.outerHeight(),
						ww100 = $thisSection.width(),
						aspect = '16:9',
						video_w = aspect[0],
						video_h = aspect[1],
						whCover = (wh100 / video_h ) * video_w,
						wwCover = (ww100 / video_w ) * video_h;

					//fullscreen video calculations
					if ( ww100 / wh100 < video_w / video_h ) {
						css += the_id + ".section.has-video-background div.custom-video-background > iframe {width:" + whCover + "px; left: -" + (whCover - ww100) / 2 + "px;}\n";
					}
					else {
						css += the_id + ".section.has-video-background div.custom-video-background > iframe {height:" + wwCover + "px; top: -" + (wwCover - wh100) / 2 + "px;}\n";
					}

					cssContent = cssContent + css;
				} );

				try {
					$cssBlock.text( cssContent );
				}
				catch ( err ) {
					$cssBlock.remove();
					$cssBlock = $( '<style type="text/css" id="pojo-size-sections">' + cssContent + "</style>" ).appendTo( 'head:first' );
				}
			};

			$( window ).on( 'resize', calcSizeSections );
			calcSizeSections();

			var $pbYoutubeFrame = $( 'div.pb-youtube-frame' );

			if ( ! $pbYoutubeFrame.length ) {
				return;
			}

			onYoutubeApiReady( function() {
				$pbYoutubeFrame.each( function() {
					var $video = $( this ),
						params = $video.data(),
						isMobile = Modernizr.mq( 'only screen and (max-width: 992px)' );

					if ( !! document.createElement( 'video' ).canPlayType ) {
						params.html5 = 1;
					}
					
					// Hide in mobile mode
					if ( isMobile && 'hide' === $video.data( 'mobile_mode' ) ) {
						return;
					}

					var height = $video.closest( '.has-video-background' ).find( 'div.container-section, div.container' ).outerHeight();
					var player = new YT.Player( $video.attr( 'id' ), {
						videoId: params.videoid,
						height: height,
						width: 1600,
						playerVars: params,
						events: {
							'onReady': function() {
								player.mute();
								player.playVideo();
							},
							'onError': function( player ) {
								console.log( 'Error: ' + player );
							},
							'onStateChange': function( event ) {
								if ( event.data === YT.PlayerState.ENDED ) {
									player.seekTo( 0 );
								}
							}
						}
					} );
				} );
				
				setTimeout( function() {
					$( 'section.has-video-background .custom-video-background' ).css( 'visibility', 'visible' );
				}, 1000 );
			} );
		},

		initScrollUp = function() {
			var $scrollUp = $( '#pojo-scroll-up' ),
				activeClass = 'pojo-scroll-up-active';
			
			if ( ! $scrollUp.length ) {
				return;
			}
			
			var scrollUpOffset = $scrollUp.data( 'offset' );
			
			if ( 'always' !== scrollUpOffset ) {
				scrollUpOffset = parseInt( scrollUpOffset );
				$( 'body' )
					.waypoint( function( direction ) {
						if ( 'down' === direction ) {
							$scrollUp.fadeIn( 'fast' );
						}
					}, {
						offset: function() {
							return - ( ( $(this).height() * scrollUpOffset / 100 ) - $( window ).height() );
						}
					} )
					
					.waypoint( function( direction ) {
						if ( 'up' === direction ) {
							$scrollUp.fadeOut( 'fast' );
						}
					}, {
						offset: function() {
							return - ( ( $(this).height() * scrollUpOffset / 100 ) - $( window ).height() );
						}
					} );
			} else {
				$scrollUp.show();
			}
			
			$scrollUp.find( 'a' ).on( 'click', function( event ) {
				event.preventDefault();
				$( 'body, html' ).animate( { scrollTop: 0 }, $scrollUp.data( 'duration' ) );
			} );
		},

		initElementor = function() {

		},
		
		initContactForm = function() {
			$( 'form.form-ajax' ).on( 'submit', function() {
				var form_serialize = $( this ).serialize(),
					this_form = $( this ),
					prefix_field_wrap = this_form.data( 'prefix' ) || '',
					submit_button = this_form.find( 'div.form-actions button.submit' );
				
				if ( this_form.hasClass( 'form-waiting' ) ) {
					return false;
				}
				
				this_form
					.animate( { opacity: '0.45' }, 500 )
					.addClass( 'form-waiting' );
				
				submit_button
					.attr( 'disabled', 'disabled' )
					.html( '<i class="fa fa-spinner fa-spin"></i> ' + submit_button.html() );
				
				this_form
					.find( 'div.form-message' )
					.remove();
				
				this_form
					.find( 'div.field-group' )
					.removeClass( 'error' )
					.find( 'span.form-help-inline' )
					.remove();
				
				$.post( Pojo.ajaxurl, form_serialize, function( data ) {
					if ( ! data.hide_form || 'success' !== data.status ) {
						submit_button
							.html( submit_button.text() )
							.removeAttr( 'disabled' );
	
						this_form
							.animate( { opacity: '1' }, 100 )
							.removeClass( 'form-waiting' );
					}
					
					if ( 'error' === data.status ) {
						$.each( data.fields, function( key, title ) {
							this_form
								.find( 'div.field-group.' + prefix_field_wrap + key )
								.addClass( 'error' )
								//.find( 'div.controls')
								.append( '<span class="help-inline form-help-inline">' + title + '</span>' );
						} );
						this_form.append( '<div class="form-message form-message-danger">' + data.msg + '</div>' );
					} else if ( 'success' === data.status ) {
						if ( ! data.hide_form ) {
							/*this_form
								.find( 'div.field-group, div.form-actions' )
								.remove();*/
							this_form.trigger( 'reset' );
							
						}
						
						if ( '' !== data.msg ) {
							this_form.append( '<div class="form-message form-message-success">' + data.msg + '</div>' );
						}
						if ( '' !== data.link ) {
							//setTimeout( function() {
								location.href = data.link;
							//}, 2500 );
						}
					}
				}, 'json' );
				return false;
			} )
				.on( 'click', 'a.button.choose-theme', function( e ) {
					e.preventDefault();
					$( this )
						.closest( 'div.theme-item' )
						.find( 'input[type="radio"]' )
						.attr( 'checked', 'checked' )
						.closest( 'form' )
						.trigger( 'submit' );
				} );
			
			$( 'input.submit-on-click' ).on( 'click', function() {
				//$( this ).closest( 'form' ).trigger( 'submit' );
			} );
		},
		
		showAlert = function( msg, type ) {
			// Set default type.
			type = type || 'success';
			// Added Bootstrap Alert in #main element.
			$( '#main' ).prepend( '<div class="alert alert-block alert-' + type + ' fade in"><a class="close" data-dismiss="alert">&times;</a>' + msg + '</div>' );
		};
	
	// Public members.
	return {
		init: init,
		showAlert: showAlert
	};
} ( jQuery, window, document ) );

jQuery( document ).ready( function( $ ) {
	'use strict';
	Pojo.methods.init();

	Pojo.anchorManager = new Pojo_Anchor_Links_Manager();
} );
