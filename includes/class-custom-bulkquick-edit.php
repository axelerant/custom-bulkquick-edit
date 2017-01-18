<?php
/**
 * Copyright 2015 Axelerant
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

require_once AIHR_DIR_INC . 'class-aihrus-common.php';
require_once CBQE_DIR_INC . 'class-custom-bulkquick-edit-settings.php';

if ( class_exists( 'Custom_Bulkquick_Edit' ) ) {
	return;
}


class Custom_Bulkquick_Edit extends Aihrus_Common {
	const BASE    = CBQE_BASE;
	const ID      = 'custom-bulkquick-edit';
	const SLUG    = 'cbqe_';
	const VERSION = CBQE_VERSION;

	private static $fields_enabled     = array();
	private static $no_instance        = true;
	private static $post_fields_ignore = array(
		'ID',
		'comment_count',
		'comment_status',
		'guid',
		'menu_order',
		'ping_status',
		'pinged',
		'post_author',
		'post_category',
		'post_content',
		'post_content_filtered',
		'post_date',
		'post_date_gmt',
		'post_excerpt',
		'post_mime_type',
		'post_modified',
		'post_modified_gmt',
		'post_name',
		'post_parent',
		'post_password',
		'post_status',
		'post_title',
		'post_type',
		'to_ping',
	);
	private static $post_types_ignore  = array(
		'attachment',
	);

	public static $bulk_edit_save = false;
	public static $class          = __CLASS__;
	public static $notice_key;
	public static $plugin_assets;
	public static $post_types      = array();
	public static $post_types_keys = array();
	public static $scripts_bulk    = array();
	public static $scripts_extra   = array();
	public static $scripts_quick   = array();
	public static $settings_link   = '';


	public function __construct() {
		parent::__construct();

		self::$plugin_assets = plugins_url( '/assets/', dirname( __FILE__ ) );
		self::$plugin_assets = self::strip_protocol( self::$plugin_assets );

		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
		add_action( 'init', array( __CLASS__, 'init' ) );
	}


	public static function admin_init() {
		self::$settings_link = '<a href="' . get_admin_url() . 'options-general.php?page=' . Custom_Bulkquick_Edit_Settings::ID . '">' . esc_html__( 'Settings', 'custom-bulkquick-edit' ) . '</a>';

		self::update();

		add_action( 'admin_footer', array( __CLASS__, 'admin_footer' ) );
		add_action( 'bulk_edit_custom_box', array( __CLASS__, 'bulk_edit_custom_box' ), 10, 2 );
		add_action( 'quick_edit_custom_box', array( __CLASS__, 'quick_edit_custom_box' ), 10, 2 );
		add_action( 'save_post', array( __CLASS__, 'save_post' ), 25 );
		add_action( 'wp_ajax_save_post_bulk_edit', array( __CLASS__, 'save_post_bulk_edit' ) );
		add_filter( 'plugin_action_links', array( __CLASS__, 'plugin_action_links' ), 10, 2 );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
		add_filter( 'pre_update_option_active_plugins', array( __CLASS__, 'pre_update_option_active_plugins' ), 10, 2 );

		if ( self::do_load() ) {
			self::styles();
		}
	}


	public static function init() {
		load_plugin_textdomain( self::ID, false, 'custom-bulkquick-edit/languages' );
	}


	public static function plugin_action_links( $links, $file ) {
		if ( self::BASE == $file ) {
			array_unshift( $links, self::$settings_link );
		}

		return $links;
	}


	public static function uninstall() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		global $wpdb;

		require_once CBQE_DIR_INC . 'class-custom-bulkquick-edit-settings.php';

		$delete_data = cbqe_get_option( 'delete_data', false );
		if ( $delete_data ) {
			delete_option( Custom_Bulkquick_Edit_Settings::ID );
			$wpdb->query( 'OPTIMIZE TABLE `' . $wpdb->options . '`' );
		}
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.LongVariable)
	 */
	public static function plugin_row_meta( $input, $file ) {
		if ( self::BASE != $file ) {
			return $input;
		}

		$disable_donate = cbqe_get_option( 'disable_donate' );
		if ( $disable_donate ) {
			return $input;
		}

		$links = array(
			self::$donate_link,
		);

		global $Custom_Bulkquick_Edit_Premium;
		if ( ! isset( $Custom_Bulkquick_Edit_Premium ) ) {
			$links[] = CBQE_PREMIUM_LINK;
		}

		$input = array_merge( $input, $links );

		return $input;
	}


	public static function notice_0_0_1() {
		$text = sprintf( __( 'If your Custom Bulk/Quick Edit display has gone to funky town, please <a href="%s">read the FAQ</a> about possible CSS fixes.', 'custom-bulkquick-edit' ), 'https://axelerant.atlassian.net/wiki/display/WPFAQ/Major+Changes+Since+2.10.0' );

		aihr_notice_updated( $text );
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public static function notice_donate( $disable_donate = null, $item_name = null ) {
		$disable_donate = cbqe_get_option( 'disable_donate' );

		parent::notice_donate( $disable_donate, CBQE_NAME );
	}


	public static function update() {
		$prior_version = cbqe_get_option( 'admin_notices' );
		if ( $prior_version ) {
			if ( $prior_version < '0.0.1' ) {
				add_action( 'admin_notices', array( __CLASS__, 'notice_0_0_1' ) );
			}

			if ( $prior_version < self::VERSION ) {
				cbqe_requirements_check( true );
				do_action( 'custom_bulkquick_edit_update' );
			}

			cbqe_set_option( 'admin_notices' );
		}

		// display donate on major/minor version release
		$donate_version = cbqe_get_option( 'donate_version', false );
		if ( ! $donate_version || ( self::VERSION != $donate_version && preg_match( '#\.0$#', self::VERSION ) ) ) {
			self::set_notice( 'notice_donate' );
			cbqe_set_option( 'donate_version', self::VERSION );
		}
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public static function manage_posts_custom_column_precapture( $column, $post_id ) {
		ob_start();
	}


	public static function manage_posts_custom_column_capture( $column, $post_id ) {
		$buffer = ob_get_contents();
		ob_end_clean();
		self::manage_posts_custom_column( $column, $post_id, $buffer );
	}


	public static function manage_posts_custom_column( $column, $post_id, $buffer = null ) {
		global $post;

		$ignore_custom_column = apply_filters( 'cbqe_ignore_posts_custom_column', array(), $column, $post_id );
		if ( in_array( $column, $ignore_custom_column ) ) {
			echo $buffer;
			return;
		}

		$field_type = self::is_field_enabled( $post->post_type, $column );
		if ( ! $field_type ) {
			echo $buffer;
			return;
		}

		if ( 1 == $field_type ) {
			$field_type = self::check_field_type( $field_type, $column );
		}

		$details = self::get_field_config( $post->post_type, $column );
		$options = explode( "\n", $details );

		$result = '';
		switch ( $column ) {
			case 'post_excerpt':
				$result = $post->post_excerpt;
				break;

			case 'post_title':
				$result = $post->post_title;
				break;

			default:
				$save_as = self::get_field_save_as( $post->post_type, $column );
				$current = get_post_meta( $post_id, $column, true );
				if ( 'post_meta' == $save_as ) {
					$current = get_post_meta( $post_id, $column );
				} elseif ( 'csv' == $save_as ) {
					$current = explode( ',', $current );
				}

				switch ( $field_type ) {
					case 'categories':
						$result = self::column_categories( $post_id, $column, $current, $options, $field_type );
						break;

					case 'show_only':
					case 'taxonomy':
						$result = self::column_taxonomies( $post_id, $column, $current, $options, $field_type );
						break;

					case 'input':
					case 'textarea':
						$result = $current;
						if ( is_numeric( $result ) && 0 == $result && 1 == strlen( $result ) ) {
							$result = '&Oslash;';
						}
						break;

					case 'checkbox':
					case 'radio':
						$result = self::column_checkbox_radio( $column, $current, $options, $field_type );
						break;

					case 'select':
						$result = self::column_select( $column, $current, $options, $field_type );
						break;

					default:
						$result = apply_filters( 'cbqe_manage_posts_custom_column_field_type', $current, $field_type, $column, $post_id );
						break;
				}
		}

		$result = apply_filters( 'cbqe_posts_custom_column', $result, $column, $post_id );

		if ( $result ) {
			echo $result;
		}
	}


	public static function manage_columns( $columns ) {
		global $post;

		if ( is_null( $post ) ) {
			return $columns;
		}

		$fields = self::get_enabled_fields( $post->post_type );
		foreach ( $fields as $key => $field_name ) {
			$title                  = Custom_Bulkquick_Edit_Settings::$settings[ $key ]['label'];
			$columns[ $field_name ] = $title;
		}

		$columns = apply_filters( 'cbqe_columns', $columns );

		return $columns;
	}


	public static function get_enabled_fields( $post_type ) {
		$fields   = array();
		$settings = Custom_Bulkquick_Edit_Settings::$settings;
		foreach ( $settings as $key => $value ) {
			if ( $post_type != $value['section'] ) {
				continue;
			}

			if ( false !== strstr( $key, Custom_Bulkquick_Edit_Settings::CONFIG ) ) {
				continue;
			}

			if ( false !== strstr( $key, Custom_Bulkquick_Edit_Settings::SAVE ) ) {
				continue;
			}

			$field_name = str_replace( $post_type . Custom_Bulkquick_Edit_Settings::ENABLE, '', $key );
			$field_type = self::is_field_enabled( $post_type, $field_name );
			if ( $field_type ) {
				$fields[ $key ] = $field_name;
			}
		}

		return $fields;
	}


	public static function get_scripts() {
		if ( self::$scripts_called ) {
			return;
		}

		echo '
			<script type="text/javascript">
jQuery( document ).ready( function() {
	var wp_inline_edit = inlineEditPost.edit;
	inlineEditPost.edit = function( id ) {
		wp_inline_edit.apply( this, arguments );
		var post_id = 0;
		if ( typeof( id ) == "object" )
			post_id = parseInt( this.getId( id ) );

		if ( post_id > 0 ) {
			// define the edit row
			var edit_row = jQuery( "#edit-" + post_id );
			var post_row = jQuery( "#post-" + post_id );
			';

		$scripts = implode( "\n", self::$scripts_quick );
		echo $scripts;

		echo '
		}
	};

	';

		$scripts = implode( "\n", self::$scripts_extra );
		echo $scripts;

		echo '

	jQuery( "#bulk_edit" ).on( "click", function() {
		var bulk_row = jQuery( "#bulk-edit" );
		var post_ids = new Array();
		bulk_row.find( "#bulk-titles" ).children().each( function() {
			post_ids.push( jQuery( this ).attr( "id" ).replace( /^(ttle)/i, "" ) );
		});

		jQuery.ajax({
			url: ajaxurl,
			type: "POST",
			async: false,
			cache: false,
			data: {
				action: "save_post_bulk_edit",
				post_ids: post_ids,
			';

		$scripts = implode( ",\n", self::$scripts_bulk );
		echo $scripts;

		echo '
			}
		});
	});
});
</script>
			';

		self::$scripts_called = true;
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.Superglobals)
	 * @SuppressWarnings(PHPMD.ExitExpression)
	 */
	public static function save_post_bulk_edit() {
		self::$bulk_edit_save = true;

		$post_ids = ! empty( $_POST['post_ids'] ) ? $_POST['post_ids'] : array();
		if ( ! empty( $post_ids ) && is_array( $post_ids ) ) {
			remove_action( 'save_post', array( __CLASS__, 'save_post' ), 25 );

			foreach ( $post_ids as $post_id ) {
				self::save_post_items( $post_id, 'bulk_edit' );
			}
		}

		die();
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public static function save_post_items( $post_id, $mode = '' ) {
		if ( ! preg_match( '#^\d+$#', $post_id ) ) {
			return;
		}

		unset( $_POST['action'] );
		unset( $_POST['post_ids'] );

		if ( empty( $_POST ) ) {
			return;
		}

		$post      = get_post( $post_id );
		$post_type = $post->post_type;

		if ( empty( $mode ) ) {
			// unset unchecked checkboxes from quick edit
			$fields = self::get_enabled_fields( $post_type );
			foreach ( $fields as $key => $field ) {
				$field_type = self::is_field_enabled( $post_type, $field );
				if ( self::is_field_checkbox( $field_type ) ) {
					$field_name = self::SLUG . $field;
					if ( ! isset( $_POST[ $field_name ] ) ) {
						$_POST[ $field_name ] = Custom_Bulkquick_Edit_Settings::RESET;
					}
				}
			}
		}

		foreach ( $_POST as $field => $value ) {
			if ( in_array( $field, self::$post_fields_ignore ) ) {
				continue;
			} elseif ( '' == $value && 'bulk_edit' == $mode ) {
				continue;
			}

			if ( 'tax_input' != $field ) {
				self::save_post_item( $post_id, $post_type, $field, $value );
			} else {
				foreach ( $value as $key => $val ) {
					self::save_post_item( $post_id, $post_type, $key, $val );
				}
			}
		}

		do_action( 'cbqe_save_post', $post_id );
	}


	/**
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public static function save_post_item( $post_id, $post_type, $field, $value ) {
		$field_name = str_replace( self::SLUG, '', $field );
		$field_type = self::is_field_enabled( $post_type, $field_name );
		if ( empty( $field_type ) ) {
			return;
		}

		if ( false !== strstr( $field_name, Custom_Bulkquick_Edit_Settings::REMOVE ) ) {
			$terms = array_map( 'intval', $value );

			$taxonomy = str_replace( Custom_Bulkquick_Edit_Settings::REMOVE, '', $field_name );
			wp_remove_object_terms( $post_id, $terms, $taxonomy );

			return;
		}

		if ( false !== strstr( $field_name, Custom_Bulkquick_Edit_Settings::RESET ) ) {
			$taxonomy = str_replace( Custom_Bulkquick_Edit_Settings::RESET, '', $field_name );
			wp_delete_object_term_relationships( $post_id, $taxonomy );

			return;
		}

		if ( in_array( $field_type, array( 'categories', 'taxonomy' ) ) ) {
			$value = stripslashes_deep( $value );
			if ( 'taxonomy' == $field_type ) {
				// WordPress doesn't keep " enclosed CSV terms together, so don't worry about it here then by using `str_getcsv`
				$values = explode( ',', $value );
				wp_set_object_terms( $post_id, $values, $field_name );
			} else {
				$value = array_map( 'intval', $value );
				$value = array_unique( $value );
				if ( isset( $value[0] ) && 0 === $value[0] ) {
					unset( $value[0] );
				}

				wp_set_object_terms( $post_id, $value, $field_name );
			}

			return;
		}

		if ( ! is_array( $value ) ) {
			$delete = strstr( $value, Custom_Bulkquick_Edit_Settings::DELETE );
		} else {
			$delete = in_array( Custom_Bulkquick_Edit_Settings::DELETE, $value );
		}

		if ( false !== strstr( $field_name, Custom_Bulkquick_Edit_Settings::DELETE ) ) {
			$field_name = str_replace( Custom_Bulkquick_Edit_Settings::DELETE, '', $field_name );
			$delete     = true;
		}

		if ( Custom_Bulkquick_Edit_Settings::RESET == $value ) {
			$delete = true;
		}

		$post_save_fields = apply_filters( 'cbqe_post_save_fields', self::$post_fields_ignore );
		if ( ! in_array( $field_name, $post_save_fields ) ) {
			if ( ! $delete ) {
				$save_as = self::get_field_save_as( $post_type, $field_name );
				$updated = false;
				if ( 'post_meta' == $save_as ) {
					delete_post_meta( $post_id, $field_name );
					foreach ( $value as $key => $val ) {
						add_post_meta( $post_id, $field_name, $val );
					}

					$updated = true;
				} elseif ( 'csv' == $save_as && is_array( $value ) ) {
					$value = implode( ',', $value );
				}

				if ( ! $updated ) {
					update_post_meta( $post_id, $field_name, $value );
				}
			} else {
				delete_post_meta( $post_id, $field_name );
			}
		} else {
			if ( $delete ) {
				$value = '';
			}

			$value = apply_filters( 'cbqe_post_save_value', $value, $post_id, $field_name );
			if ( is_null( $value ) ) {
				return;
			}

			$data = array(
				'ID' => $post_id,
				$field_name => $value,
			);
			wp_update_post( $data );
		}
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public static function get_post_types() {
		if ( ! empty( self::$post_types ) ) {
			return self::$post_types;
		}

		if ( isset( $_GET['post_type'] ) ) {
			$post_type                      = esc_attr( $_GET['post_type'] );
			self::$post_types[ $post_type ] = $post_type;
			self::$post_types_keys[]        = $post_type;

			return self::$post_types;
		} else {
			self::$post_types_ignore = apply_filters( 'cbqe_post_types_ignore', self::$post_types_ignore );

			$args = array(
				'public' => true,
				'_builtin' => true,
			);

			$args = apply_filters( 'cbqe_get_post_types_args', $args );

			$post_types = get_post_types( $args, 'objects' );
			foreach ( $post_types as $post_type ) {
				if ( in_array( $post_type->name, self::$post_types_ignore ) ) {
					continue;
				}

				self::$post_types[ $post_type->name ] = $post_type->label;
				self::$post_types_keys[]              = $post_type->name;
			}

			return self::$post_types;
		}
	}


	public static function get_field_config( $post_type = null, $field_name = null ) {
		$key = self::get_field_key( $post_type, $field_name );

		if ( empty( $key ) ) {
			return false;
		}

		$key    .= Custom_Bulkquick_Edit_Settings::CONFIG;
		$details = cbqe_get_option( $key );

		return $details;
	}


	public static function get_field_key( $post_type = null, $field_name = null ) {
		if ( is_null( $post_type ) ) {
			global $post;

			if ( is_null( $post ) ) {
				return false;
			}

			$post_type = $post->post_type;
		}

		if ( false !== strstr( $field_name, self::SLUG ) ) {
			$field_name = preg_replace( '#^' . self::SLUG . '#', '', $field_name );
		}

		$key = $post_type . Custom_Bulkquick_Edit_Settings::ENABLE . $field_name;

		return $key;
	}


	public static function is_field_enabled( $post_type = null, $field_name = null ) {
		if ( is_null( $field_name ) ) {
			return false;
		}

		if ( is_null( $post_type ) ) {
			global $post;

			if ( is_null( $post ) ) {
				return false;
			}

			$post_type = $post->post_type;
		}

		$key = self::get_field_key( $post_type, $field_name );
		if ( isset( self::$fields_enabled[ $key ] ) ) {
			return self::$fields_enabled[ $key ];
		}

		$enable = cbqe_get_option( $key );
		if ( ! empty( $enable )  ) {
			self::$fields_enabled[ $key ] = $enable;

			return $enable;
		} else {
			self::$fields_enabled[ $key ] = false;

			return false;
		}
	}


	public static function bulk_edit_custom_box( $column_name, $post_type ) {
		self::quick_edit_custom_box( $column_name, $post_type, true );
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public static function quick_edit_custom_box( $column_name, $post_type, $bulk_mode = false ) {
		if ( ! in_array( $post_type, self::$post_types_keys ) ) {
			return;
		}

		$column_name = apply_filters( 'cbqe_quick_edit_custom_box_column', $column_name, $post_type, $bulk_mode );

		$field_type = self::is_field_enabled( $post_type, $column_name );
		if ( empty( $field_type ) ) {
			return;
		}

		if ( 'show_only' == $field_type ) {
			return;
		}

		if ( self::$no_instance ) {
			self::$no_instance = false;
			wp_nonce_field( self::BASE, self::ID );
		}

		$key        = self::get_field_key( $post_type, $column_name );
		$field_name = self::SLUG . $column_name;

		$open_fieldset  = '<fieldset class="inline-edit-col-%1$s cbqe">';
		$close_fieldset = '</fieldset>';

		$open_div  = '<div class="inline-edit-col">';
		$close_div = '</div>';

		if ( $bulk_mode ) {
			$return_now = false;

			$ignore_bulk_edit = apply_filters( 'cbqe_ignore_bulk_edit', array() );
			if ( in_array( $column_name, $ignore_bulk_edit ) ) {
				return;
			}

			$setting = $post_type . Custom_Bulkquick_Edit_Settings::ENABLE . $column_name;
			$enable  = cbqe_get_option( $setting );
			if ( empty( $enable ) ) {
				return;
			}

			$result = '';
			$row    = 1;

			$valid_delete = strstr( $setting, Custom_Bulkquick_Edit_Settings::DELETE );
			if ( $valid_delete ) {
				$orig_field  = preg_replace( '#(^|' . Custom_Bulkquick_Edit_Settings::ENABLE . '|' . Custom_Bulkquick_Edit_Settings::DELETE . ')#', '', $column_name );
				$orig_column = self::SLUG . $orig_field;

				$result .= self::custom_box_reset( $orig_column, $orig_field, $setting, true );

				$return_now = true;
			}

			$valid_reset = strstr( $setting, Custom_Bulkquick_Edit_Settings::RESET );
			if ( $valid_reset ) {
				$orig_field  = preg_replace( '#(^|' . Custom_Bulkquick_Edit_Settings::ENABLE . '|' . Custom_Bulkquick_Edit_Settings::RESET . ')#', '', $column_name );
				$orig_column = self::SLUG . $orig_field;

				$result .= self::custom_box_reset( $orig_column, $orig_field, $setting );

				$return_now = true;
			}

			$valid_remove = strstr( $setting, Custom_Bulkquick_Edit_Settings::REMOVE );
			if ( $valid_remove ) {
				$field = preg_replace( '#(^' . $post_type . '|' . Custom_Bulkquick_Edit_Settings::ENABLE . ')#', '', $setting );

				$result = self::custom_box_remove( $field, $setting );
				$row++;

				$return_now = true;
			}

			if ( ! empty( $result ) ) {
				echo sprintf( $open_fieldset, 'right' );
				echo $open_div;
				echo '<div class="inline-edit-group">';
				echo $result;
				echo $close_div;
				echo $close_div;
				echo $close_fieldset;
			}

			// return now otherwise taxonomy entries are duplicated
			if ( $return_now || in_array( $field_type, array( 'categories', 'taxonomy' ) ) ) {
				$bulk_edit_taxonomy = apply_filters( 'cbqe_bulk_edit_taxonomy', array() );
				if ( ! in_array( $column_name, $bulk_edit_taxonomy ) ) {
					return;
				}
			}
		} else {
			if ( false !== strstr( $key, Custom_Bulkquick_Edit_Settings::DELETE ) ) {
				return;
			}

			if ( false !== strstr( $key, Custom_Bulkquick_Edit_Settings::RESET ) ) {
				return;
			}

			if ( false !== strstr( $key, Custom_Bulkquick_Edit_Settings::REMOVE ) ) {
				return;
			}

			$ignore_quick_edit = array( 'post_title', 'post_parent' );
			$ignore_quick_edit = apply_filters( 'cbqe_ignore_quick_edit', $ignore_quick_edit );
			if ( in_array( $column_name, $ignore_quick_edit ) ) {
				return;
			}
		}

		$field_type     = self::check_field_type( $field_type, $column_name );
		$field_name_var = str_replace( '-', '_', $field_name );
		$title          = Custom_Bulkquick_Edit_Settings::$settings[ $key ]['label'];

		echo sprintf( $open_fieldset, 'left' );
		echo $open_div;

		$class = '';
		if ( 'categories' == $field_type ) {
			$class = 'inline-edit-categories-label';
		} elseif ( 'taxonomy' != $field_type ) {
			echo '<label class="alignleft">';
		} else {
			echo '<label class="inline-edit-tags">';
		}

		echo '<span class="title ' . $class . '">' . $title . '</span>';

		$details = self::get_field_config( $post_type, $column_name );
		$options = explode( "\n", $details );

		switch ( $field_type ) {
			case 'checkbox':
				if ( ! $bulk_mode ) {
					$result = self::custom_box_checkbox( $column_name, $field_name, $field_name_var, $options );
				} else {
					$result = self::custom_box_select_multiple( $column_name, $field_name, $field_name_var, $options, $bulk_mode );
				}
				break;

			case 'radio':
				if ( ! $bulk_mode ) {
					$result = self::custom_box_radio( $column_name, $field_name, $field_name_var, $options );
				} else {
					$result = self::custom_box_select( $column_name, $field_name, $field_name_var, $options, $bulk_mode );
				}

				break;

			case 'input':
				$result = self::custom_box_input( $column_name, $field_name, $field_name_var );
				break;

			case 'select':
				$result = self::custom_box_select( $column_name, $field_name, $field_name_var, $options, $bulk_mode );
				break;

			case 'textarea':
				$result = self::custom_box_textarea( $column_name, $field_name, $field_name_var );
				break;

			case 'categories':
				$result = self::custom_box_categories( $field_name, $bulk_mode );
				break;

			case 'taxonomy':
				$warning = esc_html__( 'Use commas to separate your values. Ex: "1,two,Three"', 'custom-bulkquick-edit' );
				echo '<span class="warning">' . $warning . '</span>';

				$result = self::custom_box_taxonomy( $column_name, $field_name, $field_name_var );
				break;

			default:
				$result = apply_filters( 'cbqe_quick_edit_custom_box_field', '', $field_type, $field_name, $options, $bulk_mode );

				self::$scripts_bulk  = apply_filters( 'cbqe_quick_scripts_bulk', self::$scripts_bulk, $post_type, $column_name, $field_name, $field_type, $field_name_var );
				self::$scripts_quick = apply_filters( 'cbqe_quick_scripts_quick', self::$scripts_quick, $post_type, $column_name, $field_name, $field_type, $field_name_var );
				self::$scripts_extra = apply_filters( 'cbqe_quick_scripts_extra', self::$scripts_extra, $post_type, $column_name, $field_name, $field_type, $field_name_var );
				break;
		}

		echo $result;
		echo '</label>';
		echo $close_div;
		echo $close_fieldset;
	}


	public static function custom_box_checkbox( $column_name, $field_name, $field_name_var, $options ) {
		$result = '<div class="inline-edit-group cbqe">';

		$multiple     = '';
		$do_pre_title = true;
		if ( 1 < count( $options ) ) {
			$multiple = '[]';
		} else {
			$do_pre_title = false;
		}

		foreach ( $options as $option ) {
			if ( $do_pre_title ) {
				$result .= '<label class="alignleft">';
			}

			$parts = explode( '|', $option );
			$value = array_shift( $parts );
			if ( empty( $parts ) ) {
				$name = $value;
			} else {
				$name = array_shift( $parts );
			}

			$result .= '<input type="checkbox" name="' . $field_name . $multiple . '" value="' . $value . '" />';
			$result .= ' ' . $name . '&nbsp;';

			if ( $do_pre_title ) {
				$result .= '</label>';
			}
		}

		$result .= '</div>';

		self::$scripts_quick[ $column_name . '1' ] = "var {$field_name_var} = jQuery( '.column-{$column_name} input:checkbox:checked', post_row ).map( function(){ return jQuery( this ).val(); } ).get();";
		self::$scripts_quick[ $column_name . '2' ] = "jQuery.each( {$field_name_var}, function( key, value ){ jQuery( ':input[name^={$field_name}]', edit_row ).filter('[value=\"' + value + '\"]').prop('checked', true); } );";

		return $result;
	}


	public static function custom_box_radio( $column_name, $field_name, $field_name_var, $options ) {
		$result = '<div class="inline-edit-group cbqe">';

		$unset_option = Custom_Bulkquick_Edit_Settings::RESET . '|' . esc_html__( '&mdash; Unset &mdash;', 'custom-bulkquick-edit' );
		array_unshift( $options, $unset_option );

		foreach ( $options as $option ) {
			$result .= '<label class="alignleft">';

			$parts = explode( '|', $option );
			$value = array_shift( $parts );
			if ( empty( $parts ) ) {
				$name = $value;
			} else {
				$name = array_shift( $parts );
			}

			$result .= '<input type="radio" name="' . $field_name . '" value="' . $value . '" />';
			$result .= ' ' . $name . '&nbsp;';
			$result .= '</label>';
		}

		$result .= '</div>';

		self::$scripts_quick[ $column_name . '1' ] = "var {$field_name_var} = jQuery( '.column-{$column_name} input:radio:checked', post_row ).val();";
		self::$scripts_quick[ $column_name . '2' ] = "jQuery( ':input[name={$field_name}]', edit_row ).filter('[value=' + {$field_name_var} + ']').prop('checked', true);";

		return $result;
	}


	public static function custom_box_select_multiple( $column_name, $field_name, $field_name_var, $options, $bulk_mode ) {
		$result = self::custom_box_select( $column_name, $field_name, $field_name_var, $options, $bulk_mode, true );

		return $result;
	}


	public static function custom_box_select( $column_name, $field_name, $field_name_var, $options, $bulk_mode = false, $multiple = false, $bypass_no_change = false ) {
		$result = '<select name="' . $field_name;
		if ( $multiple ) {
			if ( ! $bulk_mode ) {
				$result .= '[]';
			}

			$result .= '" multiple="multiple';
		}

		$result .= '">';

		if ( empty( $bypass_no_change ) ) {
			$unset_option = Custom_Bulkquick_Edit_Settings::RESET . '|' . esc_html__( '&mdash; Unset &mdash;', 'custom-bulkquick-edit' );
			array_unshift( $options, $unset_option );

			if ( $bulk_mode && ! $multiple ) {
				$no_change_option = '|' . esc_html__( '&mdash; No Change &mdash;', 'custom-bulkquick-edit' );
				array_unshift( $options, $no_change_option );
			}
		}

		foreach ( $options as $option ) {
			$parts = explode( '|', $option );
			$value = array_shift( $parts );
			if ( empty( $parts ) ) {
				$name = $value;
			} else {
				$name = array_shift( $parts );
			}

			$result .= '<option value="' . $value . '">' . $name . '</option>';
		}
		$result .= '</select>';

		if ( ! $bulk_mode ) {
			self::$scripts_quick[ $column_name . '1' ] = "var {$field_name_var} = jQuery( '.column-{$column_name} option:selected', post_row ).map( function(){ return jQuery( this ).val(); } ).get();";
			self::$scripts_quick[ $column_name . '2' ] = "jQuery.each( {$field_name_var}, function( key, value ){ jQuery( ':input[name^={$field_name}] option[value=\"' + value + '\"]', edit_row ).prop('selected', true); } );";
		} else {
			self::$scripts_bulk[ $column_name ] = "'{$field_name}': bulk_row.find( 'select[name={$field_name}]' ).val()";
		}

		return $result;
	}


	public static function custom_box_reset( $column_name, $field_name, $key_reset, $delete = false ) {
		if ( empty( $delete ) ) {
			$field_reset  = $field_name . Custom_Bulkquick_Edit_Settings::RESET;
			$column_reset = $column_name . Custom_Bulkquick_Edit_Settings::RESET;
		} else {
			$field_reset  = $field_name . Custom_Bulkquick_Edit_Settings::DELETE;
			$column_reset = $column_name . Custom_Bulkquick_Edit_Settings::DELETE;
		}

		$title_reset = Custom_Bulkquick_Edit_Settings::$settings[ $key_reset ]['label'];

		$result  = '<label>';
		$result .= '<input type="checkbox" name="' . $field_reset . '" />';
		$result .= ' ';
		$result .= '<span class="checkbox-title">' . $title_reset . '</span>';
		$result .= '</label>';

		self::$scripts_bulk[ $column_reset ] = "'{$field_reset}': bulk_row.find( 'input[name^={$field_reset}]:checkbox:checked' ).map( function(){ return jQuery( this ).val(); } ).get()";

		return $result;
	}


	public static function custom_box_taxonomy( $column_name, $field_name, $field_name_var ) {
		$taxonomy  = str_replace( self::SLUG, '', $field_name );
		$tax_class = 'tax_input_' . $taxonomy;
		$result    = '<textarea cols="22" rows="1" name="tax_input[' . $taxonomy . ']" class="' . $tax_class . '" autocomplete="off"></textarea>';

		self::$scripts_bulk[ $column_name ] = "'{$field_name}': bulk_row.find( '.{$tax_class}' ).val()";

		if ( false === strstr( $field_name, '-' ) ) {
			self::$scripts_quick[ $column_name . '1' ] = "var {$field_name_var} = jQuery( '.column-{$column_name}', post_row ).text();";
			self::$scripts_quick[ $column_name . '2' ] = "jQuery( '.{$tax_class}', edit_row ).val( {$field_name_var} );";
		}

		return $result;
	}


	public static function custom_box_categories( $field_name, $bulk_mode = false ) {
		global $post;

		$post_id  = isset( $post->ID ) && empty( $bulk_mode ) ? $post->ID : null;
		$post_id  = null;
		$taxonomy = str_replace( self::SLUG, '', $field_name );

		ob_start();
		wp_terms_checklist( $post_id, array( 'taxonomy' => $taxonomy ) );
		$terms = ob_get_contents();
		ob_end_clean();

		$result     = '';
		$input_name = 'tax_input';
		if ( 'category' != $taxonomy ) {
			$result .= '<input type="hidden" name="' . $input_name . '[' . $taxonomy . '][]" value="0" />';

			self::$scripts_quick[ $field_name . '1' ] = "var {$taxonomy}_terms = jQuery( '#{$taxonomy}_' + post_id ).text();";
			self::$scripts_quick[ $field_name . '2' ] = "if ( {$taxonomy}_terms ) {
				jQuery( 'ul.{$taxonomy}-checklist :checkbox', edit_row ).val( {$taxonomy}_terms.split(',') );
		}
			";
		} else {
			$input_name = 'post_category';
			$result    .= '<input type="hidden" name="' . $input_name . '[]" value="0" />';
		}

		$result .= '<ul class="cat-checklist ' . esc_attr( $taxonomy ) . '-checklist">';
		$result .= $terms;
		$result .= '</ul>';

		return $result;
	}


	public static function custom_box_remove( $field_name, $key_reset = null ) {
		$field_name = str_replace( self::SLUG, '', $field_name );
		$taxonomy   = str_replace( Custom_Bulkquick_Edit_Settings::REMOVE, '', $field_name );

		$title_reset = Custom_Bulkquick_Edit_Settings::$settings[ $key_reset ]['label'];

		$result  = '<label>';
		$result .= '<span class="checkbox-title">' . $title_reset . '</span>';

		ob_start();
		wp_terms_checklist( null, array( 'taxonomy' => $taxonomy ) );
		$terms = ob_get_contents();
		ob_end_clean();

		$terms = preg_replace( "#(post_category|tax_input\[{$taxonomy}\])#", $field_name, $terms );

		$result .= '<input type="hidden" name="' . $field_name . '[]" value="0" />';
		$result .= '<ul class="cat-checklist ' . esc_attr( $field_name ) . '-checklist">';
		$result .= $terms;
		$result .= '</ul>';
		$result .= '</label>';

		self::$scripts_bulk[ $field_name ] = "'{$field_name}': bulk_row.find( 'input[name^={$field_name}]:checked:checked' ).map( function(){ return jQuery( this ).val(); } ).get()";

		return $result;
	}


	public static function custom_box_textarea( $column_name, $field_name, $field_name_var ) {
		$result = '<textarea cols="22" rows="1" name="' . $field_name . '" autocomplete="off"></textarea>';

		self::$scripts_bulk[ $column_name ] = "'{$field_name}': bulk_row.find( 'textarea[name={$field_name}]' ).val()";

		self::$scripts_quick[ $column_name . '1' ] = "var {$field_name_var} = jQuery( '.column-{$column_name}', post_row ).text();";
		self::$scripts_quick[ $column_name . '2' ] = "jQuery( ':input[name={$field_name}]', edit_row ).val( {$field_name_var} );";

		return $result;
	}


	public static function custom_box_input( $column_name, $field_name, $field_name_var ) {
		$result = '<input type="text" name="' . $field_name . '" autocomplete="off" />';

		self::$scripts_bulk[ $column_name ] = "'{$field_name}': bulk_row.find( 'input[name={$field_name}]' ).val()";

		self::$scripts_quick[ $column_name . '1' ] = "var {$field_name_var} = jQuery( '.column-{$column_name}', post_row ).text();";
		self::$scripts_quick[ $column_name . '2' ] = "if ( '&Oslash;' == {$field_name_var} || 'Ø' == {$field_name_var} ) {$field_name_var} = 0;";
		self::$scripts_quick[ $column_name . '3' ] = "jQuery( ':input[name={$field_name}]', edit_row ).val( {$field_name_var} );";

		return $result;
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public static function save_post( $post_id ) {
		$post_type = get_post_type( $post_id );

		if ( ! in_array( $post_type, self::$post_types_keys ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( 'revision' == $post_type ) {
			return;
		}

		if ( isset( $_POST[ self::ID ] ) && ! wp_verify_nonce( $_POST[ self::ID ], self::BASE ) ) {
			return;
		}

		remove_action( 'save_post', array( __CLASS__, 'save_post' ), 25 );

		self::save_post_items( $post_id );
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public static function admin_footer() {
		if ( self::$no_instance ) {
			return;
		}

		if ( empty( $_GET['post_type'] ) ) {
			global $post;

			$_GET['post_type'] = ! empty( $post->post_type ) ? $post->post_type : false;
		}

		if ( in_array( $_GET['post_type'], self::$post_types_keys ) ) {
			self::get_scripts();
		}
	}


	public static function column_checkbox_radio( $column, $current, $options, $field_type ) {
		$result = '';

		if ( ! is_array( $current ) ) {
			$current = array( $current );
		}

		foreach ( $options as $option ) {
			$parts = explode( '|', $option );
			$value = array_shift( $parts );
			if ( empty( $parts ) ) {
				$name = $value;
			} else {
				$name = array_shift( $parts );
			}

			if ( in_array( $value, $current ) ) {
				$result .= '<input type="' . $field_type . '" name="' . $column . '" value="' . $value . '" checked="checked" disabled="disabled" /> ' . $name . '<br />';
			}
		}

		return $result;
	}


	public static function column_select( $column, $current, $options, $field_type ) {
		$result = '';

		if ( ! is_array( $current ) ) {
			$current = array( $current );
		}

		$select_options = '';
		foreach ( $options as $option ) {
			$parts = explode( '|', $option );
			$value = array_shift( $parts );
			if ( empty( $parts ) ) {
				$name = $value;
			} else {
				$name = array_shift( $parts );
			}

			if ( in_array( $value, $current ) ) {
				$select_options .= '<option value="' . $value . '" selected="selected">' . $name . '</option>';
			}
		}

		$multiple = '';
		if ( 'multiple' == $field_type ) {
			$multiple = '" multiple="multiple';
		}

		if ( $select_options ) {
			$result  = '<select name="' . $column . $multiple . '" disabled="disabled">';
			$result .= $select_options;
			$result .= '</select>';
		}

		return $result;
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public static function column_taxonomies( $post_id, $column, $current, $options, $field_type ) {
		$taxonomy   = $column;
		$post_type  = get_post_type( $post_id );
		$post_terms = array();

		$terms = get_the_terms( $post_id, $taxonomy );
		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$post_terms[] = '<a href="edit.php?post_type=' . $post_type . '&' . $taxonomy . '=' . $term->slug . '">' . esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, $taxonomy, 'edit' ) ) . '</a>';
			}
		}

		$result = implode( ', ', $post_terms );

		return $result;
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public static function do_load() {
		$do_load = false;
		if ( ! empty( $GLOBALS['pagenow'] ) && in_array( $GLOBALS['pagenow'], array( 'edit.php', 'options.php' ) ) ) {
			$do_load = true;
		} elseif ( ! empty( $_REQUEST['page'] ) && Custom_Bulkquick_Edit_Settings::ID == $_REQUEST['page'] ) {
			$do_load = true;
		} elseif ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$do_load = true;
		}

		return $do_load;
	}


	public static function check_field_type( $field_type, $column_name ) {
		if ( in_array( $column_name, array( 'post_excerpt', 'post_title' ) ) ) {
			$field_type = 'textarea';
		}

		$field_type = apply_filters( 'cbqe_check_field_type', $field_type, $column_name );

		return $field_type;
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public static function pre_update_option_active_plugins( $plugins, $old_value ) {
		if ( ! is_array( $plugins ) ) {
			return $plugins;
		}

		if ( defined( 'CBQEP_BASE' ) ) {
			return $plugins;
		}

		$index = array_search( self::BASE, $plugins );
		if ( false !== $index && ! empty( $index ) ) {
			unset( $plugins[ $index ] );
			array_unshift( $plugins, self::BASE );
		}

		return $plugins;
	}


	public static function styles() {
		if ( is_admin() ) {
			wp_register_style( __CLASS__, self::$plugin_assets . 'css/custom-bulkquick-edit.css' );
		}

		wp_enqueue_style( __CLASS__ );

		do_action( 'cbqe_styles' );
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public static function column_categories( $post_id, $column, $current, $options, $field_type ) {
		$post_terms = array();
		$post_type  = get_post_type( $post_id );
		$taxonomy   = $column;
		$term_ids   = array();

		$terms = get_the_terms( $post_id, $taxonomy );
		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$post_terms[] = '<a href="edit.php?post_type=' . $post_type . '&' . $taxonomy . '=' . $term->slug . '">' . esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, $taxonomy, 'edit' ) ) . '</a>';
			}

			$term_ids = wp_list_pluck( $terms, 'term_id' );
		}

		$result  = implode( ', ', $post_terms );
		$result .= '<div class="hidden" id="' . $taxonomy . '_' . $post_id . '">' . implode( ',', $term_ids ) . '</div>';

		return $result;
	}


	public static function get_field_save_as( $post_type = null, $field_name = null ) {
		$key = self::get_field_key( $post_type, $field_name );

		if ( empty( $key ) ) {
			return false;
		}

		$key    .= Custom_Bulkquick_Edit_Settings::SAVE;
		$details = cbqe_get_option( $key );

		return $details;
	}


	public static function is_field_checkbox( $field_type ) {
		$field_type = apply_filters( 'cbqe_field_type_core', $field_type );
		if ( 'checkbox' == $field_type ) {
			return true;
		}

		return false;
	}
}

?>
