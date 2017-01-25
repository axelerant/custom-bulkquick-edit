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

if ( ! defined( 'AIHR_BASE' ) ) {
	define( 'AIHR_BASE', __FILE__ );
}

if ( ! defined( 'AIHR_DIR' ) ) {
	define( 'AIHR_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'AIHR_DIR_INC' ) ) {
	define( 'AIHR_DIR_INC', AIHR_DIR . 'includes/' );
}

if ( ! defined( 'AIHR_DIR_LIB' ) ) {
	define( 'AIHR_DIR_LIB', AIHR_DIR_INC . 'libraries/' );
}

if ( ! defined( 'AIHR_VERSION' ) ) {
	define( 'AIHR_VERSION', '1.2.9' );
}

require_once ABSPATH . 'wp-admin/includes/plugin.php';


if ( ! function_exists( 'aihr_check_aihrus_framework' ) ) {
	function aihr_check_aihrus_framework( $file = null, $name = null, $aihr_min = '1.0.0' ) {
		if ( is_null( $file ) ) {
			aihr_notice_error( __( '`aihr_check_aihrus_framework` requires $file argument' ) );

			return false;
		}

		if ( ! defined( 'AIHR_VERSION' ) ) {
			$check_okay = false;
		} else {
			$check_okay = version_compare( AIHR_VERSION, $aihr_min, '>=' );
		}

		$file = plugin_basename( $file );
		if ( ! $check_okay && __FILE__ != $file ) {
			if ( ! defined( 'AIHR_VERSION_FILE' ) ) {
				define( 'AIHR_VERSION_FILE', $file );
			}

			if ( ! is_null( $name ) && ! defined( 'AIHR_VERSION_NAME' ) ) {
				define( 'AIHR_VERSION_NAME', $name );
			}

			if ( ! defined( 'AIHR_VERSION_MIN' ) ) {
				define( 'AIHR_VERSION_MIN', $aihr_min );
			}

			add_action( 'admin_notices', 'aihr_notice_aihrus_framework' );
		}

		return $check_okay;
	}
}

if ( ! function_exists( 'aihr_notice_aihrus_framework' ) ) {
	function aihr_notice_aihrus_framework() {
		if ( defined( 'AIHR_VERSION_NAME' ) ) {
			$name = AIHR_VERSION_NAME;
		} else {
			$name = basename( dirname( AIHR_VERSION_FILE ) );
			$name = str_replace( '-', ' ', $name );
			$name = ucwords( $name );
		}

		$help_url  = esc_url( 'https://axelerant.atlassian.net/wiki/display/WPFAQ/Axelerant+Framework+Out+of+Date' );
		$help_link = sprintf( __( '<a href="%1$s">Update plugins</a>. <a href="%2$s" target="_blank">More information</a>.' ), self_admin_url( 'update-core.php' ), $help_url );

		$note = '';
		if ( defined( 'AIHR_BASE' ) ) {
			$plugin = plugin_basename( AIHR_BASE );
			$plugin = explode( '/', $plugin );

			$plugin_name = $plugin[0];
			$plugin_name = str_replace( '-', ' ', $plugin_name );
			$plugin_name = ucwords( $plugin_name );

			$note = sprintf( esc_html__( 'Plugin "%1$s" is causing the out of date issue.' ), $plugin_name );
		}

		$aihr_version = defined( 'AIHR_VERSION' ) ? AIHR_VERSION : '0.0.0';

		$text = sprintf( esc_html__( 'Plugin "%1$s" has been deactivated as it requires Aihrus Framework %2$s or newer. You\'re running Aihrus Framework %4$s. Once corrected, "%1$s" can be activated. %5$s %3$s' ), $name, AIHR_VERSION_MIN, $help_link, $aihr_version, $note );

		aihr_notice_error( $text );
	}
}

if ( ! function_exists( 'aihr_check_php' ) ) {
	function aihr_check_php( $file = null, $name = null, $php_min = '5.3.0' ) {
		if ( is_null( $file ) ) {
			aihr_notice_error( __( '`aihr_check_php` requires $file argument' ) );

			return false;
		}

		$check_okay = version_compare( PHP_VERSION, $php_min, '>=' );
		$file       = plugin_basename( $file );
		if ( ! $check_okay && __FILE__ != $file ) {
			if ( ! defined( 'AIHR_PHP_VERSION_FILE' ) ) {
				define( 'AIHR_PHP_VERSION_FILE', $file );
			}

			if ( ! is_null( $name ) && ! defined( 'AIHR_PHP_VERSION_NAME' ) ) {
				define( 'AIHR_PHP_VERSION_NAME', $name );
			}

			if ( ! defined( 'AIHR_PHP_VERSION_MIN' ) ) {
				define( 'AIHR_PHP_VERSION_MIN', $php_min );
			}

			add_action( 'admin_notices', 'aihr_notice_php' );
		}

		return $check_okay;
	}
}

if ( ! function_exists( 'aihr_notice_php' ) ) {
	function aihr_notice_php() {
		if ( defined( 'AIHR_PHP_VERSION_NAME' ) ) {
			$name = AIHR_PHP_VERSION_NAME;
		} else {
			$name = basename( dirname( AIHR_PHP_VERSION_FILE ) );
			$name = str_replace( '-', ' ', $name );
			$name = ucwords( $name );
		}

		$help_url = esc_url( 'https://axelerant.atlassian.net/wiki/pages/viewpage.action?pageId=12845151' );

		$text = sprintf( __( 'Plugin "%1$s" has been deactivated as it requires PHP %2$s or newer. You\'re running PHP %4$s. Once corrected, "%1$s" can be activated. <a href="%3$s" target="_blank">More information</a>.' ), $name, AIHR_PHP_VERSION_MIN, $help_url, PHP_VERSION );

		aihr_notice_error( $text );
	}
}

if ( ! function_exists( 'aihr_check_wp' ) ) {
	function aihr_check_wp( $file = null, $name = null, $wp_min = '3.6' ) {
		if ( is_null( $file ) ) {
			aihr_notice_error( __( '`aihr_check_wp` requires $file argument' ) );

			return false;
		}

		global $wp_version;

		$check_okay = version_compare( $wp_version, $wp_min, '>=' );
		$file       = plugin_basename( $file );
		if ( ! $check_okay && __FILE__ != $file ) {
			if ( ! defined( 'AIHR_WP_VERSION_FILE' ) ) {
				define( 'AIHR_WP_VERSION_FILE', $file );
			}

			if ( ! is_null( $name ) && ! defined( 'AIHR_WP_VERSION_NAME' ) ) {
				define( 'AIHR_WP_VERSION_NAME', $name );
			}

			if ( ! defined( 'AIHR_WP_VERSION_MIN' ) ) {
				define( 'AIHR_WP_VERSION_MIN', $wp_min );
			}

			add_action( 'admin_notices', 'aihr_notice_wp' );
		}

		return $check_okay;
	}
}

if ( ! function_exists( 'aihr_notice_wp' ) ) {
	function aihr_notice_wp() {
		global $wp_version;

		if ( defined( 'AIHR_WP_VERSION_NAME' ) ) {
			$name = AIHR_WP_VERSION_NAME;
		} else {
			$name = basename( dirname( AIHR_WP_VERSION_FILE ) );
			$name = str_replace( '-', ' ', $name );
			$name = ucwords( $name );
		}

		$help_url = network_admin_url( 'update-core.php' );

		$text = sprintf( __( 'Plugin "%1$s" has been deactivated as it requires WordPress %2$s or newer. You\'re running WordPress %4$s. Once corrected, "%1$s" can be activated. <a href="%3$s">Update WordPress</a>.' ), $name, AIHR_WP_VERSION_MIN, $help_url, $wp_version );

		aihr_notice_error( $text );
	}
}

if ( ! function_exists( 'aihr_notice_error' ) ) {
	function aihr_notice_error( $text ) {
		aihr_notice_updated( $text, 'error error-twp' );
		error_log( $text );
	}
}

if ( ! function_exists( 'aihr_notice_updated' ) ) {
	function aihr_notice_updated( $text, $class = 'updated' ) {
		if ( 'updated' == $class ) {
			$class .= ' fade';
		}

		$content  = '';
		$content .= '<div class="' . $class . '"><p>';
		$content .= $text;
		$content .= '</p></div>';

		echo $content;
	}
}

if ( ! function_exists( 'aihr_notice_version' ) ) {
	function aihr_notice_version( $required_name, $required_version, $item_name ) {
		$text = sprintf( __( 'Plugin "%2$s" has been deactivated. Please install and activate "%3$s" version %1$s or newer before activating "%2$s".' ), $required_version, $item_name, $required_name );

		aihr_notice_error( $text );
	}
}

if ( ! function_exists( 'aihr_notice_license' ) ) {
	function aihr_notice_license( $post_type, $settings_id, $free_name, $purchase_url, $item_name, $product_id = null, $license = null ) {
		if ( empty( $post_type ) ) {
			$link = get_admin_url() . 'options-general.php?page=' . $settings_id;
		} else {
			$link = get_admin_url() . 'edit.php?post_type=' . $post_type . '&page=' . $settings_id;
		}

		$text = __( '<a href="%1$s">%2$s &gt; Settings</a>, <em>Premium</em> tab, <em>License Key</em>' );

		$settings_link = sprintf( $text, $link, $free_name );

		$link = esc_url( 'https://axelerant.atlassian.net/wiki/display/WPFAQ/Where+does+my+license+key+go' );
		$text = __( '<a href="%s" target="_blank">Where\'s my license key?</a>' );

		$faq_link = sprintf( $text, $link );

		$link = esc_url( $purchase_url );
		$text = __( '<a href="%1$s">Purchase</a>' );

		$buy_link = sprintf( $text, $link, $item_name );

		$renew_link = '';
		if ( ! empty( $license ) ) {
			$link = parse_url( $purchase_url );
			$link = $link['host'];
			$text = __( '%1$s/checkout/?edd_license_key=%2$s&download_id=%3$s' );
			$renew_url = sprintf( $text, $link, $license, $product_id );

			$link = esc_url( $renew_url );
			$text = __( '<a href="%1$s">Renew</a> or ' );

			$renew_link = sprintf( $text, $link, $item_name );
		}

		$text = sprintf( __( 'Plugin "%1$s" requires license activation for software updates and support. Please activate the license via %2$s. See %3$s for help. Alternately, %5$s%4$s a %1$s license.' ), $item_name, $settings_link, $faq_link, $buy_link, $renew_link );
		$text = links_add_target( $text, '_blank' );

		aihr_notice_error( $text );
	}
}

if ( ! function_exists( 'aihr_deactivate_plugin' ) ) {
	function aihr_deactivate_plugin( $file = null, $name = null, $reason = '' ) {
		if ( is_null( $file ) ) {
			aihr_notice_error( __( '`aihr_deactivate_plugin` requires $file argument' ) );

			return false;
		}

		if ( is_null( $name ) && empty( $reason ) ) {
			aihr_deactivate_plugin_do( $file );
			return false;
		}

		if ( ! defined( 'AIHR_DEACTIVATE_REASON' ) ) {
			define( 'AIHR_DEACTIVATE_REASON', $reason );
		}

		if ( ! defined( 'AIHR_DEACTIVATE_FILE' ) ) {
			define( 'AIHR_DEACTIVATE_FILE', $file );
		}

		if ( ! is_null( $name ) && ! defined( 'AIHR_DEACTIVATE_NAME' ) ) {
			define( 'AIHR_DEACTIVATE_NAME', $name );
		}

		add_action( 'admin_notices', 'aihr_notice_deactivate' );
	}
}

if ( ! function_exists( 'aihr_notice_deactivate' ) ) {
	function aihr_notice_deactivate() {
		if ( defined( 'AIHR_DEACTIVATE_NAME' ) ) {
			$name = AIHR_DEACTIVATE_NAME;
		} else {
			$name = basename( dirname( AIHR_DEACTIVATE_FILE ) );
			$name = str_replace( '-', ' ', $name );
			$name = ucwords( $name );
		}

		if ( defined( 'AIHR_DEACTIVATE_REASON' ) ) {
			$reason = AIHR_DEACTIVATE_REASON;
		} else {
			$reason = esc_html__( 'Unknown' );
		}

		$file        = AIHR_DEACTIVATE_FILE;
		$plugin_slug = dirname( plugin_basename( $file ) );
		$url         = 'https://wordpress.org/plugins/' . $plugin_slug . '/developers/';

		$text = sprintf( __( 'Plugin "%1$s" has been deactivated due to "%2$s". Once corrected, "%1$s" can be activated.</p><p>If you want to revert "%1$s", look for <a href="%3$s">older versions on WordPress</a> or <a href="mailto:support@axelerant.com?subject=Old+Plugin+Version+Request">email Axelerant support</a> if this is a premium plugin.' ), $name, $reason, $url );

		aihr_notice_error( $text );

		aihr_deactivate_plugin_do( $file );
	}
}

if ( ! function_exists( 'aihr_deactivate_plugin_do' ) ) {
	function aihr_deactivate_plugin_do( $file = null ) {
		if ( is_null( $file ) ) {
			aihr_notice_error( __( '`aihr_deactivate_plugin_do` requires $file argument' ) );

			return false;
		}

		deactivate_plugins( $file );
	}
}

?>
