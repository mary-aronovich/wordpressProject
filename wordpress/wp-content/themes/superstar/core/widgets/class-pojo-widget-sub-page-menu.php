<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Widget_Sub_Page_Menu extends Pojo_Widget_Base {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		$this->_form_fields = array();

		$this->_form_fields[] = array(
			'id' => 'title',
			'title' => __( 'Title:', 'pojo' ),
			'std' => '',
		);

		$post_types = get_post_types( array( 'public' => true ), 'names' );
		unset( $post_types['attachment'] );
		$post_types_options = array();
		foreach ( $post_types as $post_type ) {
			$post_types_options[ $post_type ] = ucfirst( $post_type );
		}
		
		$nav_menus = get_terms( 'nav_menu' );
		$nav_menus_options = array();
		$nav_menus_options[''] = __( 'Do not show anything', 'pojo' );
		if ( ! empty( $nav_menus ) ) {
			foreach ( $nav_menus as $nav_menu ) {
				$nav_menus_options[ $nav_menu->name ] = $nav_menu->name;
			}
		}
		
		$this->_form_fields[] = array(
			'id' => 'post_type',
			'title' => __( 'Post Type:', 'pojo' ),
			'type' => 'multi_checkbox',
			'options' => $post_types_options,
			'std' => array( 'page' ),
		);

		$this->_form_fields[] = array(
			'id' => 'sortby',
			'title' => __( 'Sort by:', 'pojo' ),
			'type' => 'select',
			'std' => 'title',
			'options' => array(
				'title' => __( 'Post title', 'pojo' ),
				'menu_order' => __( 'Page order', 'pojo' ),
				'ID' => __( 'Page ID', 'pojo' ),
			),
			'filter' => array( &$this, '_valid_by_options' ),
		);
		
		$this->_form_fields[] = array(
			'id' => 'exclude',
			'title' => __( 'Exclude:', 'pojo' ),
			'std' => '',
			'desc' => __( 'Page IDs, separated by commas.', 'pojo' ),
			'filter' => 'sanitize_text_field',
		);

		$this->_form_fields[] = array(
			'id' => 'menu',
			'title' => __( 'Show menu if no found sub pages:', 'pojo' ),
			'type' => 'select',
			'std' => '',
			'options' => $nav_menus_options,
			'filter' => array( &$this, '_valid_by_options' ),
		);
		
		parent::__construct(
			'pojo_sub_page_menu',
			__( 'Sub Page Menu', 'pojo' ),
			array( 'description' => __( 'Display a list of sub pages', 'pojo' ), )
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$args = $this->_parse_widget_args( $args, $instance );
		
		/**
		 * @var $_posts         WP_Post[]
		 * @var $current_post   WP_Post
		 */
		extract( $args );
		
		$sortby    = empty( $instance['sortby'] ) ? 'menu_order' : $instance['sortby'];
		$post_type = empty( $instance['post_type'] ) ? array( 'page' ) : $instance['post_type'];
		$menu      = empty( $instance['menu'] ) ? '' : $instance['menu'];
		$exclude   = empty( $instance['exclude'] ) ? '' : $instance['exclude'];

		// sanitize, mostly to keep spaces out
		$exclude = preg_replace( '/[^0-9,]/', '', $exclude );

		$exclude_array = ( $exclude ) ? explode( ',', $exclude ) : array();

		if ( 'menu_order' === $sortby ) {
			$sortby = 'menu_order title';
		}

		$current_post = $this->get_current_post();
		
		if ( is_null( $current_post ) )
			return;
		
		$post_parent = $current_post->ID;
		if ( 0 !== $current_post->post_parent ) {
			$post_parent = $current_post->post_parent;
		}

		$custom_query = new WP_Query(
			array(
				'posts_per_page' => - 1,
				'post_parent' => $post_parent,
				'orderby' => $sortby,
				'order' => 'ASC',
				'post_type' => $post_type,
				'post__not_in' => $exclude_array,
			)
		);

		if ( ! $custom_query->have_posts() && empty( $menu ) )
			return;

		$_posts = $custom_query->get_posts();

		echo $before_widget;

		if ( ! empty( $instance['title'] ) ) {
			echo $before_title . $instance['title'] . $after_title;
		}

		if ( $_posts ) {
			$count_posts = sizeof( $_posts ) - 1;
			echo '<ul class="sub-page-menu">';
			
			printf(
				'<li class="first-page-item page-item menu-item page-item-%3$d%4$s"><a href="%1$s">%2$s</a></li>',
				get_permalink( $_posts[0]->post_parent ),
				get_the_title( $_posts[0]->post_parent ),
				$_posts[0]->post_parent,
				( $_posts[0]->post_parent === $current_post->ID ) ? ' current_page_item' : ''
			);
			
			foreach ( $_posts as $index => $_post ) {
				$li_classes = array( 'page-item', 'menu-item', 'page-item-' . $_post->ID );
				
				if ( $_post->ID === $current_post->ID )
					$li_classes[] = 'current_page_item';
				
				if ( $count_posts === $index )
					$li_classes[] = 'last-page-item';
				
				printf(
					'<li class="%3$s"><a href="%1$s">%2$s</a></li>',
					get_permalink( $_post->ID ),
					$_post->post_title,
					implode( ' ', $li_classes )
				);
			}
			echo '</ul>';
		}
		else {
			wp_nav_menu( array( 'menu' => $menu, 'container' => false ) );
		}

		echo $after_widget;
	}

	protected function get_current_post() {
		global $post;
		return $post;
	}
}