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
class Custom_Bulk_Quick_Edit {
	const ID          = 'custom-bulk-quick-edit';
	const PLUGIN_FILE = 'custom-bulk-quick-edit/custom-bulk-quick-edit.php';
	const VERSION     = '0.0.1';

	private static $base              = null;
	private static $field_key         = 'cbqe_';
	private static $post_types_ignore = array(
		'attachment',
		'page',
	);
	private static $no_instance       = true;

	public static $css             = array();
	public static $css_called      = false;
	public static $donate_button   = '';
	public static $instance_number = 0;
	public static $post_types      = array();
	public static $post_types_keys = array();
	public static $scripts_bulk    = array();
	public static $scripts_quick   = array();
	public static $scripts_called  = false;
	public static $settings_link   = '';


	public function __construct() {
		add_action( 'admin_init', array( &$this, 'admin_init' ) );
		add_action( 'init', array( &$this, 'init' ) );
		load_plugin_textdomain( self::ID, false, 'custom-bulk-quick-edit/languages' );
	}


	public function admin_init() {
		self::$settings_link = '<a href="' . get_admin_url() . 'options-general.php?page=' . Custom_Bulk_Quick_Edit_Settings::ID . '">' . __( 'Settings', 'custom-bulk-quick-edit' ) . '</a>';

		$this->update();
		add_action( 'admin_footer', array( &$this, 'admin_footer' ) );
		add_action( 'bulk_edit_custom_box', array( &$this, 'quick_edit_custom_box' ), 10, 2 );
		add_action( 'manage_' . self::ID . '_posts_custom_column', array( &$this, 'manage_posts_custom_column' ), 10, 2 );
		add_action( 'manage_posts_custom_column', array( &$this, 'manage_posts_custom_column' ), 10, 2 );
		add_action( 'quick_edit_custom_box', array( &$this, 'quick_edit_custom_box' ), 10, 2 );
		add_action( 'save_post', array( &$this, 'save_post' ), 25 );
		add_action( 'wp_ajax_save_post_bulk_edit', array( 'Custom_Bulk_Quick_Edit', 'save_post_bulk_edit' ) );
		add_filter( 'manage_' . self::ID . '_posts_columns', array( &$this, 'manage_edit_columns' ) );
		add_filter( 'manage_post_posts_columns', array( &$this, 'manage_edit_columns' ) );
		add_filter( 'plugin_action_links', array( &$this, 'plugin_action_links' ), 10, 2 );
		add_filter( 'plugin_row_meta', array( &$this, 'plugin_row_meta' ), 10, 2 );
		self::styles();
		self::scripts();
	}


	public function init() {
		self::$donate_button = <<<EOD
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="WM4F995W9LHXE">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
EOD;

		self::$base = plugin_basename( __FILE__ );
	}


	public function plugin_action_links( $links, $file ) {
		if ( $file == self::$base )
			array_unshift( $links, self::$settings_link );

		return $links;
	}


	public function activation() {
		if ( ! current_user_can( 'activate_plugins' ) )
			return;
	}


	public function deactivation() {
		if ( ! current_user_can( 'activate_plugins' ) )
			return;
	}


	public function uninstall() {
		if ( ! current_user_can( 'activate_plugins' ) )
			return;

		global $wpdb;

		require_once 'lib/class-custom-bulk-quick-edit-settings.php';
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
		);
		// '<a href="http://aihr.us/downloads/custom-bulk-quick-edit-premium-wordpress-plugin/">Purchase Custom Bulk/Quick Edit Premium</a>',

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
			case 'post_excerpt':
			$post   = get_post( $post_id );
			$result = $post->post_excerpt;
			break;

