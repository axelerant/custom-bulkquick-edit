<?php
/*
	Copyright 2015 Axelerant

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

require_once CBQE_DIR_LIB . 'aihrus-framework/aihrus-framework.php';


function cbqe_requirements_check( $force_check = false ) {
	$check_okay = get_transient( 'cbqe_requirements_check' );
	if ( empty( $force_check ) && false !== $check_okay ) {
		return $check_okay;
	}

	$deactivate_reason = false;
	if ( ! function_exists( 'aihr_check_aihrus_framework' ) ) {
		$deactivate_reason = esc_html__( 'Missing Aihrus Framework', 'custom-bulkquick-edit' );
		add_action( 'admin_notices', 'cbqe_notice_aihrus' );
	} elseif ( ! aihr_check_aihrus_framework( CBQE_BASE, CBQE_NAME, CBQE_AIHR_VERSION ) ) {
		$deactivate_reason = esc_html__( 'Old Aihrus Framework version detected', 'custom-bulkquick-edit' );
	}

	if ( ! aihr_check_php( CBQE_BASE, CBQE_NAME ) ) {
		$deactivate_reason = esc_html__( 'Old PHP version detected', 'custom-bulkquick-edit' );
	}

	if ( ! aihr_check_wp( CBQE_BASE, CBQE_NAME ) ) {
		$deactivate_reason = esc_html__( 'Old WordPress version detected', 'custom-bulkquick-edit' );
	}

	if ( ! empty( $deactivate_reason ) ) {
		aihr_deactivate_plugin( CBQE_BASE, CBQE_NAME, $deactivate_reason );
	}

	$check_okay = empty( $deactivate_reason );
	if ( $check_okay ) {
		delete_transient( 'cbqe_requirements_check' );
		set_transient( 'cbqe_requirements_check', $check_okay, HOUR_IN_SECONDS );
	}

	return $check_okay;
}


function cbqe_notice_aihrus() {
	$help_url  = esc_url( 'https://nodedesk.zendesk.com/hc/en-us/articles/202381391' );
	$help_link = sprintf( __( '<a href="%1$s">Update plugins</a>. <a href="%2$s">More information</a>.', 'custom-bulkquick-edit' ), self_admin_url( 'update-core.php' ), $help_url );

	$text = sprintf( esc_html__( 'Plugin "%1$s" has been deactivated as it requires a current Aihrus Framework. Once corrected, "%1$s" can be activated. %2$s', 'custom-bulkquick-edit' ), CBQE_NAME, $help_link );

	aihr_notice_error( $text );
}

?>
