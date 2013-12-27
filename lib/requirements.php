<?php
/*
	Copyright 2013 Michael Cannon (email: mc@aihr.us)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

require_once CBQE_DIR_LIB . '/aihrus/requirements.php';


function cbqe_requirements_check() {
	$valid_requirements = true;
	if ( ! function_exists( 'aihr_check_aihrus_framework' ) ) {
		$valid_requirements = false;
		add_action( 'admin_notices', 'cbqe_notice_aihrus' );
	} elseif ( ! aihr_check_aihrus_framework( CBQE_BASE, CBQE_NAME, CBQE_AIHR_VERSION ) ) {
		$valid_requirements = false;
	}

	if ( ! aihr_check_php( CBQE_BASE, CBQE_NAME ) ) {
		$valid_requirements = false;
	}

	if ( ! aihr_check_wp( CBQE_BASE, CBQE_NAME ) ) {
		$valid_requirements = false;
	}

	if ( ! $valid_requirements ) {
		deactivate_plugins( CBQE_BASE );
	}

	return $valid_requirements;
}


function cbqe_notice_aihrus() {
	$help_url  = esc_url( 'https://aihrus.zendesk.com/entries/35689458' );
	$help_link = sprintf( __( '<a href="%1$s">Update plugins</a>. <a href="%2$s">More information</a>.', 'custom-bulkquick-edit' ), self_admin_url( 'update-core.php' ), $help_url );

	$text = sprintf( esc_html__( 'Plugin "%1$s" has been deactivated as it requires a current Aihrus Framework. Once corrected, "%1$s" can be activated. %2$s', 'custom-bulkquick-edit' ), CBQE_NAME, $help_link );

	aihr_notice_error( $text );
}

?>
