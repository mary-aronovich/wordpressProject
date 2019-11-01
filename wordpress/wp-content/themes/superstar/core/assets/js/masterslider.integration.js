/* global PojoSliders */

var MasterSliderIntegration = ( function( $, window ) {
	var is_rtl = false,
		
		_pojo_slides = [],
		lastIndex = 1,

		_createSlider = function( sliderOptions ) {
			_pojo_slides[ lastIndex ] = new MasterSlider();
			_pojo_slides[ lastIndex ].setup( sliderOptions.id, sliderOptions.params );

			if ( sliderOptions.arrows ) {
				_pojo_slides[ lastIndex ].control( 'arrows' );
			}

			if ( sliderOptions.thumblist ) {
				_pojo_slides[ lastIndex ].control( 'thumblist', sliderOptions.thumblist_params );
			}

			if ( sliderOptions.bullets ) {
				_pojo_slides[ lastIndex ].control( 'bullets', sliderOptions.bullets_params );
			}

			if ( sliderOptions.lightbox ) {
				_pojo_slides[ lastIndex ].control( 'lightbox' );
			}

			lastIndex++;
		};

	return {
		createSlider: _createSlider
	};
} ( jQuery, window ) );