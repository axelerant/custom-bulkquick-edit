<?php
/**
Aihrus Framework
Copyright (C) 2015 Axelerant

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

if ( class_exists( 'Aihrus_Licensing' ) ) {
	return;
}


abstract class Aihrus_Licensing {
	public $author;
	public $item_name;
	public $item_name_encoded;
	public $slug;
	public $store_url;

	public static $settings_saved = false;


	public function __construct( $slug, $item_name, $author = 'Axelerant', $store_url = 'https://store.axelerant.com' ) {
		$this->author            = $author;
		$this->item_name_encoded = urlencode( $item_name );
		$this->item_name         = $item_name;
		$this->slug              = $slug;
		$this->store_url         = $store_url;
	}


	public function license_key() {
		$key = $this->slug . 'license_key';

		return $key;
	}


	public function get_license() {
		$key     = $this->license_key();
		$license = get_transient( $key );

		return $license;
	}


	public function update_license( $value = null ) {
		$license = $this->get_license();
		if ( $license === $value ) {
			return $value;
		}

		$deactivate_license = false;
		if ( $this->valid_hash( $value ) ) {
			$this->set_license( $value );
			$value = $this->activate_license();
			$this->set_license( $value );
		} else {
			$this->deactivate_license();
			$this->delete_license();

			$deactivate_license = true;
			$value              = '';
		}

		if ( $this->valid_license() ) {
			$text = esc_html__( '%s license saved.' );
			$text = sprintf( $text, $this->item_name );
			add_settings_error( static::$settings_id, 'license_saved', $text, 'updated' );
		} elseif ( empty( $deactivate_license ) ) {
			$text = esc_html__( '%s license not saved.' );
			$text = sprintf( $text, $this->item_name );
			add_settings_error( static::$settings_id, 'license_not_saved', $text, 'error' );
		}

		if ( empty( self::$settings_saved ) ) {
			self::$settings_saved = true;
			set_transient( 'settings_errors', get_settings_errors(), 30 );
		}

		return $value;
	}


	public function set_license( $value = null ) {
		$key = $this->license_key();
		delete_transient( $key );

		if ( ! is_null( $value ) ) {
			set_transient( $key, $value, YEAR_IN_SECONDS );
		}
	}


	public function valid_license() {
		$license = $this->get_license();
		if ( $this->valid_hash( $license ) ) {
			return true;
		} else {
			return false;
		}
	}


	public function get_api_call( $action ) {
		$license    = $this->get_license();
		$api_params = array(
			'edd_action' => $action,
			'item_name' => $this->item_name_encoded,
			'license' => $license,
		);

		$api_call = add_query_arg(
			$api_params,
			$this->store_url
		);

		return $api_call;
	}


	public function get_remote_get( $api_call ) {
		$response = wp_remote_get( $api_call );

		return $response;
	}


	public function activate_license() {
		$license_data = $this->get_license_data( 'activate_license' );
		if ( false !== $license_data ) {
			if ( 'valid' == $license_data->license ) {
				$license = $this->get_license();

				return $license;
			}

			return $license_data->license;
		}

		return false;
	}


	public function get_license_data( $action = 'check_license' ) {
		$api_call = $this->get_api_call( $action );
		$response = $this->get_remote_get( $api_call );
		if ( is_wp_error( $response ) ) {
			return false;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		return $license_data;
	}


	public function deactivate_license() {
		$license_data = $this->get_license_data( 'deactivate_license' );
		if ( false !== $license_data ) {
			if ( 'deactivated' == $license_data->license ) {
				$text = esc_html__( '%s license deactivated.' );
				$text = sprintf( $text, $this->item_name );
				add_settings_error( static::$settings_id, 'license_deactivated', $text, 'updated' );

				return true;
			}

			return $license_data->license;
		}

		return false;
	}


	public function delete_license() {
		$this->set_license();
	}


	public function license_notice() {
		$result = '<p><em>' . esc_html( 'Premium features require licensing to function.' ) . '</em></p>';

		return $result;
	}


	public function valid_hash( $value = null ) {
		if ( is_string( $value ) && preg_match( '#^[0-9a-f]{32}$#i', $value ) ) {
			return true;
		}

		return false;
	}


	public static function settings( $settings ) {
		return $settings;
	}
}


?>
