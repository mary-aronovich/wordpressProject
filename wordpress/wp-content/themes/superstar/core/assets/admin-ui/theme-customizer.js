/* global _pojo_webfont_list */
var pojo_admin_customizer = ( function( $, window ) {
	
	var _pojo_typography_event_on_change = function() {
			
		},
		
		_convertHexToRgb = function( hex ) {
			hex = hex.replace( '#', '' );
			if ( !hex.length ) {
				return '';
			}
			colorR = parseInt( hex.substring( 0, 2 ), 16 );
			colorG = parseInt( hex.substring( 2, 4 ), 16 );
			colorB = parseInt( hex.substring( 4, 6 ), 16 );
			
			return colorR + ',' + colorG + ',' + colorB;
		},
		
		init = function() {
			$.each( parent._wpCustomizeSettings.controls, function( key_id, data ) {
				/** @namespace data.change_type */
				if ( undefined === data.selector || '' === data.selector || undefined === data.change_type ) {
					return;
				}
				
				if ( 'text' === data.change_type || 'textarea' === data.change_type ) {
					wp.customize( key_id, function( value ) {
						value.bind(function( to ) {
							var text_element = $( data.selector );
							text_element.text( to );
							// Tweak multi line.
							text_element.html( text_element.html().replace( /\n/g, '<br/>' ) );
						});
					});
				} else if ( 'color' === data.change_type ) {
					wp.customize( key_id, function( value ) {
						value.bind( function( to ) {
							$( data.selector ).css( 'color', to ? to : '' );
						});
					});
				} else if ( 'border_color' === data.change_type ) {
					wp.customize( key_id, function( value ) {
						value.bind( function( to ) {
							$( data.selector ).css( 'border-color', to ? to : '' );
						});
					} );
				} else if ( 'border_left_color' === data.change_type ) {
					wp.customize( key_id, function( value ) {
						value.bind( function( to ) {
							$( data.selector ).css( 'border-left-color', to ? to : '' );
						});
					} );
				} else if ( 'border_right_color' === data.change_type ) {
					wp.customize( key_id, function( value ) {
						value.bind( function( to ) {
							$( data.selector ).css( 'border-right-color', to ? to : '' );
						});
					} );
				} else if ( 'bg_color' === data.change_type ) {
					wp.customize( key_id, function( value ) {
						value.bind(function( to ) {
							$( data.selector ).css( 'background-color', to ? to : '' );
						});
					});
				} else if ( 'bg_position' === data.change_type ) {
					wp.customize( key_id, function( value ) {
						value.bind(function( to ) {
							$( data.selector ).css( 'background-position', to ? to : '' );
						});
					});
				} else if ( 'bg_repeat' === data.change_type ) {
					wp.customize( key_id, function( value ) {
						value.bind(function( to ) {
							$( data.selector ).css( 'background-repeat', to ? to : '' );
						});
					});
				} else if ( 'bg_size' === data.change_type ) {
					wp.customize( key_id, function( value ) {
						value.bind(function( to ) {
							$( data.selector ).css( 'background-size', to ? to : 'auto' );
						});
					});
				} else if ( 'bg_attachment' === data.change_type ) {
					wp.customize( key_id, function( value ) {
						value.bind(function( to ) {
							$( data.selector ).css( 'background-attachment', to ? to : 'scroll' );
						});
					});
				} else if ( 'bg_image' === data.change_type ) {
					wp.customize( key_id, function( value ) {
						value.bind(function( to ) {
							if ( '' !== to ) {
								to = 'url("' + to + '")';
							} else {
								to = 'none';
							}
							$( data.selector ).css( 'background-image', to );
						});
					});
                } else if ( 'width' === data.change_type ) {
                    wp.customize( key_id, function( value ) {
                        value.bind(function( to ) {
                            $( data.selector ).css( 'width', to ? to : '' );
                        });
                    });
				} else if ( 'height' === data.change_type ) {
					wp.customize( key_id, function( value ) {
						value.bind( function( to ) {
							$( data.selector )
								.css( 'height', to )
								.css( 'line-height', to );
						} );
					} );
				} else if ( 'line_height' === data.change_type ) {
					wp.customize( key_id, function( value ) {
						value.bind( function( to ) {
							$( data.selector ).css( 'line-height', to );
						} );
					} );
				} else if ( 'typography' === data.change_type ) {
					$.each( [ 'size', 'family', 'weight', 'color', 'transform', 'letter_spacing', 'style', 'line_height' ], function( index, field_key ) {
						wp.customize( key_id + '[' + field_key + ']', function( value ) {
							value.bind(function( to ) {
								if ( ! to ) {
									return;
								}
								if ( 'size' === field_key ) {
									$( data.selector ).css( 'font-size', to );
								} else if ( 'family' === field_key ) {
									$( data.selector ).css( 'font-family', to + ', Arial, sans-serif' );

									if ( undefined !== _pojo_webfont_list[ to ] ) {
										var font_lower_string = to.replace( /\s+/g, '' ).toLowerCase();
										if ( 'googlefonts' === _pojo_webfont_list[ to ] && 0 >= $( 'link[href*="' + to + '"]' ).length ) {
											$( 'link:last' ).after( '<link href="https://fonts.googleapis.com/css?family=' + to + ':100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic" rel="stylesheet" type="text/css">' );
										}
										else if ( 'earlyaccess' === _pojo_webfont_list[ to ] && 0 >= $( 'link[href*="' + font_lower_string + '"]' ).length ) {
											$( 'link:last' ).after( '<link href="https://fonts.googleapis.com/earlyaccess/' + font_lower_string + '.css" rel="stylesheet" type="text/css">' );
										}
									}
								} else if ( 'weight' === field_key ) {
									$( data.selector ).css( 'font-weight', to );
								} else if ( 'color' === field_key ) {
									$( data.selector ).css( 'color', to );
								} else if ( 'transform' === field_key ) {
									$( data.selector ).css( 'text-transform', to );
								} else if ( 'letter_spacing' === field_key ) {
									$( data.selector ).css( 'letter-spacing', to );
								}  else if ( 'style' === field_key ) {
									$( data.selector ).css( 'font-style', to );
								} else if ( 'line_height' === field_key && undefined !== to && '' !== to ) {
									$( data.selector ).css( 'line-height', to );
								}
							});
						});
					} );
				} else if ( 'background' === data.change_type ) {
					$.each( [ 'color', 'image', 'position', 'repeat', 'size', 'attachment', 'opacity' ], function( index, field_key ) {
						wp.customize( key_id + '[' + field_key + ']', function( value ) {
							value.bind( function( to ) {
								if ( 'color' === field_key || 'opacity' === field_key ) {
									var color = wp.customize( key_id + '[color]' ).get(),
										opacity = wp.customize( key_id + '[opacity]' ).get(),
										colorRgb = _convertHexToRgb( color );
									
									$( data.selector ).css( 'background-color', colorRgb ? 'rgba(' + colorRgb + ',' + opacity / 100 + ')' : '' );
								} else if ( 'position' === field_key ) {
									$( data.selector ).css( 'background-position', to ? to : '' );
								} else if ( 'repeat' === field_key ) {
									$( data.selector ).css( 'background-repeat', to ? to : '' );
								} else if ( 'size' === field_key ) {
									$( data.selector ).css( 'background-size', to ? to : '' );
								} else if ( 'attachment' === field_key ) {
									$( data.selector ).css( 'background-attachment', to ? to : '' );
								} else if ( 'image' === field_key ) {
									var image_to;
									if ( '' !== to ) {
										image_to = 'url("' + to + '")';
									} else {
										image_to = 'none';
									}
									$( data.selector ).css( 'background-image', image_to );
								}
							} );
						} );
					} );
				} else {
					// Padding / Margin
					$.each( [ 'padding', 'margin' ], function( index, margin_or_padding ) {
						$.each( [ 'top', 'bottom', 'left', 'right' ], function( index, direction ) {
							if ( margin_or_padding + '_' + direction === data.change_type ) {
								wp.customize( key_id, function( value ) {
									value.bind( function( to ) {
										$( data.selector ).css( margin_or_padding + '-' + direction, to );
									} );
								} );
							}
						} );
					} );
				}
			});
		};
	
	return {
		init: init
	};
} ( jQuery, window ) );

( function( $ ) {
	pojo_admin_customizer.init();
} )( jQuery );