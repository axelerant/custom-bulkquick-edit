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

if ( class_exists( 'Aihrus_Common' ) )
	return;

require_once 'interface-aihrus-common.php';


abstract class Aihrus_Common implements Aihrus_Common_Interface {
	public static $donate_button;
	public static $donate_link;


	public function __construct() {
		self::set_notice_key();

		self::$donate_button = <<<EOD
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="WM4F995W9LHXE">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
EOD;

		self::$donate_link = '<a href="http://aihr.us/about-aihrus/donate/"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" alt="PayPal - The safer, easier way to pay online!" /></a>';

		add_action( 'admin_init', array( static::$class, 'check_notices' ), 9999 );
	}


	public static function set_notice( $notice_name, $frequency_limit = false ) {
		$notice_key = self::get_notice_key();

		$frequency_limit = intval( $frequency_limit );
		if ( ! empty( $frequency_limit ) ) {
			$fl_key  = $notice_key . '_' . $notice_name;
			$proceed = get_transient( $fl_key );
			if ( false === $proceed ) {
				delete_transient( $fl_key );
				set_transient( $fl_key, time(), $frequency_limit );
			} else {
				return;
			}
		}

		$notices = get_transient( $notice_key );
		if ( false === $notices )
			$notices = array();

		$notices[] = $notice_name;

		self::delete_notices();
		set_transient( $notice_key, $notices, HOUR_IN_SECONDS );
	}


	public static function delete_notices() {
		$notice_key = self::get_notice_key();

		delete_transient( $notice_key );
	}


	public static function check_notices() {
		$notice_key = self::get_notice_key();

		$notices = get_transient( $notice_key );
		if ( false === $notices )
			return;

		$notices = array_unique( $notices );
		foreach ( $notices as $notice ) {
			if ( ! is_array( $notice ) )
				add_action( 'admin_notices', array( static::$class, $notice ) );
			else
				add_action( 'admin_notices', $notice );
		}

		self::delete_notices();
	}


	public static function get_notice_key() {
		if ( is_null( static::$notice_key ) )
			self::set_notice_key();

		return static::$notice_key;
	}


	public static function set_notice_key() {
		static::$notice_key = static::SLUG . 'notices';
	}


