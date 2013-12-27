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

/**
 * Custom Bulk/Quick Edit settings class
 *
 * Based upon http://alisothegeek.com/2011/01/wordpress-settings-api-tutorial-1/
 */

if ( class_exists( 'Custom_Bulkquick_Edit_Settings' ) )
	return;

require_once CBQE_DIR_LIB . '/aihrus/class-aihrus-settings.php';


class Custom_Bulkquick_Edit_Settings extends Aihrus_Settings {
	const CONFIG = '__config__';
	const ENABLE = '__enable__';
	const ID     = 'custom-bulkquick-edit-settings';
	const NAME   = 'Custom Bulk/Quick Edit Settings';
	const RESET  = '__reset__';

	private static $post_types = array();

	public static $admin_page;
	public static $class          = __CLASS__;
	public static $config_counter = 0;
	public static $defaults       = array();
	public static $plugin_path;
	public static $plugin_url = 'http://wordpress.org/plugins/custom-bulkquick-edit-settings/';
	public static $scripts    = array();
	public static $sections   = array();
	public static $settings   = array();
	public static $version;


	public function __construct() {
		parent::__construct();

		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
		add_action( 'init', array( __CLASS__, 'init' ) );

		// restrict settings page to admins only
		if ( current_user_can( 'activate_plugins' ) )
			add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
	}


	public static function init() {
		load_plugin_textdomain( 'custom-bulkquick-edit', false, '/custom-bulkquick-edit/languages/' );

		$plugin_path = plugins_url( '', dirname( __FILE__ ) );
		$plugin_path = Custom_Bulkquick_Edit::strip_protocol( $plugin_path );

		self::$plugin_path = $plugin_path;
	}


