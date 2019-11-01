<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Theme_Reporter extends Pojo_Info_Base_Reporter {

	/**
	 * @var WP_Theme
	 */
	private $theme = null;

	public function get_title() {
		return __( 'Theme', 'pojo' );
	}

	public function get_fields() {
		$fields = array(
			'name' => __( 'Name', 'pojo' ),
			'version' => __( 'Version', 'pojo' ),
			'author' => __( 'Author', 'pojo' ),
			'is_child_theme' => __( 'Child Theme', 'pojo' ),
			'license_status' => __( 'License Status', 'pojo' ),
		);

		if ( $this->get_parent_theme() ) {
			$parent_fields = array(
				'parent_name' => __( 'Parent Theme Name', 'pojo' ),
				'parent_version' => __( 'Parent Theme Version', 'pojo' ),
				'parent_author' => __( 'Parent Theme Author', 'pojo' ),
			);
			$fields = array_merge( $fields, $parent_fields );
		}

		return $fields;
	}

	protected function _get_theme() {
		if ( is_null( $this->theme ) ) {
			$this->theme = wp_get_theme();
		}
		return $this->theme;
	}

	protected function get_parent_theme() {
		return $this->_get_theme()->parent();
	}

	public function get_name() {
		return array(
			'value' => $this->_get_theme()->get( 'Name' ),
		);
	}

	public function get_author() {
		return array(
			'value' => $this->_get_theme()->get( 'Author' ),
		);
	}

	public function get_version() {
		return array(
			'value' => $this->_get_theme()->get( 'Version' ),
		);
	}

	public function get_is_child_theme() {
		$is_child_theme = is_child_theme();

		$result = array(
			'value' => $is_child_theme ? __( 'Yes', 'pojo' ) : __( 'No', 'pojo' ),
		);

		if ( ! $is_child_theme ) {
			$result['recommendation'] = __( 'If you want to modify the source code of your theme, we recommend using a child theme. See: <a href="http://pojo.me/go/child-theme/">How to use a child theme</a>', 'pojo' );
		}

		return $result;
	}

	public function get_parent_version() {
		return array(
			'value' => $this->get_parent_theme()->get( 'Version' ),
		);
	}

	public function get_parent_author() {
		return array(
			'value' => $this->get_parent_theme()->get( 'Author' ),
		);
	}

	public function get_parent_name() {
		return array(
			'value' => $this->get_parent_theme()->get( 'Name' ),
		);
	}

	public function get_license_status() {
		$license_key = Pojo_Core::instance()->licenses->get_license_key();
		if ( ! empty( $license_key ) ) {
			$license_data = Pojo_Core::instance()->licenses->api->check_license( $license_key, true );
			
			if ( Pojo_Licenses_API::STATUS_EXPIRED === $license_data->license ) {
				$status = __( 'Expired', 'pojo' );
			} elseif ( Pojo_Licenses_API::STATUS_SITE_INACTIVE === $license_data->license ) {
				$status = __( 'No Match', 'pojo' );
			} else {
				$status = __( 'Active', 'pojo' );
			}
		} else {
			$status = __( 'Inactive', 'pojo' );
		}
		
		return array(
			'value' => $status,
		);
	}
}