	public static function notice_version( $free_base, $free_name, $free_slug, $free_version, $item_name ) {
		$is_active = is_plugin_active( $free_base );
		if ( $is_active )
			$link = sprintf( __( '<a href="%1$s">update to</a>' ), self_admin_url( 'update-core.php' ) );
		else {
			$plugins = get_plugins();
			if ( empty( $plugins[ $free_base ] ) ) {
				$install = esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $free_slug ), 'install-plugin_' . $free_slug ) );
				$link    = sprintf( __( '<a href="%1$s">install</a>' ), $install );
			} else {
				$activate = esc_url( wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=' . $free_base ), 'activate-plugin_' . $free_base ) );
				$link     = sprintf( __( '<a href="%1$s">activate</a>' ), $activate );
			}
		}

		$text = sprintf( __( 'Plugin %3$s has been deactivated. Please %1$s %4$s version %2$s or newer before activating %3$s.' ), $link, $free_version, $item_name, $free_name );

		self::notice_error( $text );
	}


	public static function notice_license( $post_type, $settings_id, $free_name, $purchase_url, $item_name ) {
		if ( empty( $post_type ) )
			$link = get_admin_url() . 'options-general.php?page=' . $settings_id;
		else
			$link = get_admin_url() . 'edit.php?post_type=' . $post_type . '&page=' . $settings_id;

		$text = __( '<a href="%1$s">%2$s &gt; Settings</a>, <em>Premium</em> tab, <em>License Key</em> entry' );

		$settings_link = sprintf( $text, $link, $free_name );

		$link = esc_url( 'https://aihrus.zendesk.com/entries/28745227-Where-s-my-license-key-' );
		$text = __( '<a href="%s">Where\'s my license key?</a>' );

		$faq_link = sprintf( $text, $link );

		$link = esc_url( $purchase_url );
		$text = __( '<a href="%1$s">%2$s</a>' );

		$buy_link = sprintf( $text, $link, $item_name );

		$text = sprintf( __( 'Plugin %1$s requires license activation before updating will work. Please activate the license key via %2$s. No license key? See %3$s or purchase %4$s.' ), $item_name, $settings_link, $faq_link, $buy_link );

		self::notice_error( $text );
	}


	public function version( $version ) {
		$version .= '-' . static::ID . '-' . static::VERSION;

		return $version;
	}


	/**
	 * flatten an arbitrarily deep multidimensional array
	 * into a list of its scalar values
	 * (may be inefficient for large structures)
	 * (will infinite recurse on self-referential structures)
	 * (could be extended to handle objects)
	 *
	 * @ref http://in1.php.net/manual/en/function.array-values.php#41967
	 */


	public function array_values_recursive( $ary ) {
		$lst = array();
		foreach ( array_keys( $ary ) as $k ) {
			$v = $ary[$k];
			if ( is_scalar( $v ) ) {
				$lst[] = $v;
			} elseif ( is_array( $v ) ) {
				$lst = array_merge(
					$lst,
					self::array_values_recursive( $v )
				);
			}
		}

		return $lst;
	}


	public static function notice_donate( $disable_donate = null, $item_name = null ) {
		if ( $disable_donate )
			return;

		$text = sprintf( esc_html__( 'Please donate $5 towards ongoing support and development of the %1$s plugin. %2$s' ), $item_name, self::$donate_button );

		self::notice_updated( $text );
	}


	public static function notice_error( $text ) {
		self::notice_updated( $text, 'error' );
	}


	public static function notice_updated( $text, $class = 'updated' ) {
		if ( 'updated' == $class )
			$class .= ' fade';

		$content  = '';
		$content .= '<div class="' . $class . '"><p>';
		$content .= $text;
		$content .= '</p></div>';

		echo $content;
	}


	public static function get_scripts() {
		if ( static::$scripts_called )
			return;

		foreach ( static::$scripts as $script )
			echo $script;

		static::$scripts_called = true;
	}


	public static function get_styles() {
		if ( static::$styles_called )
			return;

		foreach ( static::$styles as $style )
			echo $style;

		static::$styles_called = true;
	}


	public static function create_nonce( $action ) {
		$nonce = uniqid();
		$uid   = get_current_user_id();
		$check = $uid . $action;
		set_transient( $nonce, $check, HOUR_IN_SECONDS );

		return $nonce;
	}


	public static function verify_nonce( $nonce, $action ) {
		$active = get_transient( $nonce );
		$uid    = get_current_user_id();
		$check  = $uid . $action;
		$valid  = false;

		if ( $active == $check ) {
			delete_transient( $nonce );
			$valid = true;
		}

		return $valid;
	}


	/**
	 * If incoming link is empty, then get_site_url() is used instead.
	 */
	public static function create_link( $link ) {
		if ( empty( $link ) )
			$link = get_site_url();

		if ( preg_match( '#^\d+$#', $link ) ) {
			$permalink = get_permalink( $link );
			$title     = get_the_title( $link );

			$tag  = '<a href="';
			$tag .= $permalink;
			$tag .= '" title="';
			$tag .= $title;
			$tag .= '">';
			$tag .= $title;
			$tag .= '</a>';
		} else {
			$orig_link = $link;
			$do_http   = true;

			if ( 0 === strpos( $link, '/' ) )
				$do_http = false;

			if ( $do_http && 0 === preg_match( '#https?://#', $link ) )
				$link = 'http://' . $link;

			$permalink = $link;

			$tag  = '<a href="';
			$tag .= $permalink;
			$tag .= '">';
			$tag .= $orig_link;
			$tag .= '</a>';
		}

		return array(
			'link' => $permalink,
			'tag' => $tag,
		);
	}


}


?>
