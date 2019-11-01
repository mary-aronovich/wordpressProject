<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Pojo_Licenses_API {
	
	protected static $MIRRORS_URL = array(
		'http://pojo.me',
		'http://mirror1.getpojo.com',
	);
	
	// Licenses Status
	const STATUS_VALID         = 'valid';
	const STATUS_INVALID       = 'invalid';
	const STATUS_EXPIRED       = 'expired';
	const STATUS_DEACTIVATED   = 'deactivated';
	const STATUS_SITE_INACTIVE = 'site_inactive';

	protected $_curl_args;

	public function __construct() {
		$this->_curl_args = array(
			'timeout' => 15,
			'sslverify' => false,
		);
	}

	/**
	 * @param array $body_args
	 *
	 * @return stdClass|bool
	 */
	protected function _remote_post( $body_args = array() ) {
		$original_url = self::$MIRRORS_URL[0];
		
		$body_args = wp_parse_args(
			$body_args,
			array(
				'site_lang' => get_bloginfo( 'language' ),
			)
		);
		
		foreach ( self::$MIRRORS_URL as $mirror_url ) {
			$response = wp_remote_post(
				$mirror_url,
				wp_parse_args(
					array(
						'body' => $body_args,
					),
					$this->_curl_args
				)
			);
			
			if ( is_wp_error( $response ) )
				continue;
			
			if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) )
				continue;

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			if ( empty( $license_data ) )
				continue;
			
			if ( $original_url !== $mirror_url ) {
				if ( isset( $license_data->package ) ) {
					$license_data->package = str_replace( $original_url, $mirror_url, $license_data->package );
				}
				
				if ( isset( $license_data->download_link ) ) {
					$license_data->download_link = str_replace( $original_url, $mirror_url, $license_data->download_link );
				}
			}

			return $license_data;
		}
		
		return false;
	}

	public function activate_license( $license_key ) {
		$body_args = array(
			'edd_action' => 'activate_license',
			'item_name' => urlencode( Pojo_Core::instance()->licenses->updater->theme_name ),
			'license' => $license_key,
		);

		$license_data = $this->_remote_post( $body_args );
		
		if ( ! $license_data )
			return false;
		
		set_transient( Pojo_Core::instance()->licenses->get_license_data_option_key(), $license_data, 12 * HOUR_IN_SECONDS );
		
		Pojo_Core::instance()->licenses->updater->delete_theme_update_transient();
		
		return self::STATUS_VALID === $license_data->license;
	}

	public function deactivate_license( $license_key ) {
		$body_args = array(
			'edd_action' => 'deactivate_license',
			'item_name' => urlencode( Pojo_Core::instance()->licenses->updater->theme_name ),
			'license' => $license_key,
		);

		$license_data = $this->_remote_post( $body_args );
		if ( ! $license_data )
			return false;

		Pojo_Core::instance()->licenses->updater->delete_theme_update_transient();
		
		return self::STATUS_DEACTIVATED === $license_data->license;
	}

	public function check_license( $license_key, $force_request = false ) {
		$option_key   = Pojo_Core::instance()->licenses->get_option_key() . '_data';
		$license_data = get_transient( $option_key );
		
		if ( false === $license_data || $force_request ) {
			$body_args = array(
				'license' => $license_key,
				'item_name' => urlencode( Pojo_Core::instance()->licenses->updater->theme_name ),
				'edd_action' => 'check_license',
			);
			
			$license_data = $this->_remote_post( $body_args );
			
			if ( ! $license_data ) {
				$license_data = new stdClass;
				
				$license_data->license          = 'http_error';
				$license_data->payment_id       = '0';
				$license_data->license_limit    = '0';
				$license_data->site_count       = '0';
				$license_data->activations_left = '0';

				set_transient( $option_key, $license_data, 30 * MINUTE_IN_SECONDS );
				return $license_data;
			}
			
			set_transient( $option_key, $license_data, 12 * HOUR_IN_SECONDS );
		}
		
		return $license_data;
	}

	public function get_version( $license_key = '' ) {
		$body_args = array(
			'edd_action' => 'get_version_inactive',
			'name' => urlencode( Pojo_Core::instance()->licenses->updater->theme_name ),
			'slug' => Pojo_Core::instance()->licenses->updater->theme_slug,
			'version' => Pojo_Core::instance()->licenses->updater->theme_version,
			'site_url' => '',
		);
		
		if ( ! empty( $license_key ) ) {    
			$body_args['edd_action'] = 'get_version';
			$body_args['license'] = $license_key;
		}
		
		$license_data = $this->_remote_post( $body_args );

		return $license_data;
	}

}