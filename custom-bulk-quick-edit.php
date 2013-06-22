<?php
/**
 * Plugin Name: Custom Bulk/Quick Edit
 * Plugin URI: http://wordpress.org/extend/plugins/custom-bulk-quick-edit/
 * Description: Custom Bulk/Quick Edit plugin allows you to add custom fields to the edit screen bulk and quick edit panels.
 * Version: 0.0.1
 * Author: Michael Cannon
 * Author URI: http://aihr.us/about-aihrus/michael-cannon-resume/
 * License: GPLv2 or later
 */
/**
 * Copyright 2013 Michael Cannon (email: mc@aihr.us)
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


require_once 'lib/class-custom-bulk-quick-edit-settings.php';


class Custom_Bulk_Quick_Edit {
	const ID          = 'custom-bulk-quick-edit';
	const PLUGIN_FILE = 'custom-bulk-quick-edit/custom-bulk-quick-edit.php';
	const VERSION     = '0.0.1';

	private static $base = null;

	public static $cpt_category    = '';
	public static $cpt_tags        = '';
	public static $css             = array();
	public static $css_called      = false;
	public static $donate_button   = '';
	public static $instance_number = 0;
	public static $scripts         = array();
	public static $scripts_called  = false;
	public static $settings_link   = '';


	public function __construct() {
		add_action( 'admin_init', array( &$this, 'admin_init' ) );
		add_action( 'init', array( &$this, 'init' ) );
		load_plugin_textdomain( self::ID, false, 'custom-bulk-quick-edit/languages' );
		register_activation_hook( __FILE__, array( &$this, 'activation' ) );
		register_deactivation_hook( __FILE__, array( &$this, 'deactivation' ) );
		register_uninstall_hook( __FILE__, array( 'Custom_Bulk_Quick_Edit', 'uninstall' ) );
	}


	public function admin_init() {
		self::$settings_link = '<a href="' . get_admin_url() . 'options-general.php?page=' . Custom_Bulk_Quick_Edit_Settings::ID . '">' . __( 'Settings', 'custom-bulk-quick-edit' ) . '</a>';

		$this->update();
		add_action( 'manage_' . self::ID . '_posts_custom_column', array( &$this, 'manage_posts_custom_column' ), 10, 2 );
		add_filter( 'manage_' . self::ID . '_posts_columns', array( &$this, 'manage_edit_columns' ) );
		add_filter( 'plugin_action_links', array( &$this, 'plugin_action_links' ), 10, 2 );
		add_filter( 'plugin_row_meta', array( &$this, 'plugin_row_meta' ), 10, 2 );
	}


	public function init() {
		self::$cpt_category  = self::ID . '-category';
		self::$cpt_tags      = self::ID . '-post_tag';
		self::$donate_button = <<<EOD
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="WM4F995W9LHXE">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
EOD;

		self::$base = plugin_basename( __FILE__ );
		self::styles();
	}


	public function plugin_action_links( $links, $file ) {
		if ( $file == self::$base )
			array_unshift( $links, self::$settings_link );

		return $links;
	}


	public static function get_instance() {
		return self::$instance_number;
	}


	public static function add_instance() {
		self::$instance_number++;
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public function activation() {
		if ( ! current_user_can( 'activate_plugins' ) )
			return;

		$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : false;
		if ( $plugin )
			check_admin_referer( "activate-plugin_{$plugin}" );

		self::init();

		flush_rewrite_rules();
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public function deactivation() {
		if ( ! current_user_can( 'activate_plugins' ) )
			return;

		$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : false;
		if ( $plugin )
			check_admin_referer( "deactivate-plugin_{$plugin}" );

		flush_rewrite_rules();
	}


	public function uninstall() {
		if ( ! current_user_can( 'activate_plugins' ) )
			return;

		if ( __FILE__ != WP_UNINSTALL_PLUGIN )
			return;

		check_admin_referer( 'bulk-plugins' );

		global $wpdb;

		$delete_data = cbqe_get_option( 'delete_data', false );
		if ( $delete_data ) {
			delete_option( Custom_Bulk_Quick_Edit_Settings::ID );
			$wpdb->query( 'OPTIMIZE TABLE `' . $wpdb->options . '`' );
		}
	}


	public static function plugin_row_meta( $input, $file ) {
		if ( $file != self::$base )
			return $input;

		$links = array(
			'<a href="http://aihr.us/about-aihrus/donate/"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" alt="PayPal - The safer, easier way to pay online!" /></a>',
			'<a href="http://aihr.us/downloads/custom-bulk-quick-edit-premium-wordpress-plugin/">Purchase Custom Bulk/Quick Edit Premium</a>',
		);

		$input = array_merge( $input, $links );

		return $input;
	}


	public function admin_notices_0_0_1() {
		$content  = '<div class="updated"><p>';
		$content .= sprintf( __( 'If your Custom Bulk/Quick Edit display has gone to funky town, please <a href="%s">read the FAQ</a> about possible CSS fixes.', 'custom-bulk-quick-edit' ), 'https://aihrus.zendesk.com/entries/23722573-Major-Changes-Since-2-10-0' );
		$content .= '</p></div>';

		echo $content;
	}


	public function admin_notices_donate() {
		$content  = '<div class="updated"><p>';
		$content .= sprintf( __( 'Please donate $2 towards keeping Custom Bulk/Quick Edit plugin supported and maintained %s', 'custom-bulk-quick-edit' ), self::$donate_button );
		$content .= '</p></div>';

		echo $content;
	}


	public function update() {
		$prior_version = cbqe_get_option( 'admin_notices' );
		if ( $prior_version ) {
			if ( $prior_version < '0.0.1' )
				add_action( 'admin_notices', array( $this, 'admin_notices_0_0_1' ) );

			cbqe_set_option( 'admin_notices' );
		}

		// display donate on major/minor version release or if it's been a month
		$donate_version = cbqe_get_option( 'donate_version', false );
		if ( ! $donate_version || ( $donate_version != self::VERSION && preg_match( '#\.0$#', self::VERSION ) ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notices_donate' ) );
			cbqe_set_option( 'donate_version', self::VERSION );
		}
	}




	public function manage_posts_custom_column( $column, $post_id ) {
		$result = false;

		switch ( $column ) {
		case 'custom-bulk-quick-edit-company':
		case 'custom-bulk-quick-edit-location':
		case 'custom-bulk-quick-edit-title':
			$result = get_post_meta( $post_id, $column, true );
			break;

		case 'custom-bulk-quick-edit-email':
		case 'custom-bulk-quick-edit-url':
			$url = get_post_meta( $post_id, $column, true );
			if ( ! empty( $url ) && ! is_email( $url ) && 0 === preg_match( '#https?://#', $url ) )
				$url = 'http://' . $url;

			$result = make_clickable( $url );
			break;

		case 'thumbnail':
			$email = get_post_meta( $post_id, 'custom-bulk-quick-edit-email', true );

			if ( has_post_thumbnail( $post_id ) ) {
				$result = get_the_post_thumbnail( $post_id, 'thumbnail' );
			} elseif ( is_email( $email ) ) {
				$result = get_avatar( $email );
			} else {
				$result = false;
			}
			break;

		case self::$cpt_category:
		case self::$cpt_tags:
			$terms  = get_the_terms( $post_id, $column );
			$result = '';
			if ( ! empty( $terms ) ) {
				$out = array();
				foreach ( $terms as $term )
					$out[] = '<a href="' . admin_url( 'edit-tags.php?action=edit&taxonomy=' . $column . '&tag_ID=' . $term->term_id . '&post_type=' . self::ID ) . '">' . $term->name . '</a>';

				$result = join( ', ', $out );
			}
			break;
		}

		$result = apply_filters( 'custom_bulk_quick_edit_posts_custom_column', $result, $column, $post_id );

		if ( $result )
			echo $result;
	}


	public function manage_edit_columns( $columns ) {
		// order of keys matches column ordering
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'thumbnail' => __( 'Image', 'custom-bulk-quick-edit' ),
			'title' => __( 'Source', 'custom-bulk-quick-edit' ),
			'shortcode' => __( 'Shortcodes', 'custom-bulk-quick-edit' ),
			'custom-bulk-quick-edit-title' => __( 'Title', 'custom-bulk-quick-edit' ),
			'custom-bulk-quick-edit-location' => __( 'Location', 'custom-bulk-quick-edit' ),
			'custom-bulk-quick-edit-email' => __( 'Email', 'custom-bulk-quick-edit' ),
			'custom-bulk-quick-edit-company' => __( 'Company', 'custom-bulk-quick-edit' ),
			'custom-bulk-quick-edit-url' => __( 'URL', 'custom-bulk-quick-edit' ),
			'author' => __( 'Published by', 'custom-bulk-quick-edit' ),
			'date' => __( 'Date', 'custom-bulk-quick-edit' ),
		);

		$use_cpt_taxonomy = cbqe_get_option( 'use_cpt_taxonomy', false );
		if ( ! $use_cpt_taxonomy ) {
			$columns[ 'categories' ] = __( 'Category', 'custom-bulk-quick-edit' );
			$columns[ 'tags' ]       = __( 'Tags', 'custom-bulk-quick-edit' );
		} else {
			$columns[ self::$cpt_category ] = __( 'Category', 'custom-bulk-quick-edit' );
			$columns[ self::$cpt_tags ]     = __( 'Tags', 'custom-bulk-quick-edit' );
		}

		$columns = apply_filters( 'custom_bulk_quick_edit_columns', $columns );

		return $columns;
	}


	public static function get_defaults() {
		return apply_filters( 'custom_bulk_quick_edit_defaults', cbqe_get_options() );
	}


	public static function scripts() {
		wp_enqueue_script( 'jquery' );
	}


	public static function styles() {
		wp_register_style( 'custom-bulk-quick-edit', plugins_url( 'custom-bulk-quick-edit.css', __FILE__ ) );
		wp_enqueue_style( 'custom-bulk-quick-edit' );
	}


	public static function get_html_css( $atts, $instance_number = null ) {
		// display attributes
		$height     = $atts['height'];
		$max_height = $atts['max_height'];
		$min_height = $atts['min_height'];

		if ( $height ) {
			$max_height = $height;
			$min_height = $height;
		}

		$css     = array();
		$id_base = self::ID . $instance_number;

		if ( $min_height ) {
			$css[] = <<<EOF
<style>
.$id_base {
min-height: {$min_height}px;
}
</style>
EOF;
		}

		if ( $max_height ) {
			$css[] = <<<EOF
<style>
.$id_base {
	max-height: {$max_height}px;
}
</style>
EOF;
		}

		$css = apply_filters( 'custom_bulk_quick_edit_css', $css, $atts, $instance_number );

		return $css;
	}


	public static function get_html_js( $items, $atts, $instance_number = null ) {
		// display attributes
		$refresh_interval = $atts['refresh_interval'];

		$id_base    = self::ID . $instance_number;
		$scripts    = array();
		$tw_padding = 'tw_padding' . $instance_number;
		$tw_wrapper = 'tw_wrapper' . $instance_number;

		$height     = $atts['height'];
		$max_height = $atts['max_height'];
		$min_height = $atts['min_height'];

		$enable_animation = 1;
		if ( $height || $max_height || $min_height )
			$enable_animation = 0;

		if ( $refresh_interval && 1 < count( $items ) ) {
			$javascript = <<<EOF
<script type="text/javascript">
if ( {$enable_animation} ) {
	var {$tw_wrapper} = jQuery('.{$id_base}');
	// tw_padding is the difference in height to take into account all styling options
	var {$tw_padding} = {$tw_wrapper}.height() - jQuery('.{$id_base} .custom-bulk-quick-edit').height();
	// fixes first animation by defining height to adjust to
	{$tw_wrapper}.height( {$tw_wrapper}.height() );
}

function nextTestimonial{$instance_number}() {
	if ( ! jQuery('.{$id_base}').first().hasClass('hovered') ) {
		var active = jQuery('.{$id_base} .active');
		var next   = (jQuery('.{$id_base} .active').next().length > 0) ? jQuery('.{$id_base} .active').next() : jQuery('.{$id_base} .custom-bulk-quick-edit:first-child');

		active.fadeOut(1250, function(){
			active.removeClass('active');
			next.fadeIn(500);
			next.removeClass('display-none');
			next.addClass('active');

			if ( {$enable_animation} ) {
				// added padding
				{$tw_wrapper}.animate({ height: next.height() + {$tw_padding} });
			}
		});
	}
}

jQuery(document).ready(function(){
	jQuery('.{$id_base}').hover(function() {
		jQuery(this).addClass('hovered')
	}, function() {
		jQuery(this).removeClass('hovered')
	});
	nextTestimonial{$instance_number}interval = setInterval('nextTestimonial{$instance_number}()', {$refresh_interval} * 1000);
});
</script>
EOF;

			$scripts[ $id_base ] = $javascript;
		}

		$scripts = apply_filters( 'custom_bulk_quick_edit_js', $scripts, $items, $atts, $instance_number );

		return $scripts;
	}


	public static function get_css() {
		if ( empty( self::$css_called ) ) {
			foreach ( self::$css as $css )
				echo $css;

			self::$css_called = true;
		}
	}


	public static function get_scripts() {
		if ( empty( self::$scripts_called ) ) {
			foreach ( self::$scripts as $script )
				echo $script;

			self::$scripts_called = true;
		}
	}


}


include_once ABSPATH . 'wp-admin/includes/plugin.php';
if ( is_plugin_active( Custom_Bulk_Quick_Edit::PLUGIN_FILE ) ) {
	$Custom_Bulk_Quick_Edit          = new Custom_Bulk_Quick_Edit();
	$Custom_Bulk_Quick_Edit_Settings = new Custom_Bulk_Quick_Edit_Settings();
}


?>