	public static function sections() {
		self::$post_types = Custom_Bulkquick_Edit::get_post_types();
		foreach ( self::$post_types as $post_type => $label )
			self::$sections[ $post_type ] = $label;

		parent::sections();

		self::$sections = apply_filters( 'cbqe_sections', self::$sections );
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.Superglobals)
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public static function settings() {
		// General
		self::$settings['general'] = array(
			'desc' => esc_html__( 'TBD', 'custom-bulkquick-edit' ),
			'type' => 'heading',
		);

		$as_types = array(
			'' => esc_html__( 'No', 'custom-bulkquick-edit' ),
			'checkbox' => esc_html__( 'As checkbox', 'custom-bulkquick-edit' ),
			'input' => esc_html__( 'As input field', 'custom-bulkquick-edit' ),
			'radio' => esc_html__( 'As radio', 'custom-bulkquick-edit' ),
			'select' => esc_html__( 'As select', 'custom-bulkquick-edit' ),
			'textarea' => esc_html__( 'As textarea', 'custom-bulkquick-edit' ),
		);
		$as_types = apply_filters( 'cbqe_settings_as_types', $as_types );

		$as_taxonomy = array(
			'' => esc_html__( 'No', 'custom-bulkquick-edit' ),
			'show_only' => esc_html__( 'No, but enable column view', 'custom-bulkquick-edit' ),
			'categories' => esc_html__( 'Like categories', 'custom-bulkquick-edit' ),
			'taxonomy' => esc_html__( 'Like tags', 'custom-bulkquick-edit' ),
		);
		$as_taxonomy = apply_filters( 'cbqe_settings_as_taxonomy', $as_taxonomy );

		$desc_conf    = esc_html__( 'You may create options formatted like "the-key|Supremely, Pretty Values" seperated by newlines.', 'custom-bulkquick-edit' );
		$desc_edit    = esc_html__( 'Force making %1$s an editable taxonomy field like checked categories or free-text tags.', 'custom-bulkquick-edit' );
		$desc_excerpt = esc_html__( 'Enable editing of %1$s\' excerpt.', 'custom-bulkquick-edit' );
		$desc_remove  = esc_html__( 'During bulk editing, easily remove all of the %1$s\' prior relationships and add new.', 'custom-bulkquick-edit' );
		$desc_title   = esc_html__( 'Enable editing of %1$s\' title.', 'custom-bulkquick-edit' );

		$title_conf    = esc_html__( '%s Configuration', 'custom-bulkquick-edit' );
		$title_edit    = esc_html__( 'Edit "%s" taxonomy?', 'custom-bulkquick-edit' );
		$title_enable  = esc_html__( 'Enable "%s"?', 'custom-bulkquick-edit' );
		$title_excerpt = esc_html__( 'Excerpt', 'custom-bulkquick-edit' );
		$title_title   = esc_html__( 'Title', 'custom-bulkquick-edit' );
		$title_remove  = esc_html__( 'Reset "%s" Relations?', 'custom-bulkquick-edit' );

		foreach ( self::$post_types as $post_type => $label ) {
			self::$settings[ $post_type . self::ENABLE . 'post_title' ] = array(
				'section' => $post_type,
				'title' => sprintf( $title_enable, $title_title ),
				'label' => $title_title,
				'desc' => sprintf( $desc_title, $label ),
				'type' => 'checkbox',
			);

			$supports_excerpt = post_type_supports( $post_type, 'excerpt' );
			if ( $supports_excerpt ) {
				self::$settings[ $post_type . self::ENABLE . 'post_excerpt' ] = array(
					'section' => $post_type,
					'title' => sprintf( $title_enable, $title_excerpt ),
					'label' => $title_excerpt,
					'desc' => sprintf( $desc_excerpt, $label ),
					'type' => 'checkbox',
				);
			}

			$taxonomy_name = array();
			$taxonomies    = get_object_taxonomies( $post_type, 'objects' );
			$taxonomies    = apply_filters( 'cbqe_settings_taxonomies', $taxonomies );
			foreach ( $taxonomies as $taxonomy ) {
				$name = $taxonomy->name;
				if ( 'post_format' == $name || empty( $taxonomy->label ) )
					continue;

				$tax_label       = $taxonomy->label;
				$taxonomy_name[] = $name;

				self::$settings[ $post_type . self::ENABLE . $name ] = array(
					'section' => $post_type,
					'title' => sprintf( $title_edit, $tax_label ),
					'label' => $tax_label,
					'desc' => sprintf( $desc_edit, $tax_label ),
					'type' => 'select',
					'choices' => $as_taxonomy,
				);

				self::$settings[ $post_type . self::ENABLE . $name . self::RESET ] = array(
					'section' => $post_type,
					'title' => sprintf( $title_remove, $tax_label ),
					'label' => sprintf( $title_remove, $tax_label ),
					'desc' => sprintf( $desc_remove, $tax_label ),
					'type' => 'checkbox',
				);
			}

			$fields = array();
			if ( 'page' != $post_type )
				$filter = 'manage_posts_columns';
			else
				$filter = 'manage_pages_columns';

			$fields      = apply_filters( $filter, $fields );
			$filter      = 'manage_' . $post_type . '_posts_columns';
			$fields      = apply_filters( $filter, $fields );
			$filter_edit = 'manage_edit-' . $post_type . '_columns';
			$fields      = apply_filters( $filter_edit, $fields );
			$fields      = apply_filters( 'cbqe_settings_fields', $fields, $post_type );
			if ( ! empty( $fields ) ) {
				// don't edit these common/static fields with this plugin
				unset( $fields['author'] );
				unset( $fields['cb'] );
				unset( $fields['date'] );
				unset( $fields['id'] );
				unset( $fields['post_excerpt'] );

				$doc = new DOMDocument();

				foreach ( $fields as $field => $label ) {
					$alt   = '';
					$title = '';

					// convert img to just alt/title tag
					if ( false !== stristr( $label, '<img' ) ) {
						$doc->loadHTML( $label );

						$xpath   = new DOMXPath( $doc );
						$results = $xpath->query( '//*[@alt]' );
						foreach ( $results as $node )
							$alt = $node->getAttribute( 'alt' );

						if ( empty( $alt ) ) {
							$results = $xpath->query( '//*[@title]' );
							foreach ( $results as $node )
								$title = $node->getAttribute( 'title' );

							if ( empty( $title ) )
								unset( $fields[ $field ] );
							else
								$fields[ $field ] = $title;
						} else {
							$fields[ $field ] = $alt;
						}
					}
				}
			}

			if ( ! empty( $fields ) ) {
				foreach ( $fields as $field => $label ) {
					if ( in_array( $field, $taxonomy_name ) )
						continue;

					self::$settings[ $post_type . self::ENABLE . $field ] = array(
						'section' => $post_type,
						'title' => sprintf( $title_enable, $label ),
						'label' => $label,
						'type' => 'select',
						'choices' => $as_types,
						'has_config' => 1,
					);

					$desc_conf_tmp = apply_filters( 'cbqe_settings_config_desc', $desc_conf, $post_type, $field );

					self::$settings[ $post_type . self::ENABLE . $field . self::CONFIG ] = array(
						'section' => $post_type,
						'title' => sprintf( $title_conf, $label ),
						'label' => $label,
						'desc' => $desc_conf_tmp,
						'type' => 'textarea',
						'validate' => 'trim',
					);
				}
			}

			self::$settings = apply_filters( 'cbqe_settings_post_type', self::$settings, $post_type, $label );

			$action = 'manage_' . $post_type . '_posts_custom_column';
			if ( ! has_action( $action ) ) {
				add_action( $action, array( 'Custom_Bulkquick_Edit', 'manage_posts_custom_column' ), 199, 2 );
			} else {
				add_action( $action, array( 'Custom_Bulkquick_Edit', 'manage_posts_custom_column_precapture' ), 1, 2 );
				add_action( $action, array( 'Custom_Bulkquick_Edit', 'manage_posts_custom_column_capture' ), 199, 2 );
			}

			add_filter( $filter, array( 'Custom_Bulkquick_Edit', 'manage_columns' ), 199 );
		}

		parent::settings();

		self::$settings = apply_filters( 'cbqe_settings', self::$settings );
		foreach ( self::$settings as $id => $parts )
			self::$settings[ $id ] = wp_parse_args( $parts, self::$default );
	}


