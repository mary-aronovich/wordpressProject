<?php

class Pojo_Dynamic_Tag_Site_Logo extends \ElementorPro\Modules\DynamicTags\Tags\Site_Logo {

	public function get_value( array $options = [] ) {
		return [
			'id' => 0,
			'url' => get_theme_mod( 'image_logo', \Elementor\Utils::get_placeholder_image_src() ),
		];
	}
}
