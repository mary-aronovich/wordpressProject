(function( $ ) {

	window.Pojo_Anchor_Links_Manager = function( userSettings ) {
		'use strict';

		var settings = {}, // All class settings. Can to be modified when implementing a new instance
			elementsCache = {}, // All required UI elements should to be stored in this object
			variables = {}, // A class variables, which can to be created and modified at run time
			self = this; // An alias to keep 'this' keyword in its original meaning

		/**
		 * Constructor
		 */
		var init = function() {
			initSettings();
			initElementsCache();
			attachEvents();
			initVariables();
		};

		/**
		 * Attaching required events to elements
		 */
		var attachEvents = function() {
			elementsCache.$window.on( 'hashchange', self.goFromHash ).trigger( 'hashchange' );
			elementsCache.$window.on( 'scroll', checkWayPoint );
			elementsCache.$links.on( 'click', self.goFromLink );
		};

		/**
		 * Changing current anchor by trigger of Waypoint event
		 */
		var changeAnchorByWayPoint = function( anchorElement ) {
			var id = $( anchorElement ).prop( 'id' );

			self.changeCurrentAnchor( '#' + id );
			self.activateNavigationItem();
		};

		/**
		 * When scrolling by user, enable Waypoint detection
		 */
		var checkWayPoint = function() {
			if ( ! variables.wayPointEnabled && variables.isUserScroll ) {
				initAnchorsWayPoints();
			}
		};

		/**
		 * Destroying the waypoint-scroll feature and set the related variables
		 */
		var destroyWaypoint = function() {
			elementsCache.$anchors.waypoint( 'destroy' );

			variables.wayPointEnabled = false;
			variables.isUserScroll = false;
		};

		/**
		 * Set the default element to store in the cache
		 */
		var getDefaultElements = function() {
			var selectors = settings.selectors;

			var elements = {
				$anchors: $( selectors.anchors ),
				$scrollable: $( selectors.scrollable ),
				$window: $( window ),
				$body: $( 'body' ),
				$links: $( selectors.links ),
				$navigationBars: $( selectors.navigationBars ),
				$stickyHeader: $( selectors.stickyHeader ),
				$navBarCollapse: $( selectors.navBarCollapse )
			};

			elements.$navigationItems = elements.$navigationBars.find( selectors.navigationItem );
			return elements;
		};

		/**
		 * Set the default settings for the class
		 */
		var getDefaultSettings = function() {
			return {
				selectors: {
					anchors: 'div.pojo-menu-anchor',
					scrollable: 'html, body',
					currentItem: 'li.current-menu-item',
					links: 'a',
					navigationBars: '.nav-main > div.navbar-collapse',
					navigationItem: 'li',
					stickyHeader: '.sticky-header',
					navBarCollapse: '.navbar-collapse'
				},
				classes: {
					pojoAnchor: 'pojo-menu-anchor',
					elementorAnchor: 'elementor-menu-anchor',
					adminBar: 'admin-bar',
					current: 'active current-menu-item current-menu-ancestor'
				},
				adminBarSize: 32,
				scrollDuration: 1000
			};
		};

		/**
		 * Initializing the Waypoint detection for each anchor
		 */
		var initAnchorsWayPoints = function() {
			var wayPointOffset = self.getWayPointOffset();

			elementsCache.$anchors
			             .waypoint( function( direction ) {
				             if ( 'down' === direction ) {
					             changeAnchorByWayPoint( this );
				             }
			             }, { offset: wayPointOffset } )
			             .waypoint( function( direction ) {
				             if ( 'up' === direction ) {
					             changeAnchorByWayPoint( this );
				             }
			             }, { offset: -wayPointOffset } );

			variables.wayPointEnabled = true;
		};

		/**
		 * Initializing the elements cache
		 */
		var initElementsCache = function() {
			elementsCache = getDefaultElements();

			elementsCache.$navBarCollapse.collapse( { toggle: false } ); // Fixed Bootstrap bug
		};

		/**
		 * Initializing the app settings according to the default and the user settings
		 */
		var initSettings = function() {
			var defaultSettings = getDefaultSettings();
			$.extend( true, settings, defaultSettings, userSettings );
		};

		/**
		 * Initializing a run-time variables
		 */
		var initVariables = function() {
			variables = {
				hasAdminBar: elementsCache.$body.hasClass( settings.classes.adminBar ),
				wayPointEnabled: false,
				isUserScroll: true
			};
		};

		var treatLinkAsDefault = function() {
			destroyWaypoint();

			setTimeout( function() {
				variables.isUserScroll = true;
			}, 100 );
		};

		/**
		 * Activating the current navigation item
		 */
		this.activateNavigationItem = function() {
			var $currentMenuItem = self.getCurrentMenuItem();
			if ( ! $currentMenuItem.length ) {
				return;
			}
			elementsCache.$navigationItems.removeClass( settings.classes.current );
			$currentMenuItem.addClass( settings.classes.current );
		};

		/**
		 * Change the current anchor
		 */
		this.changeCurrentAnchor = function( newAnchorID ) {
			var hrefWithoutHash = location.href.replace( /#.*/, '' ),
				newLocation = hrefWithoutHash + newAnchorID;

			history.pushState( {}, '', newLocation );
		};

		/**
		 * Collapsing navigation bar
		 */
		this.collapseNavigation = function() {
			if ( elementsCache.$navBarCollapse.length ) {
				elementsCache.$navBarCollapse.collapse( 'hide' );
			}
		};

		/**
		 * Getting the current menu item, by searching for link that including the current anchor
		 */
		this.getCurrentMenuItem = function() {
			return elementsCache.$navigationItems.filter( function() {
				return $( this ).find( 'a' ).attr( 'href' ) === self.getCurrentAnchorID();
			} );
		};

		/**
		 * Getting the current anchor
		 */
		this.getCurrentAnchor = function() {
			var currentAnchorID = self.getCurrentAnchorID();

			if ( ! currentAnchorID ) {
				return false;
			}

			var $anchor;

			try {
				$anchor = $( currentAnchorID );
			} catch ( e ) {
				return false;
			}

			if ( ! $anchor.length || ! ( $anchor.hasClass( settings.classes.pojoAnchor ) || $anchor.hasClass( settings.classes.elementorAnchor ) ) ) {
				return false;
			}

			return $anchor;
		};

		/**
		 * Getting the current required anchor id
		 */
		this.getCurrentAnchorID = function() {
			return location.hash;
		};

		/**
		 * Getting the current required anchor offset
		 */
		this.getCurrentAnchorOffset = function() {
			var $currentAnchor = self.getCurrentAnchor();

			if ( ! $currentAnchor ) {
				return false;
			}

			var offsetTop = $currentAnchor.offset().top;

			if ( elementsCache.$stickyHeader.length ) {
				offsetTop -= elementsCache.$stickyHeader.height();
			}

			if ( variables.hasAdminBar ) {
				offsetTop -= settings.adminBarSize;
			}

			return offsetTop;
		};

		/**
		 * Getting the offset for initializing Waypoint
		 */
		this.getWayPointOffset = function() {
			var wayPointOffset = 0;

			if ( elementsCache.$stickyHeader.length ) {
				wayPointOffset += elementsCache.$stickyHeader.height();
			}

			if ( variables.hasAdminBar ) {
				wayPointOffset += settings.adminBarSize;
			}

			return wayPointOffset;
		};

		/**
		 * Set the current anchor and menu
		 * This function fired when an hash change will trigger
		 */
		this.goFromHash = function() {
			if ( ! self.getCurrentAnchorID() ) {
				return;
			}

			if ( ! self.getCurrentAnchor() ) {
				treatLinkAsDefault();
				return;
			}

			self.activateNavigationItem();
			self.scrollToAnchor();
		};

		/**
		 * Changing current anchor by clicking on a link
		 */
		this.goFromLink = function( event ) {
			var isSamePathname = ( location.pathname === this.pathname ),
				isSameHostname = ( location.hostname === this.hostname ),
				hasHash = ( '' !== this.hash ),
				isElementorAction = 0 === this.hash.indexOf( '#elementor-action' );

			if ( ! isSameHostname || ! isSamePathname || ! hasHash || isElementorAction ) {
				return;
			}

			self.changeCurrentAnchor( this.hash );

			if ( ! self.getCurrentAnchor() ) {
				treatLinkAsDefault();
				return;
			}

			event.preventDefault();

			self.collapseNavigation();

			elementsCache.$window.trigger( 'hashchange' );
		};

		/**
		 * Scrolling the view to the current anchor
		 */
		this.scrollToAnchor = function() {
			if ( ! self.getCurrentAnchor() ) {
				return;
			}

			destroyWaypoint();

			elementsCache.$scrollable.animate( { scrollTop: self.getCurrentAnchorOffset() }, settings.scrollDuration, function() {
				variables.isUserScroll = true;
			} );
		};

		init();
	};
})( jQuery );