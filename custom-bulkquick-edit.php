<?php
/**
 * Plugin Name: Custom Bulk/Quick Edit
 * Plugin URI: http://wordpress.org/plugins/custom-bulkquick-edit/
 * Description: Custom Bulk/Quick Edit allows you to easily add custom fields to the edit screen bulk and quick edit panels.
 * Version: 1.6.7
 * Author: Axelerant
 * Author URI: https://axelerant.com/
 * License: GPLv2 or later
 * Text Domain: custom-bulkquick-edit
 * Domain Path: /languages
 */


/**
 * Copyright 2016 Axelerant
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'CBQE_AIHR_VERSION' ) ) {
	define( 'CBQE_AIHR_VERSION', '1.2.9' );
}

if ( ! defined( 'CBQE_BASE' ) ) {
	define( 'CBQE_BASE', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'CBQE_DIR' ) ) {
	define( 'CBQE_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'CBQE_DIR_INC' ) ) {
	define( 'CBQE_DIR_INC', CBQE_DIR . 'includes/' );
}

if ( ! defined( 'CBQE_DIR_LIB' ) ) {
	define( 'CBQE_DIR_LIB', CBQE_DIR_INC . 'libraries/' );
}

if ( ! defined( 'CBQE_NAME' ) ) {
	define( 'CBQE_NAME', 'Custom Bulk/Quick Edit' );
}

if ( ! defined( 'CBQE_PREMIUM_LINK' ) ) {
	define( 'CBQE_PREMIUM_LINK', '<a href="https://store.axelerant.com/downloads/custom-bulkquick-edit-premium-wordpress-plugin/">Buy Premium</a>' );
}

if ( ! defined( 'CBQE_VERSION' ) ) {
	define( 'CBQE_VERSION', '1.6.7' );
}

require_once CBQE_DIR_INC . 'requirements.php';

global $cbqe_activated;

$cbqe_activated = true;
if ( ! cbqe_requirements_check() ) {
	$cbqe_activated = false;

	return false;
}

require_once CBQE_DIR_INC . 'class-custom-bulkquick-edit.php';


add_action( 'after_setup_theme', 'custom_bulkquick_edit_init', 999 );


/**
 *
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
if ( ! function_exists( 'custom_bulkquick_edit_init' ) ) {
	function custom_bulkquick_edit_init() {
		if ( ! is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
			return;
		}

		if ( Custom_Bulkquick_Edit::version_check() ) {
			global $Custom_Bulkquick_Edit;
			if ( is_null( $Custom_Bulkquick_Edit ) ) {
				$Custom_Bulkquick_Edit = new Custom_Bulkquick_Edit();
			}

			global $Custom_Bulkquick_Edit_Settings;
			if ( is_null( $Custom_Bulkquick_Edit_Settings ) ) {
				$Custom_Bulkquick_Edit_Settings = new Custom_Bulkquick_Edit_Settings();
			}
		}
	}
}


register_activation_hook( __FILE__, array( 'Custom_Bulkquick_Edit', 'activation' ) );
register_deactivation_hook( __FILE__, array( 'Custom_Bulkquick_Edit', 'deactivation' ) );
register_uninstall_hook( __FILE__, array( 'Custom_Bulkquick_Edit', 'uninstall' ) );


?>