			case 'custom-bulk-quick-edit-title':
			$result = get_post_meta( $post_id, $column, true );
			break;
		}

		$result = apply_filters( 'custom_bulk_quick_edit_posts_custom_column', $result, $column, $post_id );

		if ( $result )
			echo $result;
	}


	public function manage_edit_columns( $columns ) {
		// order of keys matches column ordering
		global $post;
		$post_type        = $post->post_type;
		$supports_excerpt = cbqe_get_option( $post_type . '_enable_post_excerpt' );
		if ( $supports_excerpt )
			$columns['post_excerpt'] = __( 'Excerpt', 'custom-bulk-quick-edit' );

		$columns = apply_filters( 'custom_bulk_quick_edit_columns', $columns );

		return $columns;
	}


	public static function scripts() {
		wp_enqueue_script( 'jquery' );
	}


	public static function styles() {
		wp_register_style( 'custom-bulk-quick-edit', plugins_url( 'custom-bulk-quick-edit.css', __FILE__ ) );
		wp_enqueue_style( 'custom-bulk-quick-edit' );
	}


	public static function get_scripts() {
		if ( empty( self::$scripts_called ) ) {
			echo '
				<script type="text/javascript">
jQuery(document).ready(function($) {
	var wp_inline_edit = inlineEditPost.edit;
	inlineEditPost.edit = function( id ) {
		wp_inline_edit.apply( this, arguments );
		var post_id = 0;
		if ( typeof( id ) == "object" )
			post_id = parseInt( this.getId( id ) );

		if ( post_id > 0 ) {
			// define the edit row
			var edit_row = $( "#edit-" + post_id );
			var post_row = $( "#post-" + post_id );
			';

			echo implode( "\n", self::$scripts_quick );

			echo '
		}
	};

	$( "#bulk_edit" ).live( "click", function() {
		var bulk_row = $( "#bulk-edit" );

		var post_ids = new Array();
		bulk_row.find( "#bulk-titles" ).children().each( function() {
			post_ids.push( $( this ).attr( "id" ).replace( /^(ttle)/i, "" ) );
		});

		$.ajax({
			url: ajaxurl,
			type: "POST",
			async: false,
			cache: false,
			data: {
				action: "save_post_bulk_edit",
				post_ids: post_ids,
			';

			echo implode( ",\n", self::$scripts_bulk );

			echo '
			}
		});
	});
});
</script>
			';

			self::$scripts_called = true;
		}
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.Superglobals)
	 * @SuppressWarnings(PHPMD.ExitExpression)
	 */
	public function save_post_bulk_edit() {
		$post_ids = ! empty( $_POST[ 'post_ids' ] ) ? $_POST[ 'post_ids' ] : array();
		if ( ! empty( $post_ids ) && is_array( $post_ids ) ) {
			foreach ( $post_ids as $post_id ) {
				self::save_post_items( $post_id );
			}
		}

		die();
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public function save_post_items( $post_id ) {
		if ( ! preg_match( '#^\d+$#', $post_id ) )
			return;

		foreach ( $_POST as $field => $value ) {
			if ( false === strpos( $field, self::$field_key ) )
				continue;

			if ( empty( $value ) && 0 != $value )
				continue;

			$field_name = str_replace( self::$field_key, '', $field );
			if ( ! in_array( $field_name, array( 'post_excerpt' ) ) ) {
				update_post_meta( $post_id, $field_name, wp_kses_post( $value ) );
			} else {
				$data = array(
					'ID' => $post_id,
					$field_name => wp_kses_post( $value ),
				);
				wp_update_post( $data );
			}
		}
	}


	public static function get_post_types() {
		if ( ! empty( self::$post_types ) )
			return self::$post_types;

		$args = array(
			'public' => true,
			'_builtin' => true, // no custom post types
		);

		$post_types = get_post_types( $args, 'objects' );
		foreach ( $post_types as $post_type ) {
			if ( in_array( $post_type->name, self::$post_types_ignore ) )
				continue;

			self::$post_types[ $post_type->name ] = $post_type->label;
			self::$post_types_keys[]              = $post_type->name;
		}

		self::$post_types = apply_filters( 'custom_bulk_quick_edit_post_types', self::$post_types );

		return self::$post_types;
	}


	public function quick_edit_custom_box( $column_name, $post_type ) {
		if ( ! in_array( $post_type, self::$post_types_keys ) )
			return;

		$key    = $post_type . '_enable_' . $column_name;
		$enable = cbqe_get_option( $key );
		if ( ! $enable )
			return;

		if ( self::$no_instance ) {
			self::$no_instance = false;
			wp_nonce_field( plugin_basename( __FILE__ ), self::ID );
		}

		// TODO dynamically generate this
		$field_name = self::$field_key . $column_name;
?>
	<fieldset class="inline-edit-col-right inline-edit-video">
	  <div class="inline-edit-col inline-edit-<?php echo $column_name ?>">
		<label class="inline-edit-group">
			<span class="title"><?php echo $column_name ?></span>
			<textarea cols="22" rows="1" name="<?php echo $field_name ?>" autocomplete="off"></textarea>
		</label>
	  </div>
	</fieldset>
<?php

		self::$scripts_bulk[ $column_name ]        = $field_name .': bulk_row.find( \'textarea[name="' . $field_name . '"]\' ).val()';
		self::$scripts_quick[ $column_name . '1' ] = 'var ' . $field_name . ' = $( \'.column-' . $column_name . '\', post_row ).html();';
		self::$scripts_quick[ $column_name . '2' ] = '$( \':input[name="' . $field_name . '"]\', edit_row ).val( ' . $field_name . ' );';
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public function save_post( $post_id ) {
		$post_type = get_post_type( $post_id );

		if ( ! in_array( $post_type, self::$post_types_keys ) )
			return;

		if ( ! current_user_can( 'edit_post', $post_id ) )
			return;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		if ( 'revision' == $post_type )
			return;

		if ( isset( $_POST[ self::ID ] ) && ! wp_verify_nonce( $_POST[ self::ID ], plugin_basename( __FILE__ ) ) )
			return;

		remove_action( 'save_post', array( &$this, 'save_post' ), 25 );
		self::save_post_items( $post_id );
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public function admin_footer() {
		if ( self::$no_instance )
			return;

		if ( empty( $_GET['post_type'] ) ) {
			global $post;
			$_GET['post_type'] = ! empty( $post->post_type ) ? $post->post_type : false;
		}

		if ( in_array( $_GET['post_type'], self::$post_types_keys ) )
			self::get_scripts();
	}


}


register_activation_hook( __FILE__, array( 'Custom_Bulk_Quick_Edit', 'activation' ) );
register_deactivation_hook( __FILE__, array( 'Custom_Bulk_Quick_Edit', 'deactivation' ) );
register_uninstall_hook( __FILE__, array( 'Custom_Bulk_Quick_Edit', 'uninstall' ) );


add_action( 'plugins_loaded', 'custom_bulk_quick_edit_init', 199 );


/**
 *
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
function custom_bulk_quick_edit_init() {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	if ( is_plugin_active( Custom_Bulk_Quick_Edit::PLUGIN_FILE ) ) {
		require_once 'lib/class-custom-bulk-quick-edit-settings.php';

		$Custom_Bulk_Quick_Edit          = new Custom_Bulk_Quick_Edit();
		$Custom_Bulk_Quick_Edit_Settings = new Custom_Bulk_Quick_Edit_Settings();
	}

}


?>
