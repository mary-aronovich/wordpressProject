<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Woocommerce_Integration {
	
	private function add_theme_support() {
		add_theme_support( 'woocommerce' );

		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
	}

	public function init() {
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
	}

	public function register_widgets() {
		register_widget( 'Pojo_Widget_WC_Products' );
		register_widget( 'Pojo_Widget_WC_Products_Category' );
		register_widget( 'Pojo_Widget_WC_Product_Categories' );
	}

	public function page_builder_widgets( $widgets ) {
		$widgets[] = 'Pojo_Widget_WC_Products';
		$widgets[] = 'Pojo_Widget_WC_Products_Category';
		$widgets[] = 'Pojo_Widget_WC_Product_Categories';
		
		return $widgets;
	}

	public function cart_menu( $items, $args ) {
		if ( ! current_theme_supports( 'pojo-wc-menu-cart' ) )
			return $items;
		
		$has_item = false;
		
		if ( 'primary' === $args->theme_location && get_theme_mod( 'chk_enable_wc_menu_cart' ) )
			$has_item = true;

		if ( 'sticky_menu' === $args->theme_location && get_theme_mod( 'chk_enable_wc_menu_cart_sticky' ) )
			$has_item = true;
		
		if ( $has_item ) {
			$items .= $this->_get_wc_cart_menu_item();
		}
		
		return $items;
	}

	public function cart_menu_add_to_cart_fragment( $fragments ) {
		if ( current_theme_supports( 'pojo-wc-menu-cart' ) && get_theme_mod( 'chk_enable_wc_menu_cart' ) ) {
			$fragments['li.pojo-menu-cart'] = $this->_get_wc_cart_menu_item();
		}
		return $fragments;
	}

	public function section_woocommerce( $sections = array() ) {
		$fields = array();

		$fields = apply_filters( 'pojo_customizer_section_wc_before', $fields );

		if ( current_theme_supports( 'pojo-wc-menu-cart' ) ) {
			$fields[] = array(
				'id' => 'chk_enable_wc_menu_cart',
				'title' => __( 'Add WooCommerce Cart in Primary Menu', 'pojo' ),
				'type' => Pojo_Theme_Customize::FIELD_CHECKBOX,
				'std' => true,
			);

			$fields[] = array(
				'id' => 'chk_enable_wc_menu_cart_sticky',
				'title' => __( 'Add WooCommerce Cart in Sticky Menu', 'pojo' ),
				'type' => Pojo_Theme_Customize::FIELD_CHECKBOX,
				'std' => true,
			);
		}

		$fields = apply_filters( 'pojo_customizer_section_wc_after', $fields );
		
		if ( ! empty( $fields ) ) {
			$sections[] = array(
				'id'     => 'woocommerce',
				'title'  => __( 'WooCommerce', 'pojo' ),
				'desc'   => '',
				'fields' => $fields,
			);
		}
		
		return $sections;
	}
	
	protected function _get_wc_cart_menu_item() {
		$viewing_cart        = __( 'View your shopping cart', 'pojo' );
		$start_shopping      = __( 'Start shopping', 'pojo' );
		$cart_url            = wc_get_cart_url();
		$shop_page_url       = get_permalink( wc_get_page_id( 'shop' ) );
		$checkout_page_url   = get_permalink( wc_get_page_id( 'checkout' ) );
		$cart_contents_count = WC()->cart->cart_contents_count;
		$cart_contents       = sprintf( _n( '%d item', '%d items', $cart_contents_count, 'pojo' ), $cart_contents_count );
		$cart_total          = WC()->cart->get_cart_total();

		if ( 0 === $cart_contents_count ) {
			$menu_item = '<li class="pojo-menu-cart"><a class="pojo-menu-cart-contents" href="' . $shop_page_url . '" title="' . $start_shopping . '">';
		} else {
			$menu_item = '<li class="pojo-menu-cart"><a class="pojo-menu-cart-contents" href="' . $cart_url . '" title="' . $viewing_cart . '">';
		}

		$menu_item .= '<span class="icon-cart"></span> ';

		$menu_item .= '<span class="items-cart">'. $cart_contents .'</span>' . $cart_total;
		$menu_item .= '</a>';

		if ( 0 < $cart_contents_count ) {
			$menu_item .= '<ul class="sub-menu cart-contents">';

			foreach ( WC()->cart->cart_contents as $cart_item ) {
				$_post = get_post( $cart_item['data']->get_id() );
				$thumbnail_id = ( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];

				$menu_item .= strtr(
					'<li class="cart-content">
						<a href="{product-link}">
							{thumbnail}
							<div class="cart-desc">
								<span class="cart-title">{product-title}</span>
								<span class="product-quantity">{quantity} x {subtotal}</span>
							</div>
						</a>
					</li>',
					array(
						'{product-link}' => get_permalink( $cart_item['product_id'] ),
						'{thumbnail}' => get_the_post_thumbnail( $thumbnail_id, 'thumbnail' ),
						'{product-title}' => $_post->post_title,
						'{quantity}' => $cart_item['quantity'],
						'{subtotal}' => WC()->cart->get_product_subtotal( $cart_item['data'], 1 ),
					)
				);
			}

			$menu_item .= '<li class="cart-checkout">';
			$menu_item .= sprintf( '<div class="cart-link"><a href="%s">%s</a></div>', $cart_url, __( 'View Cart', 'pojo' ) );
			$menu_item .= sprintf( '<div class="checkout-link"><a href="%s">%s</a></div>', $checkout_page_url, __( 'Checkout', 'pojo' ) );
			$menu_item .= '</li>';

			$menu_item .= '</ul>';
		}

		$menu_item .= '</li>';
		
		return $menu_item;
	}
	
	public function __construct() {
		$this->add_theme_support();

		include( 'widgets/class-wc-products.php' );
		include( 'widgets/class-wc-products-category.php' );
		include( 'widgets/class-wc-product-categories.php' );
		
		add_action( 'init', array( &$this, 'init' ), 300 );
		add_filter( 'pojo_widgets_registered', array( &$this, 'register_widgets' ) );
		add_filter( 'pojo_builder_widgets', array( &$this, 'page_builder_widgets' ) );
		add_filter( 'wp_nav_menu_items', array( &$this, 'cart_menu' ), 10, 2 );
		add_filter( 'woocommerce_add_to_cart_fragments', array( &$this, 'cart_menu_add_to_cart_fragment' ), 10, 2 );
		
		// Customizer Section:
		add_filter( 'pojo_register_customize_sections', array( &$this, 'section_woocommerce' ), 250 );
	}
	
}
new Pojo_Woocommerce_Integration();