	public static function get_defaults( $mode = null, $old_version = null ) {
		$old_version = cbqe_get_option( 'version' );

		return parent::get_defaults( $mode, $old_version );
	}


	public static function admin_init() {
		$version       = cbqe_get_option( 'version' );
		self::$version = Custom_Bulkquick_Edit::VERSION;
		self::$version = apply_filters( 'cbqe_version', self::$version );

		if ( $version != self::$version )
			self::initialize_settings();

		if ( ! Custom_Bulkquick_Edit::do_load() )
			return;

		self::load_options();
		self::register_settings();
	}


	public static function admin_menu() {
		self::$admin_page = add_options_page( esc_html__( 'Custom Bulk/Quick Settings', 'custom-bulkquick-edit' ), esc_html__( 'Custom Bulk/Quick', 'custom-bulkquick-edit' ), 'manage_options', self::ID, array( __CLASS__, 'display_page' ) );

		add_action( 'admin_print_scripts-' . self::$admin_page, array( __CLASS__, 'scripts' ) );
		add_action( 'admin_print_styles-' . self::$admin_page, array( __CLASS__, 'styles' ) );
	}


	public static function display_page( $disable_donate = false ) {
		$disable_donate = cbqe_get_option( 'disable_donate' );

		parent::display_page( $disable_donate );
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public static function display_setting( $args = array(), $do_echo = true, $input = null ) {
		$content = apply_filters( 'cbqe_settings_display_setting', '', $args, $input );
		if ( empty( $content ) )
			$content = parent::display_setting( $args, false, $input );

		$id = $args['id'];
		if ( strstr( $id, Custom_Bulkquick_Edit_Settings::CONFIG ) ) {
			$field = str_replace( Custom_Bulkquick_Edit_Settings::CONFIG, '', $id );
			$f     = 'f' . ++self::$config_counter;
			$c     = 'c' . self::$config_counter;
			$hide  = "'' === val || 'input' == val || 'textarea' == val";

			$script = <<<EOD
<script type="text/javascript">
	jQuery(document).ready( function() {
		{$f} = jQuery( '#{$field}' );
		{$c} = jQuery( '#{$id}' );

		val = {$f}.val();
		if ( {$hide} )
			{$c}.parent().parent().hide();

		{$f}.change( function() {
			val = {$f}.val();
			if ( {$hide} )
				{$c}.parent().parent().hide();
			else
				{$c}.parent().parent().show();
		});
	});
</script>
EOD;

			$script = apply_filters( 'cbqe_settings_config_script', $script, $args, $id, $field, $f, $c, $hide );

			self::$scripts[] = $script;
		}

		if ( ! $do_echo )
			return $content;

		echo $content;
	}


	public static function initialize_settings( $version = null ) {
		$version = cbqe_get_option( 'version', self::$version );

		parent::initialize_settings( $version );
	}


	public static function scripts() {
		parent::scripts();

		add_action( 'admin_footer', array( __CLASS__, 'get_scripts' ), 20 );
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public static function validate_settings( $input, $options = null, $do_errors = false ) {
		if ( is_null( $options ) ) {
			$options      = self::get_settings();
			$null_options = true;
		} else
			$null_options = false;

		foreach ( $options as $id => $parts ) {
			$default = $parts['std'];

			// ensure default config
			if ( strstr( $id, Custom_Bulkquick_Edit_Settings::CONFIG ) ) {
				$config = $input[ $id ];
				if ( empty( $config ) ) {
					$field = str_replace( Custom_Bulkquick_Edit_Settings::CONFIG, '', $id );
					$type  = $input[ $field ];
					switch ( $type ) {
						case 'checkbox';
							$default = '1|' . esc_html__( 'Enable', 'custom-bulkquick-edit' );
							break;

						case 'radio';
						case 'select';
							$default  = '';
							$default .= esc_html__( 'Yes', 'custom-bulkquick-edit' );
							$default .= "\n";
							$default .= 'no|' . esc_html__( 'No', 'custom-bulkquick-edit' );
							$default .= "\n";
							$default .= 'where-beef|' . esc_html__( 'Where\'s the beef?', 'custom-bulkquick-edit' );
							break;

						default:
							$default = apply_filters( 'cbqe_configuration_default', $default, $id, $type );
							break;
					}

					$input[ $id ] = $default ;
				}
			}
		}

		if ( $null_options )
			$options = null;

		$validated = parent::validate_settings( $input, $options, $do_errors );

		if ( empty( $do_errors ) )
			$input = $validated;
		else {
			$input  = $validated['input'];
			$errors = $validated['errors'];
		}

		$input['version']        = self::$version;
		$input['donate_version'] = Custom_Bulkquick_Edit::VERSION;

		$input = apply_filters( 'cbqe_validate_settings', $input, $errors );
		if ( empty( $do_errors ) )
			$validated = $input;
		else {
			$validated = array(
				'input' => $input,
				'errors' => $errors,
			);
		}

		return $validated;
	}


}


function cbqe_get_options() {
	$options = get_option( Custom_Bulkquick_Edit_Settings::ID );

	if ( false === $options ) {
		$options = Custom_Bulkquick_Edit_Settings::get_defaults();
		update_option( Custom_Bulkquick_Edit_Settings::ID, $options );
	}

	return $options;
}


function cbqe_get_option( $option, $default = null ) {
	$options = get_option( Custom_Bulkquick_Edit_Settings::ID );

	if ( isset( $options[$option] ) )
		return $options[$option];
	else
		return $default;
}


function cbqe_set_option( $option, $value = null ) {
	$options = get_option( Custom_Bulkquick_Edit_Settings::ID );

	if ( ! is_array( $options ) )
		$options = array();

	$options[$option] = $value;
	update_option( Custom_Bulkquick_Edit_Settings::ID, $options );
}


?>
